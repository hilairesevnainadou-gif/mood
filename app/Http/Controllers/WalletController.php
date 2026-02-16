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
     * Dépôt via Kkiapay - Initialisation côté serveur
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
            // Créer une transaction en attente
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_id' => (string) Str::uuid(),
                'type' => 'credit',
                'amount' => $validated['amount'],
                'total_amount' => $validated['amount'],
                'status' => 'pending',
                'payment_method' => 'kkiapay',
                'description' => 'Dépôt via Kkiapay',
                'reference' => 'DEP-' . strtoupper(Str::random(10)),
                'metadata' => [
                    'phone' => $validated['phone'],
                    'initiated_at' => now()->toIso8601String(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction initiée',
                'transaction_id' => $transaction->transaction_id,
                'amount' => $validated['amount'],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur init dépôt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initialisation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Demande de retrait avec vérification du PIN (pas du mot de passe)
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

            // Validation conditionnelle selon la méthode
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

            // Récupérer le wallet avec verrouillage pessimiste
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Portefeuille non trouvé'
                ], 404);
            }

            // Vérifier le PIN du wallet
            if (!$wallet->pin_hash || !Hash::check($validated['pin'], $wallet->pin_hash)) {
                Log::warning('Tentative de retrait avec PIN invalide', [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'PIN incorrect'
                ], 403);
            }

            // Vérifier le solde
            $currentBalance = (float) $wallet->balance;
            $requestedAmount = (float) $validated['amount'];

            if ($currentBalance < $requestedAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant. Disponible : ' . number_format($currentBalance, 0, ',', ' ') . ' FCFA'
                ], 400);
            }

            DB::beginTransaction();

            // Décrémenter immédiatement le solde
            $wallet->balance = $currentBalance - $requestedAmount;
            $wallet->last_transaction_at = now();
            $wallet->save();

            // Préparer les métadonnées
            $metadata = [
                'requested_at' => now()->toIso8601String(),
                'requested_by' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'note' => $validated['note'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            if ($validated['withdraw_method'] === 'mobile_money') {
                $metadata['phone_number'] = $validated['phone_number'];
            } else {
                $metadata['account_name'] = $validated['account_name'];
                $metadata['account_number'] = $validated['account_number'];
                $metadata['bank_name'] = $validated['bank_name'];
            }

            // CORRECTION ICI : Utiliser 'debit' au lieu de 'withdrawal'
            // Vérifiez les valeurs autorisées dans votre table transactions (type ENUM)
            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'transaction_id' => (string) Str::uuid(),
                'type' => 'debit', // CHANGÉ : 'withdrawal' -> 'debit' (ou autre valeur valide)
                'amount' => $requestedAmount,
                'total_amount' => $requestedAmount,
                'fee' => 0,
                'status' => 'pending',
                'payment_method' => $validated['withdraw_method'],
                'description' => 'Demande de retrait - En attente de validation',
                'reference' => 'WIT-' . strtoupper(Str::random(10)),
                'metadata' => $metadata, // Laravel gère automatiquement le JSON
            ]);

            // Notification utilisateur
            Notification::create([
                'user_id' => $user->id,
                'type' => 'transaction',
                'title' => 'Demande de retrait soumise',
                'message' => 'Votre demande de ' . number_format($requestedAmount, 0, ',', ' ') . ' FCFA est en attente de validation.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => 'pending',
                    'amount' => $requestedAmount,
                    'method' => $validated['withdraw_method']
                ],
            ]);

            // Notification admin
            $this->notifyAdmin($transaction, $user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Demande soumise avec succès',
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'status' => 'pending',
                'new_balance' => $wallet->balance,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation erreur retrait:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement. Veuillez réessayer.'
            ], 500);
        }
    }


    /**
     * Annuler une demande de retrait en attente
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

            // Rembourser le solde
            $wallet->balance += $transaction->amount;
            $wallet->save();

            // Mettre à jour la transaction
            $transaction->status = 'cancelled';
            $transaction->description = 'Demande annulée par l\'utilisateur';
            $transaction->save();

            // Notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'transaction',
                'title' => 'Retrait annulé',
                'message' => 'Votre demande de retrait a été annulée. Le montant a été recrédité.',
                'data' => json_encode([
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'refund_amount' => $transaction->amount
                ]),
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
     * Notifier les administrateurs d'une nouvelle demande
     */
    private function notifyAdmin(Transaction $transaction, $user)
    {
        try {
            // Récupérer les admins ou envoyer email
            // Exemple avec notification base de données
            $admins = \App\Models\User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'admin_alert',
                    'title' => 'Nouvelle demande de retrait',
                    'message' => $user->name . ' demande un retrait de ' . number_format($transaction->amount, 0, ',', ' ') . ' FCFA',
                    'data' => json_encode([
                        'transaction_id' => $transaction->id,
                        'reference' => $transaction->reference,
                        'user_id' => $user->id
                    ]),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification admin: ' . $e->getMessage());
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
                // Débiter l'émetteur
                $wallet->decrement('balance', $validated['amount']);

                // Créditer le destinataire
                $recipientWallet->increment('balance', $validated['amount']);

                // Transaction émetteur
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

                // Transaction destinataire
                $receiverTransaction = Transaction::create([
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

                // Mise à jour des timestamps
                $wallet->update(['last_transaction_at' => now()]);
                $recipientWallet->update(['last_transaction_at' => now()]);

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
     * Méthodes utilitaires
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
}
