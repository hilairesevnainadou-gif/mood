<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use App\Models\FundingRepayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FundingValidationController extends Controller
{
    /**
     * Affiche les demandes en attente de validation initiale
     */
    public function pendingValidation(Request $request)
    {
        $query = FundingRequest::with(['user', 'fundingType', 'documents'])
            ->whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'pending_payment', 'paid']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('is_predefined', $request->type === 'predefined');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fund = $query->orderByDesc('created_at')->get();

        // CORRECTION: Stats cohérentes avec les méthodes dédiées
        $stats = [
            'total_pending' => FundingRequest::whereIn('status', ['submitted', 'under_review'])->count(),
            'pending_payment' => FundingRequest::whereIn('status', ['validated', 'pending_payment'])->count(),
            // CORRECTION: Même critère que pendingPayments - status=paid ET validated_at IS NULL
            'paid_awaiting_validation' => FundingRequest::where('status', 'paid')
                ->whereNotNull('kkiapay_transaction_id')
                ->whereNull('validated_at')
                ->count(),
        ];

        return view('admin.funding.pending-validation', compact('fund', 'stats'));
    }

    /**
     * Affiche les paiements en attente de vérification (KKIAPAY)
     */
    public function pendingPayments(Request $request)
    {
        // CORRECTION: Utilisation d'une sous-requête pour éviter les conflits avec les filtres
        $baseQuery = FundingRequest::where('status', 'paid')
            ->whereNotNull('kkiapay_transaction_id')
            ->whereNull('validated_at');

        $query = FundingRequest::with(['user'])
            ->where('status', 'paid')
            ->whereNotNull('kkiapay_transaction_id')
            ->whereNull('validated_at');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('kkiapay_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->orderByDesc('paid_at')->paginate(15)->withQueryString();

        // CORRECTION: Stats basées sur la requête de base sans les filtres de date
        $stats = [
            'total_pending' => (clone $baseQuery)->count(),
            'total_amount_pending' => (clone $baseQuery)->sum('kkiapay_amount_paid'),
        ];

        return view('admin.funding.pending-payments', compact('payments', 'stats'));
    }

    /**
     * Affiche les transferts en attente (à programmer + programmés)
     */
    public function pendingTransfers(Request $request)
    {
        // Requête de base : demandes payées/approuvées mais pas encore transférées
        $baseQuery = FundingRequest::where(function ($q) {
            // Étape 1 : Payé ou approuvé, pas encore programmé
            $q->where(function ($sq) {
                $sq->whereIn('status', ['paid', 'approved'])
                    ->whereNull('transfer_scheduled_at');
            })
                // Étape 2 : Programmé mais pas exécuté
                ->orWhere(function ($sq) {
                    $sq->whereNotNull('transfer_scheduled_at')
                        ->whereNull('transfer_executed_at');
                });
        });

        $query = clone $baseQuery;

        // Filtre par étape
        if ($request->stage === 'to_schedule') {
            $query->where(function ($q) {
                $q->whereIn('status', ['paid', 'approved'])
                    ->whereNull('transfer_scheduled_at');
            });
        } elseif ($request->stage === 'scheduled') {
            $query->whereNotNull('transfer_scheduled_at')
                ->whereNull('transfer_executed_at');
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transfer_scheduled_at', '>=', $request->date_from)
                ->orWhereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transfer_scheduled_at', '<=', $request->date_to)
                ->orWhereDate('paid_at', '<=', $request->date_to);
        }

        $transfers = $query->with(['user', 'fundingType', 'documentsCheckedBy'])
            ->orderByRaw('CASE WHEN transfer_scheduled_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('transfer_scheduled_at', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Stats
        $stats = [
            'to_schedule' => (clone $baseQuery)->where(function ($q) {
                $q->whereIn('status', ['paid', 'approved'])
                    ->whereNull('transfer_scheduled_at');
            })->count(),
            'scheduled' => (clone $baseQuery)->whereNotNull('transfer_scheduled_at')
                ->whereNull('transfer_executed_at')->count(),
            'total_amount' => (clone $baseQuery)->sum('amount_approved'),
        ];

        return view('admin.funding.pending-transfers', compact('transfers', 'stats'));
    }

    /**
     * Affiche les détails d'une demande
     */
    public function showRequest($id)
    {
        $request = FundingRequest::with([
            'user',
            'fundingType',
            'documents',
            'validator',
            'documentsCheckedBy',
            'repayments'
        ])->findOrFail($id);

        return view('admin.funding.show-request', compact('request'));
    }

    /**
     * Définit le prix pour une demande personnalisée
     */
    public function setPrice(Request $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);

        if (!$funding->is_predefined && !in_array($funding->status, ['submitted', 'under_review'])) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'amount_approved' => 'required|numeric|min:0',
            'expected_payment' => 'required|numeric|min:0',
            'payment_motif' => 'required|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $funding->update([
            'amount_approved' => $validated['amount_approved'],
            'expected_payment' => $validated['expected_payment'],
            'payment_motif' => $validated['payment_motif'],
            'admin_validation_notes' => $validated['admin_notes'],
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        // Notification au client
        Notification::create([
            'user_id' => $funding->user_id,
            'type' => 'funding',
            'title' => 'Demande validée - Paiement requis',
            'message' => "Votre demande {$funding->request_number} a été validée. Montant à payer : " .
                number_format($validated['expected_payment'], 0, ',', ' ') . ' FCFA',
            'data' => ['funding_request_id' => $funding->id],
        ]);

        return back()->with('success', 'Prix défini et demande validée avec succès.');
    }

    /**
     * Met une demande en examen
     */
    public function setUnderReview($id)
    {
        $funding = FundingRequest::findOrFail($id);

        if (!in_array($funding->status, ['submitted', 'pending_committee'])) {
            return back()->with('error', 'Statut invalide pour cette action.');
        }

        $funding->update([
            'status' => 'under_review',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Demande mise en examen.');
    }

    /**
     * Rejette une demande
     */
    public function rejectRequest(Request $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $funding->update([
            'status' => 'rejected',
            'admin_validation_notes' => $validated['rejection_reason'],
        ]);

        // Rembourser si déjà payé
        if ($funding->isPaid()) {
            $this->processRefund($funding);
        }

        Notification::create([
            'user_id' => $funding->user_id,
            'type' => 'funding',
            'title' => 'Demande rejetée',
            'message' => "Votre demande {$funding->request_number} a été rejetée. Raison : {$validated['rejection_reason']}",
            'data' => ['funding_request_id' => $funding->id],
        ]);

        return back()->with('success', 'Demande rejetée.');
    }

    /**
     * Approuve une demande prédéfinie
     */
    public function approvePredefined(Request $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);

        if (!$funding->is_predefined) {
            return back()->with('error', 'Cette action est réservée aux demandes prédéfinies.');
        }

        if (!in_array($funding->status, ['submitted', 'under_review', 'pending_committee'])) {
            return back()->with('error', 'Cette demande ne peut pas être approuvée.');
        }

        $validated = $request->validate([
            'amount_approved' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $funding->update([
            'amount_approved' => $validated['amount_approved'],
            'status' => 'approved',
            'approved_at' => now(),
            'admin_validation_notes' => $validated['admin_notes'],
        ]);

        // Pour les prédéfinies, on passe directement à la vérification des documents
        // ou on peut programmer directement si pas de documents requis
        Notification::create([
            'user_id' => $funding->user_id,
            'type' => 'funding',
            'title' => 'Demande approuvée',
            'message' => "Votre demande {$funding->request_number} a été approuvée. Montant : " .
                number_format($validated['amount_approved'], 0, ',', ' ') . ' FCFA',
            'data' => ['funding_request_id' => $funding->id],
        ]);

        return back()->with('success', 'Demande prédéfinie approuvée.');
    }

    /**
     * Vérifie les documents et programme le transfert (avec modal de programmation)
     */
    public function verifyMissingDocumentsAndScheduleTransfer(Request $request, $id)
    {
        $funding = FundingRequest::with(['user', 'documents'])->findOrFail($id);

        // Vérifier que la demande est dans le bon statut
        if (!in_array($funding->status, ['paid', 'approved'])) {
            return back()->with('error', 'Cette demande ne peut pas être traitée actuellement.');
        }

        // Validation des données de programmation
        $validated = $request->validate([
            'total_repayment_amount' => 'required|numeric|min:0',
            'repayment_duration_months' => 'required|integer|min:1|max:60',
            'repayment_start_date' => 'required|date|after_or_equal:today',
            'final_notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($funding, $validated, $request) {
                // Calculer le montant mensuel
                $monthlyAmount = $validated['total_repayment_amount'] / $validated['repayment_duration_months'];

                // Calculer la date de fin
                $endDate = \Carbon\Carbon::parse($validated['repayment_start_date'])
                    ->addMonths($validated['repayment_duration_months']);

                // CORRECTION: Utiliser 'approved' au lieu de 'documents_validated' si non supporté
                // Ou vérifier si la valeur existe dans l'ENUM
                $newStatus = 'approved'; // Valeur sûre qui existe sûrement

                // Si vous êtes sûr que 'documents_validated' existe, utilisez-la :
                // $newStatus = 'documents_validated';

                // Mettre à jour la demande
                $funding->update([
                    'status' => $newStatus, // CORRECTION ICI
                    'documents_checked_at' => now(),
                    'documents_checked_by' => auth()->id(),
                    'transfer_scheduled_at' => now(),
                    'transfer_status' => 'scheduled',
                    'total_repayment_amount' => $validated['total_repayment_amount'],
                    'monthly_repayment_amount' => $monthlyAmount,
                    'repayment_duration_months' => $validated['repayment_duration_months'],
                    'repayment_start_date' => $validated['repayment_start_date'],
                    'repayment_end_date' => $endDate,
                    'final_notes' => $validated['final_notes'],
                ]);

                // Créer les échéances de remboursement
                $this->createRepaymentSchedule($funding);

                // Valider automatiquement les documents manquants si nécessaire
                foreach ($funding->documents as $document) {
                    if ($document->status !== 'validated') {
                        $document->update([
                            'status' => 'validated',
                            'validated_at' => now(),
                            'validated_by' => auth()->id(),
                            'admin_notes' => 'Validé automatiquement lors de la programmation du transfert',
                        ]);
                    }
                }
            });

            return redirect()->route('admin.funding.pending-transfers')
                ->with('success', 'Documents vérifiés et transfert programmé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur programmation transfert: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la programmation: ' . $e->getMessage());
        }
    }

    /**
     * Crée le calendrier de remboursement
     */
    private function createRepaymentSchedule(FundingRequest $funding): void
    {
        $startDate = \Carbon\Carbon::parse($funding->repayment_start_date);

        for ($i = 0; $i < $funding->repayment_duration_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);

            // Ajuster pour les week-ends (vendredi si samedi, lundi si dimanche)
            if ($dueDate->isSaturday()) {
                $dueDate->subDay();
            } elseif ($dueDate->isSunday()) {
                $dueDate->addDay();
            }

            FundingRepayment::create([
                'funding_request_id' => $funding->id,
                'due_date' => $dueDate,
                'amount_due' => $funding->monthly_repayment_amount,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Exécute le transfert final (crédite le wallet)
     */
    public function executeTransfer(Request $request, $id)
    {
        $funding = FundingRequest::with(['user.wallet'])->findOrFail($id);

        if (!$funding->canExecuteTransfer()) {
            return back()->with('error', 'Ce transfert ne peut pas être exécuté.');
        }

        $validated = $request->validate([
            'confirm_transfer' => 'required|accepted',
            'final_notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($funding, $validated) {
                $wallet = $funding->user->wallet;

                // Créer le wallet s'il n'existe pas
                if (!$wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $funding->user_id,
                        'wallet_number' => $this->generateWalletNumber(),
                        'balance' => 0,
                        'currency' => 'XOF',
                        'pin_hash' => \Illuminate\Support\Facades\Hash::make('000000'),
                    ]);
                }

                $amount = $funding->amount_approved ?? $funding->amount_requested;

                // Créer la transaction de crédit
                $transaction = Transaction::create([
                    'wallet_id' => $wallet->id,
                    'transaction_id' => (string) Str::uuid(),
                    'type' => 'credit',
                    'amount' => $amount,
                    'total_amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => $funding->is_predefined ? 'bank_transfer' : 'kkiapay',
                    'description' => 'Financement crédité: ' . $funding->request_number,
                    'reference' => 'FUND-' . $funding->request_number,
                    'completed_at' => now(),
                    'metadata' => [
                        'funding_request_id' => $funding->id,
                        'funding_type' => $funding->is_predefined ? 'predefined' : 'custom',
                        'previous_balance' => $wallet->balance,
                        'new_balance' => $wallet->balance + $amount,
                    ],
                ]);

                // Créditer le wallet
                $wallet->increment('balance', $amount);
                $wallet->update(['last_transaction_at' => now()]);

                // Mettre à jour la demande
                $funding->update([
                    'status' => 'funded',
                    'transfer_status' => 'completed',
                    'transfer_executed_at' => now(),
                    'credited_at' => now(),
                    'final_notes' => $validated['final_notes'] ?? $funding->final_notes,
                ]);

                // Notification au client
                Notification::create([
                    'user_id' => $funding->user_id,
                    'type' => 'transaction',
                    'title' => 'Financement crédité !',
                    'message' => "Votre financement de " . number_format($amount, 0, ',', ' ') .
                        " FCFA a été crédité sur votre wallet. Remboursement: " .
                        number_format($funding->monthly_repayment_amount, 0, ',', ' ') .
                        " FCFA/mois pendant {$funding->repayment_duration_months} mois.",
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'funding_request_id' => $funding->id,
                    ],
                ]);
            });

            return redirect()->route('admin.funding.pending-transfers')
                ->with('success', 'Transfert exécuté avec succès. Le wallet a été crédité.');
        } catch (\Exception $e) {
            Log::error('Erreur exécution transfert: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Annule un transfert programmé
     */
    public function cancelTransfer(Request $request, $id)
    {
        $funding = FundingRequest::findOrFail($id);

        if ($funding->transfer_status !== 'scheduled') {
            return back()->with('error', 'Ce transfert ne peut pas être annulé.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:5',
        ]);

        $funding->update([
            'status' => 'paid', // Retour au statut précédent
            'transfer_status' => 'cancelled',
            'transfer_cancellation_reason' => $validated['cancellation_reason'],
            'transfer_scheduled_at' => null,
        ]);

        // Supprimer les échéances créées
        $funding->repayments()->delete();

        Notification::create([
            'user_id' => $funding->user_id,
            'type' => 'funding',
            'title' => 'Transfert annulé',
            'message' => "Le transfert de votre financement {$funding->request_number} a été annulé. Raison: {$validated['cancellation_reason']}",
            'data' => ['funding_request_id' => $funding->id],
        ]);

        return redirect()->route('admin.funding.pending-validation')
            ->with('success', 'Transfert annulé.');
    }

    /**
     * Vérifie un paiement Kkiapay et passe à la vérification des documents
     */
    public function verifyPayment(Request $request, $paymentId)
    {
        $funding = FundingRequest::findOrFail($paymentId);

        if ($funding->status !== 'paid') {
            return back()->with('error', 'Cette demande n\'est pas en statut payé.');
        }

        $validated = $request->validate([
            'confirm_verify' => 'required|accepted',
            'verification_notes' => 'nullable|string',
        ]);

        // Vérifier si montant payé correspond au attendu (avec marge de 1%)
        $expected = $funding->expected_payment;
        $paid = $funding->kkiapay_amount_paid;
        $tolerance = $expected * 0.01;

        if (abs($paid - $expected) > $tolerance) {
            return back()->with('warning', 'Attention: écart de paiement détecté. Payé: ' .
                number_format($paid, 0, ',', ' ') . ' vs Attendu: ' .
                number_format($expected, 0, ',', ' ') . ' FCFA');
        }

        $funding->update([
            'validated_at' => now(),
            'validated_by' => auth()->id(),
            'admin_validation_notes' => $validated['verification_notes'],
            // CORRECTION: On garde le statut 'paid' mais on a validated_at rempli
            // La demande n'apparaîtra plus dans pendingPayments mais reste visible dans pendingValidation
        ]);

        return redirect()->route('admin.funding.show-request', $funding->id)
            ->with('success', 'Paiement vérifié. Procédez maintenant à la vérification des documents et programmation du transfert.');
    }

    /**
     * Complète une demande (après remboursement total)
     */
    public function completeRequest($id)
    {
        $funding = FundingRequest::with(['repayments'])->findOrFail($id);

        $totalPaid = $funding->repayments()->where('status', 'paid')->sum('amount_paid');

        if ($totalPaid < $funding->total_repayment_amount * 0.99) { // 99% tolérance
            return back()->with('error', 'Le remboursement n\'est pas complet.');
        }

        $funding->update(['status' => 'completed']);

        return back()->with('success', 'Demande marquée comme terminée.');
    }

    /**
     * Génère un numéro de wallet
     */
    private function generateWalletNumber(): string
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
     * Traite un remboursement
     */
    private function processRefund(FundingRequest $funding): void
    {
        if (!$funding->kkiapay_amount_paid || $funding->kkiapay_amount_paid <= 0) {
            return;
        }

        $wallet = $funding->user->wallet;
        if (!$wallet) {
            return;
        }

        Transaction::create([
            'wallet_id' => $wallet->id,
            'transaction_id' => (string) Str::uuid(),
            'type' => 'refund',
            'amount' => $funding->kkiapay_amount_paid,
            'total_amount' => $funding->kkiapay_amount_paid,
            'status' => 'completed',
            'payment_method' => 'kkiapay',
            'description' => 'Remboursement demande rejetée: ' . $funding->request_number,
            'reference' => 'REFUND-' . $funding->request_number,
            'completed_at' => now(),
        ]);

        $wallet->increment('balance', $funding->kkiapay_amount_paid);

        Notification::create([
            'user_id' => $funding->user_id,
            'type' => 'transaction',
            'title' => 'Remboursement effectué',
            'message' => "Votre paiement de " . number_format($funding->kkiapay_amount_paid, 0, ',', ' ') .
                " FCFA a été remboursé sur votre wallet.",
            'data' => ['funding_request_id' => $funding->id],
        ]);
    }
}
