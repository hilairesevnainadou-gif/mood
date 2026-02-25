<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentCallbackController extends Controller
{
    /**
     * CALLBACK KKIAPAY - Seule méthode dans ce contrôleur
     */
    public function kkiapayCallback(Request $request)
    {
        try {
            Log::info('=== KKIAPAY CALLBACK ===', [
                'method' => $request->method(),
                'ip' => $request->ip(),
                'data_keys' => array_keys($request->all())
            ]);

            $data = $request->all();

            $transactionId = $data['transactionId'] ?? $data['transaction_id'] ?? null;
            $status = strtolower($data['status'] ?? 'unknown');
            $amount = (float) ($data['amount'] ?? 0);

            if (!$transactionId || $amount <= 0) {
                Log::error('Callback données invalides', ['data' => $data]);
                return response()->json(['success' => false, 'message' => 'Données invalides'], 400);
            }

            $transaction = DB::transaction(function () use ($transactionId, $status, $amount, $data) {
                $tx = Transaction::where('transaction_id', $transactionId)
                    ->orWhere('reference', $transactionId)
                    ->lockForUpdate()
                    ->first();

                if (!$tx) {
                    Log::warning('Transaction non trouvée', ['transaction_id' => $transactionId]);
                    return null;
                }

                if ($tx->status === 'completed') {
                    return ['already_processed' => true, 'transaction' => $tx];
                }

                if ($status === 'success' || $status === 'completed') {
                    $tx->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'metadata' => json_encode(array_merge(
                            json_decode($tx->metadata ?? '{}', true),
                            ['kkiapay_callback' => $data, 'processed_at' => now()->toIso8601String()]
                        ))
                    ]);

                    $wallet = Wallet::lockForUpdate()->find($tx->wallet_id);
                    if ($wallet) {
                        $wallet->increment('balance', $amount);
                        $wallet->update(['last_transaction_at' => now()]);

                        Cache::forget("wallet_user_{$wallet->user_id}");
                        Cache::forget("wallet_tx_{$wallet->id}");
                    }

                    return ['success' => true, 'transaction' => $tx, 'wallet' => $wallet];
                }

                $tx->update([
                    'status' => 'failed',
                    'metadata' => json_encode(['failure_reason' => $status, 'data' => $data])
                ]);

                return ['success' => false, 'transaction' => $tx];
            }, 3);

            if ($transaction === null) {
                return response()->json(['success' => false, 'message' => 'Transaction non trouvée'], 404);
            }

            if (isset($transaction['already_processed'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Déjà traitée',
                    'transaction_id' => $transaction['transaction']->id
                ]);
            }

            return response()->json([
                'success' => $transaction['success'] ?? false,
                'message' => ($transaction['success'] ?? false) ? 'Paiement traité' : 'Paiement échoué'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur callback Kkiapay: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Pages de paiement statiques
     */
    public function paymentSuccess(\App\Models\Transaction $transaction)
    {
        if (auth()->id() !== $transaction->wallet->user_id) abort(403);
        return view('payment.success', compact('transaction'));
    }

    public function paymentFailed(\App\Models\Transaction $transaction)
    {
        if (auth()->id() !== $transaction->wallet->user_id) abort(403);
        return view('payment.failed', compact('transaction'));
    }

    public function paymentError()
    {
        return view('payment.error');
    }

    public function paymentStatus(\App\Models\Transaction $transaction)
    {
        if (auth()->id() !== $transaction->wallet->user_id) abort(403);
        return view('payment.status', compact('transaction'));
    }
}
