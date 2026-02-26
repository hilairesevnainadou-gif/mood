<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KkiapayCallbackController extends Controller
{
    /**
     * Gère le callback de Kkiapay
     */
    public function handleCallback(Request $request)
    {
        // Log TOUTES les données reçues
        Log::info('=== KKIAPAY CALLBACK START ===', [
            'method' => $request->method(),
            'ip' => $request->ip(),
            'all_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Validation des données
            $validated = $request->validate([
                'transaction_id' => 'required|string',
                'status' => 'required|string|in:success,failed,pending',
                'amount' => 'required|numeric',
                'phone' => 'required|string',
            ]);

            $transactionId = $validated['transaction_id'];
            $status = $validated['status'];
            $amount = $validated['amount'];
            $phone = $validated['phone'];

            Log::info('Données validées', [
                'transaction_id' => $transactionId,
                'status' => $status,
                'amount' => $amount,
                'phone' => $phone
            ]);

            // Rechercher la transaction
            $transaction = Transaction::where('reference', $transactionId)
                ->orWhere('kkiapay_transaction_id', $transactionId)
                ->first();

            if (!$transaction) {
                Log::error('Transaction non trouvée', [
                    'transaction_id' => $transactionId,
                    'references_existantes' => Transaction::pluck('reference')->toArray()
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            Log::info('Transaction trouvée', [
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'current_status' => $transaction->status,
                'type' => $transaction->type,
                'wallet_id' => $transaction->wallet_id
            ]);

            // Si déjà complétée, ne pas retraiter
            if ($transaction->status === 'completed') {
                Log::info('Transaction déjà complétée, skip');
                return response()->json(['success' => true, 'message' => 'Already processed'], 200);
            }

            // Traiter le succès
            if ($status === 'success') {
                Log::info('Traitement paiement succès - DÉBUT');

                DB::beginTransaction();

                try {
                    // 1. Mettre à jour la transaction
                    $transaction->update([
                        'kkiapay_transaction_id' => $transactionId,
                        'status' => 'completed',
                        'kkiapay_response' => $request->all(),
                        'paid_at' => now(),
                        'completed_at' => now()
                    ]);

                    Log::info('Transaction mise à jour', ['transaction_id' => $transaction->id]);

                    // 2. VÉRIFIER LE TYPE ET CRÉDITER
                    Log::info('Vérification type transaction', [
                        'type' => $transaction->type,
                        'is_credit' => $transaction->type === 'credit',
                        'is_deposit' => $transaction->type === 'deposit'
                    ]);

                    // CORRECTION: Accepter 'credit' comme type valide pour dépôt
                    if ($transaction->type === 'credit' || $transaction->type === 'deposit') {
                        Log::info('Type valide pour crédit, appel creditWallet');
                        $this->creditWallet($transaction);
                    } else {
                        Log::warning('Type non valide pour crédit automatique', ['type' => $transaction->type]);
                    }

                    // 3. Confirmer les frais si applicable
                    if ($transaction->type === 'fee_payment') {
                        $this->confirmFundingRequest($transaction);
                    }

                    DB::commit();

                    Log::info('=== KKIAPAY CALLBACK SUCCESS - COMMIT EFFECTUÉ ===');

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Erreur dans transaction DB, rollback effectué', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }

            } else {
                // Échec
                $transaction->update([
                    'kkiapay_transaction_id' => $transactionId,
                    'status' => 'failed',
                    'kkiapay_response' => $request->all(),
                    'failure_reason' => $request->input('failure_reason', 'Payment failed'),
                    'paid_at' => now()
                ]);

                Log::warning('Paiement échoué', ['status' => $status]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed'
            ], 200);

        } catch (\Exception $e) {
            Log::error('=== KKIAPAY CALLBACK ERROR ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * CRÉDITE le wallet - VERSION CORRIGÉE AVEC LOGS
     */
    private function creditWallet($transaction)
    {
        Log::info('creditWallet appelé', [
            'transaction_id' => $transaction->id,
            'wallet_id' => $transaction->wallet_id,
            'amount' => $transaction->amount
        ]);

        // Récupérer le wallet
        $wallet = Wallet::find($transaction->wallet_id);

        if (!$wallet) {
            Log::error('ERREUR CRITIQUE: Wallet non trouvé', ['wallet_id' => $transaction->wallet_id]);
            throw new \Exception('Wallet not found for id: ' . $transaction->wallet_id);
        }

        Log::info('Wallet trouvé', [
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'balance_avant' => $wallet->balance
        ]);

        // Vérifier si déjà crédité
        $alreadyCredited = DB::table('wallet_histories')
            ->where('wallet_id', $wallet->id)
            ->where('transaction_id', $transaction->id)
            ->exists();

        if ($alreadyCredited) {
            Log::warning('Wallet déjà crédité pour cette transaction', ['transaction_id' => $transaction->id]);
            return;
        }

        Log::info('Créditation en cours...');

        // Incrémenter le solde
        $oldBalance = $wallet->balance;
        $wallet->increment('balance', $transaction->amount);
        $wallet->refresh(); // Recharger pour avoir le nouveau solde

        // Mettre à jour la date de dernière transaction
        $wallet->update(['last_transaction_at' => now()]);

        Log::info('Balance mise à jour', [
            'ancien_solde' => $oldBalance,
            'montant_ajoute' => $transaction->amount,
            'nouveau_solde' => $wallet->balance
        ]);

        // Créer l'historique
        $historyId = DB::table('wallet_histories')->insertGetId([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $transaction->amount,
            'description' => 'Dépôt via Kkiapay confirmé (Ref: ' . $transaction->reference . ')',
            'transaction_id' => $transaction->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('Historique créé', ['history_id' => $historyId]);

        // Notification
        try {
            $user = $wallet->user;
            if ($user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'wallet_credited',
                    'title' => 'Dépôt confirmé',
                    'message' => 'Votre dépôt de ' . number_format($transaction->amount, 0, ',', ' ') . ' FCFA a été crédité.',
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'new_balance' => $wallet->balance
                    ],
                    'is_read' => false
                ]);
                Log::info('Notification créée pour user', ['user_id' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::warning('Erreur création notification', ['error' => $e->getMessage()]);
        }

        // Vider le cache
        Cache::forget("wallet_user_{$wallet->user_id}");
        Cache::forget("wallet_tx_{$wallet->id}");

        Log::info('=== CREDIT WALLET TERMINÉ AVEC SUCCÈS ===');
    }

    /**
     * Confirme le paiement des frais pour une demande de financement
     */
    private function confirmFundingRequest($transaction)
    {
        $fundingRequest = FundingRequest::where('transaction_id', $transaction->id)->first();

        if ($fundingRequest) {
            $fundingRequest->update([
                'payment_status' => 'paid',
                'status' => 'pending_review',
                'paid_at' => now()
            ]);

            Log::info('Frais de demande confirmés', [
                'funding_request_id' => $fundingRequest->id
            ]);
        }
    }

    /**
     * Méthode alternative pour gérer le retour utilisateur (GET)
     */
    public function handleReturn(Request $request)
    {
        $transactionId = $request->get('transaction_id');

        if (!$transactionId) {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Transaction invalide');
        }

        $transaction = Transaction::where('reference', $transactionId)
            ->orWhere('kkiapay_transaction_id', $transactionId)
            ->first();

        if (!$transaction) {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Transaction non trouvée');
        }

        if ($transaction->status === 'completed') {
            return redirect()->route('client.wallet.index')
                ->with('success', 'Paiement réussi ! Votre compte a été crédité.');
        } elseif ($transaction->status === 'failed') {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Le paiement a échoué.');
        } else {
            return redirect()->route('client.wallet.index')
                ->with('warning', 'Paiement en cours de traitement...');
        }
    }

    /**
     * Vérification du statut d'une transaction (API)
     */
    public function checkStatus($reference)
    {
        $transaction = Transaction::where('reference', $reference)
            ->orWhere('kkiapay_transaction_id', $reference)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Récupérer le wallet pour avoir le solde actuel
        $wallet = Wallet::find($transaction->wallet_id);

        return response()->json([
            'reference' => $transaction->reference,
            'kkiapay_transaction_id' => $transaction->kkiapay_transaction_id,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
            'paid_at' => $transaction->paid_at,
            'completed_at' => $transaction->completed_at,
            'failure_reason' => $transaction->failure_reason,
            'is_credited' => $transaction->status === 'completed' && ($transaction->type === 'credit' || $transaction->type === 'deposit'),
            'current_wallet_balance' => $wallet ? $wallet->balance : null
        ]);
    }
}
