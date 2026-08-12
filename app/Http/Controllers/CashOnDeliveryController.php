<?php
// app/Http/Controllers/CashOnDeliveryController.php

namespace App\Http\Controllers;

use App\Models\Package\CashOnDelivery;
use App\Models\Package\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashOnDeliveryController extends Controller
{
    public function index()
    {
        $cashOnDeliveries = CashOnDelivery::with(['package.order'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cash-on-delivery.index', compact('cashOnDeliveries'));
    }

    public function create()
    {
        // Get packages that are MPL shipping and don't have cash on delivery yet
        //$packages = Package::where('shipping_service', 'MPL')
        //    ->whereDoesntHave('cashOnDelivery')
        $packages = Package::with(['order'])
            ->get();

        return view('cash-on-delivery.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id|unique:cash_on_deliveries,package_id',
            'amount' => 'required|numeric|min:0',
            'bank_account_number' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        CashOnDelivery::create([
            'package_id' => $request->package_id,
            'amount' => $request->amount,
            'bank_account_number' => $request->bank_account_number,
            'recipient_name' => $request->recipient_name,
            'bank_name' => $request->bank_name,
            'currency' => $request->currency,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('cash-on-delivery.index')
            ->with('success', 'Utánvét sikeresen létrehozva!');
    }

    public function show(CashOnDelivery $cashOnDelivery)
    {
        $cashOnDelivery->load(['package.order', 'package.items.orderItem']);

        return view('cash-on-delivery.show', compact('cashOnDelivery'));
    }

    public function edit(CashOnDelivery $cashOnDelivery)
    {
        $cashOnDelivery->load(['package']);

        return view('cash-on-delivery.edit', compact('cashOnDelivery'));
    }

    public function update(Request $request, CashOnDelivery $cashOnDelivery)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'bank_account_number' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,collected,transferred,cancelled',
        ]);

        $cashOnDelivery->update($request->all());

        return redirect()->route('cash-on-delivery.index')
            ->with('success', 'Utánvét sikeresen frissítve!');
    }

    public function destroy(CashOnDelivery $cashOnDelivery)
    {
        $cashOnDelivery->delete();

        return redirect()->route('cash-on-delivery.index')
            ->with('success', 'Utánvét sikeresen törölve!');
    }

    public function markAsCollected(CashOnDelivery $cashOnDelivery)
    {
        $cashOnDelivery->markAsCollected();

        return redirect()->route('cash-on-delivery.index')
            ->with('success', 'Utánvét státusza "Beszedve"-re módosítva!');
    }

    public function markAsTransferred(CashOnDelivery $cashOnDelivery)
    {
        $cashOnDelivery->markAsTransferred();

        return redirect()->route('cash-on-delivery.index')
            ->with('success', 'Utánvét státusza "Átutalva"-ra módosítva!');
    }

    public function pending()
    {
        $cashOnDeliveries = CashOnDelivery::with(['package.order'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cash-on-delivery.index', compact('cashOnDeliveries'))
            ->with('title', 'Függőben lévő utánvétek');
    }

    public function collected()
    {
        $cashOnDeliveries = CashOnDelivery::with(['package.order'])
            ->where('status', 'collected')
            ->orderBy('collected_at', 'desc')
            ->paginate(20);

        return view('cash-on-delivery.index', compact('cashOnDeliveries'))
            ->with('title', 'Beszedett utánvétek');
    }

    public function transferred()
    {
        $cashOnDeliveries = CashOnDelivery::with(['package.order'])
            ->where('status', 'transferred')
            ->orderBy('transferred_at', 'desc')
            ->paginate(20);

        return view('cash-on-delivery.index', compact('cashOnDeliveries'))
            ->with('title', 'Átutalt utánvétek');
    }

    public function statistics()
    {
        $stats = DB::table('cash_on_deliveries')
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('status')
            ->get();

        $totalAmount = CashOnDelivery::sum('amount');
        $collectedAmount = CashOnDelivery::where('status', 'collected')->sum('amount');
        $transferredAmount = CashOnDelivery::where('status', 'transferred')->sum('amount');
        $pendingAmount = CashOnDelivery::where('status', 'pending')->sum('amount');

        $recentActivities = CashOnDelivery::with(['package'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('cash-on-delivery.statistics', compact(
            'stats', 
            'totalAmount', 
            'collectedAmount', 
            'transferredAmount', 
            'pendingAmount',
            'recentActivities'
        ));
    }
}