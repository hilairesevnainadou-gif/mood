<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Callback Kkiapay - Gère le retour du paiement
     */
    public function kkiapayCallback(Request $request)
    {
        try {
            // Log pour debug
            Log::info('Kkiapay callback received', [
                'method' => $request->method(),
                'data' => $request->all(),
                'ip' => $request->ip()
            ]);

            // Validation des données reçues
            $validated = $request->validate([
                'transactionId' => 'required|string',
                'status' => 'required|string|in:success,failed,pending',
                'reference' => 'nullable|string',
                'amount' => 'nullable|numeric',
                'phone' => 'nullable|string',
            ]);

            // Recherche la transaction par référence ou transaction_id
            $transaction = Transaction::where('transaction_id', $validated['transactionId'])
                ->orWhere('reference', $validated['reference'] ?? null)
                ->first();

            if (!$transaction) {
                Log::error('Transaction not found', $validated);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Évite les double traitements
            if ($transaction->isCompleted()) {
                Log::info('Transaction already completed', ['id' => $transaction->id]);
                return $this->redirectAfterPayment($transaction, 'already_completed');
            }

            DB::beginTransaction();

            try {
                if ($validated['status'] === 'success') {
                    // Met à jour la transaction
                    $transaction->markAsCompleted();
                    
                    // Crédite le wallet si c'est un dépôt
                    if ($transaction->type === 'deposit' || $transaction->type === 'credit') {
                        $wallet = $transaction->wallet;
                        if ($wallet) {
                            $wallet->credit($transaction->amount);
                        }
                    }

                    DB::commit();
                    
                    Log::info('Payment completed successfully', ['transaction_id' => $transaction->id]);
                    return $this->redirectAfterPayment($transaction, 'success');

                } else {
                    // Échec du paiement
                    $transaction->markAsFailed($validated['status']);
                    DB::commit();
                    
                    return $this->redirectAfterPayment($transaction, 'failed');
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Kkiapay callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            // Redirection vers page d'erreur utilisateur
            return redirect()->route('payment.error')
                ->with('error', 'Une erreur est survenue lors du traitement du paiement.');
        }
    }

    /**
     * Redirection après paiement selon le statut
     */
    private function redirectAfterPayment(Transaction $transaction, string $status)
    {
        $routes = [
            'success' => 'payment.success',
            'failed' => 'payment.failed',
            'already_completed' => 'payment.success',
        ];

        $route = $routes[$status] ?? 'payment.status';

        // Pour API/AJAX
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => in_array($status, ['success', 'already_completed']),
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                    'amount' => $transaction->formatted_amount,
                ],
                'redirect_url' => route($route, $transaction->id)
            ]);
        }

        // Redirection web avec message
        $messages = [
            'success' => 'Paiement effectué avec succès !',
            'failed' => 'Le paiement a échoué.',
            'already_completed' => 'Paiement déjà traité.',
        ];

        return redirect()->route($route, $transaction->id)
            ->with($status === 'success' || $status === 'already_completed' ? 'success' : 'error', 
                   $messages[$status] ?? 'Statut inconnu');
    }

    /**
     * Page de succès après paiement (Vue utilisateur)
     */
    public function paymentSuccess(Transaction $transaction)
    {
        // Vérifie que l'utilisateur peut voir cette transaction
        if (auth()->check() && $transaction->wallet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.success', compact('transaction'));
    }

    /**
     * Page d'échec après paiement
     */
    public function paymentFailed(Transaction $transaction)
    {
        return view('payment.failed', compact('transaction'));
    }

    /**
     * Page d'erreur générique
     */
    public function paymentError()
    {
        return view('payment.error');
    }
}