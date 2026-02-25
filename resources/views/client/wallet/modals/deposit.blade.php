<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\FundingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class WalletController extends Controller
{
    /**
     * Affiche la page du portefeuille
     */
    public function wallet()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        // Créer le wallet s'il n'existe pas
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

        // Récupérer les transactions récentes
        $transactions = $wallet->transactions()->latest()->paginate(10);

        // Récupérer les financements en attente de crédit
        $pendingFundings = FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'transfer_pending', 'documents_validated'])
            ->whereNull('credited_at')
            ->with(['fundingType', 'documents'])
            ->latest()
            ->get();

        // Statistiques mensuelles
        $monthlyStats = [
            'deposits' => $wallet->transactions()
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),

            'withdrawals' => $wallet->transactions()
                ->where('type', 'debit')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),

            'payments' => $wallet->transactions()
                ->where('type', 'payment')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),

            'transfers' => $wallet->transactions()
                ->where('type', 'transfer')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        $monthlyStats['total_in'] = $monthlyStats['deposits'];
        $monthlyStats['total_out'] = $monthlyStats['withdrawals'] + $monthlyStats['payments'] + $monthlyStats['transfers'];
        $monthlyStats['balance_change'] = $monthlyStats['total_in'] - $monthlyStats['total_out'];

        return view('client.wallet.index', compact(
            'wallet',
            'transactions',
            'pendingFundings',
            'monthlyStats'
        ));
    }

    /**
     * Liste des transactions
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        $query = $wallet->transactions()->latest();

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $stats = [
            'total_in' => (clone $query)->whereIn('type', ['credit', 'refund'])->sum('amount'),
            'total_out' => (clone $query)->whereIn('type', ['debit', 'payment', 'fee'])->sum('amount'),
            'count' => (clone $query)->count(),
        ];

        return view('client.wallet.transactions', compact('transactions', 'stats'));
    }

     /**
     * Dépôt via Kkiapay - Initialisation
     */
    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'phone' => 'required|string|min:8',
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
            // Générer un ID de transaction unique et mémorable
            $transactionId = 'KKP-' . strtoupper(Str::random(12));
            $reference = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Créer la transaction en attente
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'type' => 'credit',
                'amount' => $validated['amount'],
                'total_amount' => $validated['amount'],
                'fee' => 0,
                'status' => 'pending',
                'payment_method' => 'kkiapay',
                'description' => 'Dépôt via Kkiapay',
                'metadata' => [
                    'phone' => $validated['phone'],
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_phone' => $user->phone,
                    'initiated_at' => now()->toIso8601String(),
                    'ip_address' => $request->ip(),
                ],
            ]);

            Log::info('Transaction dépôt créée', [
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'wallet_id' => $wallet->id,
                'amount' => $validated['amount']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction initiée',
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'amount' => $validated['amount'],
                // Données utilisateur pour Kkiapay
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_firstname' => $user->first_name ?? $user->name,
                'user_lastname' => $user->last_name ?? '',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création transaction dépôt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }


  /**
     * CALLBACK KKIAPAY - Corrigé pour trouver la transaction
     */
    public function kkiapayCallback(Request $request)
    {
        try {
            Log::info('=== KKIAPAY CALLBACK ===', [
                'method' => $request->method(),
                'all_data' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            // Récupérer toutes les données possibles
            $data = $request->all();

            // CORRECTION 1: Extraire les données du champ 'data' si présent (Kkiapay envoie les données ici)
            $additionalData = [];
            if (!empty($data['data'])) {
                $additionalData = is_string($data['data']) 
                    ? json_decode($data['data'], true) 
                    : $data['data'];
                
                Log::info('Données extraites du champ data:', $additionalData);
            }

            // CORRECTION 2: Fusionner les données pour faciliter l'accès
            // Les données dans 'data' ont priorité sur les données racine
            $mergedData = array_merge($data, $additionalData);

            // Extraire les identifiants de différentes sources possibles
            $transactionId = $mergedData['transaction_id'] 
                ?? $mergedData['transactionId'] 
                ?? $data['transactionId'] 
                ?? $data['transaction_id'] 
                ?? $data['transactionID']
                ?? null;

            $reference = $mergedData['reference'] 
                ?? $data['reference'] 
                ?? $data['ref'] 
                ?? null;

            $status = strtolower($data['status'] ?? $mergedData['status'] ?? 'unknown');
            $amount = $data['amount'] ?? $mergedData['amount'] ?? 0;
            
            // Téléphone du paiement
            $phone = $data['phone'] 
                ?? $data['phoneNumber'] 
                ?? $data['phone_number']
                ?? $mergedData['phone']
                ?? null;

            // CORRECTION 3: Récupérer wallet_id depuis les données fusionnées
            $walletId = $mergedData['wallet_id'] ?? null;

            Log::info('Données extraites callback', [
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'status' => $status,
                'amount' => $amount,
                'phone' => $phone,
                'wallet_id' => $walletId,
                'additional_data' => $additionalData,
                'merged_data' => $mergedData
            ]);

            // RECHERCHE DE LA TRANSACTION - Plusieurs stratégies

            $transaction = null;

            // 1. Recherche par transaction_id exact (notre ID interne)
            if ($transactionId) {
                $transaction = Transaction::where('transaction_id', $transactionId)
                    ->orWhere('reference', $transactionId)
                    ->first();
                
                if ($transaction) {
                    Log::info('Transaction trouvée par transaction_id', ['id' => $transaction->id]);
                }
            }

            // 2. Recherche par référence
            if (!$transaction && $reference) {
                $transaction = Transaction::where('reference', $reference)
                    ->orWhere('transaction_id', $reference)
                    ->first();
                
                if ($transaction) {
                    Log::info('Transaction trouvée par reference', ['id' => $transaction->id]);
                }
            }

            // 3. Recherche par wallet_id et statut pending (stratégie de secours)
            if (!$transaction && $walletId) {
                $transaction = Transaction::where('wallet_id', $walletId)
                    ->where('status', 'pending')
                    ->where('type', 'credit')
                    ->where('payment_method', 'kkiapay')
                    ->where('created_at', '>', now()->subMinutes(30))
                    ->latest()
                    ->first();
                
                if ($transaction) {
                    Log::info('Transaction trouvée par wallet_id + pending', ['id' => $transaction->id]);
                }
            }

            // 4. Recherche par montant et téléphone récent
            if (!$transaction && $amount > 0 && $phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $transaction = Transaction::where('status', 'pending')
                    ->where('type', 'credit')
                    ->where('amount', $amount)
                    ->where('payment_method', 'kkiapay')
                    ->where('created_at', '>', now()->subMinutes(30))
                    ->where(function($q) use ($cleanPhone) {
                        $q->whereJsonContains('metadata->phone', $cleanPhone)
                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.phone')) LIKE ?", ["%$cleanPhone%"]);
                    })
                    ->latest()
                    ->first();
                
                if ($transaction) {
                    Log::info('Transaction trouvée par montant + téléphone', ['id' => $transaction->id]);
                }
            }

            // Si toujours pas trouvée mais paiement réussi → créer la transaction
            if (!$transaction && ($status === 'success' || $status === 'completed')) {
                Log::info('Transaction non trouvée, création depuis callback');
                return $this->createTransactionFromCallback($mergedData, $additionalData);
            }

            if (!$transaction) {
                Log::error('Transaction non trouvée', [
                    'transaction_id' => $transactionId,
                    'reference' => $reference,
                    'wallet_id' => $walletId,
                    'data' => $data
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction non trouvée'
                ], 404);
            }

            Log::info('Transaction trouvée', [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'current_status' => $transaction->status
            ]);

            // Vérifier si déjà traitée
            if ($transaction->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction déjà complétée',
                    'transaction' => $transaction->toArray()
                ]);
            }

            // Traitement selon le statut
            DB::beginTransaction();

            try {
                if ($status === 'success' || $status === 'completed') {
                    // Mettre à jour la transaction
                    $transaction->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'kkiapay_callback' => $data,
                            'kkiapay_phone' => $phone,
                            'processed_at' => now()->toIso8601String(),
                        ])
                    ]);

                    // Créditer le wallet
                    $wallet = $transaction->wallet;
                    if ($wallet && in_array($transaction->type, ['credit', 'deposit'])) {
                        $oldBalance = (float) $wallet->balance;
                        $newBalance = $oldBalance + (float) $transaction->amount;
                        
                        $wallet->update([
                            'balance' => $newBalance,
                            'last_transaction_at' => now()
                        ]);

                        Log::info('Wallet crédité', [
                            'wallet_id' => $wallet->id,
                            'old_balance' => $oldBalance,
                            'new_balance' => $newBalance,
                            'amount_added' => $transaction->amount
                        ]);
                    }

                    // Notification utilisateur
                    $this->notifyUser($wallet->user_id ?? $transaction->wallet->user_id, [
                        'type' => 'transaction',
                        'title' => 'Dépôt réussi',
                        'message' => 'Votre compte a été crédité de ' . number_format($transaction->amount, 0, ',', ' ') . ' FCFA',
                        'data' => ['transaction_id' => $transaction->id]
                    ]);

                    DB::commit();

                    Log::info('Transaction complétée avec succès', ['id' => $transaction->id]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Paiement traité avec succès',
                        'transaction' => [
                            'id' => $transaction->id,
                            'transaction_id' => $transaction->transaction_id,
                            'status' => 'completed',
                            'amount' => $transaction->amount,
                            'new_balance' => $newBalance ?? $wallet->balance ?? null
                        ]
                    ]);

                } else {
                    // Échec
                    $transaction->update([
                        'status' => 'failed',
                        'completed_at' => now(),
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'failure_reason' => 'Statut Kkiapay: ' . $status,
                            'kkiapay_callback' => $data
                        ])
                    ]);

                    DB::commit();

                    return response()->json([
                        'success' => false,
                        'message' => 'Paiement échoué',
                        'status' => $status
                    ]);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Erreur callback Kkiapay: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée une transaction depuis le callback si non trouvée
     */
    private function createTransactionFromCallback(array $data, array $additionalData): \Illuminate\Http\JsonResponse
    {
        try {
            // CORRECTION: Récupérer wallet_id depuis les bonnes sources
            $walletId = $additionalData['wallet_id'] 
                ?? $data['wallet_id'] 
                ?? null;
                
            $amount = $data['amount'] 
                ?? $additionalData['amount'] 
                ?? 0;
                
            $phone = $data['phone'] 
                ?? $additionalData['phone'] 
                ?? null;

            if (!$walletId || !$amount) {
                Log::error('Données insuffisantes pour créer la transaction', [
                    'wallet_id' => $walletId,
                    'amount' => $amount,
                    'data' => $data
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Données insuffisantes pour créer la transaction'
                ], 400);
            }

            $wallet = Wallet::find($walletId);
            if (!$wallet) {
                Log::error('Wallet non trouvé', ['wallet_id' => $walletId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet non trouvé'
                ], 404);
            }

            DB::beginTransaction();

            // Générer un nouveau transaction_id si non fourni
            $newTransactionId = $data['transactionId'] 
                ?? $additionalData['transaction_id']
                ?? 'KKP-' . strtoupper(Str::random(12));
                
            $newReference = $additionalData['reference'] 
                ?? 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_id' => $newTransactionId,
                'reference' => $newReference,
                'type' => 'credit',
                'amount' => $amount,
                'total_amount' => $amount,
                'fee' => 0,
                'status' => 'completed',
                'payment_method' => 'kkiapay',
                'description' => 'Dépôt via Kkiapay (auto-créé)',
                'completed_at' => now(),
                'metadata' => [
                    'created_from_callback' => true,
                    'kkiapay_data' => $data,
                    'phone' => $phone,
                    'wallet_id' => $walletId,
                    'user_name' => $additionalData['user_name'] ?? null,
                    'user_email' => $additionalData['user_email'] ?? null,
                ]
            ]);

            // Créditer le wallet
            $wallet->increment('balance', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            $this->notifyUser($wallet->user_id, [
                'type' => 'transaction',
                'title' => 'Dépôt réussi',
                'message' => 'Votre compte a été crédité de ' . number_format($amount, 0, ',', ' ') . ' FCFA',
            ]);

            DB::commit();

            Log::info('Transaction créée depuis callback', ['id' => $transaction->id]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction créée et complétée',
                'transaction' => $transaction->toArray()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création transaction callback: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

       /**
     * Créer une notification
     */
    private function notifyUser($userId, array $data)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'data' => $data['data'] ?? [],
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur création notification: ' . $e->getMessage());
        }
    }

    /**
     * Demande de retrait
     */
    public function withdraw(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1000',
                'withdraw_method' => 'required|in:mobile_money,bank_transfer',
                'phone_number' => 'nullable|string|max:20',
                'account_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'bank_name' => 'nullable|string|max:255',
                'pin' => 'required|string|size:6',
                'note' => 'nullable|string|max:500',
            ]);

            // Validation conditionnelle
            if ($validated['withdraw_method'] === 'mobile_money' && empty($validated['phone_number'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le numéro de téléphone est requis pour Mobile Money'
                ], 422);
            }

            if ($validated['withdraw_method'] === 'bank_transfer') {
                $missingFields = [];
                if (empty($validated['account_name'])) $missingFields[] = 'nom du bénéficiaire';
                if (empty($validated['account_number'])) $missingFields[] = 'numéro de compte';
                if (empty($validated['bank_name'])) $missingFields[] = 'nom de la banque';

                if (!empty($missingFields)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Champs requis manquants : ' . implode(', ', $missingFields)
                    ], 422);
                }
            }

            $user = Auth::user();

            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Portefeuille non trouvé'
                ], 404);
            }

            if (!$wallet->pin_hash || !Hash::check($validated['pin'], $wallet->pin_hash)) {
                Log::warning('Tentative retrait PIN invalide', [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN incorrect'
                ], 403);
            }

            $currentBalance = (float) $wallet->balance;
            $requestedAmount = (float) $validated['amount'];

            if ($currentBalance < $requestedAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant. Disponible : ' . number_format($currentBalance, 0, ',', ' ') . ' FCFA'
                ], 400);
            }

            DB::beginTransaction();

            $wallet->balance = $currentBalance - $requestedAmount;
            $wallet->last_transaction_at = now();
            $wallet->save();

            $metadata = [
                'requested_at' => now()->toIso8601String(),
                'requested_by' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'note' => $validated['note'] ?? null,
            ];

            if ($validated['withdraw_method'] === 'mobile_money') {
                $metadata['phone_number'] = $validated['phone_number'];
            } else {
                $metadata['account_name'] = $validated['account_name'];
                $metadata['account_number'] = $validated['account_number'];
                $metadata['bank_name'] = $validated['bank_name'];
            }

            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_id' => (string) Str::uuid(),
                'type' => 'debit',
                'amount' => $requestedAmount,
                'total_amount' => $requestedAmount,
                'fee' => 0,
                'status' => 'pending',
                'payment_method' => $validated['withdraw_method'],
                'description' => 'Demande de retrait - En attente de validation',
                'reference' => 'WIT-' . strtoupper(Str::random(10)),
                'metadata' => $metadata,
            ]);

            $this->createNotification($user->id, [
                'type' => 'transaction',
                'title' => 'Demande de retrait soumise',
                'message' => 'Votre demande de ' . number_format($requestedAmount, 0, ',', ' ') . ' FCFA est en attente de validation.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => 'pending'
                ]
            ]);

            $this->notifyAdmins($transaction, $user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande soumise avec succès',
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'new_balance' => $wallet->balance,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement.'
            ], 500);
        }
    }

    /**
     * Notifier les admins
     */
    private function notifyAdmins(Transaction $transaction, $user)
    {
        try {
            $admins = \App\Models\User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $this->createNotification($admin->id, [
                    'type' => 'admin_alert',
                    'title' => 'Nouvelle demande de retrait',
                    'message' => $user->name . ' demande un retrait de ' . number_format($transaction->amount, 0, ',', ' ') . ' FCFA',
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification admin: ' . $e->getMessage());
        }
    }

    /**
     * Annuler un retrait
     */
    public function cancelWithdrawal(Request $request, $transactionId)
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

            DB::beginTransaction();

            $transaction = Transaction::where('id', $transactionId)
                ->where('wallet_id', $wallet->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction non trouvée ou déjà traitée'
                ], 404);
            }

            $wallet->balance += $transaction->amount;
            $wallet->save();

            $transaction->status = 'cancelled';
            $transaction->description = 'Demande annulée par l\'utilisateur';
            $transaction->save();

            $this->createNotification($user->id, [
                'type' => 'transaction',
                'title' => 'Retrait annulé',
                'message' => 'Votre demande de retrait a été annulée. Le montant a été recrédité.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'refund_amount' => $transaction->amount
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande annulée avec succès',
                'refund_amount' => $transaction->amount,
                'new_balance' => $wallet->balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur annulation retrait: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation'
            ], 500);
        }
    }

    /**
     * Transfert vers un autre wallet
     */
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
                $wallet->decrement('balance', $validated['amount']);
                $recipientWallet->increment('balance', $validated['amount']);

                $senderTransaction = Transaction::create([
                    'wallet_id' => $wallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'debit',
                    'amount' => $validated['amount'],
                    'total_amount' => $validated['amount'],
                    'status' => 'completed',
                    'payment_method' => 'transfer',
                    'description' => 'Transfert vers ' . ($validated['recipient_name'] ?? $recipientWallet->wallet_number),
                    'reference' => 'TRF-' . strtoupper(Str::random(10)),
                    'metadata' => [
                        'recipient_wallet' => $recipientWallet->wallet_number,
                        'recipient_name' => $validated['recipient_name'],
                        'reason' => $validated['reason'],
                        'direction' => 'out',
                    ],
                ]);

                Transaction::create([
                    'wallet_id' => $recipientWallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'credit',
                    'amount' => $validated['amount'],
                    'total_amount' => $validated['amount'],
                    'status' => 'completed',
                    'payment_method' => 'transfer',
                    'description' => 'Transfert reçu de ' . ($user->name ?? $wallet->wallet_number),
                    'reference' => $senderTransaction->reference,
                    'metadata' => [
                        'sender_wallet' => $wallet->wallet_number,
                        'sender_name' => $user->name,
                        'reason' => $validated['reason'],
                        'direction' => 'in',
                    ],
                ]);

                $wallet->update(['last_transaction_at' => now()]);
                $recipientWallet->update(['last_transaction_at' => now()]);

                $this->createNotification($user->id, [
                    'type' => 'transaction',
                    'title' => 'Transfert effectué',
                    'message' => 'Transfert de ' . number_format($validated['amount']) . ' XOF vers ' . $recipientWallet->wallet_number,
                ]);

                $this->createNotification($recipientWallet->user_id, [
                    'type' => 'transaction',
                    'title' => 'Transfert reçu',
                    'message' => 'Réception de ' . number_format($validated['amount']) . ' XOF de ' . $user->name,
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

    /**
     * Gestion du PIN
     */
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

            $this->createNotification($user->id, [
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

    /**
     * Vérification du PIN
     */
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
            $token = bin2hex(random_bytes(32));
            session(['wallet_auth_token' => $token]);

            return response()->json([
                'success' => true,
                'message' => 'PIN vérifié avec succès.',
                'auth_token' => $token,
                'valid_for' => 300
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'PIN incorrect.'
        ], 400);
    }

    /**
     * Génère un numéro de wallet unique
     */
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

    /**
     * Vérifie les mises à jour des financements
     */
    public function checkFundingUpdates()
    {
        try {
            $user = Auth::user();

            $pendingFundings = FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'paid', 'documents_validated', 'transfer_pending', 'completed'])
                ->where('updated_at', '>', now()->subMinutes(5))
                ->with(['fundingType', 'documents'])
                ->get();

            $updated = [];

            foreach ($pendingFundings as $funding) {
                $lastChecked = session()->get('last_funding_check_' . $funding->id, $funding->updated_at);

                if ($funding->updated_at->gt($lastChecked)) {
                    $updated[] = [
                        'id' => $funding->id,
                        'title' => $funding->title,
                        'status' => $funding->status,
                        'new_status' => $this->getStatusLabel($funding->status),
                        'updated_at' => $funding->updated_at->format('d/m/Y H:i'),
                        'request_number' => $funding->request_number ?? null,
                    ];

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

    /**
     * Labels des statuts
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'submitted' => 'Soumise',
            'under_review' => 'En examen',
            'validated' => 'Validée',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'paid' => 'Payée',
            'documents_validated' => 'Documents validés',
            'transfer_pending' => 'Transfert en attente',
            'funded' => 'Financée',
            'credited' => 'Accréditée',
            'completed' => 'Terminée'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Détails d'un financement
     */
    public function fundingDetails($id)
    {
        try {
            $funding = FundingRequest::with(['fundingType', 'documents', 'validator'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'funding' => [
                    'id' => $funding->id,
                    'title' => $funding->title,
                    'type' => $funding->is_predefined ? 'Prédéfinie' : 'Personnalisée',
                    'status' => $funding->status,
                    'status_label' => $this->getStatusLabel($funding->status),
                    'amount_requested' => $funding->amount_requested,
                    'amount_approved' => $funding->amount_approved,
                    'expected_payment' => $funding->expected_payment,
                    'request_number' => $funding->request_number,
                    'created_at' => $funding->created_at->format('d/m/Y'),
                    'updated_at' => $funding->updated_at->format('d/m/Y H:i'),
                    'validated_at' => $funding->validated_at?->format('d/m/Y H:i'),
                    'admin_notes' => $funding->admin_validation_notes,
                    'validator' => $funding->validator ? [
                        'name' => $funding->validator->name,
                        'validated_at' => $funding->validated_at?->format('d/m/Y'),
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

    /**
     * Crédite un financement sur le wallet
     */
    public function creditFunding($id)
    {
        try {
            $funding = FundingRequest::where('user_id', Auth::id())
                ->whereIn('status', ['funded', 'completed', 'transfer_pending'])
                ->findOrFail($id);

            if ($funding->credited_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce financement a déjà été crédité'
                ]);
            }

            if ($funding->is_predefined && $funding->transfer_status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le transfert n\'est pas encore finalisé par l\'administrateur'
                ], 400);
            }

            $wallet = Auth::user()->wallet;
            $amount = $funding->amount_approved ?? $funding->amount_requested;

            DB::transaction(function () use ($wallet, $funding, $amount) {
                $wallet->increment('balance', $amount);
                $wallet->update(['last_transaction_at' => now()]);

                $funding->update([
                    'credited_at' => now(),
                    'status' => 'credited'
                ]);

                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'credit',
                    'amount' => $amount,
                    'total_amount' => $amount,
                    'payment_method' => $funding->is_predefined ? 'bank_transfer' : 'kkiapay',
                    'description' => 'Financement crédité: ' . $funding->request_number,
                    'status' => 'completed',
                    'reference' => 'FUND-' . $funding->request_number,
                    'metadata' => [
                        'funding_request_id' => $funding->id,
                        'funding_type' => $funding->is_predefined ? 'predefined' : 'custom',
                        'previous_balance' => $wallet->balance - $amount,
                        'new_balance' => $wallet->balance,
                    ]
                ]);

                $this->createNotification($wallet->user_id, [
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

    /**
     * Informations du wallet (API)
     */
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

            $thirtyDaysAgo = now()->subDays(30);

            $stats = [
                'total_deposits' => $wallet->transactions()
                    ->where('type', 'credit')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),

                'total_withdrawals' => $wallet->transactions()
                    ->where('type', 'debit')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),

                'total_transfers' => $wallet->transactions()
                    ->where('type', 'transfer')
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->sum('amount'),

                'total_payments' => $wallet->transactions()
                    ->where('type', 'payment')
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
                    'security_level' => $wallet->security_level ?? 'normal',
                    'created_at' => $wallet->created_at->format('d/m/Y'),
                ],
                'stats' => $stats,
                'has_pin' => ($wallet->security_level ?? 'normal') !== 'normal'
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

    /**
     * Actions rapides (API)
     */
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

    /**
     * Pages de paiement (succès/échec)
     */
    public function paymentSuccess(Transaction $transaction)
    {
        if (auth()->check() && $transaction->wallet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.success', compact('transaction'));
    }

    public function paymentFailed(Transaction $transaction)
    {
        if (auth()->check() && $transaction->wallet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.failed', compact('transaction'));
    }

    public function paymentError()
    {
        return view('payment.error');
    }

    public function paymentStatus(Transaction $transaction)
    {
        if (auth()->check() && $transaction->wallet->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payment.status', compact('transaction'));
    }
}