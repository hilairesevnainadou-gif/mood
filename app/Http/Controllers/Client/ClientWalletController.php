<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientWalletController extends Controller
{
    protected const CACHE_DURATION = 60;

    /**
     * Affiche la page du portefeuille
     */
    public function wallet()
    {
        $user = Auth::user();
        DB::disableQueryLog();

        $wallet = $this->getOrCreateWallet($user);

        $transactions = Cache::remember("wallet_tx_{$wallet->id}", 30, function () use ($wallet) {
            return $wallet->transactions()
                ->select(['id', 'transaction_id', 'type', 'amount', 'status', 'description', 'created_at'])
                ->latest()
                ->limit(5)
                ->get();
        });

        $pendingFundings = Cache::remember("pending_fundings_{$user->id}", 60, function () use ($user) {
            return FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'transfer_pending', 'documents_validated'])
                ->whereNull('credited_at')
                ->select(['id', 'title', 'request_number', 'status', 'amount_requested', 'amount_approved', 'created_at'])
                ->with(['fundingType:id,name'])
                ->latest()
                ->limit(10)
                ->get();
        });

        $monthlyStats = $this->getMonthlyStats($wallet);

        return view('client.wallet.index', compact('wallet', 'transactions', 'pendingFundings', 'monthlyStats'));
    }



    /**
     * Liste des transactions
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        DB::disableQueryLog();

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();

        $query = $wallet->transactions()
            ->select(['id', 'transaction_id', 'type', 'amount', 'status', 'payment_method',
                     'description', 'reference', 'created_at', 'metadata'])
            ->latest();

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
            'total_in' => $wallet->transactions()
                ->whereIn('type', ['credit', 'refund'])
                ->where('status', 'completed')
                ->sum('amount'),
            'total_out' => $wallet->transactions()
                ->whereIn('type', ['debit', 'payment', 'fee'])
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('client.wallet.transactions', compact('transactions', 'stats'));
    }

    /**
     * Dépôt via Kkiapay
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
            $transactionId = 'KKP-' . strtoupper(Str::random(12));
            $reference = 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

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
                'metadata' => json_encode([
                    'phone' => $validated['phone'],
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'initiated_at' => now()->toIso8601String(),
                ]),
            ]);

            Cache::forget("wallet_user_{$user->id}");
            Cache::forget("wallet_tx_{$wallet->id}");

            return response()->json([
                'success' => true,
                'message' => 'Transaction initiée',
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'amount' => $validated['amount'],
                'user_name' => $user->name,
                'user_email' => $user->email,
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

            if ($validated['withdraw_method'] === 'mobile_money' && empty($validated['phone_number'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le numéro de téléphone est requis pour Mobile Money'
                ], 422);
            }

            $user = Auth::user();
            $wallet = Wallet::where('user_id', $user->id)->first();

            if (!$wallet || !Hash::check($validated['pin'], $wallet->pin_hash)) {
                return response()->json(['success' => false, 'message' => 'PIN incorrect'], 403);
            }

            $amount = (float) $validated['amount'];
            if ((float) $wallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant'
                ], 400);
            }

            $wallet->decrement('balance', $amount);
            $wallet->update(['last_transaction_at' => now()]);

            $transaction = Transaction::create([
                'wallet_id' => $wallet->id,
                'transaction_id' => (string) Str::uuid(),
                'type' => 'debit',
                'amount' => $amount,
                'total_amount' => $amount,
                'status' => 'pending',
                'payment_method' => $validated['withdraw_method'],
                'description' => 'Demande de retrait',
                'reference' => 'WIT-' . strtoupper(Str::random(10)),
                'metadata' => json_encode([
                    'phone' => $validated['phone_number'] ?? null,
                    'account_name' => $validated['account_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'bank_name' => $validated['bank_name'] ?? null,
                    'note' => $validated['note'] ?? null,
                ]),
            ]);

            Cache::forget("wallet_user_{$user->id}");
            Cache::forget("wallet_tx_{$wallet->id}");

            return response()->json([
                'success' => true,
                'message' => 'Demande soumise',
                'reference' => $transaction->reference,
                'new_balance' => $wallet->balance,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur retrait: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
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
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $senderWallet = Wallet::where('user_id', $user->id)->first();
        $recipientWallet = Wallet::where('wallet_number', $validated['recipient_wallet'])->first();

        if (!$senderWallet || !$recipientWallet) {
            return response()->json(['success' => false, 'message' => 'Portefeuille non trouvé'], 404);
        }

        if ($senderWallet->id === $recipientWallet->id) {
            return response()->json(['success' => false, 'message' => 'Transfert vers soi-même interdit'], 400);
        }

        $amount = (float) $validated['amount'];
        if ((float) $senderWallet->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'Solde insuffisant'], 400);
        }

        try {
            DB::transaction(function () use ($senderWallet, $recipientWallet, $amount, $user, $validated) {
                $senderWallet->decrement('balance', $amount);
                $recipientWallet->increment('balance', $amount);

                $ref = 'TRF-' . strtoupper(Str::random(10));

                Transaction::create([
                    'wallet_id' => $senderWallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'debit',
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'transfer',
                    'description' => 'Transfert vers ' . $recipientWallet->wallet_number,
                    'reference' => $ref,
                ]);

                Transaction::create([
                    'wallet_id' => $recipientWallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'credit',
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'transfer',
                    'description' => 'Transfert de ' . $user->name,
                    'reference' => $ref,
                ]);
            }, 3);

            Cache::forget("wallet_user_{$user->id}");
            Cache::forget("wallet_tx_{$senderWallet->id}");
            Cache::forget("wallet_user_{$recipientWallet->user_id}");
            Cache::forget("wallet_tx_{$recipientWallet->id}");

            return response()->json([
                'success' => true,
                'message' => 'Transfert effectué',
                'new_balance' => $senderWallet->fresh()->balance
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur transfert: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
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
            return response()->json(['success' => false, 'message' => 'Portefeuille non trouvé'], 404);
        }

        if ($validated['current_pin'] && !Hash::check($validated['current_pin'], $wallet->pin_hash)) {
            return response()->json(['success' => false, 'message' => 'PIN actuel incorrect'], 400);
        }

        $wallet->update([
            'pin_hash' => Hash::make($validated['new_pin']),
            'security_level' => 'protected',
        ]);

        return response()->json(['success' => true, 'message' => 'PIN mis à jour']);
    }

    /**
     * Vérification du PIN
     */
    public function verifyPin(Request $request)
    {
        $validated = $request->validate(['pin' => 'required|string|size:6']);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || !Hash::check($validated['pin'], $wallet->pin_hash)) {
            return response()->json(['success' => false, 'message' => 'PIN incorrect'], 400);
        }

        $token = bin2hex(random_bytes(32));
        session(['wallet_auth_token' => $token]);

        return response()->json([
            'success' => true,
            'auth_token' => $token,
            'valid_for' => 300
        ]);
    }

    /**
     * Informations du wallet (API)
     */
