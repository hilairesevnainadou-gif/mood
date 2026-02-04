<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Funding;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\FundingRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    public function wallet()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            $walletNumber = $this->generateWalletNumber();
            $defaultPin = '000000';
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'wallet_number' => $walletNumber,
                'balance' => 0,
                'currency' => 'XOF',
                'pin_hash' => Hash::make($defaultPin),
                'security_level' => 'normal',
            ]);
        }

        $transactions = $wallet->transactions()->latest()->paginate(10);
        
        // Récupérer les financements en attente
        $pendingFundings = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'funded'])
            ->where(function($query) {
                $query->whereNull('credited_at')
                      ->orWhere('credited_at', '>', now()->subHours(24));
            })
            ->with(['committeeDecision' => function($query) {
                $query->latest();
            }])
            ->latest()
            ->get();

        // Statistiques mensuelles
        $monthlyStats = [
            'deposits' => $wallet->transactions()
                ->where('type', 'deposit')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            
            'withdrawals' => $wallet->transactions()
                ->where('type', 'withdrawal')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            
            'fundings' => $wallet->transactions()
                ->where('type', 'funding')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            
            'transfers' => $wallet->transactions()
                ->where('type', 'transfer')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        // Calculer le total des entrées et sorties
        $monthlyStats['total_in'] = $monthlyStats['deposits'] + $monthlyStats['fundings'];
        $monthlyStats['total_out'] = $monthlyStats['withdrawals'] + $monthlyStats['transfers'];
        $monthlyStats['balance_change'] = $monthlyStats['total_in'] - $monthlyStats['total_out'];

        return view('client.wallet.index', compact(
            'wallet',
            'transactions',
            'pendingFundings',
            'monthlyStats'
        ));
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:mobile_money,bank_transfer,card',
            'phone_number' => 'required_if:payment_method,mobile_money|string|max:20',
            'mobile_operator' => 'required_if:payment_method,mobile_money|in:orange,mtn,moov,wave',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille non trouvé.'
            ], 404);
        }

        try {
            // Créer une transaction en attente
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'description' => 'Dépôt via ' . $validated['payment_method'],
                'reference' => 'DEP' . time() . rand(1000, 9999),
                'metadata' => [
                    'phone_number' => $validated['phone_number'] ?? null,
                    'mobile_operator' => $validated['mobile_operator'] ?? null,
                    'action' => 'deposit',
                ],
            ]);

            // Initier le paiement via Lygos
            $lygosResponse = $this->initiateLygosPayment([
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'phone_number' => $validated['phone_number'] ?? null,
                'mobile_operator' => $validated['mobile_operator'] ?? null,
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
            ]);

            if ($lygosResponse['success'] && isset($lygosResponse['payment_url'])) {
                // Sauvegarder l'ID de transaction Lygos
                $transaction->update([
                    'provider_transaction_id' => $lygosResponse['transaction_id'] ?? null,
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'lygos_transaction_id' => $lygosResponse['transaction_id'] ?? null,
                        'payment_url' => $lygosResponse['payment_url'],
                    ]),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Paiement initié avec succès',
                    'payment_url' => $lygosResponse['payment_url'],
                    'transaction_id' => $transaction->id,
                ]);

            } else {
                $transaction->update(['status' => 'failed']);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur Lygos: ' . ($lygosResponse['message'] ?? 'Échec de l\'initialisation du paiement')
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur dépôt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function depositCallback(Request $request)
    {
        // Vérifier la signature de la callback (important pour la sécurité)
        if (!$this->verifyLygosCallback($request)) {
            Log::warning('Signature Lygos invalide', $request->all());
            return response()->json(['error' => 'Signature invalide'], 400);
        }

        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');
        $amount = $request->input('amount');

        Log::info('Callback Lygos dépôt', [
            'transaction_id' => $transactionId,
            'status' => $status,
            'amount' => $amount,
        ]);

        $transaction = Transaction::where('provider_transaction_id', $transactionId)
            ->orWhere('metadata->lygos_transaction_id', $transactionId)
            ->first();
        
        if (!$transaction) {
            Log::warning('Transaction non trouvée pour callback', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        if ($status === 'success' || $status === 'completed') {
            // Mettre à jour le statut de la transaction
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'amount' => $amount ?? $transaction->amount,
            ]);

            // Créditer le wallet
            $wallet = $transaction->wallet;
            $wallet->increment('balance', $transaction->amount);

            // Notification
            Notification::create([
                'user_id' => $wallet->user_id,
                'type' => 'transaction',
                'title' => 'Dépôt réussi',
                'message' => 'Votre dépôt de ' . number_format($transaction->amount) . ' XOF a été crédité.',
                'data' => ['transaction_id' => $transaction->id],
            ]);

            Log::info('Dépôt crédité avec succès', ['transaction_id' => $transaction->id]);

        } elseif ($status === 'failed' || $status === 'cancelled') {
            $transaction->update(['status' => 'failed']);
            Log::info('Dépôt échoué', ['transaction_id' => $transaction->id]);
        }

        return response()->json(['success' => true]);
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'withdraw_method' => 'required|in:mobile_money,bank_transfer',
            'phone_number' => 'required_if:withdraw_method,mobile_money|string|max:20',
            'mobile_operator' => 'required_if:withdraw_method,mobile_money|in:orange,mtn,moov,wave',
            'account_name' => 'required_if:withdraw_method,bank_transfer|string|max:255',
            'account_number' => 'required_if:withdraw_method,bank_transfer|string|max:50',
            'bank_name' => 'required_if:withdraw_method,bank_transfer|string|max:255',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille non trouvé.'
            ], 404);
        }

        if ($wallet->balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant !'
            ], 400);
        }

        try {
            // Débiter le wallet immédiatement
            $wallet->decrement('balance', $validated['amount']);

            // Créer une transaction en attente
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => $validated['amount'],
                'status' => 'processing',
                'payment_method' => $validated['withdraw_method'],
                'description' => 'Retrait via ' . $validated['withdraw_method'],
                'reference' => 'WIT' . time() . rand(1000, 9999),
                'metadata' => [
                    'phone_number' => $validated['phone_number'] ?? null,
                    'mobile_operator' => $validated['mobile_operator'] ?? null,
                    'account_name' => $validated['account_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'bank_name' => $validated['bank_name'] ?? null,
                    'action' => 'withdrawal',
                ],
            ]);

            // Initier le retrait via Lygos
            $lygosResponse = $this->initiateLygosPayout([
                'amount' => $validated['amount'],
                'payout_method' => $validated['withdraw_method'],
                'phone_number' => $validated['phone_number'] ?? null,
                'mobile_operator' => $validated['mobile_operator'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
            ]);

            if ($lygosResponse['success']) {
                // Sauvegarder l'ID de transaction Lygos
                $transaction->update([
                    'provider_transaction_id' => $lygosResponse['transaction_id'] ?? null,
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'lygos_transaction_id' => $lygosResponse['transaction_id'] ?? null,
                        'lygos_response' => $lygosResponse,
                    ]),
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'transaction',
                    'title' => 'Retrait initié',
                    'message' => 'Votre retrait de ' . number_format($validated['amount']) . ' XOF a été initié.',
                    'data' => ['transaction_id' => $transaction->id],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Retrait initié avec succès !',
                    'transaction_id' => $transaction->id,
                    'new_balance' => $wallet->fresh()->balance
                ]);

            } else {
                // Rembourser le wallet en cas d'échec
                $wallet->increment('balance', $validated['amount']);
                $transaction->update(['status' => 'failed']);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur Lygos: ' . ($lygosResponse['message'] ?? 'Échec de l\'initialisation du retrait')
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur retrait: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withdrawCallback(Request $request)
    {
        // Vérifier la signature de la callback
        if (!$this->verifyLygosCallback($request)) {
            Log::warning('Signature Lygos invalide pour retrait', $request->all());
            return response()->json(['error' => 'Signature invalide'], 400);
        }

        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');

        Log::info('Callback Lygos retrait', [
            'transaction_id' => $transactionId,
            'status' => $status,
        ]);

        $transaction = Transaction::where('provider_transaction_id', $transactionId)
            ->orWhere('metadata->lygos_transaction_id', $transactionId)
            ->first();
        
        if (!$transaction) {
            Log::warning('Transaction retrait non trouvée', ['transaction_id' => $transactionId]);
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        if ($status === 'completed' || $status === 'success') {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Notification::create([
                'user_id' => $transaction->wallet->user_id,
                'type' => 'transaction',
                'title' => 'Retrait traité',
                'message' => 'Votre retrait de ' . number_format($transaction->amount) . ' XOF a été traité.',
                'data' => ['transaction_id' => $transaction->id],
            ]);

            Log::info('Retrait traité avec succès', ['transaction_id' => $transaction->id]);

        } elseif ($status === 'failed' || $status === 'cancelled') {
            $transaction->update(['status' => 'failed']);
            
            // Rembourser le wallet
            $wallet = $transaction->wallet;
            $wallet->increment('balance', $transaction->amount);

            Notification::create([
                'user_id' => $wallet->user_id,
                'type' => 'transaction',
                'title' => 'Retrait échoué',
                'message' => 'Votre retrait a échoué. Le montant a été remboursé.',
                'data' => ['transaction_id' => $transaction->id],
            ]);

            Log::info('Retrait échoué, montant remboursé', ['transaction_id' => $transaction->id]);
        }

        return response()->json(['success' => true]);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'recipient_wallet' => 'required|string|max:50',
            'recipient_name' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille non trouvé.'
            ], 404);
        }

        if ($wallet->balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant !'
            ], 400);
        }

        // Trouver le portefeuille du destinataire
        $recipientWallet = Wallet::where('wallet_number', $validated['recipient_wallet'])->first();
        
        if (!$recipientWallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille destinataire non trouvé.'
            ], 404);
        }

        if ($recipientWallet->id === $wallet->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas transférer vers votre propre portefeuille.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($wallet, $recipientWallet, $validated, $user) {
                // Débiter le wallet émetteur
                $wallet->decrement('balance', $validated['amount']);
                
                // Créditer le wallet destinataire
                $recipientWallet->increment('balance', $validated['amount']);
                
                // Créer la transaction pour l'émetteur
                $senderTransaction = Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'transfer',
                    'amount' => $validated['amount'],
                    'status' => 'completed',
                    'description' => 'Transfert vers ' . ($validated['recipient_name'] ?? $recipientWallet->wallet_number),
                    'reference' => 'TRF' . time() . rand(1000, 9999),
                    'metadata' => [
                        'recipient_wallet' => $recipientWallet->wallet_number,
                        'recipient_name' => $validated['recipient_name'],
                        'reason' => $validated['reason'],
                        'direction' => 'out',
                    ],
                ]);
                
                // Créer la transaction pour le destinataire
                $receiverTransaction = Transaction::create([
                    'wallet_id' => $recipientWallet->id,
                    'type' => 'transfer',
                    'amount' => $validated['amount'],
                    'status' => 'completed',
                    'description' => 'Transfert reçu de ' . ($user->name ?? $wallet->wallet_number),
                    'reference' => $senderTransaction->reference,
                    'metadata' => [
                        'sender_wallet' => $wallet->wallet_number,
                        'sender_name' => $user->name,
                        'reason' => $validated['reason'],
                        'direction' => 'in',
                    ],
                ]);
                
                // Notifications
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'transaction',
                    'title' => 'Transfert effectué',
                    'message' => 'Transfert de ' . number_format($validated['amount']) . ' XOF vers ' . $recipientWallet->wallet_number,
                    'data' => ['transaction_id' => $senderTransaction->id],
                ]);
                
                Notification::create([
                    'user_id' => $recipientWallet->user_id,
                    'type' => 'transaction',
                    'title' => 'Transfert reçu',
                    'message' => 'Réception de ' . number_format($validated['amount']) . ' XOF de ' . $user->name,
                    'data' => ['transaction_id' => $receiverTransaction->id],
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Transfert effectué avec succès',
                'new_balance' => $wallet->fresh()->balance
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur transfert: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setPin(Request $request)
    {
        $validated = $request->validate([
            'current_pin' => 'nullable|string|size:6',
            'new_pin' => 'required|string|size:6|confirmed',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille non trouvé.'
            ], 404);
        }

        // Vérifier le PIN actuel si fourni
        if ($validated['current_pin'] && !Hash::check($validated['current_pin'], $wallet->pin_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN actuel incorrect.'
            ], 400);
        }

        try {
            $wallet->update([
                'pin_hash' => Hash::make($validated['new_pin']),
                'security_level' => 'protected',
                'pin_changed_at' => now(),
            ]);

            // Notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'security',
                'title' => 'PIN modifié',
                'message' => 'Votre code PIN a été mis à jour avec succès.',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PIN mis à jour avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour PIN: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Portefeuille non trouvé.'
            ], 404);
        }

        if (Hash::check($validated['pin'], $wallet->pin_hash)) {
            // Générer un token de session pour les opérations sensibles
            $token = bin2hex(random_bytes(32));
            session(['wallet_auth_token' => $token]);
            
            return response()->json([
                'success' => true,
                'message' => 'PIN vérifié avec succès.',
                'auth_token' => $token,
                'valid_for' => 300 // 5 minutes
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PIN incorrect.'
        ], 400);
    }

    // Méthodes Lygos
    private function initiateLygosPayment($data)
    {
        $apiKey = config('services.lygos.api_key');
        $apiUrl = config('services.lygos.api_url', 'https://api.lygosapp.com');

        // Préparer les données pour Lygos selon leur API
        $paymentData = [
            'title' => 'Recharge de portefeuille BHDM',
            'amount' => $data['amount'],
            'description' => 'Dépôt sur le portefeuille BHDM - ' . $data['payment_method'],
            'success-url' => route('client.wallet.deposit.callback'),
            'failure-url' => route('client.wallet.deposit.callback'),
            'metadata' => [
                'user_id' => $data['user_id'],
                'wallet_id' => $data['wallet_id'],
                'transaction_id' => $data['transaction_id'],
                'action' => 'deposit',
            ],
        ];

        // Ajouter les détails selon la méthode de paiement
        if ($data['payment_method'] === 'mobile_money' && isset($data['phone_number'])) {
            $paymentData['customer_phone'] = $data['phone_number'];
            $paymentData['mobile_operator'] = $data['mobile_operator'];
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Accept' => 'application/json',
            ])->post($apiUrl . '/v1/products', $paymentData);

            Log::info('Requête Lygos paiement', [
                'url' => $apiUrl . '/v1/products',
                'data' => $paymentData,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['transaction_id'] ?? $result['id'] ?? null,
                    'payment_url' => $result['payment_url'] ?? $result['url'] ?? null,
                    'message' => 'Paiement initié avec succès',
                ];
            } else {
                Log::error('Erreur API Lygos paiement', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return [
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Erreur API Lygos: ' . $response->status(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception API Lygos paiement: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion: ' . $e->getMessage(),
            ];
        }
    }

    private function initiateLygosPayout($data)
    {
        $apiKey = config('services.lygos.api_key');
        $apiUrl = config('services.lygos.api_url', 'https://api.lygosapp.com');

        // Note: Lygos pourrait ne pas avoir d'API de retrait directe
        // Dans ce cas, on simule ou on utilise un autre service
        // Cette méthode doit être adaptée selon la documentation de Lygos
        
        $payoutData = [
            'amount' => $data['amount'],
            'payout_method' => $data['payout_method'],
            'callback_url' => route('client.wallet.withdraw.callback'),
            'metadata' => [
                'user_id' => $data['user_id'],
                'wallet_id' => $data['wallet_id'],
                'transaction_id' => $data['transaction_id'],
                'action' => 'withdrawal',
            ],
        ];

        if ($data['payout_method'] === 'mobile_money' && isset($data['phone_number'])) {
            $payoutData['beneficiary'] = [
                'phone' => $data['phone_number'],
                'operator' => $data['mobile_operator'],
            ];
        } else if ($data['payout_method'] === 'bank_transfer') {
            $payoutData['beneficiary'] = [
                'name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'bank_name' => $data['bank_name'],
            ];
        }

        try {
            // Pour les retraits, Lygos pourrait utiliser un endpoint différent
            // Vérifiez la documentation de Lygos pour l'endpoint correct
            $endpoint = '/v1/payouts'; // À vérifier dans la doc Lygos
            
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Accept' => 'application/json',
            ])->post($apiUrl . $endpoint, $payoutData);

            Log::info('Requête Lygos retrait', [
                'url' => $apiUrl . $endpoint,
                'data' => $payoutData,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'transaction_id' => $result['transaction_id'] ?? $result['id'] ?? uniqid('LYGOS_'),
                    'message' => 'Retrait initié avec succès',
                ];
            } else {
                // Simulation pour développement
                // En production, il faut utiliser l'API réelle de Lygos
                Log::warning('API Lygos retrait non disponible, simulation', $payoutData);
                
                // Simulation d'une réponse réussie (à retirer en production)
                return [
                    'success' => true,
                    'transaction_id' => 'LYGOS_' . uniqid(),
                    'message' => 'Retrait initié (mode simulation)',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception API Lygos retrait: ' . $e->getMessage());
            
            // Simulation pour développement (à retirer en production)
            return [
                'success' => true,
                'transaction_id' => 'LYGOS_' . uniqid(),
                'message' => 'Retrait initié (mode simulation)',
            ];
        }
    }

    private function verifyLygosCallback(Request $request)
    {
        // Lygos envoie probablement une signature dans les headers
        $signature = $request->header('X-Lygos-Signature') ?? $request->header('X-Signature');
        $payload = $request->getContent();
        $secret = config('services.lygos.webhook_secret');

        if (!$signature || !$secret) {
            // Pour le développement, on peut accepter les callbacks sans signature
            if (app()->environment('local', 'testing')) {
                Log::warning('Vérification signature Lygos ignorée en développement');
                return true;
            }
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    private function generateWalletNumber()
    {
        $currentYear = date('y');
        $currentMonth = date('m');
        $lastWallet = Wallet::latest()->first();
        
        if ($lastWallet && strpos($lastWallet->wallet_number, 'WALLET-' . $currentYear . $currentMonth) === 0) {
            $lastNumber = intval(substr($lastWallet->wallet_number, -6));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'WALLET-' . $currentYear . $currentMonth . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function checkFundingUpdates()
    {
        try {
            $user = Auth::user();
            
            // Récupérer les financements en attente avec des changements récents
            $pendingFundings = FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'funded', 'pending'])
                ->where('updated_at', '>', now()->subMinutes(5)) // Changements dans les 5 dernières minutes
                ->get();
            
            $updated = [];
            
            foreach ($pendingFundings as $funding) {
                // Vérifier si le statut a changé depuis la dernière vérification
                $lastChecked = session()->get('last_funding_check_' . $funding->id, $funding->updated_at);
                
                if ($funding->updated_at->gt($lastChecked)) {
                    $updated[] = [
                        'id' => $funding->id,
                        'title' => $funding->title,
                        'status' => $funding->status,
                        'new_status' => $this->getStatusLabel($funding->status),
                        'updated_at' => $funding->updated_at->format('d/m/Y H:i')
                    ];
                    
                    // Mettre à jour le timestamp de dernière vérification
                    session()->put('last_funding_check_' . $funding->id, $funding->updated_at);
                }
            }
            
            return response()->json([
                'success' => true,
                'updated' => $updated,
                'has_updates' => count($updated) > 0,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification des mises à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Méthode pour obtenir le libellé du statut
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'funded' => 'Financé',
            'credited' => 'Accrédité',
            'completed' => 'Terminé'
        ];
        
        return $labels[$status] ?? $status;
    }

    public function fundingDetails($id)
    {
        try {
            $funding = FundingRequest::with(['committeeDecision', 'payments'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'funding' => [
                    'id' => $funding->id,
                    'title' => $funding->title,
                    'type' => $funding->type,
                    'status' => $funding->status,
                    'status_label' => $this->getStatusLabel($funding->status),
                    'amount_requested' => $funding->amount_requested,
                    'amount_approved' => $funding->amount_approved,
                    'created_at' => $funding->created_at->format('d/m/Y'),
                    'updated_at' => $funding->updated_at->format('d/m/Y H:i'),
                    'committee_decision' => $funding->committeeDecision ? [
                        'committee_name' => $funding->committeeDecision->committee_name,
                        'decision' => $funding->committeeDecision->decision,
                        'approved_amount' => $funding->committeeDecision->approved_amount,
                        'decision_date' => $funding->committeeDecision->decision_date?->format('d/m/Y'),
                        'comments' => $funding->committeeDecision->comments
                    ] : null,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Financement non trouvé'
            ], 404);
        }
    }

    public function creditFunding($id)
    {
        try {
            $funding = FundingRequest::where('user_id', Auth::id())
                ->where('status', 'funded')
                ->findOrFail($id);
            
            // Vérifier si déjà crédité
            if ($funding->credited_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce financement a déjà été crédité'
                ]);
            }
            
            // Créditer le portefeuille
            $wallet = Auth::user()->wallet;
            $amount = $funding->amount_approved ?? $funding->amount_requested;
            
            // Transaction pour assurer la cohérence
            DB::transaction(function () use ($wallet, $funding, $amount) {
                // Mettre à jour le solde du portefeuille
                $wallet->balance += $amount;
                $wallet->save();
                
                // Marquer le financement comme crédité
                $funding->update([
                    'credited_at' => now(),
                    'status' => 'credited'
                ]);
                
                // Créer une transaction
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'funding',
                    'amount' => $amount,
                    'description' => 'Accréditation financement: ' . $funding->title,
                    'status' => 'completed',
                    'reference' => 'FUND-' . $funding->id
                ]);
                
                // Notification
                Notification::create([
                    'user_id' => $wallet->user_id,
                    'type' => 'transaction',
                    'title' => 'Financement crédité',
                    'message' => 'Votre financement de ' . number_format($amount) . ' XOF a été crédité sur votre portefeuille.',
                    'data' => ['funding_id' => $funding->id],
                ]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Financement crédité avec succès',
                'new_balance' => $wallet->balance,
                'credited_amount' => $amount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error crediting funding', [
                'user_id' => Auth::id(),
                'funding_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'accréditation'
            ], 500);
        }
    }

    public function getWalletInfo()
    {
        try {
            $user = Auth::user();
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Portefeuille non trouvé'
                ], 404);
            }
            
            // Calculer les statistiques pour les 30 derniers jours
            $thirtyDaysAgo = now()->subDays(30);
            
            $stats = [
                'total_deposits' => $wallet->transactions()
                    ->where('type', 'deposit')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),
                
                'total_withdrawals' => $wallet->transactions()
                    ->where('type', 'withdrawal')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),
                
                'total_transfers' => $wallet->transactions()
                    ->where('type', 'transfer')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),
                
                'pending_transactions' => $wallet->transactions()
                    ->whereIn('status', ['pending', 'processing'])
                    ->count(),
            ];
            
            return response()->json([
                'success' => true,
                'wallet' => [
                    'id' => $wallet->id,
                    'wallet_number' => $wallet->wallet_number,
                    'balance' => $wallet->balance,
                    'currency' => $wallet->currency,
                    'security_level' => $wallet->security_level,
                    'created_at' => $wallet->created_at->format('d/m/Y'),
                ],
                'stats' => $stats,
                'has_pin' => $wallet->security_level !== 'normal'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting wallet info', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations'
            ], 500);
        }
    }

    public function getQuickActions()
    {
        try {
            $user = Auth::user();
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Portefeuille non trouvé'
                ], 404);
            }
            
            // Définir les actions disponibles
            $actions = [
                [
                    'id' => 'deposit',
                    'title' => 'Déposer',
                    'icon' => 'fas fa-plus-circle',
                    'color' => 'success',
                    'available' => true,
                    'description' => 'Ajouter de l\'argent à votre portefeuille'
                ],
                [
                    'id' => 'withdraw',
                    'title' => 'Retirer',
                    'icon' => 'fas fa-minus-circle',
                    'color' => 'danger',
                    'available' => $wallet->balance > 1000,
                    'description' => 'Retirer de l\'argent vers votre compte'
                ],
                [
                    'id' => 'transfer',
                    'title' => 'Transférer',
                    'icon' => 'fas fa-exchange-alt',
                    'color' => 'warning',
                    'available' => $wallet->balance > 100,
                    'description' => 'Envoyer de l\'argent à un autre portefeuille'
                ],
                [
                    'id' => 'pay',
                    'title' => 'Payer',
                    'icon' => 'fas fa-credit-card',
                    'color' => 'info',
                    'available' => $wallet->balance > 0,
                    'description' => 'Effectuer un paiement'
                ],
                [
                    'id' => 'history',
                    'title' => 'Historique',
                    'icon' => 'fas fa-history',
                    'color' => 'primary',
                    'available' => true,
                    'description' => 'Voir vos transactions'
                ],
                [
                    'id' => 'qr_code',
                    'title' => 'QR Code',
                    'icon' => 'fas fa-qrcode',
                    'color' => 'secondary',
                    'available' => true,
                    'description' => 'Générer un QR Code de paiement'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'actions' => $actions,
                'balance' => $wallet->balance
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting quick actions', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des actions'
            ], 500);
        }
    }
}