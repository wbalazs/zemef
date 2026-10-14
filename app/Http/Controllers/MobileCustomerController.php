<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCard\CustomerCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class MobileCustomerController extends Controller
{
    public function login(Request $request)
    {
        if ($request->session()->has('mobile_customer')) {
            return redirect()->route('mobile.customer');
        }

        return response()->view('mobile.customer.login')->header('Cache-Control', 'no-store, private');
    }

    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:50'],
        ]);
        $key = 'mobile-customer-login:' . hash('sha256', (string) $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return redirect()->route('mobile.customer.login')
                ->withErrors(['email' => 'Túl sok sikertelen próbálkozás. Próbálja újra egy perc múlva.'])
                ->withInput(['email' => $validated['email']]);
        }

        $card = CustomerCard::where('code', trim($validated['password']))
            ->where('is_active', true)
            ->first();
        $customers = $card ? $card->customers()->where('email', trim($validated['email']))
            ->where('status', 1)->get() : collect();
        if ($customers->count() !== 1 || !hash_equals((string) $card->code, trim($validated['password']))) {
            RateLimiter::hit($key, 60);
            return redirect()->route('mobile.customer.login')
                ->withErrors(['email' => 'Az email cím és a kártyakód párosa nem megfelelő, vagy a kártya inaktív.'])
                ->withInput(['email' => $validated['email']]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->put('mobile_customer', [
            'customer_id' => $customers->first()->customer_id,
            'card_id' => $card->id,
        ]);

        return redirect()->route('mobile.customer');
    }

    public function show(Request $request)
    {
        $identity = $request->session()->get('mobile_customer', []);
        $customer = isset($identity['customer_id'])
            ? Customer::where('status', 1)->find($identity['customer_id']) : null;
        $card = $customer && isset($identity['card_id'])
            ? CustomerCard::with(['businesses', 'products', 'productTypeRules.productType', 'productGroupRules.productGroup'])
                ->where('is_active', true)
                ->whereHas('customers', fn ($query) => $query->where('customers.customer_id', $customer->customer_id))
                ->find($identity['card_id']) : null;
        if (!$customer || !$card) {
            $request->session()->forget('mobile_customer');
            return redirect()->route('mobile.customer.login');
        }

        return response()->view('mobile.customer.show', compact('customer', 'card'))
            ->header('Cache-Control', 'no-store, private');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('mobile_customer');
        $request->session()->regenerate();

        return redirect()->route('mobile.customer.login');
    }
}