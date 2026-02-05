<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('wallet')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function validateTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Transaction validée.');
    }
}
