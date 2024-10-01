<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The DashboardController is responsible for handling user-specific operations in a dashboard setting.
 * This includes displaying a list of transactions related to the authenticated user and marking transactions as sent.
 */
class DashboardController extends Controller
{
    /**
     * Display a listing of transactions related to the authenticated user.
     * Retrieves transactions associated with the products of the authenticated user, typically an artisan,
     * and passes them to the dashboard view.
     *
     * @return View returns a view of the dashboard with transaction details
     */
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

    /**
     * Mark a transaction as sent.
     * Updates the status of a transaction to 'sent' if the authenticated user (an artisan) is authorized to do so.
     * Responds with JSON indicating the success or unauthorized access of the operation.
     *
     * @param  Transaction  $transaction the transaction to be marked as sent
     * @return JsonResponse returns JSON response with a success message or an unauthorized access message
     */
    public function markAsSent(Transaction $transaction): JsonResponse
    {
        if ($transaction->product->artisan_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        $transaction->update(['status' => 'sent']);

        return response()->json(['message' => 'Product marked as sent']);
    }
}