public function getWalletInfo()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        $wallet = Cache::remember("wallet_info_{$user->id}", 30, function () use ($user) {
            return Wallet::where('user_id', $user->id)
                ->select(['id', 'wallet_number', 'balance', 'currency', 'status', 'created_at']) // ← RETIRÉ 'security_level'
                ->first();
        });

        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Portefeuille non trouvé'], 404);
        }

        return response()->json([
            'success' => true,
            'wallet' => [
                'id' => $wallet->id,
                'wallet_number' => $wallet->wallet_number,
                'balance' => (float) $wallet->balance,
                'currency' => $wallet->currency,
                'status' => $wallet->status,
                // 'security_level' => 'normal', // ← COMMENTÉ ou SUPPRIMÉ
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error wallet info: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
    }
}
    /**
     * Vérifie les mises à jour des financements
     */
    public function checkFundingUpdates()
    {
        try {
            $user = Auth::user();

            $recentUpdates = FundingRequest::where('user_id', $user->id)
                ->where('updated_at', '>', now()->subMinutes(10))
                ->whereIn('status', ['approved', 'paid', 'completed', 'transfer_pending'])
                ->select(['id', 'title', 'status', 'updated_at', 'request_number'])
                ->limit(5)
                ->get();

            $updated = $recentUpdates->map(function ($funding) {
                return [
                    'id' => $funding->id,
                    'title' => $funding->title,
                    'status' => $funding->status,
                    'new_status' => $this->getStatusLabel($funding->status),
                    'updated_at' => $funding->updated_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'updated' => $updated,
                'has_updates' => $updated->isNotEmpty(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur'], 500);
        }
    }

    /**
     * Détails d'un financement
     */
    public function fundingDetails($id)
    {
        try {
            $funding = FundingRequest::where('user_id', Auth::id())
                ->where('id', $id)
                ->select(['id', 'title', 'status', 'amount_requested', 'amount_approved',
                         'request_number', 'created_at', 'updated_at', 'validated_at',
                         'admin_validation_notes', 'is_predefined'])
                ->with(['fundingType:id,name', 'validator:id,name'])
                ->first();

            if (!$funding) {
                return response()->json(['success' => false, 'message' => 'Non trouvé'], 404);
            }

            return response()->json([
                'success' => true,
                'funding' => [
                    'id' => $funding->id,
                    'title' => $funding->title,
                    'status' => $funding->status,
                    'status_label' => $this->getStatusLabel($funding->status),
                    'amount_requested' => $funding->amount_requested,
                    'amount_approved' => $funding->amount_approved,
                    'request_number' => $funding->request_number,
                    'created_at' => $funding->created_at->format('d/m/Y'),
                    'admin_notes' => $funding->admin_validation_notes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur'], 500);
        }
    }

    /**
     * Créditer un financement sur le wallet
     */
    public function creditFunding($id)
    {
        try {
            $user = Auth::user();

            $funding = FundingRequest::where('user_id', $user->id)
                ->where('id', $id)
                ->whereIn('status', ['funded', 'completed', 'transfer_pending'])
                ->whereNull('credited_at')
                ->select(['id', 'amount_approved', 'amount_requested', 'request_number', 'is_predefined', 'transfer_status'])
                ->first();

            if (!$funding) {
                return response()->json(['success' => false, 'message' => 'Financement non disponible'], 400);
            }

            if ($funding->is_predefined && $funding->transfer_status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Transfert non finalisé'], 400);
            }

            $wallet = Wallet::where('user_id', $user->id)->first();
            $amount = $funding->amount_approved ?? $funding->amount_requested;

            DB::transaction(function () use ($wallet, $funding, $amount) {
                $wallet->increment('balance', $amount);

                $funding->update(['credited_at' => now(), 'status' => 'credited']);

                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'credit',
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'funding',
                    'description' => 'Financement: ' . $funding->request_number,
                    'reference' => 'FUND-' . $funding->request_number,
                ]);
            }, 3);

            Cache::forget("wallet_user_{$user->id}");
            Cache::forget("wallet_tx_{$wallet->id}");
            Cache::forget("pending_fundings_{$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Financement crédité',
                'new_balance' => $wallet->fresh()->balance
            ]);

        } catch (\Exception $e) {
            Log::error('Error crediting funding: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Actions rapides
     */
    public function getQuickActions()
    {
        try {
            $user = Auth::user();

            $wallet = Cache::remember("wallet_quick_{$user->id}", 30, function () use ($user) {
                return Wallet::where('user_id', $user->id)
                    ->select(['id', 'balance', 'security_level'])
                    ->first();
            });

            if (!$wallet) {
                return response()->json(['success' => false, 'message' => 'Portefeuille non trouvé'], 404);
            }

            $balance = (float) $wallet->balance;

            $actions = [
                ['id' => 'deposit', 'title' => 'Déposer', 'icon' => 'fa-plus-circle',
                 'color' => 'success', 'available' => true],
                ['id' => 'withdraw', 'title' => 'Retirer', 'icon' => 'fa-minus-circle',
                 'color' => 'danger', 'available' => $balance > 1000],
                ['id' => 'transfer', 'title' => 'Transférer', 'icon' => 'fa-exchange-alt',
                 'color' => 'warning', 'available' => $balance > 100],
                ['id' => 'history', 'title' => 'Historique', 'icon' => 'fa-history',
                 'color' => 'primary', 'available' => true],
            ];

            return response()->json(['success' => true, 'actions' => $actions, 'balance' => $balance]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur'], 500);
        }
    }

    /**
     * Méthodes privées utilitaires
     */

    private function getOrCreateWallet($user)
    {
        return Cache::remember("wallet_user_{$user->id}", 60, function () use ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();

            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'wallet_number' => $this->generateWalletNumber(),
                    'balance' => 0,
                    'currency' => 'XOF',
                    'pin_hash' => Hash::make('000000'),
                    'security_level' => 'normal',
                ]);
            }

            return $wallet;
        });
    }

    private function getMonthlyStats($wallet)
    {
        return Cache::remember("wallet_stats_{$wallet->id}_" . now()->format('Y-m'), 300, function () use ($wallet) {
            $startOfMonth = now()->startOfMonth();

            $stats = $wallet->transactions()
                ->where('created_at', '>=', $startOfMonth)
                ->where('status', 'completed')
                ->selectRaw("
                    SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as deposits,
                    SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as withdrawals,
                    SUM(CASE WHEN type = 'payment' THEN amount ELSE 0 END) as payments
                ")
                ->first();

            return [
                'deposits' => (float) ($stats->deposits ?? 0),
                'withdrawals' => (float) ($stats->withdrawals ?? 0),
                'payments' => (float) ($stats->payments ?? 0),
            ];
        });
    }

    private function generateWalletNumber()
    {
        $prefix = 'WALLET-' . date('ym');
        $last = Wallet::where('wallet_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->wallet_number, -6) + 1) : 1;
        return $prefix . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'submitted' => 'Soumise',
            'under_review' => 'En examen',
            'approved' => 'Approuvée',
            'paid' => 'Payée',
            'completed' => 'Terminée',
            'funded' => 'Financée',
            'credited' => 'Créditée',
        ];
        return $labels[$status] ?? $status;
    }
}
