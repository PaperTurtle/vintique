<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with('product')
            ->whereHas('product', function ($query) {
                $query->where('artisan_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                $transaction->delivered_on = $transaction->updated_at->format('F j, Y');

                return $transaction;
            })
            ->groupBy(function ($transaction) {
                return $transaction->created_at->toDateTimeString();
            });

        return view('dashboard', compact('transactions'));
    }

    public function markAsSent(Transaction $transaction): JsonResponse
    {
        if ($transaction->product->artisan_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        $transaction->update(['status' => 'sent']);

        return response()->json(['message' => 'Product marked as sent']);
    }
}
