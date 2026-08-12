<?php

namespace App\Providers;

use App\Tag;
use App\Item;
use App\Models\Employee;
use App\Models\Business;
use App\Models\Tool;
use App\Models\Company;
use App\Models\Property;
use App\Models\Vehicle;
use App\Models\Product;
use App\Models\ProductSet;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\CustomerType;
use App\Models\PurchaseTicket;
use App\Models\PurchaseTicketItem;
use App\Models\Photography;
use App\Role;
use App\User;
use App\Category;
use App\Policies\TagPolicy;
use App\Policies\ItemPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\BusinessPolicy;
use App\Policies\ToolPolicy;
use App\Policies\MasterPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductSetPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CustomerTypePolicy;
use App\Policies\PostingPolicy;
use App\Policies\PurchaseTicketPolicy;
use App\Policies\PurchaseTicketItemPolicy;
use App\Policies\PhotographyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Category::class => CategoryPolicy::class,
        CustomerType::class => CustomerTypePolicy::class,
        Item::class => ItemPolicy::class,
        Employee::class => EmployeePolicy::class,
        Vehicle::class => VehiclePolicy::class,
        Business::class => BusinessPolicy::class,
        Tool::class => ToolPolicy::class,
        Posting::class => PostingPolicy::class,
        Bank::class => MasterPolicy::class,
        Company::class => CompanyPolicy::class,
        Property::class => PropertyPolicy::class,
        Product::class => ProductPolicy::class,
        ProductSet::class => ProductSetPolicy::class,
        Customer::class => CustomerPolicy::class,
        PurchaseTicket::class => PurchaseTicketPolicy::class,
        PurchaseTicketItem::class => PurchaseTicketItemPolicy::class,
        Role::class => RolePolicy::class,
        Tag::class => TagPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        Photography::class => PhotographyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-masters', 'App\Policies\UserPolicy@manageMasters');
        Gate::define('manage-purchase_tickets', 'App\Policies\UserPolicy@managePurchaseTickets');
        Gate::define('manage-postings', 'App\Policies\UserPolicy@managePostings');
        Gate::define('manage-companies', 'App\Policies\UserPolicy@manageCompanies');
        Gate::define('manage-vehicles', 'App\Policies\UserPolicy@manageVehicles');
        Gate::define('manage-businesses', 'App\Policies\UserPolicy@manageBusinesses');
        Gate::define('manage-customers', 'App\Policies\UserPolicy@manageCustomers');
        Gate::define('manage-tools', 'App\Policies\UserPolicy@manageTools');
        Gate::define('manage-products', 'App\Policies\UserPolicy@manageProducts');
        Gate::define('manage-employees', 'App\Policies\UserPolicy@manageEmployees');
        Gate::define('manage-items', 'App\Policies\UserPolicy@manageItems');
        Gate::define('manage-users', 'App\Policies\UserPolicy@manageUsers');
        Gate::define('manage-warehouses', 'App\Policies\UserPolicy@manageWarehouses');
        Gate::define('manage-photography', 'App\Policies\UserPolicy@managePhotography');
    }
}
