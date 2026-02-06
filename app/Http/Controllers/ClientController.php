<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Setting;
use App\Models\Document;
use App\Models\Training;
use App\Models\Certificate;
use App\Models\FundingType;
use App\Models\QuizAttempt;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\FundingRequest;
use App\Models\SupportMessage;
use App\Models\RequiredDocument;
use App\Models\TrainingCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

class ClientController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Méthodes publiques
    |--------------------------------------------------------------------------
    */

    public function home()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function services()
    {
        return view('services');
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Envoyer l'email
        // Mail::to('contact@bhdm-bii.org')->send(new ContactMail($validated));

        return redirect()->back()->with('success', 'Votre message a été envoyé avec succès !');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended('client/dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants sont incorrects.',
        ]);
    }

    public function showRegisterForm()
    {
        $fundingTypes = \App\Models\FundingType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('fundingTypes'));
    }


    public function register(Request $request)
    {
        Log::info('Début inscription utilisateur', [
            'ip' => $request->ip(),
            'account_type' => $request->input('account_type'),
            'email' => $request->input('email'),
            'all_data' => $request->except(['password', 'password_confirmation'])
        ]);

        $messages = [
            'required' => 'Le champ :attribute est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.unique' => 'Ce numéro de téléphone est déjà associé à un compte.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'terms.accepted' => 'Vous devez accepter les conditions générales.',
            'company_name.required_if' => 'Le nom de l\'entreprise est obligatoire pour les comptes entreprise.',
            'company_type.required_if' => 'La forme juridique est obligatoire pour les comptes entreprise.',
            'sector.required_if' => 'Le secteur d\'activité est obligatoire pour les comptes entreprise.',
            'position.required_if' => 'La fonction est obligatoire pour les comptes entreprise.',
        ];

        $attributes = [
            'email' => 'adresse email',
            'phone' => 'numéro de téléphone',
            'name' => 'nom complet',
            'password' => 'mot de passe',
            'terms' => 'les conditions générales',
            'company_name' => 'nom de l\'entreprise',
            'company_type' => 'forme juridique',
            'sector' => 'secteur d\'activité',
            'position' => 'fonction',
        ];

        try {
            // ================= VALIDATION CONDITIONNELLE =================
            $rules = [
                'account_type' => 'required|in:particulier,entreprise',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users',
                'phone' => 'required|string|max:20|unique:users',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'required|min:8|confirmed',
                'terms' => 'required|accepted',
            ];

            // Ajouter les règles spécifiques aux entreprises uniquement si c'est une entreprise
            if ($request->input('account_type') === 'entreprise') {
                $rules = array_merge($rules, [
                    'company_name' => 'required|string|max:255',
                    'company_type' => 'required|string|max:100',
                    'position' => 'required|string|max:255',
                    'sector' => 'required|string|max:255',
                ]);
            }

            $validated = $request->validate($rules, $messages, $attributes);

            Log::info('Validation utilisateur réussie', [
                'email' => $validated['email'],
                'account_type' => $validated['account_type'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('ÉCHEC VALIDATION ÉTAPE 1', [
                'errors' => $e->errors(),
                'input' => $request->except(['password', 'password_confirmation'])
            ]);
            throw $e;
        }

        DB::beginTransaction();

        try {
            // ================= USER =================
            $nameParts = explode(' ', $validated['name'], 2);

            $userData = [
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'address' => $validated['address'],
                'password' => Hash::make($validated['password']),
                'member_type' => $validated['account_type'],
                'member_id' => $this->generateMemberId(),
                'is_active' => true,
                'is_verified' => false,
                'member_status' => 'pending',
                'member_since' => now(),
            ];

            // Ajouter les champs entreprise uniquement si c'est une entreprise
            if ($validated['account_type'] === 'entreprise') {
                $userData = array_merge($userData, [
                    'company_name' => $validated['company_name'],
                    'company_type' => $validated['company_type'],
                    'job_title' => $validated['position'],
                    'sector' => $validated['sector'],
                ]);
            }

            $user = User::create($userData);

            Log::info('Utilisateur créé', ['user_id' => $user->id, 'type' => $validated['account_type']]);

            // ================= WALLET (Pour tous) =================
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'wallet_number' => $this->generateWalletNumber(),
                'balance' => 0,
                'currency' => 'XOF',
                'pin_hash' => Hash::make('000000'),
            ]);

            // ================= ENTREPRISE UNIQUEMENT =================
            if ($validated['account_type'] === 'entreprise') {
                try {
                    $entrepriseRules = [
                        'project_name' => 'nullable|string|max:255',
                        'project_description' => 'nullable|string|min:50',
                        'funding_type_id' => 'nullable|exists:funding_types,id',
                        'funding_needed' => 'nullable|numeric|min:1000',
                        'duration' => 'nullable|integer|min:6|max:60',
                    ];

                    $entrepriseData = $request->validate($entrepriseRules, $messages);

                    Log::info('Validation projet entreprise', $entrepriseData);

                    // Création de la demande de financement uniquement si une offre est sélectionnée
                    if (!empty($entrepriseData['funding_type_id'])) {
                        $type = FundingType::find($entrepriseData['funding_type_id']);

                        if ($type) {
                            $fundingRequest = FundingRequest::create([
                                'user_id' => $user->id,
                                'request_number' => $this->generateRequestNumber(),
                                'funding_type_id' => $type->id,
                                'title' => $entrepriseData['project_name'] ?? ($type->name . ' - ' . $user->company_name),
                                'description' => $entrepriseData['project_description'] ?? 'Projet à compléter',
                                'type' => $type->category ?? 'autre',
                                'is_predefined' => true,
                                'amount_requested' => $entrepriseData['funding_needed'] ?? $type->amount ?? 0,
                                'duration' => $entrepriseData['duration'] ?? 12,
                                'expected_payment' => $type->registration_fee ?? 0,
                                'status' => 'pending',
                                'local_committee_country' => $user->country,
                                'project_location' => $user->city . ', ' . $user->country,
                                'expected_jobs' => 0,
                            ]);

                            Log::info('Demande de financement créée', ['funding_request_id' => $fundingRequest->id]);
                        }
                    } else {
                        Log::info('Aucune offre de financement sélectionnée pour cette entreprise');
                    }
                } catch (\Illuminate\Validation\ValidationException $e) {
                    Log::error('ÉCHEC VALIDATION PROJET ENTREPRISE', [
                        'errors' => $e->errors(),
                        'user_id' => $user->id
                    ]);
                    DB::rollBack();
                    throw $e;
                }
            } else {
                Log::info('Compte particulier créé sans demande de financement');
            }

            DB::commit();

            Log::info('INSCRIPTION COMPLÈTE', [
                'user_id' => $user->id,
                'type' => $validated['account_type']
            ]);

            $user->sendEmailVerificationNotification();
            Auth::login($user);

            return redirect()->route('verification.notice')->with([
                'success' => 'Inscription réussie ! Veuillez vérifier votre email.',
                'account_type' => $validated['account_type'],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ERREUR CRITIQUE INSCRIPTION', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }
    // ============= MÉTHODES AUXILIAIRES =============
    private function generateMemberId()
    {
        $currentYear = date('Y');
        $lastUser = User::whereYear('created_at', $currentYear)->latest()->first();
        $nextNumber = $lastUser ? intval(substr($lastUser->member_id, -6)) + 1 : 1;

        return 'BHDM-' . $currentYear . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
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

    private function generateRequestNumber()
    {
        $currentYear = date('Y');
        $maxAttempts = 5;
        $attempt = 0;

        do {
            $attempt++;

            // Utilisation d'un verrou pour éviter les conflits
            $lastRequest = \DB::table('funding_requests')
                ->whereYear('created_at', $currentYear)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastRequest ? intval(substr($lastRequest->request_number, -6)) + 1 : 1;
            $requestNumber = 'REQ-' . $currentYear . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Vérifier si ce numéro n'existe pas déjà (double sécurité)
            $exists = DB::table('funding_requests')
                ->where('request_number', $requestNumber)
                ->exists();

            if (!$exists) {
                return $requestNumber;
            }

            // Si existe, attendre un peu et réessayer avec un nouveau numéro
            usleep(100000); // 100ms
            $nextNumber++;
        } while ($attempt < $maxAttempts);

        // Fallback: utiliser un timestamp + random pour garantir l'unicité
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -4));
        return 'REQ-' . $currentYear . '-' . $timestamp . '-' . $random;
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function submitTest(Request $request)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $score = array_sum($validated['answers']);

        Auth::user()->update([
            'test_score' => $score,
            'test_completed_at' => now(),
        ]);

        return redirect()->route('questionnaire')->with('success', 'Test d\'évaluation soumis avec succès !');
    }

    public function submitQuestionnaire(Request $request)
    {
        $validated = $request->validate([
            'business_type' => 'required|string|max:255',
            'business_age' => 'required|integer|min:0',
            'annual_revenue' => 'required|numeric|min:0',
            'employees_count' => 'required|integer|min:0',
            'funding_purpose' => 'required|string',
            'preferred_program' => 'required|in:grants,funding,training,assistance',
        ]);

        Auth::user()->update([
            'business_type' => $validated['business_type'],
            'business_age' => $validated['business_age'],
            'annual_turnover' => $validated['annual_revenue'],
            'employees_count' => $validated['employees_count'],
            'funding_purpose' => $validated['funding_purpose'],
            'preferred_program' => $validated['preferred_program'],
            'questionnaire_completed_at' => now(),
        ]);

        return redirect()->route('programs.grants')->with('success', 'Questionnaire soumis avec succès !');
    }

    public function apply($program)
    {
        $user = Auth::user();

        if (! $user->test_completed_at || ! $user->questionnaire_completed_at) {
            return redirect()->route('evaluation.test')
                ->with('warning', 'Veuillez compléter le test d\'évaluation et le questionnaire avant de postuler.');
        }

        return view('programs.apply', compact('program'));
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes de l'espace client
    |--------------------------------------------------------------------------
    */

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
            'active_requests' => FundingRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->count(),

            'approved_requests' => FundingRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count(),

            'active_trainings' => $user->trainings()
                ->wherePivot('status', 'enrolled')
                ->count(),

            'completed_trainings' => $user->trainings()
                ->wherePivot('status', 'completed')
                ->count(),

            'pending_actions' => $this->calculatePendingActions($user),

            'new_requests' => FundingRequest::where('user_id', $user->id)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count(),

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

    private function getEntrepriseStats($user)
    {
        $allRequests = FundingRequest::where('user_id', $user->id)->get();

        // Calculer le montant total demandé et approuvé
        $totalRequested = $allRequests->sum('amount_requested');
        $totalApproved = $allRequests->where('status', 'approved')->sum('amount_approved');

        // Calculer les emplois créés (à partir des projets approuvés)
        $jobsCreated = $allRequests->where('status', 'approved')->sum('expected_jobs');

        // Calculer le revenu attendu total
        $expectedRevenue = $allRequests->where('status', 'approved')->sum('expected_revenue');

        // Projets par statut
        $projectsByStatus = [
            'pending' => $allRequests->where('status', 'pending')->count(),
            'processing' => $allRequests->where('status', 'processing')->count(),
            'approved' => $allRequests->where('status', 'approved')->count(),
            'rejected' => $allRequests->where('status', 'rejected')->count(),
            'completed' => $allRequests->where('status', 'completed')->count(),
        ];

        // Projets par type
        $projectsByType = $allRequests->groupBy('project_type')->map->count();

        return [
            'total_projects' => $allRequests->count(),
            'total_requested' => $totalRequested,
            'total_approved' => $totalApproved,
            'jobs_created' => $jobsCreated,
            'expected_revenue' => $expectedRevenue,
            'projects_by_status' => $projectsByStatus,
            'projects_by_type' => $projectsByType,
            'company_size' => $user->employees_count > 0 ? $this->getCompanySizeCategory($user->employees_count) : 'Non spécifié',
            'annual_turnover' => $user->annual_turnover ? number_format($user->annual_turnover, 0, ',', ' ') . ' XOF' : 'Non spécifié',
            'sector' => $user->sector ?? 'Non spécifié',
            'registration_number' => $user->registration_number ?? 'Non spécifié',
        ];
    }

    private function getParticulierStats($user)
    {
        $allRequests = FundingRequest::where('user_id', $user->id)->get();

        // Demande personnelle par type
        $personalLoans = $allRequests->where('category', 'loan');
        $personalGrants = $allRequests->where('category', 'grant');
        $trainingRequests = $allRequests->where('category', 'training');

        // Formations complétées avec certificats
        $certificates = Certificate::where('user_id', $user->id)->count();

        // Progression moyenne dans les formations
        $enrolledTrainings = $user->trainings()->wherePivot('status', 'enrolled')->get();
        $averageProgress = $enrolledTrainings->isNotEmpty()
            ? round($enrolledTrainings->avg('pivot.progress'), 1)
            : 0;

        // Documents uploadés par type
        $documents = Document::where('user_id', $user->id)->get();
        $documentsByType = $documents->groupBy('type')->map->count();

        // Assistance technique utilisée
        $supportTickets = SupportTicket::where('user_id', $user->id)->count();

        return [
            'total_requests' => $allRequests->count(),
            'personal_loans' => [
                'count' => $personalLoans->count(),
                'total_requested' => $personalLoans->sum('amount_requested'),
                'total_approved' => $personalLoans->where('status', 'approved')->sum('amount_approved'),
            ],
            'personal_grants' => [
                'count' => $personalGrants->count(),
                'total_requested' => $personalGrants->sum('amount_requested'),
                'total_approved' => $personalGrants->where('status', 'approved')->sum('amount_approved'),
            ],
            'training_requests' => [
                'count' => $trainingRequests->count(),
                'approved' => $trainingRequests->where('status', 'approved')->count(),
            ],
            'certificates_earned' => $certificates,
            'average_training_progress' => $averageProgress,
            'documents_by_type' => $documentsByType,
            'support_tickets' => $supportTickets,
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
        // Cette méthode pourrait récupérer les compétences développées à partir des formations complétées
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
        // Recommandations de parcours d'apprentissage basées sur les intérêts
        $enrolledCategories = $user->trainings()->with('category')->get()->pluck('category.name')->unique()->toArray();

        if (empty($enrolledCategories)) {
            return ['Développement personnel', 'Gestion financière', 'Création d\'entreprise'];
        }

        return $enrolledCategories;
    }

    private function calculatePendingActions($user)
    {
        $count = 0;

        $count += Document::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $count += FundingRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'requires_info'])
            ->count();

        $count += SupportTicket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'waiting'])
            ->count();

        return $count;
    }

    private function calculateWalletChange($wallet)
    {
        if (! $wallet) {
            return 0;
        }

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $deposits = $wallet->transactions()
            ->where('type', 'deposit')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $withdrawals = $wallet->transactions()
            ->where('type', 'withdrawal')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netChange = $deposits - $withdrawals;
        $previousBalance = max(1, $wallet->balance - $netChange);

        return round(($netChange / $previousBalance) * 100, 1);
    }

    public function wallet()
    {
        $user = Auth::user();
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

        $transactions = $wallet->transactions()->latest()->paginate(10);

        return view('client.wallet.index', compact('wallet', 'transactions'));
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:mobile_money,bank_transfer',
            'phone_number' => 'required_if:payment_method,mobile_money|string|max:20',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return redirect()->back()->with('error', 'Portefeuille non trouvé.');
        }

        $transaction = $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'description' => 'Dépôt via ' . $validated['payment_method'],
            'metadata' => [
                'phone_number' => $validated['phone_number'] ?? null,
                'transaction_id' => $validated['transaction_id'] ?? null,
            ],
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'transaction',
            'title' => 'Dépôt en attente',
            'message' => 'Votre dépôt de ' . number_format($validated['amount']) . ' XOF est en attente de validation.',
            'data' => ['transaction_id' => $transaction->id],
        ]);

        return redirect()->route('client.wallet')->with('success', 'Dépôt initié avec succès !');
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'bank_name' => 'required|string',
            'swift_code' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return redirect()->back()->with('error', 'Portefeuille non trouvé.');
        }

        if ($wallet->balance < $validated['amount']) {
            return redirect()->back()->with('error', 'Solde insuffisant !');
        }

        $wallet->decrement('balance', $validated['amount']);

        $transaction = $wallet->transactions()->create([
            'type' => 'withdrawal',
            'amount' => $validated['amount'],
            'status' => 'pending',
            'description' => 'Retrait vers ' . $validated['bank_name'],
            'metadata' => [
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'bank_name' => $validated['bank_name'],
                'swift_code' => $validated['swift_code'] ?? null,
            ],
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'transaction',
            'title' => 'Retrait initié',
            'message' => 'Votre retrait de ' . number_format($validated['amount']) . ' XOF a été initié.',
            'data' => ['transaction_id' => $transaction->id],
        ]);

        return redirect()->route('client.wallet')->with('success', 'Demande de retrait soumise avec succès !');
    }

    public function transactions()
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            $transactions = collect();
        } else {
            $transactions = $wallet->transactions()
                ->latest()
                ->paginate(20);
        }

        return view('client.transactions', compact('transactions'));
    }

    public function requests()
    {
        $user = Auth::user();
        $requests = FundingRequest::where('user_id', $user->id)->latest()->paginate(10);

        return view('client.requests.index', compact('requests'));
    }

    public function createRequest()
    {
        $user = Auth::user();

        // Pour les entreprises, pré-remplir avec les informations du projet initial
        if ($user->isEntreprise()) {
            $initialData = [
                'title' => $user->project_name ?? '',
                'description' => $user->project_description ?? '',
                'amount' => $user->funding_needed ?? 0,
                'project_duration' => $user->project_duration ?? 12,
                'expected_jobs' => $user->expected_jobs ?? 0,
                'expected_revenue' => $user->expected_revenue ?? 0,
            ];

            return view('client.requests.create', compact('initialData'));
        }

        return view('client.requests.create');
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'category' => 'required|in:grant,loan,training,assistance',
            'project_duration' => 'required|integer|min:1',
            'expected_result' => 'required|string',
            'business_plan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Pour les entreprises, ajouter des champs spécifiques
        if ($user->isEntreprise()) {
            $entrepriseData = $request->validate([
                'expected_jobs' => 'required|integer|min:0',
                'expected_revenue' => 'nullable|numeric|min:0',
                'project_type' => 'required|string|max:255',
            ]);

            $validated = array_merge($validated, $entrepriseData);
        }

        $fundingRequest = FundingRequest::create(array_merge([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'amount_requested' => $validated['amount'],
            'category' => $validated['category'],
            'project_duration' => $validated['project_duration'],
            'expected_result' => $validated['expected_result'],
            'status' => 'pending',
            'reference' => 'BHDM-' . date('Ymd') . '-' . str_pad(FundingRequest::count() + 1, 4, '0', STR_PAD_LEFT),
        ], $user->isEntreprise() ? [
            'expected_jobs' => $validated['expected_jobs'],
            'expected_revenue' => $validated['expected_revenue'] ?? 0,
            'project_type' => $validated['project_type'],
        ] : []));

        if ($request->hasFile('business_plan')) {
            $filename = time() . '_business_plan.' . $request->business_plan->extension();
            $path = $request->business_plan->storeAs('public/business_plans', $filename);

            $fundingRequest->update([
                'business_plan' => $filename,
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => 'request',
            'title' => 'Nouvelle demande créée',
            'message' => 'Votre demande "' . $validated['title'] . '" a été créée avec succès.',
            'data' => ['request_id' => $fundingRequest->id],
        ]);

        return redirect()->route('client.requests')->with('success', 'Demande créée avec succès !');
    }

    public function showRequest($id)
    {
        $user = Auth::user();
        $request = FundingRequest::where('user_id', $user->id)->findOrFail($id);

        return view('client.requests.show', compact('request'));
    }

    public function editRequest($id)
    {
        $user = Auth::user();
        $request = FundingRequest::where('user_id', $user->id)->findOrFail($id);

        if ($request->status != 'pending') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier une demande déjà traitée.');
        }

        return view('client.requests.edit', compact('request'));
    }

    public function updateRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'category' => 'required|in:grant,loan,training,assistance',
            'project_duration' => 'required|integer|min:1',
            'expected_result' => 'required|string',
            'business_plan' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $user = Auth::user();
        $fundingRequest = FundingRequest::where('user_id', $user->id)->findOrFail($id);

        if ($fundingRequest->status != 'pending') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier une demande déjà traitée.');
        }

        if ($request->hasFile('business_plan')) {
            if ($fundingRequest->business_plan) {
                Storage::delete('public/business_plans/' . $fundingRequest->business_plan);
            }

            $filename = time() . '_business_plan.' . $request->business_plan->extension();
            $path = $request->business_plan->storeAs('public/business_plans', $filename);
            $validated['business_plan'] = $filename;
        }

        $fundingRequest->update($validated);

        return redirect()->route('client.requests')->with('success', 'Demande mise à jour avec succès !');
    }

    public function deleteRequest($id)
    {
        $user = Auth::user();
        $request = FundingRequest::where('user_id', $user->id)->findOrFail($id);

        if ($request->status != 'pending') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer une demande déjà traitée.');
        }

        if ($request->business_plan) {
            Storage::delete('public/business_plans/' . $request->business_plan);
        }

        $request->delete();

        return redirect()->route('client.requests')->with('success', 'Demande supprimée avec succès !');
    }
    /**
     * Affiche le profil
     */
    public function profile()
    {
        $user = Auth::user();
        return view('client.profile', compact('user'));
    }

    /**
     * Met à jour le profil complet (infos + photo + mot de passe)
     * Version unifiée recommandée
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // DEBUG : Log des données reçues
        Log::info('Update Profile Request', [
            'has_file' => $request->hasFile('photo'),
            'file_valid' => $request->file('photo') ? $request->file('photo')->isValid() : false,
            'all_inputs' => $request->except(['password', 'current_password']),
        ]);

        // Validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Préparation des données
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ];

        // GESTION DE LA PHOTO - CORRIGÉE
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            try {
                // 1. Supprimer l'ancienne photo si existe
                if ($user->profile_photo && Storage::disk('public')->exists('profiles/' . $user->profile_photo)) {
                    Storage::disk('public')->delete('profiles/' . $user->profile_photo);
                    Log::info('Ancienne photo supprimée', ['filename' => $user->profile_photo]);
                }

                // 2. Générer un nom unique
                $extension = $request->file('photo')->getClientOriginalExtension();
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $extension;

                // 3. Stocker la nouvelle photo
                $path = $request->file('photo')->storeAs('profiles', $filename, 'public');

                if ($path) {
                    $userData['profile_photo'] = $filename;
                    Log::info('Nouvelle photo sauvegardée', ['path' => $path, 'filename' => $filename]);
                } else {
                    Log::error('Échec du stockage de la photo');
                    return redirect()->back()->with('error', 'Erreur lors de la sauvegarde de la photo');
                }
            } catch (\Exception $e) {
                Log::error('Exception lors du traitement de la photo', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Erreur technique: ' . $e->getMessage());
            }
        }

        // Mise à jour mot de passe si fourni
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                    ->withInput();
            }

            $userData['password'] = Hash::make($request->new_password);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'security',
                'title' => 'Mot de passe modifié',
                'message' => 'Votre mot de passe a été changé avec succès.',
            ]);
        }

        // Sauvegarde en base
        $user->update($userData);

        Log::info('Profil mis à jour', ['user_id' => $user->id, 'data' => $userData]);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Méthode alternative : Change uniquement le mot de passe (si formulaire séparé)
     * À utiliser si vous préférez séparer les actions
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        // Vérification du mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Mise à jour
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'security',
            'title' => 'Mot de passe changé',
            'message' => 'Votre mot de passe a été changé avec succès.',
        ]);

        return redirect()->back()->with('success', 'Mot de passe changé avec succès !');
    }

    /**
     * Supprime la photo de profil
     */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::exists('public/profiles/' . $user->profile_photo)) {
            Storage::delete('public/profiles/' . $user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return redirect()->back()->with('success', 'Photo de profil supprimée.');
    }

    // public function documents()
    // {
    //     $user = Auth::user();
    //     $documents = Document::where('user_id', $user->id)->latest()->get();

    //     return view('client.documents', compact('documents'));
    // }

    // public function uploadDocument(Request $request)
    // {
    //     $user = Auth::user();

    //     // Types de documents spécifiques selon le type d'utilisateur
    //     $documentTypes = $user->isEntreprise()
    //         ? ['id_card', 'proof_address', 'business_registration', 'tax_certificate', 'financial_statements', 'project_documents', 'other']
    //         : ['id_card', 'proof_address', 'tax_certificate', 'income_proof', 'other'];

    //     $validated = $request->validate([
    //         'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
    //         'type' => 'required|in:' . implode(',', $documentTypes),
    //         'description' => 'nullable|string|max:500',
    //     ]);

    //     if ($request->hasFile('document')) {
    //         $filename = time() . '_' . $user->id . '_' . $request->document->getClientOriginalName();
    //         $path = $request->document->storeAs('public/documents', $filename);

    //         $document = Document::create([
    //             'user_id' => $user->id,
    //             'filename' => $filename,
    //             'original_name' => $request->document->getClientOriginalName(),
    //             'type' => $validated['type'],
    //             'description' => $validated['description'],
    //             'size' => $request->document->getSize(),
    //             'mime_type' => $request->document->getMimeType(),
    //             'status' => 'pending',
    //         ]);

    //         Notification::create([
    //             'user_id' => $user->id,
    //             'type' => 'document',
    //             'title' => 'Document uploadé',
    //             'message' => 'Votre document "' . $document->original_name . '" a été uploadé avec succès.',
    //             'data' => ['document_id' => $document->id],
    //         ]);

    //         return redirect()->back()->with('success', 'Document uploadé avec succès !');
    //     }

    //     return redirect()->back()->with('error', 'Erreur lors de l\'upload du document.');
    // }

    // public function deleteDocument($id)
    // {
    //     $user = Auth::user();
    //     $document = Document::where('user_id', $user->id)->findOrFail($id);

    //     Storage::delete('public/documents/' . $document->filename);
    //     $document->delete();

    //     return redirect()->back()->with('success', 'Document supprimé avec succès !');
    // }

    // public function downloadDocument($id)
    // {
    //     $user = Auth::user();
    //     $document = Document::where('user_id', $user->id)->findOrFail($id);

    //     return Storage::download('public/documents/' . $document->filename, $document->original_name);
    // }

    // Documents de profil avec vérification des documents requis et pourcentage de complétion

    /**
     * Afficher la liste des documents
     */
    public function documents()
    {
        $user = Auth::user();

        // Documents de profil uniquement
        $documents = Document::where('user_id', $user->id)
            ->profileDocuments()
            ->recentFirst()
            ->get();

        // Types de documents requis selon le type d'utilisateur
        $requiredDocuments = RequiredDocument::getByMemberType($user->member_type, true);

        // Vérifier les documents manquants
        $missingDocuments = [];
        $completedRequired = 0;

        foreach ($requiredDocuments as $requiredDoc) {
            $exists = $documents->where('type', $requiredDoc->document_type)
                ->where('status', 'validated')
                ->where(function ($query) use ($requiredDoc) {
                    if ($requiredDoc->has_expiry_date) {
                        $query->where('is_expired', false)
                            ->where(function ($q) {
                                $q->whereNull('expiry_date')
                                    ->orWhere('expiry_date', '>', now());
                            });
                    }
                })
                ->first();

            if (! $exists && $requiredDoc->is_required) {
                $missingDocuments[] = $requiredDoc;
            } elseif ($exists && $requiredDoc->is_required) {
                $completedRequired++;
            }
        }

        // Calculer le pourcentage de complétion
        $totalRequired = $requiredDocuments->where('is_required', true)->count();
        $completionPercentage = $totalRequired > 0 ? round(($completedRequired / $totalRequired) * 100) : 100;

        return view('client.documents.index', compact(
            'documents',
            'requiredDocuments',
            'missingDocuments',
            'completionPercentage',
            'completedRequired',
            'totalRequired'
        ));
    }

    /**
     * Afficher le formulaire d'upload
     */
    public function uploadForm(Request $request)
    {
        $type = $request->query('type');
        $user = Auth::user();

        // Récupérer les documents requis pour le type de membre
        $requiredDocuments = RequiredDocument::where('is_active', true)
            ->where('member_type', $user->member_type)
            ->get();

        // Vérifier si le type demandé existe dans les documents requis
        $requiredDoc = $requiredDocuments->firstWhere('document_type', $type);

        if (! $requiredDoc) {
            return redirect()->route('client.documents.index')
                ->with('error', 'Type de document non autorisé ou introuvable.');
        }

        // Vérifier si l'utilisateur a déjà uploadé ce type de document
        $existingDocument = Document::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_profile_document', true)
            ->whereIn('status', ['pending', 'validated'])
            ->first();

        return view('client.documents.upload', [
            'type' => $type,
            'requiredDocuments' => $requiredDocuments,
            'existingDocument' => $existingDocument,
            'requiredDoc' => $requiredDoc,
        ]);
    }

    /**
     * Traiter l'upload d'un document
     */
    public function uploadDocument(Request $request)
    {
        $user = Auth::user();

        // Validation de base
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'document' => 'required|file',
            'description' => 'nullable|string|max:500',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $documentType = $validated['type'];

        // Récupérer les informations du document requis
        $documentInfo = RequiredDocument::where('member_type', $user->member_type)
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->first();

        if (! $documentInfo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Type de document non autorisé pour votre profil.');
        }

        // Vérifier les doublons (validés)
        $existingValidatedDocument = Document::where('user_id', $user->id)
            ->where('type', $documentType)
            ->where('is_profile_document', true)
            ->where('status', 'validated')
            ->where('is_expired', false)
            ->first();

        if ($existingValidatedDocument) {
            return redirect()->route('client.documents.index')
                ->with('error', 'Vous avez déjà un document de ce type validé et non expiré.');
        }

        // Vérifier s'il y a un document en attente
        $existingPendingDocument = Document::where('user_id', $user->id)
            ->where('type', $documentType)
            ->where('is_profile_document', true)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingDocument) {
            return redirect()->route('client.documents.index')
                ->with('warning', 'Vous avez déjà un document de ce type en attente de validation.');
        }

        // Validation spécifique au document
        $validationRules = [
            'document' => ['required', 'file'],
        ];

        // Validation des formats
        if ($documentInfo->allowed_formats && count($documentInfo->allowed_formats) > 0) {
            $validationRules['document'][] = 'mimes:' . implode(',', $documentInfo->allowed_formats);
        }

        // Validation de la taille
        if ($documentInfo->max_size_mb) {
            $maxSizeKB = $documentInfo->max_size_mb * 1024; // Convertir en Ko
            $validationRules['document'][] = "max:$maxSizeKB";
        }

        // Valider avec les règles spécifiques
        $request->validate($validationRules);

        // Validation de la date d'expiration si requise
        if ($documentInfo->has_expiry_date && ! $request->has('expiry_date')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La date d\'expiration est requise pour ce document.');
        }

        try {
            // Upload du fichier
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

            // Définir le chemin de stockage
            $path = 'documents/users/' . $user->id . '/' . $documentType;
            $filePath = $file->storeAs($path, $filename, 'public');

            // Calculer la date d'expiration si nécessaire
            $expiryDate = null;
            if ($request->has('expiry_date')) {
                $expiryDate = $request->input('expiry_date');
            } elseif ($documentInfo->validity_days) {
                $expiryDate = now()->addDays($documentInfo->validity_days);
            }

            // Créer l'enregistrement du document
            $document = new Document([
                'user_id' => $user->id,
                'type' => $documentType,
                'name' => $validated['name'],
                'original_filename' => $originalName,
                'path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $request->input('description'),
                'expiry_date' => $expiryDate,
                'status' => 'pending',
                'uploaded_at' => now(),
                'is_profile_document' => true,
                'is_required' => $documentInfo->is_required,
                'category' => $documentInfo->category,
                'document_type' => $documentInfo->document_type,
            ]);

            $document->save();

            // Vérifier immédiatement l'expiration
            $document->checkExpiry();

            // Notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'profile_document_uploaded',
                'title' => 'Document uploadé',
                'message' => 'Votre document "' . $documentInfo->name . '" a été uploadé avec succès.',
                'data' => [
                    'document_id' => $document->id,
                    'document_type' => $documentType,
                    'document_name' => $documentInfo->name,
                ],
                'read' => false,
            ]);

            // Notification pour l'administration (si nécessaire)
            if (config('app.notify_admin_on_document_upload')) {
                Notification::create([
                    'user_id' => 1, // ID de l'admin
                    'type' => 'new_document_uploaded',
                    'title' => 'Nouveau document uploadé',
                    'message' => $user->name . ' a uploadé un nouveau document: ' . $documentInfo->name,
                    'data' => [
                        'user_id' => $user->id,
                        'document_id' => $document->id,
                        'document_type' => $documentType,
                    ],
                    'read' => false,
                ]);
            }

            return redirect()->route('client.documents.index')
                ->with('success', 'Document uploadé avec succès ! Il sera validé par notre équipe.');
        } catch (\Exception $e) {
            \Log::error('Erreur upload document: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'document_type' => $documentType,
                'error' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'upload du document: ' . $e->getMessage());
        }
    }

    /**
     * Afficher un document
     */
    public function viewDocumentPage($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Récupérer le nom du type de document
        $requiredDocuments = RequiredDocument::where('is_active', true)->get();
        $documentTypeName = $requiredDocuments->firstWhere('document_type', $document->type)->name ?? $document->type;

        // Obtenir les informations du fichier
        $fileExists = Storage::disk('public')->exists($document->path);
        $fileSize = $fileExists ? Storage::disk('public')->size($document->path) : 0;
        $formattedSize = $this->formatFileSize($fileSize);

        return view('client.documents.view', compact(
            'document',
            'documentTypeName',
            'formattedSize',
            'fileExists'
        ));
    }

    /**
     * Télécharger un document
     */
    public function downloadDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (! Storage::disk('public')->exists($document->path)) {
            abort(404, 'Document non trouvé');
        }

        return Storage::disk('public')->download(
            $document->path,
            $document->original_filename
        );
    }

    /**
     * Visualiser un document directement
     */
    public function viewDocument($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (! Storage::disk('public')->exists($document->path)) {
            abort(404, 'Document non trouvé');
        }

        // Récupérer le contenu du fichier
        $file = Storage::disk('public')->get($document->path);
        $mimeType = Storage::disk('public')->mimeType($document->path);

        // Retourner la réponse
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->original_filename . '"');
    }

    /**
     * Générer une URL temporaire pour visualiser un document
     */
    public function viewDocumentUrl($id)
    {
        $user = Auth::user();

        $document = Document::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si le fichier existe
        if (! Storage::disk('public')->exists($document->path)) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé',
            ], 404);
        }

        try {
            // Générer une URL (publique ou temporaire)
            if (config('filesystems.default') === 'public') {
                $url = Storage::disk('public')->url($document->path);
            } else {
                // Pour S3 ou autres, générer une URL temporaire
                $url = Storage::disk('public')->temporaryUrl(
                    $document->path,
                    now()->addMinutes(30)
                );
            }

            // Déterminer le type MIME
            $mimeType = Storage::disk('public')->mimeType($document->path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'mime_type' => $mimeType,
                'filename' => $document->original_filename,
                'is_image' => $document->isImage(),
                'is_pdf' => $document->isPdf(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating document URL: ' . $e->getMessage(), [
                'document_id' => $document->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer l\'URL du document',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes pour les formations
    |--------------------------------------------------------------------------
    */

    public function trainings()
    {
        $user = Auth::user();

        // Pour les entreprises, prioriser les formations liées à leur secteur
        if ($user->isEntreprise()) {
            $sector = $user->sector;

            $trainings = Training::with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
                ->where('is_active', true)
                ->where(function ($query) use ($sector) {
                    if ($sector) {
                        $query->where('related_sectors', 'like', '%' . $sector . '%')
                            ->orWhere('category', 'LIKE', '%entreprise%')
                            ->orWhere('title', 'LIKE', '%' . $sector . '%');
                    }
                })
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $trainings = Training::with(['users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        $categories = TrainingCategory::where('is_active', true)->get();

        $enrolledTrainingIds = $user->trainings()
            ->wherePivot('status', 'enrolled')
            ->pluck('trainings.id')
            ->toArray();

        $completedTrainingIds = $user->trainings()
            ->wherePivot('status', 'completed')
            ->pluck('trainings.id')
            ->toArray();

        return view('client.trainings.index', compact(
            'trainings',
            'categories',
            'enrolledTrainingIds',
            'completedTrainingIds'
        ));
    }

    public function trainingDetail($id)
    {
        $user = Auth::user();
        $training = Training::with(['modules', 'resources', 'quizzes'])->findOrFail($id);

        $enrollment = $training->users()
            ->where('user_id', $user->id)
            ->first();

        $isEnrolled = $enrollment ? true : false;
        $isCompleted = $enrollment && $enrollment->pivot->status === 'completed';
        $progress = $enrollment ? $enrollment->pivot->progress : 0;
        $certificate = null;

        if ($isCompleted) {
            $certificate = Certificate::where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->first();
        }

        return view('client.trainings.detail', compact(
            'training',
            'enrollment',
            'progress',
            'isEnrolled',
            'isCompleted',
            'certificate'
        ));
    }

    public function enrollTraining($id)
    {
        $user = Auth::user();
        $training = Training::findOrFail($id);

        if (! $training->isAvailable()) {
            return redirect()->back()->with('error', 'Cette formation n\'est plus disponible pour inscription.');
        }

        if ($user->trainings()->where('training_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cette formation.');
        }

        $user->trainings()->attach($id, [
            'enrolled_at' => now(),
            'status' => 'enrolled',
            'progress' => 0,
        ]);

        $training->increment('current_participants');

        Notification::create([
            'user_id' => $user->id,
            'type' => 'training',
            'title' => 'Inscription à une formation',
            'message' => 'Vous êtes inscrit à la formation "' . $training->title . '".',
            'data' => ['training_id' => $training->id],
        ]);

        return redirect()->route('client.trainings.my')->with('success', 'Inscription réussie !');
    }

    public function unenrollTraining($id)
    {
        $user = Auth::user();
        $training = Training::findOrFail($id);

        if (! $user->trainings()->where('training_id', $id)->exists()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à cette formation.');
        }

        $user->trainings()->detach($id);
        $training->decrement('current_participants');

        return redirect()->route('client.trainings.my')->with('success', 'Désinscription réussie !');
    }

    public function updateTrainingProgress(Request $request, $id)
    {
        $user = Auth::user();
        $training = Training::findOrFail($id);

        $enrollment = $user->trainings()
            ->where('training_id', $id)
            ->first();

        if (! $enrollment) {
            return response()->json(['error' => 'Vous n\'êtes pas inscrit à cette formation.'], 400);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'module_completed' => 'nullable|integer',
        ]);

        $user->trainings()
            ->updateExistingPivot($id, [
                'progress' => $validated['progress'],
                'last_accessed_at' => now(),
            ]);

        if ($validated['progress'] == 100) {
            $this->completeTraining($user, $training);
        }

        return response()->json(['success' => 'Progression mise à jour avec succès.']);
    }

    private function completeTraining($user, $training)
    {
        $user->trainings()
            ->updateExistingPivot($training->id, [
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100,
            ]);

        if ($training->certification_included) {
            $this->generateCertificate($user, $training);
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => 'training',
            'title' => 'Formation complétée',
            'message' => 'Vous avez complété la formation "' . $training->title . '".',
            'data' => ['training_id' => $training->id],
        ]);
    }

    private function generateCertificate($user, $training)
    {
        $certificateId = 'CERT-' . strtoupper(uniqid()) . '-' . $user->id . '-' . $training->id;

        Certificate::create([
            'certificate_id' => $certificateId,
            'user_id' => $user->id,
            'training_id' => $training->id,
            'full_name' => $user->full_name,
            'certificate_number' => 'BHDM-CERT-' . date('Ymd') . '-' . str_pad(Certificate::count() + 1, 6, '0', STR_PAD_LEFT),
            'issue_date' => now(),
            'expiry_date' => $training->certification_validity ? now()->addYears($training->certification_validity) : null,
            'final_score' => 100,
            'template' => 'default',
            'is_verified' => true,
            'verification_code' => strtoupper(uniqid()),
        ]);
    }

    public function myTrainings()
    {
        $user = Auth::user();

        $ongoingTrainings = $user->trainings()
            ->wherePivot('status', 'enrolled')
            ->orderBy('training_user.enrolled_at', 'desc')
            ->paginate(10, ['*'], 'ongoing_page');

        $completedTrainings = $user->trainings()
            ->wherePivot('status', 'completed')
            ->orderBy('training_user.completed_at', 'desc')
            ->paginate(10, ['*'], 'completed_page');

        return view('client.trainings.my', compact(
            'ongoingTrainings',
            'completedTrainings'
        ));
    }

    public function trainingModules($id)
    {
        $user = Auth::user();
        $training = Training::with('modules')->findOrFail($id);

        $enrollment = $user->trainings()
            ->where('training_id', $id)
            ->first();

        if (! $enrollment) {
            return redirect()->route('client.trainings.detail', $id)
                ->with('error', 'Vous devez être inscrit pour accéder aux modules.');
        }

        return view('client.trainings.modules', compact('training', 'enrollment'));
    }

    public function trainingResources($id)
    {
        $user = Auth::user();
        $training = Training::with('resources')->findOrFail($id);

        $enrollment = $user->trainings()
            ->where('training_id', $id)
            ->first();

        if (! $enrollment) {
            return redirect()->route('client.trainings.detail', $id)
                ->with('error', 'Vous devez être inscrit pour accéder aux ressources.');
        }

        return view('client.trainings.resources', compact('training', 'enrollment'));
    }

    public function takeQuiz($trainingId, $quizId = null)
    {
        $user = Auth::user();
        $training = Training::findOrFail($trainingId);

        $enrollment = $user->trainings()
            ->where('training_id', $trainingId)
            ->first();

        if (! $enrollment) {
            return redirect()->route('client.trainings.detail', $trainingId)
                ->with('error', 'Vous devez être inscrit pour passer le quiz.');
        }

        if (! $quizId) {
            $quiz = $training->quizzes()->first();
            if (! $quiz) {
                return redirect()->route('client.trainings.detail', $trainingId)
                    ->with('error', 'Aucun quiz disponible pour cette formation.');
            }
            $quizId = $quiz->id;
        }

        $quiz = Quiz::findOrFail($quizId);

        $attemptsCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->count();

        if ($quiz->max_attempts && $attemptsCount >= $quiz->max_attempts) {
            return redirect()->route('client.trainings.detail', $trainingId)
                ->with('error', 'Vous avez atteint le nombre maximum de tentatives pour ce quiz.');
        }

        return view('client.trainings.quiz', compact('training', 'quiz', 'attemptsCount'));
    }

    public function submitQuiz(Request $request, $trainingId, $quizId)
    {
        $user = Auth::user();
        $quiz = Quiz::findOrFail($quizId);

        $enrollment = $user->trainings()
            ->where('training_id', $trainingId)
            ->first();

        if (! $enrollment) {
            return response()->json(['error' => 'Vous devez être inscrit pour soumettre un quiz.'], 400);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'time_spent' => 'nullable|integer',
        ]);

        $score = $this->calculateQuizScore($quiz, $validated['answers']);
        $totalQuestions = count($quiz->questions);
        $percentage = round(($score / $totalQuestions) * 100, 2);
        $passed = $percentage >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
            'attempt_number' => QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quizId)->count() + 1,
            'answers' => $validated['answers'],
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $percentage,
            'passed' => $passed,
            'time_spent_seconds' => $validated['time_spent'] ?? 0,
            'started_at' => now()->subSeconds($validated['time_spent'] ?? 0),
            'completed_at' => now(),
        ]);

        $this->updateQuizStats($quiz);

        if ($passed) {
            $this->updateTrainingAfterQuiz($user, $trainingId);
        }

        return response()->json([
            'success' => true,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $percentage,
            'passed' => $passed,
            'attempt_id' => $attempt->id,
        ]);
    }

    private function calculateQuizScore($quiz, $answers)
    {
        $score = 0;
        $questions = $quiz->questions;

        foreach ($questions as $index => $question) {
            if (isset($answers[$index]) && $answers[$index] == $question['correct_answer']) {
                $score++;
            }
        }

        return $score;
    }

    private function updateQuizStats($quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id);
        $averageScore = $attempts->avg('percentage');
        $attemptsCount = $attempts->count();

        $quiz->update([
            'attempts_count' => $attemptsCount,
            'average_score' => $averageScore,
        ]);
    }

    private function updateTrainingAfterQuiz($user, $trainingId)
    {
        $training = Training::find($trainingId);
        $userTraining = $user->trainings()->where('training_id', $trainingId)->first();

        if ($userTraining) {
            $currentProgress = $userTraining->pivot->progress;
            $quizCompletionPoints = 25;
            $newProgress = min(100, $currentProgress + $quizCompletionPoints);

            $user->trainings()
                ->updateExistingPivot($trainingId, [
                    'progress' => $newProgress,
                ]);

            $totalQuizzes = $training->quizzes()->count();
            $passedQuizzes = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $training->quizzes()->pluck('id'))
                ->where('passed', true)
                ->count();

            if ($totalQuizzes > 0 && $passedQuizzes == $totalQuizzes) {
                $this->completeTraining($user, $training);
            }
        }
    }

    public function certificates()
    {
        $user = Auth::user();
        $certificates = Certificate::where('user_id', $user->id)
            ->with('training')
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('client.trainings.certificates', compact('certificates'));
    }

    public function downloadCertificate($id)
    {
        $user = Auth::user();
        $certificate = Certificate::where('user_id', $user->id)
            ->findOrFail($id);

        return view('client.trainings.certificate-pdf', compact('certificate'));
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes pour le support
    |--------------------------------------------------------------------------
    */

    public function createSupport()
    {
        return view('client.support.create');
    }

    public function submitSupport(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:general,technical,billing,account,training,funding,document,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'ticket_number' => 'BHDM-' . date('Ymd') . '-' . str_pad(SupportTicket::count() + 1, 4, '0', STR_PAD_LEFT),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'metadata' => $attachments ? ['attachments' => $attachments] : null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'support',
            'title' => 'Nouveau ticket de support',
            'message' => 'Votre ticket #' . $ticket->ticket_number . ' a été créé avec succès.',
            'data' => ['ticket_id' => $ticket->id],
        ]);

        return redirect()->route('client.support')->with('success', 'Ticket créé avec succès !');
    }

    public function support(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status');

        $query = SupportTicket::where('user_id', $user->id)
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }]);

        if ($status && in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()->paginate(10);

        return view('client.support.index', compact('tickets', 'status'));
    }

    public function showTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)
            ->with('messages.user')
            ->findOrFail($id);

        $ticket->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_admin', true)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return view('client.support.show', compact('ticket'));
    }

    public function replyTicket(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if (! $ticket->canBeReplied()) {
            return redirect()->back()->with('error', 'Ce ticket ne peut plus recevoir de réponses.');
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_admin' => false,
            'attachments' => $attachments ?: null,
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        } elseif ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        $this->notifyAdminOfNewMessage($ticket, $message);

        return redirect()->back()->with('success', 'Réponse envoyée avec succès !');
    }

    private function notifyAdminOfNewMessage($ticket, $message)
    {
        $adminUsers = User::where('is_admin', true)->get();

        foreach ($adminUsers as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'support_admin',
                'title' => 'Nouvelle réponse sur un ticket',
                'message' => 'Le ticket #' . $ticket->ticket_number . ' a reçu une nouvelle réponse.',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'message_id' => $message->id,
                ],
            ]);
        }
    }

    public function closeTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if ($ticket->isClosed()) {
            return redirect()->back()->with('error', 'Ce ticket est déjà fermé.');
        }

        $ticket->markAsClosed();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'support',
            'title' => 'Ticket fermé',
            'message' => 'Votre ticket #' . $ticket->ticket_number . ' a été fermé.',
            'data' => ['ticket_id' => $ticket->id],
        ]);

        return redirect()->back()->with('success', 'Ticket fermé avec succès !');
    }

    public function reopenTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if (! $ticket->isClosed() && ! $ticket->isResolved()) {
            return redirect()->back()->with('error', 'Ce ticket est déjà ouvert.');
        }

        $ticket->reopen();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'support',
            'title' => 'Ticket rouvert',
            'message' => 'Votre ticket #' . $ticket->ticket_number . ' a été rouvert.',
            'data' => ['ticket_id' => $ticket->id],
        ]);

        return redirect()->back()->with('success', 'Ticket rouvert avec succès !');
    }

    public function markTicketAsRead($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        $ticket->markMessagesAsRead($user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Télécharger une pièce jointe
     */
    public function downloadAttachment($ticketId, $messageId, $attachmentIndex)
    {
        try {
            $user = Auth::user();

            // Récupérer le ticket avec vérification d'appartenance
            $ticket = SupportTicket::where('user_id', $user->id)
                ->findOrFail($ticketId);

            // Récupérer le message spécifique au ticket
            $message = $ticket->messages()
                ->where('id', $messageId)
                ->firstOrFail();

            // Vérifier les pièces jointes
            if (! $message->hasAttachments()) {
                abort(404, 'Aucune pièce jointe trouvée pour ce message.');
            }

            $attachments = $message->attachments;

            // Vérifier que l'index existe et est valide
            if (! isset($attachments[$attachmentIndex])) {
                abort(404, 'Pièce jointe introuvable (index: ' . $attachmentIndex . ')');
            }

            $attachment = $attachments[$attachmentIndex];

            // Validation des données de la pièce jointe
            if (! isset($attachment['path']) || empty($attachment['path'])) {
                abort(404, 'Chemin de fichier invalide.');
            }

            $filePath = storage_path('app/public/' . $attachment['path']);

            // Vérifier l'existence physique du fichier
            if (! file_exists($filePath)) {
                Log::error('Fichier introuvable: ' . $filePath);
                abort(404, 'Le fichier n\'existe plus sur le serveur.');
            }

            // Retourner le téléchargement avec le nom original
            return response()->download(
                $filePath,
                $attachment['name'] ?? basename($attachment['path']),
                [
                    'Content-Type' => $attachment['mime'] ?? mime_content_type($filePath),
                    'Content-Disposition' => 'attachment; filename="' . ($attachment['name'] ?? basename($attachment['path'])) . '"',
                ]
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Ticket ou message non trouvé.');
        } catch (\Exception $e) {
            Log::error('Erreur téléchargement pièce jointe: ' . $e->getMessage());
            abort(500, 'Erreur lors du téléchargement.');
        }
    }

    public function searchTickets(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $category = $request->input('category');
        $priority = $request->input('priority');

        $query = SupportTicket::where('user_id', $user->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $tickets = $query->latest()->paginate(10);

        return view('client.support.index', compact('tickets'));
    }

    public function getTicketStats()
    {
        $user = Auth::user();

        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('user_id', $user->id)->where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('user_id', $user->id)->where('status', 'closed')->count(),
            'unread_messages' => SupportMessage::whereHas('ticket', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_admin', true)->where('read', false)->count(),
        ];

        return response()->json($stats);
    }

    public function deleteTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if (! $ticket->isClosed() && ! $ticket->isResolved()) {
            return redirect()->back()->with('error', 'Vous ne pouvez supprimer que les tickets fermés ou résolus.');
        }

        if ($ticket->metadata && isset($ticket->metadata['attachments'])) {
            foreach ($ticket->metadata['attachments'] as $attachment) {
                if (isset($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        foreach ($ticket->messages as $message) {
            if ($message->attachments) {
                foreach ($message->attachments as $attachment) {
                    if (isset($attachment['path'])) {
                        Storage::disk('public')->delete($attachment['path']);
                    }
                }
            }
            $message->delete();
        }

        $ticket->delete();

        return redirect()->route('client.support')->with('success', 'Ticket supprimé avec succès !');
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes pour les notifications
    |--------------------------------------------------------------------------
    */

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('client.notifications', compact('notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function deleteNotification($id)
    {
        $user = Auth::user();
        $notification = Notification::where('user_id', $user->id)->findOrFail($id);

        $notification->delete();

        return redirect()->back()->with('success', 'Notification supprimée.');
    }

    public function listNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->take(10)->get();

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'type' => $notification->type,
                'read_at' => $notification->read_at,
                'time' => $notification->created_at->diffForHumans(),
                'icon' => $notification->icon,
                'color' => $notification->color,
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $formattedNotifications,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes pour les paramètres
    |--------------------------------------------------------------------------
    */

    public function settings()
    {
        $user = Auth::user();
        $userSettings = $user->settings ?? new Setting;

        return view('client.settings', compact('user', 'userSettings'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'notification_email' => 'nullable|boolean',
            'notification_sms' => 'nullable|boolean',
            'notification_push' => 'nullable|boolean',
            'newsletter_subscribed' => 'nullable|boolean',
            'two_factor_auth' => 'nullable|boolean',
            'language' => 'required|in:fr,en,es',
            'timezone' => 'required|timezone',
            'date_format' => 'required|in:d/m/Y,m/d/Y,Y-m/d',
            'currency' => 'required|in:XOF,EUR,USD',
            'theme' => 'nullable|in:light,dark',
            'auto_logout_time' => 'nullable|integer|in:15,30,60,120',
        ]);

        // Gestion des checkboxes (convertir "1" en true et null/false en false)
        $settings = [
            'user_id' => $user->id,
            'notification_email' => (bool) ($request->input('notification_email', 0)),
            'notification_sms' => (bool) ($request->input('notification_sms', 0)),
            'notification_push' => (bool) ($request->input('notification_push', 0)),
            'newsletter_subscribed' => (bool) ($request->input('newsletter_subscribed', 0)),
            'two_factor_auth' => (bool) ($request->input('two_factor_auth', 0)),
            'language' => $validated['language'],
            'timezone' => $validated['timezone'],
            'date_format' => $validated['date_format'],
            'currency' => $validated['currency'],
            'theme' => $validated['theme'] ?? 'light',
            'auto_logout_time' => $validated['auto_logout_time'] ?? 30,
        ];

        Setting::updateOrCreate(
            ['user_id' => $user->id],
            $settings
        );

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès !');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes API
    |--------------------------------------------------------------------------
    */

    public function checkNewNotifications()
    {
        $user = Auth::user();
        $count = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getStats()
    {
        $user = Auth::user();
        $wallet = $user->wallet ?? null;

        $stats = [
            'wallet_balance' => $wallet ? $wallet->balance : 0,
            'pending_requests' => $user->fundingRequests()->where('status', 'pending')->count(),
            'approved_requests' => $user->fundingRequests()->where('status', 'approved')->count(),
            'active_trainings' => $user->trainings()->wherePivot('status', 'enrolled')->count(),
            'open_tickets' => $user->supportTickets()->where('status', 'open')->count(),
            'pending_documents' => $user->documents()->where('status', 'pending')->count(),
            'unread_notifications' => $user->notifications()->whereNull('read_at')->count(),
        ];

        return response()->json($stats);
    }

    public function checkPermission(Request $request)
    {
        $permission = $request->input('permission');
        $user = Auth::user();

        $allowed = false;

        switch ($permission) {
            case 'create_request':
                $allowed = $user->status === 'active';
                break;
            case 'make_deposit':
                $allowed = $user->status === 'active';
                break;
            case 'withdraw_funds':
                $allowed = $user->status === 'active' && ($user->wallet->balance ?? 0) > 0;
                break;
            case 'enroll_training':
                $allowed = $user->status === 'active';
                break;
            default:
                $allowed = true;
        }

        return response()->json(['allowed' => $allowed]);
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    public function searchTrainings(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Training::where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $trainings = $query->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.trainings.index', compact('trainings'));
    }

    public function getTrainingProgress($id)
    {
        $user = Auth::user();
        $training = Training::findOrFail($id);

        $enrollment = $user->trainings()
            ->where('training_id', $id)
            ->first();

        if (! $enrollment) {
            return response()->json(['error' => 'Non inscrit'], 404);
        }

        return response()->json([
            'progress' => $enrollment->pivot->progress,
            'status' => $enrollment->pivot->status,
            'enrolled_at' => $enrollment->pivot->enrolled_at,
            'completed_at' => $enrollment->pivot->completed_at,
        ]);
    }

    public function getQuizAttempts($quizId)
    {
        $user = Auth::user();
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($attempts);
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes pour la gestion du PIN du wallet
    |--------------------------------------------------------------------------
    */

    public function setWalletPin(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return redirect()->back()->with('error', 'Portefeuille non trouvé.');
        }

        $validated = $request->validate([
            'current_pin' => 'nullable|string|size:6',
            'new_pin' => 'required|string|size:6|confirmed',
            'new_pin_confirmation' => 'required|string|size:6',
        ]);

        // Si le wallet a déjà un PIN (pas le PIN par défaut), vérifier le PIN actuel
        if ($wallet->pin_hash && $wallet->pin_hash !== Hash::make('000000')) {
            if (! Hash::check($validated['current_pin'], $wallet->pin_hash)) {
                return redirect()->back()->withErrors(['current_pin' => 'Le PIN actuel est incorrect.']);
            }
        }

        $wallet->update([
            'pin_hash' => Hash::make($validated['new_pin']),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'security',
            'title' => 'PIN du wallet mis à jour',
            'message' => 'Le PIN de votre wallet a été mis à jour avec succès.',
        ]);

        return redirect()->back()->with('success', 'PIN du wallet mis à jour avec succès !');
    }

    public function verifyWalletPin(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (! $wallet) {
            return response()->json(['valid' => false, 'message' => 'Portefeuille non trouvé.'], 404);
        }

        $validated = $request->validate([
            'pin' => 'required|string|size:6',
        ]);

        $valid = Hash::check($validated['pin'], $wallet->pin_hash);

        return response()->json([
            'valid' => $valid,
            'message' => $valid ? 'PIN vérifié avec succès.' : 'PIN incorrect.',
        ]);
    }
}
