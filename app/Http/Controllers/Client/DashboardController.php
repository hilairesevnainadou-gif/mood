<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Document;
use App\Models\FundingRequest;
use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Vérification de l'email
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Veuillez vérifier votre adresse email pour accéder au tableau de bord.');
        }

        $missingDocuments = $user->getMissingUploadedRequiredDocuments();

        if (! empty($missingDocuments)) {
            $missingNames = collect($missingDocuments)
                ->pluck('name')
                ->filter()
                ->values()
                ->implode(', ');

            $message = 'Veuillez télécharger vos pièces d\'identité obligatoires pour accéder au tableau de bord.';

            if ($missingNames !== '') {
                $message = 'Documents manquants : ' . $missingNames . '. Veuillez les télécharger pour accéder au tableau de bord.';
            }

            return redirect()->route('client.documents.index')
                ->with('warning', $message);
        }

        // Récupérer ou créer le wallet
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
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

        $requests = FundingRequest::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentTransactions = $wallet->transactions()
            ->latest()
            ->limit(5)
            ->get();

        // Statistiques générales communes
        $generalStats = [
            // Demandes actives (en cours de traitement, pas terminées)
            'active_requests' => FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'pending_payment', 'paid', 'approved'])
                ->count(),

            // Demandes approuvées (funded et completed aussi)
            'approved_requests' => FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'funded', 'completed'])
                ->count(),

            // Formations en cours
            'active_trainings' => $user->trainings()
                ->wherePivot('status', 'enrolled')
                ->count(),

            // Formations terminées
            'completed_trainings' => $user->trainings()
                ->wherePivot('status', 'completed')
                ->count(),

            // Actions en attente (documents + demandes + tickets)
            'pending_actions' => $this->calculatePendingActions($user),

            // Nouvelles demandes (7 derniers jours)
            'new_requests' => FundingRequest::where('user_id', $user->id)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count(),

            // Évolution du wallet (30 derniers jours)
            'wallet_change' => $this->calculateWalletChange($wallet),
        ];

        // Progression du profil
        $user->profile_completion = $user->getCompletionPercentage();

        // Statistiques spécifiques selon le type de compte
        if ($user->isEntreprise()) {
            $entrepriseStats = $this->getEntrepriseStats($user);

            return view('client.dashboard.entreprise', compact(
                'user',
                'wallet',
                'requests',
                'notifications',
                'recentTransactions',
                'generalStats',
                'entrepriseStats'
            ));
        } else {
            $particulierStats = $this->getParticulierStats($user);

            return view('client.dashboard.particulier', compact(
                'user',
                'wallet',
                'requests',
                'notifications',
                'recentTransactions',
                'generalStats',
                'particulierStats'
            ));
        }
    }

    /**
     * Génère un numéro de wallet unique
     */
    private function generateWalletNumber()
    {
        $prefix = 'WLT';
        $year = date('Y');
        $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));

        return $prefix . '-' . $year . '-' . $random;
    }

    /**
     * Fonction helper pour calculer le montant effectif
     * Utilise amount_approved si disponible, sinon amount_requested
     */
    private function getEffectiveAmount($request)
    {
        return $request->amount_approved > 0 ? $request->amount_approved : $request->amount_requested;
    }

    /**
     * Statistiques pour les entreprises
     * Logique: UNIQUEMENT funded = montant effectivement reçu (pas completed)
     */
    private function getEntrepriseStats($user)
    {
        $allRequests = FundingRequest::where('user_id', $user->id)->get();

        // Montant total demandé (toutes demandes)
        $totalRequested = $allRequests->sum('amount_requested');

        // Montant total approuvé (status approved uniquement - pas encore versé)
        $totalApproved = $allRequests
            ->where('status', 'approved')
            ->sum(function($req) {
                return $this->getEffectiveAmount($req);
            });

        // Montant total reçu UNIQUEMENT sur status funded (pas completed)
        // CORRECTION: Utilise getEffectiveAmount pour gérer amount_approved NULL
        $totalReceived = $allRequests
            ->where('status', 'funded')
            ->sum(function($req) {
                return $this->getEffectiveAmount($req);
            });

        // Montant total en attente de versement (soumis jusqu'à approved, exclu funded)
        $totalPending = $allRequests
            ->whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'pending_payment', 'paid', 'approved'])
            ->sum('amount_requested');

        // Emplois attendus (projets approuvés et exécutés)
        $jobsExpected = $allRequests
            ->whereIn('status', ['approved', 'funded', 'completed'])
            ->sum('expected_jobs');

        // Projets par statut détaillé
        $projectsByStatus = [
            'draft' => $allRequests->where('status', 'draft')->count(),
            'submitted' => $allRequests->where('status', 'submitted')->count(),
            'under_review' => $allRequests->where('status', 'under_review')->count(),
            'pending_committee' => $allRequests->where('status', 'pending_committee')->count(),
            'validated' => $allRequests->where('status', 'validated')->count(),
            'pending_payment' => $allRequests->where('status', 'pending_payment')->count(),
            'paid' => $allRequests->where('status', 'paid')->count(),
            'approved' => $allRequests->where('status', 'approved')->count(),
            'funded' => $allRequests->where('status', 'funded')->count(),
            'completed' => $allRequests->where('status', 'completed')->count(),
            'rejected' => $allRequests->where('status', 'rejected')->count(),
            'cancelled' => $allRequests->where('status', 'cancelled')->count(),
        ];

        // Projets par type (predefined vs custom)
        $projectsByType = [
            'predefined' => $allRequests->where('is_predefined', true)->count(),
            'custom' => $allRequests->where('is_predefined', false)->count(),
        ];

        // Taux de succès (approuvées+funded+completed / total soumises)
        $submittedCount = $allRequests->where('status', '!=', 'draft')->count();
        $successRate = $submittedCount > 0
            ? round(($allRequests->whereIn('status', ['approved', 'funded', 'completed'])->count() / $submittedCount) * 100, 1)
            : 0;

        // Demandes prédéfinies (prêts standards entreprise)
        $predefinedRequests = $allRequests->where('is_predefined', true);
        $predefinedStats = [
            'count' => $predefinedRequests->count(),
            'approved' => $predefinedRequests->whereIn('status', ['approved', 'funded', 'completed'])->count(),
            'total_requested' => $predefinedRequests->sum('amount_requested'),
            'total_approved' => $predefinedRequests
                ->where('status', 'approved')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            // CORRECTION: Utilise getEffectiveAmount
            'total_received' => $predefinedRequests
                ->where('status', 'funded')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
        ];

        // Demandes personnalisées (projets sur mesure)
        $customRequests = $allRequests->where('is_predefined', false);
        $customStats = [
            'count' => $customRequests->count(),
            'approved' => $customRequests->whereIn('status', ['approved', 'funded', 'completed'])->count(),
            'total_requested' => $customRequests->sum('amount_requested'),
            'total_approved' => $customRequests
                ->where('status', 'approved')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            // CORRECTION: Utilise getEffectiveAmount
            'total_received' => $customRequests
                ->where('status', 'funded')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
        ];

        return [
            'total_projects' => $allRequests->count(),
            'total_requested' => $totalRequested,
            'total_approved' => $totalApproved,
            'total_received' => $totalReceived,
            'total_pending' => $totalPending,
            'jobs_expected' => $jobsExpected,
            'projects_by_status' => $projectsByStatus,
            'projects_by_type' => $projectsByType,
            'success_rate' => $successRate,
            'predefined_requests' => $predefinedStats,
            'custom_requests' => $customStats,
            'total_received_all' => $predefinedStats['total_received'] + $customStats['total_received'],
            'company_size' => $user->employees_count > 0 ? $this->getCompanySizeCategory($user->employees_count) : 'Non spécifié',
            'annual_turnover' => $user->annual_turnover ? number_format($user->annual_turnover, 0, ',', ' ') . ' XOF' : 'Non spécifié',
            'sector' => $user->sector ?? 'Non spécifié',
            'registration_number' => $user->registration_number ?? 'Non spécifié',
        ];
    }

    /**
     * Statistiques pour les particuliers
     * Logique: UNIQUEMENT funded = montant effectivement reçu (pas completed)
     */
    private function getParticulierStats($user)
    {
        $allRequests = FundingRequest::where('user_id', $user->id)->get();

        // Demandes par type (predefined vs custom)
        $predefinedRequests = $allRequests->where('is_predefined', true);
        $customRequests = $allRequests->where('is_predefined', false);

        // Statistiques des demandes prédéfinies (prêts standards)
        $predefinedStats = [
            'count' => $predefinedRequests->count(),
            'approved' => $predefinedRequests->whereIn('status', ['approved', 'funded', 'completed'])->count(),
            'total_requested' => $predefinedRequests->sum('amount_requested'),
            // CORRECTION: Utilise getEffectiveAmount pour gérer amount_approved NULL
            'total_approved' => $predefinedRequests
                ->where('status', 'approved')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            // CORRECTION: Montant effectivement reçu UNIQUEMENT funded avec montant effectif
            'total_received' => $predefinedRequests
                ->where('status', 'funded')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            'pending' => $predefinedRequests->whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'pending_payment', 'paid', 'approved'])->count(),
        ];

        // Statistiques des demandes personnalisées
        $customStats = [
            'count' => $customRequests->count(),
            'approved' => $customRequests->whereIn('status', ['approved', 'funded', 'completed'])->count(),
            'total_requested' => $customRequests->sum('amount_requested'),
            // CORRECTION: Utilise getEffectiveAmount
            'total_approved' => $customRequests
                ->where('status', 'approved')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            // CORRECTION: Montant effectivement reçu UNIQUEMENT funded avec montant effectif
            'total_received' => $customRequests
                ->where('status', 'funded')
                ->sum(function($req) {
                    return $this->getEffectiveAmount($req);
                }),
            'pending' => $customRequests->whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'pending_payment', 'paid', 'approved'])->count(),
        ];

        // Formations
        $enrolledTrainings = $user->trainings()->wherePivot('status', 'enrolled')->get();
        $completedTrainings = $user->trainings()->wherePivot('status', 'completed')->get();

        // Certificats obtenus
        $certificates = Certificate::where('user_id', $user->id)->count();

        // Progression moyenne dans les formations en cours
        $averageProgress = $enrolledTrainings->isNotEmpty()
            ? round($enrolledTrainings->avg('pivot.progress'), 1)
            : 0;

        // Documents uploadés
        $documents = Document::where('user_id', $user->id)
            ->profileDocuments()
            ->get();

        $documentsByType = $documents->groupBy('type')->map->count();
        $documentsValidated = $documents->where('status', 'validated')->count();
        $documentsPending = $documents->where('status', 'pending')->count();

        // Tickets de support
        $supportTickets = SupportTicket::where('user_id', $user->id)->count();
        $openTickets = SupportTicket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // Taux de succès global (basé sur les demandes soumises vs approuvées/funded/completed)
        $submittedCount = $allRequests->where('status', '!=', 'draft')->count();
        $successRate = $submittedCount > 0
            ? round(($allRequests->whereIn('status', ['approved', 'funded', 'completed'])->count() / $submittedCount) * 100, 1)
            : 0;

        // Total général reçu (prêts + demandes perso) - UNIQUEMENT funded
        $totalReceivedGeneral = $predefinedStats['total_received'] + $customStats['total_received'];

        return [
            'total_requests' => $allRequests->count(),
            'predefined_requests' => $predefinedStats,
            'custom_requests' => $customStats,
            'total_received_all' => $totalReceivedGeneral,
            'enrolled_trainings' => $enrolledTrainings->count(),
            'completed_trainings' => $completedTrainings->count(),
            'certificates_earned' => $certificates,
            'average_training_progress' => $averageProgress,
            'documents_by_type' => $documentsByType,
            'documents_validated' => $documentsValidated,
            'documents_pending' => $documentsPending,
            'support_tickets_total' => $supportTickets,
            'support_tickets_open' => $openTickets,
            'success_rate' => $successRate,
            'profession' => $user->profession ?? 'Non spécifié',
            'skills_developed' => $this->getUserSkills($user),
            'learning_path' => $this->getLearningPath($user),
        ];
    }

    private function getCompanySizeCategory($employeesCount)
    {
        if ($employeesCount <= 10) {
            return 'TPE (Très Petite Entreprise)';
        }
        if ($employeesCount <= 50) {
            return 'PME (Petite et Moyenne Entreprise)';
        }
        if ($employeesCount <= 250) {
            return 'ETI (Entreprise de Taille Intermédiaire)';
        }

        return 'Grande Entreprise';
    }

    private function getUserSkills($user)
    {
        $completedTrainings = $user->trainings()->wherePivot('status', 'completed')->get();

        $skills = [];
        foreach ($completedTrainings as $training) {
            if ($training->skills) {
                $trainingSkills = explode(',', $training->skills);
                $skills = array_merge($skills, $trainingSkills);
            }
        }

        return array_unique(array_map('trim', $skills));
    }

    private function getLearningPath($user)
    {
        $enrolledCategories = $user->trainings()->with('category')->get()->pluck('category.name')->unique()->toArray();

        if (empty($enrolledCategories)) {
            return ['Développement personnel', 'Gestion financière', 'Création d\'entreprise'];
        }

        return $enrolledCategories;
    }

    private function calculatePendingActions($user)
    {
        $count = 0;

        // Documents en attente de validation
        $count += Document::where('user_id', $user->id)
            ->profileDocuments()
            ->pending()
            ->count();

        // Demandes nécessitant une action (paiement ou info)
        $count += FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['validated', 'pending_payment'])
            ->count();

        // Tickets de support ouverts
        $count += SupportTicket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return $count;
    }

    private function calculateWalletChange($wallet)
    {
        // Calculer l'évolution sur les 30 derniers jours
        $thirtyDaysAgo = now()->subDays(30);

        // Utilisation du modèle Transaction avec relation wallet
        $balanceAtStart = Transaction::where('wallet_id', $wallet->id)
            ->where('created_at', '<', $thirtyDaysAgo)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first()
            ?->balance_after ?? 0;

        $currentBalance = $wallet->balance;

        $change = $currentBalance - $balanceAtStart;
        $percentage = $balanceAtStart > 0 ? round(($change / $balanceAtStart) * 100, 1) : 0;

        return [
            'amount' => $change,
            'percentage' => $percentage,
            'direction' => $change >= 0 ? 'up' : 'down',
        ];
    }
}
