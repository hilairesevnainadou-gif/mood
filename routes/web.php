<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\FundingValidationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RequiredDocumentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\StatController;
use App\Http\Controllers\Api\WalletController as ApiWalletController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\DocumentController as ClientDocumentController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\RequestFundingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\SSEController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\WalletController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Institutional Website)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
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
| Client Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

// Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [ClientController::class, 'login'])->name('login.submit');

// Registration
Route::get('/register', [ClientController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [ClientController::class, 'register'])->name('register.submit');

// Forgot Password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.forgot');

Route::post('/forgot-password', [ClientController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset/{token}', function ($token) {
    $email = request('email');
    Log::info('Affichage formulaire reset', [
        'token' => $token,
        'email' => $email,
        'full_url' => request()->fullUrl()
    ]);

    return view('auth.reset-password', [
        'token' => $token,
        'email' => $email
    ]);
})->name('password.reset');

Route::post('/reset', [ClientController::class, 'resetPassword'])->name('password.update');

// Confirm Password
Route::get('/confirm-password', function () {
    return view('auth.confirm-password');
})->middleware('auth')->name('password.confirm');

Route::post('/confirm-password', function (Request $request) {
    if (!Hash::check($request->password, $request->user()->password)) {
        return back()->withErrors([
            'password' => ['The provided password is incorrect.']
        ]);
    }

    $request->session()->put('auth.password_confirmed_at', time());
    return redirect()->intended();
})->middleware(['auth', 'throttle:6,1'])->name('password.confirm.post');

Route::post('/logout', [ClientController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('client.documents.index')
            ->with('success', 'Email verified! Please upload your documents now.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'A new verification link has been sent to your email address.');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Application Process Routes
|--------------------------------------------------------------------------
*/

Route::get('/create-wallet', function () {
    return view('wallet.create');
})->name('wallet.create');

Route::get('/evaluation-test', function () {
    return view('evaluation.test');
})->name('evaluation.test');

Route::post('/submit-test', [ClientController::class, 'submitTest'])->name('evaluation.submit');

Route::get('/questionnaire', function () {
    return view('questionnaire.form');
})->name('questionnaire');

Route::post('/submit-questionnaire', [ClientController::class, 'submitQuestionnaire'])->name('questionnaire.submit');

/*
|--------------------------------------------------------------------------
| Program Routes
|--------------------------------------------------------------------------
*/

Route::get('/programs/grants', function () {
    return view('programs.grants');
})->name('programs.grants');

Route::get('/programs/funding', function () {
    return view('programs.funding');
})->name('programs.funding');

Route::get('/programs/training', function () {
    return view('programs.training');
})->name('programs.training');

Route::get('/programs/assistance', function () {
    return view('programs.assistance');
})->name('programs.assistance');

Route::get('/apply/{program}', [ClientController::class, 'apply'])->name('programs.apply');

/*
|--------------------------------------------------------------------------
| API SSE & Service Worker Routes
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
| KKIAPAY CALLBACK - ROUTES PUBLIQUES (hors auth et hors CSRF)
|--------------------------------------------------------------------------
*/

// CALLBACK PRINCIPAL KKIAPAY - Doit être accessible publiquement
Route::match(['get', 'post'], '/kkiapay/callback', [WalletController::class, 'kkiapayCallback'])
    ->name('kkiapay.callback')
    ->withoutMiddleware(['auth', 'verified', \App\Http\Middleware\VerifyCsrfToken::class]);

// CALLBACK ALTERNATIF pour dépôt wallet
Route::post('/wallet/deposit/callback', [WalletController::class, 'kkiapayCallback'])
    ->name('client.wallet.deposit.callback')
    ->withoutMiddleware(['auth', 'verified', \App\Http\Middleware\VerifyCsrfToken::class]);

// Routes de paiement (protégées par auth)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/payment/success/{transaction}', [WalletController::class, 'paymentSuccess'])
        ->name('payment.success');
    Route::get('/payment/failed/{transaction}', [WalletController::class, 'paymentFailed'])
        ->name('payment.failed');
    Route::get('/payment/error', [WalletController::class, 'paymentError'])
        ->name('payment.error');
    Route::get('/payment/status/{transaction}', [WalletController::class, 'paymentStatus'])
        ->name('payment.status');
});

/*
|--------------------------------------------------------------------------
| Session Check API
|--------------------------------------------------------------------------
*/

Route::get('/api/session-check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token()
    ]);
})->middleware('web');

