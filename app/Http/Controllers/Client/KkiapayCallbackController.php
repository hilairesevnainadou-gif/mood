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
     * CALLBACK PRINCIPAL - Appelé par Kkiapay (webhook)
     * URL: POST /kkiapay/callback
     * Déclenche le crédit wallet sur événement "success"
     */
    public function handleCallback(Request $request)
    {
        Log::info('=== KKIAPAY CALLBACK ===', [
            'ip' => $request->ip(),
            'all_data' => $request->all(),
            'time' => now()->toDateTimeString()
        ]);

        try {
            // Récupération des données Kkiapay
            $kkiapayId = $request->input('transaction_key')  // "C-dNw1Oro"
                ?? $request->input('reference')              // "22997000000_C-dNw1Oro"
                ?? $request->input('transaction_id');

            $status = $this->mapKkiapayStatus($request->input('status'));
            $amount = $request->input('amount');

            // 🔑 CLÉ: Votre référence est dans "state", pas "data" !
            $state = $request->input('state');
            $yourReference = null;
            $userId = null;

            if (is_array($state)) {
                $yourReference = $state['reference'] ?? null;  // "DEP-20260227-OSE2PO"
                $userId = $state['user_id'] ?? null;           // 5
            } elseif (is_string($state)) {
                // Au cas où state serait JSON string
                $decoded = json_decode($state, true);
                if ($decoded) {
                    $yourReference = $decoded['reference'] ?? null;
                    $userId = $decoded['user_id'] ?? null;
                }
            }

            Log::info('Données extraites', [
                'kkiapay_id' => $kkiapayId,
                'status' => $status,
                'your_reference' => $yourReference,
                'user_id_from_state' => $userId,
                'amount' => $amount
            ]);

            if (!$yourReference) {
                Log::error('Référence non trouvée dans state', ['state' => $state]);
                return response()->json(['error' => 'Reference not found in state'], 400);
            }

            // 🔍 RECHERCHE par VOTRE référence (prioritaire)
            $transaction = Transaction::where('reference', $yourReference)->first();

            if (!$transaction) {
                // Fallback: chercher par ID Kkiapay si déjà stocké
                $transaction = Transaction::where('kkiapay_transaction_id', $kkiapayId)->first();
            }

            if (!$transaction) {
                Log::error('Transaction non trouvée', [
                    'reference' => $yourReference,
                    'kkiapay_id' => $kkiapayId
                ]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            Log::info('Transaction trouvée', [
                'id' => $transaction->id,
                'reference' => $transaction->reference,
                'type' => $transaction->type,
                'montant' => $transaction->amount,
                'wallet_id' => $transaction->wallet_id,
                'statut_actuel' => $transaction->status
            ]);

            // Déjà traitée ?
            if ($transaction->status === 'completed') {
                Log::info('Transaction déjà traitée');
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // === ÉVÉNEMENT SUCCESS === CRÉDIT DU WALLET
            if ($status === 'success') {

                DB::transaction(function () use ($transaction, $kkiapayId, $request) {

                    // 1. Mettre à jour la transaction avec l'ID Kkiapay
                    $transaction->update([
                        'kkiapay_transaction_id' => $kkiapayId,
                        'status' => 'completed',
                        'kkiapay_response' => json_encode($request->all()),
                        'paid_at' => now(),
                        'completed_at' => now()
                    ]);

                    Log::info('Transaction marquée completed', [
                        'transaction_id' => $transaction->id,
                        'kkiapay_id' => $kkiapayId
                    ]);

                    // 2. DÉCIDER si on crédite le wallet
                    $isCredit = in_array($transaction->type, ['credit', 'deposit', 'refund']);
                    $isFee = $this->isFeePayment($transaction);

                    Log::info('Analyse type transaction', [
                        'type' => $transaction->type,
                        'is_credit' => $isCredit,
                        'is_fee' => $isFee
                    ]);

                    if ($isCredit && !$isFee) {
                        Log::info('>>> CRÉDIT WALLET DEMANDÉ <<<');
                        $this->creditWallet($transaction);
                    } else {
                        Log::info('Pas de crédit wallet', [
                            'type' => $transaction->type,
                            'is_fee' => $isFee
                        ]);
                    }

                    // 3. Si c'est des frais, confirmer la demande
                    if ($isFee) {
                        $this->confirmFundingRequest($transaction);
                    }
                });

                Log::info('=== TRAITEMENT RÉUSSI ===', [
                    'transaction_id' => $transaction->id
                ]);

            } else {
                // Échec
                $transaction->update([
                    'kkiapay_transaction_id' => $kkiapayId,
                    'status' => 'failed',
                    'kkiapay_response' => json_encode($request->all()),
                    'failure_reason' => $request->input('failure_reason', 'Payment failed'),
                ]);

                Log::warning('Paiement échoué', ['status' => $status]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('ERREUR CALLBACK', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Map le status Kkiapay (1, 2, 3) vers nos statuts
     */
    private function mapKkiapayStatus($status): string
    {
        // D'après le payload: status: 1 = pending/success ?
        // À vérifier avec la doc Kkiapay officielle
        return match((int)$status) {
            1 => 'success',    // ou 'pending' selon doc Kkiapay
            2 => 'success',
            3 => 'failed',
            default => 'unknown'
        };
    }

    /**
     * Vérifie si c'est un paiement de frais (pas de crédit wallet)
     */
    private function isFeePayment($transaction)
    {
        if ($transaction->type === 'fee') {
            return true;
        }

        if ($transaction->metadata) {
            $meta = is_string($transaction->metadata)
                ? json_decode($transaction->metadata, true)
                : $transaction->metadata;

            if (isset($meta['type']) && $meta['type'] === 'funding_fee') {
                return true;
            }
        }

        return FundingRequest::where('kkiapay_transaction_id', $transaction->kkiapay_transaction_id)
            ->orWhere('transfer_transaction_id', $transaction->id)
            ->exists();
    }

    /**
     * CRÉDIT DU WALLET - Méthode principale
     */
    private function creditWallet($transaction)
    {
        Log::info('Début creditWallet', [
            'transaction_id' => $transaction->id,
            'wallet_id' => $transaction->wallet_id,
            'montant' => $transaction->amount
        ]);

        if (!$transaction->wallet_id) {
            throw new \Exception('Transaction sans wallet_id');
        }

        // Verrouillage pour éviter les doubles crédits
        $wallet = Wallet::where('id', $transaction->wallet_id)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            throw new \Exception('Wallet introuvable: ' . $transaction->wallet_id);
        }

        // Vérification idempotence (déjà crédité ?)
        $alreadyCredited = DB::table('wallet_histories')
            ->where('wallet_id', $wallet->id)
            ->where('transaction_id', $transaction->id)
            ->exists();

        if ($alreadyCredited) {
            Log::warning('Déjà crédité, abandon', ['transaction_id' => $transaction->id]);
            return;
        }

        // Calcul du nouveau solde
        $oldBalance = (float) $wallet->balance;
        $amount = (float) $transaction->amount;
        $newBalance = $oldBalance + $amount;

        Log::info('Mise à jour solde', [
            'wallet_id' => $wallet->id,
            'ancien' => $oldBalance,
            'ajout' => $amount,
            'nouveau' => $newBalance
        ]);

        // Mise à jour du wallet
        $wallet->balance = $newBalance;
        $wallet->last_transaction_at = now();
        $wallet->save();

        // Création de l'historique
        DB::table('wallet_histories')->insert([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => $amount,
            'description' => 'Dépôt Kkiapay ref: ' . $transaction->reference,
            'transaction_id' => $transaction->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info('Historique wallet créé', [
            'wallet_id' => $wallet->id,
            'transaction_id' => $transaction->id
        ]);

        // Notification au client
        try {
            if ($wallet->user) {
                \App\Models\Notification::create([
                    'user_id' => $wallet->user_id,
                    'type' => 'wallet_credited',
                    'title' => 'Dépôt confirmé',
                    'message' => number_format($amount, 0, ',', ' ') . ' FCFA crédités sur votre compte',
                    'data' => json_encode([
                        'transaction_id' => $transaction->id,
                        'amount' => $amount,
                        'new_balance' => $newBalance,
                        'reference' => $transaction->reference
                    ])
                ]);
                Log::info('Notification créée', ['user_id' => $wallet->user_id]);
            }
        } catch (\Exception $e) {
            Log::warning('Erreur notification', ['error' => $e->getMessage()]);
        }

        // Invalidation du cache
        Cache::forget("wallet_user_{$wallet->user_id}");
        Cache::forget("wallet_tx_{$wallet->id}");

        Log::info('Wallet crédité avec succès', [
            'wallet_id' => $wallet->id,
            'new_balance' => $newBalance
        ]);
    }

    /**
     * Confirme les frais de demande de financement
     */
    private function confirmFundingRequest($transaction)
    {
        $funding = FundingRequest::where('kkiapay_transaction_id', $transaction->kkiapay_transaction_id)
            ->orWhere('transfer_transaction_id', $transaction->id)
            ->first();

        if ($funding) {
            $funding->update([
                'status' => 'submitted',
                'paid_at' => now(),
                'validated_at' => now()
            ]);

            Log::info('Frais confirmés', [
                'funding_id' => $funding->id,
                'request_number' => $funding->request_number
            ]);

            try {
                \App\Models\Notification::create([
                    'user_id' => $funding->user_id,
                    'type' => 'funding_request_paid',
                    'title' => 'Demande confirmée',
                    'message' => 'Votre demande #' . $funding->request_number . ' a été confirmée.',
                    'data' => json_encode(['funding_request_id' => $funding->id])
                ]);
            } catch (\Exception $e) {
                Log::warning('Erreur notification funding', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * RETOUR UTILISATEUR - Redirection après paiement (GET)
     * URL: /kkiapay/return?transaction_id=XXX
     */
    public function handleReturn(Request $request)
    {
        $txId = $request->get('transaction_id');

        if (!$txId) {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Transaction invalide');
        }

        // Recherche par référence ou ID Kkiapay
        $transaction = Transaction::where('reference', $txId)
            ->orWhere('kkiapay_transaction_id', $txId)
            ->first();

        if (!$transaction) {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Transaction non trouvée');
        }

        // Attendre le callback si pending
        if ($transaction->status === 'pending') {
            sleep(2);
            $transaction->refresh();
        }

        // Redirection selon le statut
        if ($transaction->status === 'completed') {
            return redirect()->route('client.wallet.index')
                ->with('success', number_format($transaction->amount, 0, ',', ' ') . ' FCFA ont été crédités sur votre compte !');
        } elseif ($transaction->status === 'failed') {
            return redirect()->route('client.wallet.index')
                ->with('error', 'Le paiement a échoué.');
        } else {
            return redirect()->route('client.wallet.index')
                ->with('warning', 'Paiement en cours de traitement...');
        }
    }

    /**
     * API: Vérification du statut (pour polling frontend)
     * URL: /kkiapay/status/{reference}
     */
    public function checkStatus($reference)
    {
        $tx = Transaction::where('reference', $reference)
            ->orWhere('kkiapay_transaction_id', $reference)
            ->first();

        if (!$tx) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $wallet = Wallet::find($tx->wallet_id);

        // Vérifier si vraiment crédité dans l'historique
        $isCredited = DB::table('wallet_histories')
            ->where('transaction_id', $tx->id)
            ->exists();

        return response()->json([
            'reference' => $tx->reference,
            'kkiapay_id' => $tx->kkiapay_transaction_id,
            'status' => $tx->status,
            'amount' => $tx->amount,
            'type' => $tx->type,
            'is_credited' => $isCredited,
            'wallet_balance' => $wallet?->balance,
            'paid_at' => $tx->paid_at,
            'completed_at' => $tx->completed_at
        ]);
    }
}
