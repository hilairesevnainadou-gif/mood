<!DOCTYPE html>
<html lang="fr" data-online="true">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- CSRF Token - CRITIQUE -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-cache headers pour pages protégées -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BHDM">
    <meta name="application-name" content="BHDM">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#1b5a8d">
    <meta name="msapplication-TileColor" content="#1b5a8d">
    <meta name="msapplication-starturl" content="/client/dashboard">
    <meta name="format-detection" content="telephone=no">
    <meta name="HandheldFriendly" content="true">
    <meta http-equiv="Permissions-Policy" content="unload=(self)">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}" crossorigin="use-credentials">

    <!-- Favicon & Icons -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/icons/icon-192.png') }}" sizes="192x192">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icons/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/icons/icon-512.png') }}">

    <!-- Splash Screen for iOS -->
    <link rel="apple-touch-startup-image" href="{{ asset('images/splash/splash-2048x2732.png') }}"
        media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="{{ asset('images/splash/splash-2732x2048.png') }}"
        media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="{{ asset('images/splash/splash-640x1136.png') }}"
        media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('images/splash/splash-750x1334.png') }}"
        media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
    <link rel="apple-touch-startup-image" href="{{ asset('images/splash/splash-1242x2208.png') }}"
        media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- CSS Client -->
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">

    <title>@yield('title', 'BHDM')</title>
    @stack('styles')

    <!-- Styles pour la photo de profil -->
    <style>
        .profile-photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-avatar-container {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
            background-color: #e2e8f0;
        }

        .user-avatar-large .profile-avatar-container {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255,255,255,0.4);
        }

        .bottom-nav-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .nav-item.active .bottom-nav-avatar {
            border-color: var(--primary-500, #1b5a8d);
            box-shadow: 0 0 0 2px rgba(27, 90, 141, 0.2);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 1rem;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .user-avatar-large .avatar-placeholder {
            font-size: 1.5rem;
        }

        /* Style pour l'avatar par défaut */
        .default-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Styles pour le statut documents complets */
        .status-badge.status-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        /* Animation pour le badge Nouveau */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .badge-pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body class="client-body">
    @php
        $user = Auth::user();
        $hasSubmittedRequiredDocuments = $user?->hasSubmittedRequiredDocuments() ?? false;
        $hasValidatedRequiredDocuments = $user?->hasAllRequiredDocuments() ?? false;
    @endphp

    <!-- Preloader Professionnel -->
    <div class="app-preloader" id="appPreloader">
        <div class="preloader-content">
            <div class="preloader-logo">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M40 0C17.908 0 0 17.908 0 40C0 62.092 17.908 80 40 80C62.092 80 80 62.092 80 40C80 17.908 62.092 0 40 0ZM40 72C22.36 72 8 57.64 8 40C8 22.36 22.36 8 40 8C57.64 8 72 22.36 72 40C72 57.64 57.64 72 40 72Z"
                        fill="#1b5a8d" />
                    <path
                        d="M40 16C26.744 16 16 26.744 16 40C16 53.256 26.744 64 40 64C53.256 64 64 53.256 64 40C64 26.744 53.256 16 40 16Z"
                        fill="#4aafff" />
                    <path
                        d="M40 24C30.064 24 22 32.064 22 42C22 51.936 30.064 60 40 60C49.936 60 58 51.936 58 42C58 32.064 49.936 24 40 24Z"
                        fill="#ffffff" />
                </svg>
            </div>
            <div class="preloader-text">
                <h2>BHDM</h2>
                <p>Chargement de votre espace...</p>
            </div>
            <div class="preloader-progress">
                <div class="progress-track">
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Transition Overlay -->
    <div class="page-transition-overlay" id="pageTransitionOverlay">
        <div class="transition-content">
            <div class="three-dots-loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <p class="transition-text">Chargement...</p>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main App Container -->
    <div class="app-container" id="appContainer">

        <!-- Top Navigation Bar -->
        <header class="app-header">
            <div class="header-container">
                <div class="header-brand">
                    <button class="menu-trigger" id="menuTrigger" aria-label="Ouvrir le menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="BHDM" class="logo-img">
                        <div class="brand-text">
                            <h1 class="app-title">BHDM</h1>
                            <div class="app-status" id="appStatus">
                                <span class="status-indicator online"></span>
                                <span class="status-text">En ligne</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-actions">
                    @php
                        $unreadCount = 0;
                        if (auth()->check()) {
                            $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                        }
                    @endphp

                    <button class="action-btn notifications-btn" id="notificationsTrigger" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadge" class="badge-count {{ $unreadCount > 0 ? '' : 'd-none' }}">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </button>

                    <div class="user-profile">
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-avatar" tabindex="0" aria-label="Menu profil utilisateur">
                                <div class="profile-avatar-container" aria-hidden="true">
                                    @if($user && $user->profile_photo_url)
                                        <img src="{{ $user->profile_photo_url }}?v={{ time() }}"
                                             alt="Photo de profil"
                                             class="profile-photo-img"
                                             onerror="this.onerror=null; this.src='{{ asset('images/avatar.png') }}';">
                                    @else
                                        <img src="{{ asset('images/avatar.png') }}"
                                             alt="{{ Auth::user()->name ?? 'Utilisateur' }}"
                                             class="default-avatar-img"
                                             loading="lazy">
                                    @endif
                                </div>
                                <div class="profile-info">
                                    <span class="profile-name">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                                    <span class="profile-role">Client</span>
                                </div>
                                <i class="fas fa-chevron-down dropdown-arrow" aria-hidden="true"></i>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <a href="{{ route('client.profile') }}" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                    <span>Mon Profil</span>
                                </a>
                                <div class="dropdown-divider" role="separator"></div>
                                <button class="dropdown-item logout-item" id="logoutTrigger" role="menuitem">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- BANNIERES DE STATUT -->
        @if (! $hasSubmittedRequiredDocuments)
            <div class="status-banner">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge status-warning">
                            <i class="fas fa-file-upload" aria-hidden="true"></i>
                            Documents requis
                        </span>
                        <p class="status-banner-message">
                            Téléchargez vos pièces d'identité obligatoires pour accéder à toutes les fonctionnalités.
                        </p>
                    </div>
                    <div class="status-banner-actions">
                        <a href="{{ route('client.documents.upload.form') }}" class="btn-primary">
                            <i class="fas fa-upload"></i> Télécharger maintenant
                        </a>
                        <a href="{{ route('client.documents.index') }}" class="btn-secondary">
                            Voir mes documents
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($hasSubmittedRequiredDocuments && ! $hasValidatedRequiredDocuments)
            <div class="status-banner" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%); border-left: 4px solid #10b981;">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge status-success">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Documents reçus
                        </span>
                        <p class="status-banner-message">
                            <strong>Bonne nouvelle !</strong> Vos documents sont en cours de validation.
                            En attendant, vous pouvez utiliser <strong>toutes les fonctionnalités</strong>.
                        </p>
                    </div>
                    <div class="status-banner-actions">
                        <a href="{{ route('client.requests.create') }}" class="btn-success-custom">
                            <i class="fas fa-plus-circle"></i> Nouvelle demande
                        </a>
                        <a href="{{ route('client.documents.index') }}" class="btn-secondary">
                            <i class="fas fa-folder-open"></i> Mes documents
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="status-banner" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%); border-left: 4px solid #3b82f6;">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                            Compte vérifié
                        </span>
                        <p class="status-banner-message">
                            Vos documents ont été validés. Vous avez accès à l'ensemble des fonctionnalités BHDM.
                        </p>
                    </div>
                    <div class="status-banner-actions">
                        <a href="{{ route('client.requests.create') }}" class="btn-primary">
                            <i class="fas fa-plus-circle"></i> Nouvelle demande
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <main class="app-main" id="mainContent">
            <!-- Side Navigation -->
            <nav class="app-sidebar" id="appSidebar" aria-label="Navigation principale">
                <div class="sidebar-header">
                    <div class="sidebar-user">
                        <div class="user-avatar-large">
                            <div class="profile-avatar-container" aria-hidden="true">
                                @if($user && $user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}?v={{ time() }}"
                                         alt="Photo de profil"
                                         class="profile-photo-img"
                                         onerror="this.onerror=null; this.src='{{ asset('images/avatar.png') }}';">
                                @else
                                    <img src="{{ asset('images/avatar.png') }}"
                                         alt="{{ Auth::user()->name ?? 'Utilisateur' }}"
                                         class="default-avatar-img"
                                         loading="lazy">
                                @endif
                            </div>
                            <div class="user-details">
                                <h4>{{ Auth::user()->name ?? 'Utilisateur' }}</h4>
                                <span class="user-id">{{ Auth::user()->member_id ?? 'Membre' }}</span>
                                <div class="user-status">
                                    <span class="status-dot {{ $hasSubmittedRequiredDocuments ? 'online' : 'offline' }}" id="sidebarStatusDot" aria-hidden="true"></span>
                                    <span id="sidebarStatusText">
                                        @if($hasValidatedRequiredDocuments)
                                            Compte vérifié
                                        @elseif($hasSubmittedRequiredDocuments)
                                            Validation en cours
                                        @else
                                            Documents requis
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="sidebar-close" id="sidebarClose" aria-label="Fermer le menu">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="sidebar-menu">
                    <div class="menu-section">
                        <h6 class="section-title">Navigation</h6>
                        <ul class="menu-list" role="menu">
                            @if ($hasSubmittedRequiredDocuments)
                                <li class="menu-item {{ Request::is('client/dashboard*') ? 'active' : '' }}">
                                    <a href="{{ route('client.dashboard') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                        <span class="menu-text">Tableau de bord</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('client/wallet*') ? 'active' : '' }}">
                                    <a href="{{ route('client.wallet.index') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-wallet"></i>
                                        </span>
                                        <span class="menu-text">Mon Portefeuille</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ Request::is('client/requests*') ? 'active' : '' }}">
                                    <a href="{{ route('client.requests.index') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-file-contract"></i>
                                        </span>
                                        <span class="menu-text">Mes Demandes</span>
                                    </a>
                                </li>
                            @else
                                <li class="menu-item disabled" style="opacity: 0.5;">
                                    <span class="menu-link" style="cursor: not-allowed;">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                        <span class="menu-text">Tableau de bord</span>
                                        <i class="fas fa-lock ms-auto" style="font-size: 0.7rem;"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="menu-section">
                        <h6 class="section-title">Contenus</h6>
                        <ul class="menu-list" role="menu">
                            <li class="menu-item {{ Request::is('client/documents*') ? 'active' : '' }}">
                                <a href="{{ route('client.documents.index') }}" class="menu-link page-transition"
                                    role="menuitem">
                                    <span class="menu-icon" aria-hidden="true">
                                        <i class="fas fa-folder"></i>
                                    </span>
                                    <span class="menu-text">Documents</span>
                                </a>
                            </li>
                            @if ($hasSubmittedRequiredDocuments)
                                <li class="menu-item {{ Request::is('client/training*') ? 'active' : '' }}">
                                    <a href="{{ route('client.trainings') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-graduation-cap"></i>
                                        </span>
                                        <span class="menu-text">Formations</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="menu-section">
                        <h6 class="section-title">Support</h6>
                        <ul class="menu-list" role="menu">
                            <li class="menu-item {{ Request::is('client/notifications*') ? 'active' : '' }}">
                                <a href="{{ route('client.notifications.index') }}" class="menu-link page-transition"
                                    role="menuitem">
                                    <span class="menu-icon" aria-hidden="true">
                                        <i class="fas fa-bell"></i>
                                    </span>
                                    <span class="menu-text">Notifications</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('client/support*') ? 'active' : '' }}">
                                <a href="{{ route('client.support.index') }}" class="menu-link page-transition"
                                    role="menuitem">
                                    <span class="menu-icon" aria-hidden="true">
                                        <i class="fas fa-headset"></i>
                                    </span>
                                    <span class="menu-text">Support Client</span>
                                </a>
                            </li>
                            <li class="menu-item {{ Request::is('client/profile*') ? 'active' : '' }}">
                                <a href="{{ route('client.profile') }}" class="menu-link page-transition"
                                    role="menuitem">
                                    <span class="menu-icon" aria-hidden="true">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <span class="menu-text">Mon Profil</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="sidebar-footer">
                    <div class="app-info">
                        <div class="version">Version 1.0.0</div>
                        <div class="copyright">© {{ date('Y') }} BHDM</div>
                    </div>
                    <button class="sidebar-logout-btn" id="sidebarLogoutBtn" aria-label="Se déconnecter">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        <span>Déconnexion</span>
                    </button>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="app-content">
                <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>
                <div class="content-wrapper" id="contentWrapper">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="app-bottom-nav" id="bottomNav" aria-label="Navigation mobile">
            @if ($hasSubmittedRequiredDocuments)
                <a href="{{ route('client.dashboard') }}"
                    class="nav-item page-transition {{ Request::is('client/dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
                <a href="{{ route('client.wallet.index') }}"
                    class="nav-item page-transition {{ Request::is('client/wallet*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Portefeuille</span>
                </a>
                <a href="{{ route('client.requests.index') }}"
                    class="nav-item page-transition {{ Request::is('client/requests*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Demandes</span>
                </a>
                <a href="{{ route('client.trainings') }}"
                    class="nav-item page-transition {{ Request::is('client/training*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Formations</span>
                </a>
            @else
                <a href="{{ route('client.documents.index') }}"
                    class="nav-item page-transition {{ Request::is('client/documents*') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i>
                    <span>Documents</span>
                </a>
            @endif
            <a href="{{ route('client.profile') }}"
                class="nav-item page-transition {{ Request::is('client/profile*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <!-- Notifications Panel -->
    <div class="notifications-panel" id="notificationsPanel" aria-hidden="true">
        <div class="panel-overlay" id="notificationsOverlay"></div>
        <div class="panel-content">
            <div class="panel-header">
                <h3><i class="fas fa-bell"></i> Notifications</h3>
                <button class="panel-close" id="notificationsClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="panel-body">
                <div class="notifications-list" id="notificationsList"></div>
            </div>
            <div class="panel-footer">
                <a href="{{ route('client.notifications.index') }}" class="view-all page-transition">
                    Voir toutes les notifications
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Déconnexion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmLogout">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- ========================================== -->
    <!-- SCRIPTS - ORDRE IMPORTANT -->
    <!-- ========================================== -->

    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. SDK Kkiapay - AVANT les scripts de l'application -->
    <script src="https://cdn.kkiapay.me/k.js"></script>

    <!-- 4. Vérification SDK -->
    <script>
        window.kkiapayReady = false;

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Kkiapay !== 'undefined') {
                console.log('✅ SDK Kkiapay chargé avec succès');
                window.kkiapayReady = true;
            } else {
                console.error('❌ SDK Kkiapay non chargé');
                // Tentative de rechargement
                setTimeout(function() {
                    if (typeof window.Kkiapay === 'undefined') {
                        console.log('🔄 Tentative de rechargement du SDK...');
                        var script = document.createElement('script');
                        script.src = 'https://cdn.kkiapay.me/k.js';
                        script.onload = function() {
                            console.log('✅ SDK Kkiapay rechargé');
                            window.kkiapayReady = true;
                        };
                        document.head.appendChild(script);
                    }
                }, 1000);
            }
        });
    </script>

    <!-- 5. Scripts de l'application -->
    <script>
        // App Initialization
        document.addEventListener('DOMContentLoaded', function() {
            initApp();
        });

        function initApp() {
            const preloader = document.getElementById('appPreloader');
            const appContainer = document.getElementById('appContainer');
            const minLoadTime = 800;

            setTimeout(() => {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                    appContainer.style.display = 'block';
                    setTimeout(() => {
                        initComponents();
                    }, 100);
                }, 500);
            }, minLoadTime);
        }

        function initComponents() {
            initNavigation();
            initNotifications();
            initOnlineStatus();
            initPageTransitions();
            initEventListeners();
            initSessionKeepAlive();
        }
    </script>

    <!-- Toast System -->
    <script>
        class ToastSystem {
            constructor() {
                this.container = document.getElementById('toastContainer');
                this.toasts = [];
                this.toastId = 0;
                this.colors = {
                    success: '#10b981',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#3b82f6'
                };

                if (!this.container) {
                    this.createContainer();
                }
            }

            createContainer() {
                this.container = document.createElement('div');
                this.container.id = 'toastContainer';
                this.container.className = 'toast-container';
                document.querySelector('.app-content').prepend(this.container);
            }

            show(options) {
                const { title = '', message = '', type = 'info', duration = 5000 } = options;
                const toastId = `toast-${++this.toastId}`;
                const color = this.colors[type] || this.colors.info;

                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <div class="toast-content">
                        <div class="toast-icon" style="color: ${color};">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
                        </div>
                        <div class="toast-body">
                            ${title ? `<div class="toast-title">${title}</div>` : ''}
                            ${message ? `<div class="toast-message">${message}</div>` : ''}
                        </div>
                        <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                this.container.appendChild(toast);
                setTimeout(() => toast.classList.add('show'), 10);
                if (duration > 0) {
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 300);
                    }, duration);
                }
            }

            success(title, message) { this.show({ title, message, type: 'success' }); }
            error(title, message) { this.show({ title, message, type: 'error' }); }
            warning(title, message) { this.show({ title, message, type: 'warning' }); }
            info(title, message) { this.show({ title, message, type: 'info' }); }
        }

        let toastSystem;
    </script>

    <!-- Navigation -->
    <script>
        function initNavigation() {
            const menuTrigger = document.getElementById('menuTrigger');
            const sidebar = document.getElementById('appSidebar');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar.classList.add('open');
                sidebarOverlay.style.display = 'block';
                setTimeout(() => sidebarOverlay.classList.add('active'), 10);
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('active');
                setTimeout(() => {
                    sidebarOverlay.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }

            menuTrigger?.addEventListener('click', openSidebar);
            sidebarClose?.addEventListener('click', closeSidebar);
            sidebarOverlay?.addEventListener('click', closeSidebar);

            // Profile dropdown
            const profileDropdown = document.getElementById('profileDropdown');
            const profileAvatar = profileDropdown?.querySelector('.profile-avatar');

            profileAvatar?.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!profileDropdown?.contains(e.target)) {
                    profileDropdown?.classList.remove('open');
                }
            });
        }
    </script>

    <!-- Online Status -->
    <script>
        function initOnlineStatus() {
            function updateStatus() {
                const isOnline = navigator.onLine;
                const indicator = document.querySelector('.status-indicator');
                const text = document.querySelector('.status-text');

                if (indicator && text) {
                    indicator.className = `status-indicator ${isOnline ? 'online' : 'offline'}`;
                    text.textContent = isOnline ? 'En ligne' : 'Hors ligne';
                }
            }

            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
            updateStatus();
        }
    </script>

    <!-- Notifications -->
    <script>
        function initNotifications() {
            const trigger = document.getElementById('notificationsTrigger');
            const panel = document.getElementById('notificationsPanel');
            const close = document.getElementById('notificationsClose');
            const overlay = document.getElementById('notificationsOverlay');

            function open() {
                panel.classList.add('open');
                document.body.style.overflow = 'hidden';
                loadNotifications();
            }

            function closePanel() {
                panel.classList.remove('open');
                document.body.style.overflow = '';
            }

            trigger?.addEventListener('click', open);
            close?.addEventListener('click', closePanel);
            overlay?.addEventListener('click', closePanel);
        }

        async function loadNotifications() {
            const list = document.getElementById('notificationsList');
            if (!list) return;

            try {
                const response = await fetch('{{ route('client.notifications.list') }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.success && data.notifications?.length > 0) {
                    list.innerHTML = data.notifications.map(n => `
                        <div class="notification-item ${n.read_at ? '' : 'unread'}">
                            <div class="notification-content">
                                <p>${n.message}</p>
                                <span>${n.time}</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<div class="empty-state">Aucune notification</div>';
                }
            } catch (error) {
                list.innerHTML = '<div class="error-state">Erreur de chargement</div>';
            }
        }
    </script>

    <!-- Page Transitions -->
    <script>
        function initPageTransitions() {
            document.querySelectorAll('.page-transition').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.target === '_blank' || this.getAttribute('href')?.startsWith('#')) return;

                    e.preventDefault();
                    const href = this.href;
                    document.getElementById('pageTransitionOverlay')?.classList.add('active');

                    setTimeout(() => window.location.href = href, 600);
                });
            });
        }
    </script>

    <!-- Event Listeners -->
    <script>
        function initEventListeners() {
            toastSystem = new ToastSystem();

            window.toast = {
                success: (t, m) => toastSystem.success(t, m),
                error: (t, m) => toastSystem.error(t, m),
                warning: (t, m) => toastSystem.warning(t, m),
                info: (t, m) => toastSystem.info(t, m)
            };

            // Logout
            document.getElementById('logoutTrigger')?.addEventListener('click', () => {
                new bootstrap.Modal(document.getElementById('logoutModal')).show();
            });

            document.getElementById('sidebarLogoutBtn')?.addEventListener('click', () => {
                new bootstrap.Modal(document.getElementById('logoutModal')).show();
            });

            document.getElementById('confirmLogout')?.addEventListener('click', () => {
                document.getElementById('logout-form').submit();
            });
        }
    </script>

    <!-- Session Keep Alive -->
    <script>
        function initSessionKeepAlive() {
            setInterval(async () => {
                try {
                    await fetch('/api/session-check', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                } catch (e) {
                    console.error('Keep-alive failed');
                }
            }, 300000);
        }
    </script>

    <!-- Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ route('service-worker') }}')
                .then(reg => console.log('Service Worker registered'))
                .catch(err => console.error('Service Worker error:', err));
        }
    </script>

    <!-- Global Modal Functions -->
    <script>
        window.showDepositModal = function() {
            if (!navigator.onLine) {
                window.toast?.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
                return;
            }

            const modal = document.getElementById('depositSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                // Réinitialiser le formulaire
                resetDepositForm();
            } else {
                console.error('Modal depositSlide non trouvé');
            }
        };

        window.closeSlide = function(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        };

        function resetDepositForm() {
            document.getElementById('depositForm')?.reset();
            document.getElementById('summaryCard') && (document.getElementById('summaryCard').style.display = 'none');
            document.getElementById('kkiapayButtonContainer') && (document.getElementById('kkiapayButtonContainer').style.display = 'none');
            document.getElementById('prepareButton') && (document.getElementById('prepareButton').style.display = 'flex');
            document.getElementById('payButton') && (document.getElementById('payButton').style.display = 'none');
            document.getElementById('depositStep1') && (document.getElementById('depositStep1').style.display = 'block');
            document.getElementById('depositStep2') && (document.getElementById('depositStep2').style.display = 'none');
            document.getElementById('depositStep3') && (document.getElementById('depositStep3').style.display = 'none');
            document.querySelectorAll('.amount-option').forEach(btn => btn.classList.remove('active'));
        }
    </script>

    @stack('scripts')
</body>
</html>
