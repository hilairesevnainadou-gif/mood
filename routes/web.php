<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PwaController;
use App\Http\Controllers\SSEController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Api\StatController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\WalletController as ApiWalletController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Client\RequestFundingController;
use App\Http\Controllers\Admin\FundingValidationController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Client\DocumentController as ClientDocumentController;

/*
|--------------------------------------------------------------------------
| Routes publiques (Site institutionnel)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/a-propos', function () {
    return view('about');
})->name('about');

Route::get('/nos-services', function () {
    return view('services');
})->name('services');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ClientController::class, 'contact'])->name('contact.submit');

Route::get('/services/{service}', function ($service) {
    return view('service-detail', compact('service'));
})->name('services.detail');

/*
|--------------------------------------------------------------------------
| Routes d'authentification Client
|--------------------------------------------------------------------------
*/

// Connexion
Route::get('/connexion', function () {
    return view('auth.login');
})->name('login');

Route::post('/connexion', [ClientController::class, 'login'])->name('login.submit');

// Inscription
Route::get('/inscription', [ClientController::class, 'showRegisterForm'])->name('register');

Route::post('/inscription', [ClientController::class, 'register'])->name('register.submit');

// Mot de passe oublié
Route::get('/mot-de-passe-oublie', function () {
    return view('auth.forgot-password');
})->name('password.forgot');

Route::post('/mot-de-passe-oublie', [ClientController::class, 'sendResetLink'])->name('password.email');

// Réinitialisation
Route::get('/reinitialisation/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reinitialisation', [ClientController::class, 'resetPassword'])->name('password.update');

// Confirmation de mot de passe
Route::get('/confirmer-mot-de-passe', function () {
    return view('auth.confirm-password');
})->middleware('auth')->name('password.confirm');