/*
|--------------------------------------------------------------------------
| Protected Client Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('client')->name('client.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'dashboard'])->name('dashboard');

    // Funding Requests
    Route::prefix('requests')
        ->name('requests.')
        ->middleware('profile.documents.validated')
        ->group(function () {

            Route::get('/', [RequestFundingController::class, 'index'])->name('index');
            Route::get('/create', [RequestFundingController::class, 'create'])->name('create');
            Route::post('/', [RequestFundingController::class, 'store'])->name('store');
            Route::get('/{id}', [RequestFundingController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::delete('/{id}/cancel', [RequestFundingController::class, 'cancel'])->name('cancel')->where('id', '[0-9]+');
            Route::get('/{id}/payment', [RequestFundingController::class, 'paymentPage'])->name('payment')->where('id', '[0-9]+');
            Route::post('/{id}/payment', [RequestFundingController::class, 'processCustomPayment'])->name('payment.process')->where('id', '[0-9]+');
        });

    // Wallet Routes (sans le callback qui est public)
    Route::prefix('wallet')->name('wallet.')->middleware('profile.documents.validated')->group(function () {

        Route::get('/', [WalletController::class, 'wallet'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/transfer', [WalletController::class, 'transfer'])->name('transfer');
        Route::post('/set-pin', [WalletController::class, 'setPin'])->name('set-pin');
        Route::post('/verify-pin', [WalletController::class, 'verifyPin'])->name('verify-pin');
        Route::get('/get-info', [WalletController::class, 'getWalletInfo'])->name('get-info');
        Route::get('/check-funding-updates', [WalletController::class, 'checkFundingUpdates'])->name('funding.check');
        Route::get('/funding/{id}/details', [WalletController::class, 'fundingDetails'])->name('funding.details');
        Route::post('/funding/{id}/credit', [WalletController::class, 'creditFunding'])->name('funding.credit');
        Route::get('/quick-actions', [WalletController::class, 'getQuickActions'])->name('quick-actions');
    });

    // User Profile
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');

    // Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [ClientDocumentController::class, 'index'])->name('index');
        Route::get('/upload', [ClientDocumentController::class, 'uploadForm'])->name('upload.form');
        Route::post('/upload', [ClientDocumentController::class, 'uploadDocument'])->name('upload');
        Route::get('/{id}/view', [ClientDocumentController::class, 'viewDocumentPage'])->name('view.page')->where('id', '[0-9]+');
        Route::get('/{id}/download', [ClientDocumentController::class, 'downloadDocument'])->name('download')->where('id', '[0-9]+');
        Route::get('/{id}/view-direct', [ClientDocumentController::class, 'viewDocument'])->name('view.direct')->where('id', '[0-9]+');
        Route::delete('/{id}', [ClientDocumentController::class, 'deleteDocument'])->name('delete')->where('id', '[0-9]+');
        Route::get('/{id}/view-url', [ClientDocumentController::class, 'viewDocumentUrl'])->name('view.url')->where('id', '[0-9]+');
        Route::patch('/{id}/description', [ClientDocumentController::class, 'updateDescription'])->name('update.description')->where('id', '[0-9]+');
        Route::patch('/{id}/expiry-date', [ClientDocumentController::class, 'updateExpiryDate'])->name('update.expiry')->where('id', '[0-9]+');
        Route::post('/{id}/renew', [ClientDocumentController::class, 'renewDocument'])->name('renew')->where('id', '[0-9]+');
        Route::get('/{id}/status', [ClientDocumentController::class, 'checkDocumentStatus'])->name('status')->where('id', '[0-9]+');
        Route::get('/stats', [ClientDocumentController::class, 'getStats'])->name('stats');
        Route::get('/api/list', [ClientDocumentController::class, 'apiIndex'])->name('api.index');
    });

    // Training
    Route::middleware('profile.documents.validated')->group(function () {
        Route::get('/training', [ClientController::class, 'trainings'])->name('trainings');
        Route::get('/training/{id}', [ClientController::class, 'trainingDetail'])->name('trainings.detail');
        Route::post('/training/{id}/enroll', [ClientController::class, 'enrollTraining'])->name('trainings.enroll');
        Route::get('/training/my-courses', [ClientController::class, 'myTrainings'])->name('trainings.my');
    });

    // Support
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [ClientController::class, 'support'])->name('index');
        Route::get('/create', [ClientController::class, 'createSupport'])->name('create');
        Route::post('/', [ClientController::class, 'submitSupport'])->name('submit');
        Route::get('/{id}', [ClientController::class, 'showTicket'])->name('show');
        Route::post('/{id}/reply', [ClientController::class, 'replyTicket'])->name('reply');
        Route::post('/{id}/close', [ClientController::class, 'closeTicket'])->name('close');
        Route::post('/{id}/reopen', [ClientController::class, 'reopenTicket'])->name('reopen');
        Route::post('/{id}/mark-read', [ClientController::class, 'markTicketAsRead'])->name('mark-read');
        Route::delete('/{id}', [ClientController::class, 'deleteTicket'])->name('destroy');
        Route::get('/{ticketId}/attachment/{messageId}/{attachmentIndex}', [ClientController::class, 'downloadAttachment'])
            ->where(['ticketId' => '[0-9]+', 'messageId' => '[0-9]+', 'attachmentIndex' => '[0-9]+'])
            ->name('download-attachment');
        Route::get('/stats', [ClientController::class, 'getTicketStats'])->name('stats');
        Route::get('/search', [ClientController::class, 'searchTickets'])->name('search');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ClientController::class, 'notifications'])->name('index');
        Route::post('/{id}/read', [ClientController::class, 'markNotificationAsRead'])->name('read');
        Route::post('/read-all', [ClientController::class, 'markAllNotificationsAsRead'])->name('read-all');
        Route::delete('/{id}', [ClientController::class, 'deleteNotification'])->name('delete');
        Route::get('/list', [ClientController::class, 'listNotifications'])->name('list');
    });

    // Settings
    Route::get('/settings', [ClientController::class, 'settings'])->name('settings');
    Route::put('/settings', [ClientController::class, 'updateSettings'])->name('settings.update');

    // Client API
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/new-notifications', [ClientController::class, 'checkNewNotifications'])->name('notifications.check');
        Route::get('/statistics', [ClientController::class, 'getStats'])->name('stats');
        Route::get('/check-permission', [ClientController::class, 'checkPermission'])->name('check-permission');
    });
});

// Email Verification Status Route
Route::get('/api/user/verification-status', function () {
    return response()->json([
        'verified' => auth()->check() && auth()->user()->hasVerifiedEmail(),
        'email' => auth()->check() ? auth()->user()->email : null
    ]);
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Public Admin Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/export', [UserController::class, 'export'])->name('export');
            Route::get('/create-admin', [UserController::class, 'createAdmin'])->name('create-admin');
            Route::post('/store-admin', [UserController::class, 'storeAdmin'])->name('store-admin');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/{id}/activate', [UserController::class, 'activate'])->name('activate');
            Route::post('/{id}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::get('/export', [TransactionController::class, 'export'])->name('export');
            Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
            Route::post('/{id}/validate', [TransactionController::class, 'validateTransaction'])->name('validate');
            Route::post('/{id}/reject', [TransactionController::class, 'rejectTransaction'])->name('reject');
            Route::post('/{id}/cancel', [TransactionController::class, 'cancelTransaction'])->name('cancel');
        });

        // Funding Validation
        Route::prefix('funding')->name('funding.')->group(function () {
            Route::get('/pending-validation', [FundingValidationController::class, 'pendingValidation'])->name('pending-validation');
            Route::get('/pending-transfers', [FundingValidationController::class, 'pendingTransfers'])->name('pending-transfers');
            Route::get('/pending-payments', [FundingValidationController::class, 'pendingPayments'])->name('pending-payments');
            Route::get('/{id}', [FundingValidationController::class, 'showRequest'])->name('show-request');
            Route::post('/{id}/set-price', [FundingValidationController::class, 'setPrice'])->name('set-price');
            Route::post('/{id}/under-review', [FundingValidationController::class, 'setUnderReview'])->name('under-review');
            Route::post('/{id}/reject', [FundingValidationController::class, 'rejectRequest'])->name('reject-request');
            Route::post('/{id}/approve-predefined', [FundingValidationController::class, 'approvePredefined'])->name('approve-predefined');
            Route::post('/{id}/check-documents', [FundingValidationController::class, 'checkDocuments'])->name('check-documents');
            Route::post('/{id}/verify-and-schedule', [FundingValidationController::class, 'verifyMissingDocumentsAndScheduleTransfer'])->name('verify-and-schedule');
            Route::post('/{id}/execute-transfer', [FundingValidationController::class, 'executeTransfer'])->name('execute-transfer');
            Route::post('/{id}/cancel-transfer', [FundingValidationController::class, 'cancelTransfer'])->name('cancel-transfer');
            Route::post('/{id}/complete', [FundingValidationController::class, 'completeRequest'])->name('complete');
            Route::post('/payments/{paymentId}/verify', [FundingValidationController::class, 'verifyPayment'])->name('verify-payment');
        });

        // Documents
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
            Route::post('/bulk-validate', [AdminDocumentController::class, 'bulkValidate'])->name('bulk-validate');
            Route::post('/user/{userId}/validate-all', [AdminDocumentController::class, 'validateUserDocuments'])->name('validate-user');
            Route::get('/user/{userId}', [AdminDocumentController::class, 'show'])->name('show');
            Route::get('/{id}/download', [AdminDocumentController::class, 'download'])->name('download');
            Route::post('/{id}/validate', [AdminDocumentController::class, 'validateDocument'])->name('validate');
            Route::post('/{id}/reject', [AdminDocumentController::class, 'reject'])->name('reject');
            Route::post('/{id}/pending', [AdminDocumentController::class, 'pending'])->name('pending');
        });

        // Required Documents
        Route::prefix('required-documents')->name('required-documents.')->group(function () {
            Route::get('/', [RequiredDocumentController::class, 'index'])->name('index');
            Route::get('/create', [RequiredDocumentController::class, 'create'])->name('create');
            Route::post('/', [RequiredDocumentController::class, 'store'])->name('store');
            Route::get('/{requiredDocument}/edit', [RequiredDocumentController::class, 'edit'])->name('edit');
            Route::put('/{requiredDocument}', [RequiredDocumentController::class, 'update'])->name('update');
            Route::delete('/{requiredDocument}', [RequiredDocumentController::class, 'destroy'])->name('destroy');
            Route::post('/{requiredDocument}/toggle-status', [RequiredDocumentController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Trainings
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', [AdminTrainingController::class, 'index'])->name('index');
            Route::get('/create', [AdminTrainingController::class, 'create'])->name('create');
            Route::post('/', [AdminTrainingController::class, 'store'])->name('store');
            Route::get('/{training}/edit', [AdminTrainingController::class, 'edit'])->name('edit');
            Route::put('/{training}', [AdminTrainingController::class, 'update'])->name('update');
            Route::delete('/{training}', [AdminTrainingController::class, 'destroy'])->name('destroy');
        });

        // Support
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [AdminSupportController::class, 'index'])->name('index');
            Route::get('/{ticket}', [AdminSupportController::class, 'show'])->name('show');
            Route::post('/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('reply');
            Route::post('/{ticket}/close', [AdminSupportController::class, 'close'])->name('close');
            Route::post('/{ticket}/assign', [AdminSupportController::class, 'assign'])->name('assign');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/generate', [ReportController::class, 'generate'])->name('generate');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::put('/', [SettingController::class, 'update'])->name('update');
        });
    });
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/statistics', [StatController::class, 'index'])->name('stats');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact');
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs');

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
| PWA Routes
|--------------------------------------------------------------------------
*/

