<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCard\CustomerCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class MobileCustomerTest extends TestCase
{
    private array $temporaryTables = [];
    private $eventDispatcher;
    private Customer $customer;
    private CustomerCard $card;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        $tables = ['customers', 'customer_cards', 'customer_card_customer', 'customer_card_business',
            'customer_card_inventory', 'businesses', 'inventory',
            (new CustomerCard())->productTypeRules()->getRelated()->getTable(),
            (new CustomerCard())->productGroupRules()->getRelated()->getTable()];
        foreach ($tables as $table) {
            $columns = DB::select('SHOW COLUMNS FROM `' . $table . '`');
            $definitions = array_map(function ($column) {
                return '`' . $column->Field . '` ' . $column->Type
                    . (str_contains($column->Extra, 'auto_increment') ? ' NOT NULL AUTO_INCREMENT PRIMARY KEY' : ' NULL');
            }, $columns);
            DB::statement('CREATE TEMPORARY TABLE `' . $table . '` (' . implode(', ', $definitions) . ') ENGINE=InnoDB');
            $this->temporaryTables[] = $table;
        }
        $this->customer = Customer::create(['name' => 'Test Customer', 'email' => 'customer@example.test', 'phone' => '+36 30 123 4567', 'status' => 1]);
        $this->card = CustomerCard::create(['name' => 'Customer Card', 'code' => 'SECRET-123', 'type' => 'percent', 'value' => 12.5,
            'start_date' => today()->subDay(), 'end_date' => today()->addMonth(), 'is_active' => true]);
        $this->card->customers()->attach($this->customer->customer_id);
        RateLimiter::clear('mobile-customer-login:' . hash('sha256', '127.0.0.1'));
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryTables as $table) DB::statement('DROP TEMPORARY TABLE `' . $table . '`');
        if ($this->eventDispatcher) Model::setEventDispatcher($this->eventDispatcher);
        parent::tearDown();
    }

    private function credentials(): array
    {
        return ['email' => $this->customer->email, 'password' => $this->card->code];
    }

    public function test_guest_routes_and_successful_customer_session(): void
    {
        $this->get('/mobil/customer')->assertRedirect(route('mobile.customer.login'));
        $this->get('/mobil/customer/login')->assertOk()->assertSee('Bejelentkezés');
        $this->withSession(['erp_test_state' => 'preserved'])->post('/mobil/customer/login', $this->credentials())
            ->assertRedirect(route('mobile.customer'))->assertSessionHas('mobile_customer.customer_id', $this->customer->customer_id);
        $this->assertGuest();
        $this->get('/mobil/customer')->assertOk()->assertSee('Test Customer')->assertSee($this->customer->email)
            ->assertSee($this->customer->phone)->assertSee('12,5')->assertSee('Érvényes')
            ->assertDontSee($this->card->code)->assertHeader('Cache-Control', 'no-store, private');
        $this->post('/mobil/customer/logout')->assertRedirect(route('mobile.customer.login'))
            ->assertSessionMissing('mobile_customer')->assertSessionHas('erp_test_state', 'preserved');
        $this->get('/mobil/customer')->assertRedirect(route('mobile.customer.login'));
    }

    public function test_unrelated_email_and_inactive_card_cannot_log_in(): void
    {
        Customer::create(['name' => 'Other Customer', 'email' => 'other@example.test', 'status' => 1]);
        $this->post('/mobil/customer/login', ['email' => 'other@example.test', 'password' => $this->card->code])
            ->assertSessionHasErrors('email')->assertSessionMissing('mobile_customer');
        $this->assertNull(session()->get('_old_input.password'));
        $this->get('/mobil/customer/login')->assertDontSee($this->card->code);
        $this->card->update(['is_active' => false]);
        $this->post('/mobil/customer/login', $this->credentials())->assertSessionHasErrors('email')->assertSessionMissing('mobile_customer');
    }

    public function test_expired_discount_is_shown_and_revoked_ownership_ends_access(): void
    {
        $this->card->update(['start_date' => today()->subMonth(), 'end_date' => today()->subDay()]);
        $this->post('/mobil/customer/login', $this->credentials())->assertRedirect(route('mobile.customer'));
        $this->get('/mobil/customer')->assertOk()->assertSee('Lejárt');
        $this->card->customers()->detach($this->customer->customer_id);
        $this->get('/mobil/customer')->assertRedirect(route('mobile.customer.login'))->assertSessionMissing('mobile_customer');
    }

    public function test_failed_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/mobil/customer/login', ['email' => $this->customer->email, 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/mobil/customer/login', $this->credentials())->assertSessionHasErrors('email')->assertSessionMissing('mobile_customer');
        $this->get('/mobil/customer/login')->assertSee('Túl sok sikertelen próbálkozás');
    }
}