Route::post('/confirmer-mot-de-passe', function (Request $request) {
    if (!Hash::check($request->password, $request->user()->password)) {
        return back()->withErrors([
            'password' => ['Le mot de passe fourni est incorrect.']
        ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());
    return redirect()->intended();
})->middleware(['auth', 'throttle:6,1'])->name('password.confirm.post');

// Déconnexion globale
Route::post('/deconnexion', [ClientController::class, 'logout'])->name('logout');

// Email verification
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verifier', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verifier/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('client.documents.index')
            ->with('success', 'Email vérifié ! Veuillez maintenant télécharger vos documents.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Routes du processus de demande
|--------------------------------------------------------------------------
*/

Route::get('/creer-portefeuille', function () {
    return view('wallet.create');
})->name('wallet.create');

Route::get('/test-evaluation', function () {
    return view('evaluation.test');
})->name('evaluation.test');

Route::post('/soumettre-test', [ClientController::class, 'submitTest'])->name('evaluation.submit');

Route::get('/questionnaire', function () {
    return view('questionnaire.form');
})->name('questionnaire');

Route::post('/soumettre-questionnaire', [ClientController::class, 'submitQuestionnaire'])->name('questionnaire.submit');

/*
|--------------------------------------------------------------------------
| Routes des programmes
|--------------------------------------------------------------------------
*/

Route::get('/programmes/subventions', function () {
    return view('programs.grants');
})->name('programs.grants');

Route::get('/programmes/financement', function () {
    return view('programs.funding');
})->name('programs.funding');

Route::get('/programmes/formation', function () {
    return view('programs.training');
})->name('programs.training');

Route::get('/programmes/assistance', function () {
    return view('programs.assistance');
})->name('programs.assistance');

Route::get('/postuler/{program}', [ClientController::class, 'apply'])->name('programs.apply');

/*
|--------------------------------------------------------------------------
| Routes API SSE & Service Worker
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/api/events', [SSEController::class, 'stream']);
    Route::post('/api/events/broadcast', [SSEController::class, 'broadcast']);
    Route::get('/api/events/connections', [SSEController::class, 'connections']);
});

Route::get('/service-worker.js', function () {
    return response(file_get_contents(public_path('js/service-worker.js')), 200, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/'
    ]);
})->name('service-worker');

Route::get('/funding/check-updates', [WalletController::class, 'checkFundingUpdates'])
    ->name('funding.check-updates')
    ->middleware(['auth', 'verified']);

/*
|--------------------------------------------------------------------------
| Routes protégées de l'espace client
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('client')->name('client.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');

    // DEMANDES DE FINANCEMENT
    Route::prefix('demandes')->name('requests.')->middleware('profile.documents.validated')->group(function () {
        Route::get('/', [RequestFundingController::class, 'index'])->name('index');
        Route::get('/nouvelle', [RequestFundingController::class, 'create'])->name('create');
        Route::post('/', [RequestFundingController::class, 'store'])->name('store');

        Route::get('/{id}/paiement', [RequestFundingController::class, 'paymentPage'])
            ->name('payment')
            ->where('id', '[0-9]+');

        Route::post('/{id}/paiement/confirmer', [RequestFundingController::class, 'confirmPayment'])
            ->name('payment.confirm')
            ->where('id', '[0-9]+');

        Route::post('/{id}/documents', [RequestFundingController::class, 'uploadDocuments'])
            ->name('documents.upload')
            ->where('id', '[0-9]+');

        Route::post('/{id}/transfert', [RequestFundingController::class, 'initiateTransfer'])
            ->name('transfer')
            ->where('id', '[0-9]+');

        Route::get('/{id}', [RequestFundingController::class, 'show'])
            ->name('show')
            ->where('id', '[0-9]+');
    });

    // Portefeuille
    Route::prefix('portefeuille')->name('wallet.')->middleware('profile.documents.validated')->group(function () {
        Route::get('/', [WalletController::class, 'wallet'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/transfer', [WalletController::class, 'transfer'])->name('transfer');
        Route::post('/set-pin', [WalletController::class, 'setWalletPin'])->name('set-pin');
        Route::post('/verify-pin', [WalletController::class, 'verifyWalletPin'])->name('verify-pin');

        // Callbacks Lygos
        Route::post('/deposit/callback', [WalletController::class, 'depositCallback'])->name('deposit.callback');
        Route::post('/withdraw/callback', [WalletController::class, 'withdrawCallback'])->name('withdraw.callback');

        // Financements
        Route::get('/funding/{id}/details', [WalletController::class, 'fundingDetails'])->name('funding.details');
        Route::post('/funding/{id}/credit', [WalletController::class, 'creditFunding'])->name('funding.credit');
        Route::get('/funding/check-updates', [WalletController::class, 'checkFundingUpdates'])->name('funding.check-updates');

        // Vérification de wallet
        Route::post('/check-wallet', [WalletController::class, 'checkWallet'])->name('check-wallet');
        Route::get('/get-info', [WalletController::class, 'getWalletInfo'])->name('get-info');
        Route::get('/quick-actions', [WalletController::class, 'getQuickActions'])->name('quick-actions');
    });

    // Profil utilisateur
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    Route::put('/profile', [ClientController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ClientController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ClientController::class, 'removePhoto'])->name('profile.photo.remove');

    // Routes pour les documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [ClientDocumentController::class, 'index'])->name('index');
        Route::get('/upload', [ClientDocumentController::class, 'uploadForm'])->name('upload.form');
        Route::post('/upload', [ClientDocumentController::class, 'uploadDocument'])->name('upload');
        Route::get('/{id}/view', [ClientDocumentController::class, 'viewDocumentPage'])->name('view.page');
        Route::get('/{id}/download', [ClientDocumentController::class, 'downloadDocument'])->name('download');
        Route::get('/{id}/view-direct', [ClientDocumentController::class, 'viewDocument'])->name('view.direct');
        Route::delete('/{id}', [ClientDocumentController::class, 'deleteDocument'])->name('delete');
        Route::get('/{id}/view-url', [ClientDocumentController::class, 'viewDocumentUrl'])->name('view.url');
        Route::patch('/{id}/description', [ClientDocumentController::class, 'updateDescription'])->name('update.description');
        Route::patch('/{id}/expiry-date', [ClientDocumentController::class, 'updateExpiryDate'])->name('update.expiry');
        Route::post('/{id}/renew', [ClientDocumentController::class, 'renewDocument'])->name('renew');
        Route::get('/{id}/status', [ClientDocumentController::class, 'checkDocumentStatus'])->name('status');
        Route::get('/stats', [ClientDocumentController::class, 'getStats'])->name('stats');

        // API routes
        Route::get('/api/list', [ClientDocumentController::class, 'apiIndex'])->name('api.index');
    });

    // Formations
    Route::middleware('profile.documents.validated')->group(function () {
        Route::get('/formations', [ClientController::class, 'trainings'])->name('trainings');
        Route::get('/formations/{id}', [ClientController::class, 'trainingDetail'])->name('trainings.detail');
        Route::post('/formations/{id}/inscription', [ClientController::class, 'enrollTraining'])->name('trainings.enroll');
        Route::get('/formations/mes-cours', [ClientController::class, 'myTrainings'])->name('trainings.my');
    });

    // Support Client
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [ClientController::class, 'support'])->name('index');
        Route::get('/creer', [ClientController::class, 'createSupport'])->name('create');
        Route::post('/', [ClientController::class, 'submitSupport'])->name('submit');
        Route::get('/{id}', [ClientController::class, 'showTicket'])->name('show');
        Route::post('/{id}/repondre', [ClientController::class, 'replyTicket'])->name('reply');
        Route::post('/{id}/fermer', [ClientController::class, 'closeTicket'])->name('close');
        Route::post('/{id}/reouvrir', [ClientController::class, 'reopenTicket'])->name('reopen');
        Route::post('/{id}/marquer-lu', [ClientController::class, 'markTicketAsRead'])->name('mark-read');
        Route::delete('/{id}', [ClientController::class, 'deleteTicket'])->name('delete');

        Route::get('/{ticketId}/attachment/{messageId}/{attachmentIndex}', [ClientController::class, 'downloadAttachment'])
            ->where(['ticketId' => '[0-9]+', 'messageId' => '[0-9]+', 'attachmentIndex' => '[0-9]+'])
            ->name('download-attachment');

        Route::get('/stats', [ClientController::class, 'getTicketStats'])->name('stats');
        Route::get('/rechercher', [ClientController::class, 'searchTickets'])->name('search');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ClientController::class, 'notifications'])->name('index');
        Route::post('/{id}/lire', [ClientController::class, 'markNotificationAsRead'])->name('read');
        Route::post('/tout-lire', [ClientController::class, 'markAllNotificationsAsRead'])->name('read-all');
        Route::delete('/{id}', [ClientController::class, 'deleteNotification'])->name('delete');
        Route::get('/list', [ClientController::class, 'listNotifications'])->name('list');
    });

    // Paramètres
    Route::get('/parametres', [ClientController::class, 'settings'])->name('settings');
    Route::put('/parametres', [ClientController::class, 'updateSettings'])->name('settings.update');

    // Déconnexion client
    Route::post('/deconnexion', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/connexion')->with('success', 'Vous avez été déconnecté avec succès.');
    })->name('logout');

    // API Client
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/nouvelles-notifications', [ClientController::class, 'checkNewNotifications'])->name('notifications.check');
        Route::get('/statistiques', [ClientController::class, 'getStats'])->name('stats');
        Route::get('/verifier-permission', [ClientController::class, 'checkPermission'])->name('check-permission');
    });
});

// Route pour vérifier le statut de vérification d'email
Route::get('/api/user/verification-status', function () {
    return response()->json([
        'verified' => auth()->check() && auth()->user()->hasVerifiedEmail(),
        'email' => auth()->check() ? auth()->user()->email : null
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Routes administratives
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // ============================================================
    // ROUTES PUBLIQUES (Non authentifiées)
    // ============================================================

    // Login GET - Afficher le formulaire
    Route::get('/connexion', [AuthController::class, 'showLoginForm'])
        ->name('login');

    // Login POST - Soumettre le formulaire
    Route::post('/connexion', [AuthController::class, 'login'])
        ->name('login.submit');

    // ============================================================
    // ROUTES PROTÉGÉES (Authentifiées admin)
    // ============================================================

    Route::middleware(['auth:admin'])->group(function () {

        // --- DASHBOARD ---
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // --- DÉCONNEXION ---
        Route::post('/deconnexion', [AuthController::class, 'logout'])
            ->name('logout');

        // --- GESTION DES UTILISATEURS ---
        Route::prefix('utilisateurs')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/{id}/activer', [UserController::class, 'activate'])->name('activate');
            Route::post('/{id}/desactiver', [UserController::class, 'deactivate'])->name('deactivate');
        });

        // --- GESTION DES TRANSACTIONS ---
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::post('/{id}/valider', [TransactionController::class, 'validateTransaction'])
                ->name('validate');
        });

        // --- VALIDATION DES DEMANDES DE FINANCEMENT ---
        Route::prefix('funding')->name('funding.')->group(function () {
            Route::get('/validation-prix', [FundingValidationController::class, 'pendingValidation'])
                ->name('pending-validation');
            Route::post('/{id}/definir-prix', [FundingValidationController::class, 'setPrice'])
                ->name('set-price');
            Route::get('/verifications-paiements', [FundingValidationController::class, 'pendingPayments'])
                ->name('pending-payments');
            Route::post('/paiements/{paymentId}/verifier', [FundingValidationController::class, 'verifyPayment'])
                ->name('verify-payment');
        });

        // --- GESTION DES DOCUMENTS ---
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
            Route::post('/{id}/valider', [AdminDocumentController::class, 'validateDocument'])
                ->name('validate');
        });

        // --- GESTION DES FORMATIONS ---
        Route::prefix('formations')->name('trainings.')->group(function () {
            Route::get('/', [AdminTrainingController::class, 'index'])->name('index');
            Route::get('/creer', [AdminTrainingController::class, 'create'])->name('create');
            Route::post('/', [AdminTrainingController::class, 'store'])->name('store');
        });

        // --- SUPPORT ADMIN ---
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [AdminSupportController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminSupportController::class, 'show'])->name('show');
            Route::post('/{id}/repondre', [AdminSupportController::class, 'reply'])->name('reply');
        });

        // --- RAPPORTS ---
        Route::prefix('rapports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/generer', [ReportController::class, 'generate'])->name('generate');
        });

        // --- PARAMÈTRES SYSTÈME ---
        Route::prefix('parametres')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/', [SettingController::class, 'update'])->name('update');
        });

    }); // Fin middleware auth:admin

}); // Fin prefix admin

/*
|--------------------------------------------------------------------------
| Routes API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    // API Publique
    Route::get('/statistiques', [StatController::class, 'index'])->name('stats');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact');
    Route::get('/programmes', [ProgramController::class, 'index'])->name('programs');

    // API Authentifiée
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/wallet/balance', [ApiWalletController::class, 'balance']);
        Route::post('/wallet/deposit', [ApiWalletController::class, 'deposit']);
        Route::post('/wallet/withdraw', [ApiWalletController::class, 'withdraw']);
    });
});

/*
|--------------------------------------------------------------------------
| Routes PWA
|--------------------------------------------------------------------------
*/

Route::get('/manifest.json', [PwaController::class, 'manifest']);
Route::get('/offline', function () {
    return view('pwa.offline');
})->name('offline');

/*
|--------------------------------------------------------------------------
| Routes de pages statiques
|--------------------------------------------------------------------------
*/

Route::get('/confidentialite', function () {
    return view('static.privacy');
})->name('privacy');

Route::get('/conditions-utilisation', function () {
    return view('static.terms');
})->name('terms');

Route::get('/faq', function () {
    return view('static.faq');
})->name('faq');

Route::get('/actualites', function () {
    return view('static.news');
})->name('news');

Route::get('/actualites/{slug}', function ($slug) {
    return view('static.news-detail', compact('slug'));
})->name('news.detail');

Route::get('/temoignages', function () {
    return view('static.testimonials');
})->name('testimonials');

Route::get('/partenaires', function () {
    return view('static.partners');
})->name('partners');

Route::get('/mentions-legales', function () {
    return view('static.legal');
})->name('legal');

Route::get('/accessibilite', function () {
    return view('static.accessibility');
})->name('accessibility');

Route::get('/plan-du-site', function () {
    return view('static.sitemap');
})->name('sitemap');

Route::get('/cgu', function () {
    return view('static.cgu');
})->name('cgu');

/*
|--------------------------------------------------------------------------
| Routes de compatibilité et redirections
|--------------------------------------------------------------------------
*/

Route::redirect('/register', '/inscription', 301);
Route::redirect('/login', '/connexion', 301);
Route::redirect('/signup', '/inscription', 301);
Route::redirect('/signin', '/connexion', 301);
Route::redirect('/about', '/a-propos', 301);
Route::redirect('/services', '/nos-services', 301);
Route::redirect('/contact-us', '/contact', 301);
Route::redirect('/get-started', '/inscription', 301);
Route::redirect('/sign-out', '/deconnexion', 301);
Route::redirect('/account', '/client/profile', 301);

/*
|--------------------------------------------------------------------------
| Routes de test et développement
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/test/email', function () {
        return view('emails.test');
    })->name('test.email');

    Route::get('/test/components', function () {
        return view('test.components');
    })->name('test.components');

    Route::get('/test/dashboard', function () {
        return view('test.dashboard');
    })->name('test.dashboard');
}

/*
|--------------------------------------------------------------------------
| Routes de maintenance
|--------------------------------------------------------------------------
*/

Route::get('/maintenance', function () {
    if (!app()->isDownForMaintenance()) {
        return redirect('/');
    }
    return view('maintenance');
})->name('maintenance');

/*
|--------------------------------------------------------------------------
| Route fallback pour les pages 404
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('errors.404');
});