Route::get('/manifest.json', [PwaController::class, 'manifest']);
Route::get('/offline', function () {
    return view('pwa.offline');
})->name('offline');

/*
|--------------------------------------------------------------------------
| Static Pages
|--------------------------------------------------------------------------
*/

Route::get('/privacy', function () {
    return view('static.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('static.terms');
})->name('terms');

Route::get('/faq', function () {
    return view('static.faq');
})->name('faq');

Route::get('/news', function () {
    return view('static.news');
})->name('news');

Route::get('/news/{slug}', function ($slug) {
    return view('static.news-detail', compact('slug'));
})->name('news.detail');

Route::get('/testimonials', function () {
    return view('static.testimonials');
})->name('testimonials');

Route::get('/partners', function () {
    return view('static.partners');
})->name('partners');

Route::get('/legal', function () {
    return view('static.legal');
})->name('legal');

Route::get('/accessibility', function () {
    return view('static.accessibility');
})->name('accessibility');

Route::get('/sitemap', function () {
    return view('static.sitemap');
})->name('sitemap');

Route::get('/cgu', function () {
    return view('static.cgu');
})->name('cgu');

/*
|--------------------------------------------------------------------------
| Test Routes (Local Only)
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
| Maintenance & Fallback
|--------------------------------------------------------------------------
*/

Route::get('/maintenance', function () {
    if (!app()->isDownForMaintenance()) {
        return redirect('/');
    }
    return view('maintenance');
})->name('maintenance');

Route::fallback(function () {
    return view('errors.404');
});