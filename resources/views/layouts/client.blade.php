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
    <meta name="apple-mobile-web-app-title" content="BHDM Client">
    <meta name="application-name" content="BHDM Client">
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

    <title>@yield('title', 'BHDM Client')</title>
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
        }

        .user-avatar-large .avatar-placeholder {
            font-size: 1.5rem;
        }
    </style>
</head>

<body class="client-body">
    @php
        $user = Auth::user();
        $hasUploadedRequiredDocuments = $user?->hasUploadedRequiredDocuments() ?? false;
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
                <h2>BHDM Client</h2>
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
                <!-- Logo & Brand -->
                <div class="header-brand">
                    <button class="menu-trigger" id="menuTrigger" aria-label="Ouvrir le menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="BHDM" class="logo-img">
                        <div class="brand-text">
                            <h1 class="app-title">BHDM Client</h1>
                            <div class="app-status" id="appStatus">
                                <span class="status-indicator online"></span>
                                <span class="status-text">En ligne</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <button class="action-btn search-btn" id="searchTrigger" aria-label="Rechercher"
                            title="Rechercher">
                            <i class="fas fa-search"></i>
                        </button>

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
                    </div>

                    <!-- User Profile -->
                    <div class="user-profile">
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="profile-avatar" tabindex="0" aria-label="Menu profil utilisateur">
                                <div class="profile-avatar-container" aria-hidden="true">
                                    @if($user && $user->profile_photo_url)
                                        <img src="{{ $user->profile_photo_url }}?v={{ time() }}"
                                             alt="Photo de profil"
                                             class="profile-photo-img"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-placeholder" style="display: none; background-color: #{{ substr(md5(Auth::id() ?? '1'), 0, 6) }};">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @else
                                        <div class="avatar-placeholder" style="background-color: #{{ substr(md5(Auth::id() ?? '1'), 0, 6) }};">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </div>
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
                                <a href="{{ route('client.settings') }}" class="dropdown-item" role="menuitem">
                                    <i class="fas fa-cog" aria-hidden="true"></i>
                                    <span>Paramètres</span>
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

            <!-- Search Bar -->
            <div class="search-overlay" id="searchOverlay">
                <div class="search-container">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon" aria-hidden="true"></i>
                        <input type="text" class="search-input"
                            placeholder="Rechercher transactions, documents, etc..." id="globalSearchInput"
                            aria-label="Recherche globale">
                        <button class="search-clear" id="searchClear" aria-label="Effacer la recherche">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button class="search-close" id="searchClose" aria-label="Fermer la recherche">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </header>

        @if (! $hasUploadedRequiredDocuments)
            <div class="status-banner">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge status-warning">
                            <i class="fas fa-file-upload" aria-hidden="true"></i>
                            Documents requis
                        </span>
                        <p class="status-banner-message">
                            Téléchargez vos pièces d'identité obligatoires pour accéder au tableau de bord.
                        </p>
                    </div>
                    <div class="status-banner-actions">
                        <a href="{{ route('client.documents.upload.form') }}" class="btn-primary">
                            Télécharger maintenant
                        </a>
                        <a href="{{ route('client.documents.index') }}" class="btn-secondary">
                            Voir mes documents
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($hasUploadedRequiredDocuments && ! $hasValidatedRequiredDocuments)
            <div class="status-banner">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge status-warning">
                            <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                            Validation en cours
                        </span>
                        <p class="status-banner-message">
                            Vos documents sont en cours de validation. Les demandes, wallet et formations seront
                            disponibles après validation.
                        </p>
                    </div>
                    <div class="status-banner-actions">
                        <a href="{{ route('client.documents.index') }}" class="btn-secondary">
                            Suivre mes documents
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
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-placeholder" style="display: none; background-color: #{{ substr(md5(Auth::id() ?? '1'), 0, 6) }};">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="avatar-placeholder" style="background-color: #{{ substr(md5(Auth::id() ?? '1'), 0, 6) }};">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="user-details">
                                <h4>{{ Auth::user()->name ?? 'Utilisateur' }}</h4>
                                <span class="user-id">{{ Auth::user()->member_id ?? 'Membre' }}</span>
                                <div class="user-status">
                                    <span class="status-dot online" id="sidebarStatusDot" aria-hidden="true"></span>
                                    <span id="sidebarStatusText">Connecté</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="sidebar-close" id="sidebarClose" aria-label="Fermer le menu">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="sidebar-menu">
                    <!-- Navigation Principale -->
                    <div class="menu-section">
                        <h6 class="section-title">Navigation</h6>
                        <ul class="menu-list" role="menu">
                            @if ($hasUploadedRequiredDocuments)
                                <li class="menu-item {{ Request::is('client/dashboard*') ? 'active' : '' }}">
                                    <a href="{{ route('client.dashboard') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                        <span class="menu-text">Tableau de bord</span>
                                    </a>
                                </li>
                            @endif
                            @if ($hasValidatedRequiredDocuments)
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
                            @endif
                        </ul>
                    </div>

                    <!-- Contenus -->
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
                            @if ($hasValidatedRequiredDocuments)
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

                    <!-- Support & Paramètres -->
                    <div class="menu-section">
                        <h6 class="section-title">Support & Paramètres</h6>
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
                            <li class="menu-item {{ Request::is('client/settings*') ? 'active' : '' }}">
                                <a href="{{ route('client.settings') }}" class="menu-link page-transition"
                                    role="menuitem">
                                    <span class="menu-icon" aria-hidden="true">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    <span class="menu-text">Paramètres</span>
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

                <!-- Sidebar Footer -->
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
                <!-- Messages Toast Container -->
                <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true">
                    <!-- Messages toast s'afficheront ici -->
                </div>

                <!-- Actual Content -->
                <div class="content-wrapper" id="contentWrapper">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Bottom Navigation (Mobile Only) -->
        <nav class="app-bottom-nav" id="bottomNav" aria-label="Navigation mobile">
            @if ($hasUploadedRequiredDocuments)
                <a href="{{ route('client.dashboard') }}"
                    class="nav-item page-transition {{ Request::is('client/dashboard*') ? 'active' : '' }}"
                    aria-label="Accueil">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    <span>Accueil</span>
                </a>
            @endif
            @if ($hasValidatedRequiredDocuments)
                <a href="{{ route('client.wallet.index') }}"
                    class="nav-item page-transition {{ Request::is('client/wallet*') ? 'active' : '' }}"
                    aria-label="Portefeuille">
                    <i class="fas fa-wallet" aria-hidden="true"></i>
                    <span>Portefeuille</span>
                </a>
                <a href="{{ route('client.requests.index') }}"
                    class="nav-item page-transition {{ Request::is('client/requests*') ? 'active' : '' }}"
                    aria-label="Demandes">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    <span>Demandes</span>
                </a>
            @endif
            <a href="{{ route('client.profile') }}"
                class="nav-item page-transition {{ Request::is('client/profile*') ? 'active' : '' }}"
                aria-label="Profil">
                @if($user && $user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}?v={{ time() }}"
                         alt="Profil"
                         class="bottom-nav-avatar"
                         onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=\'fas fa-user\' aria-hidden=\'true\'></i><span>Profil</span>';">
                @else
                    <i class="fas fa-user" aria-hidden="true"></i>
                @endif
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <!-- Notifications Panel -->
    <div class="notifications-panel" id="notificationsPanel" aria-hidden="true">
        <div class="panel-overlay" id="notificationsOverlay"></div>
        <div class="panel-content">
            <div class="panel-header">
                <h3><i class="fas fa-bell" aria-hidden="true"></i> Notifications</h3>
                <button class="panel-close" id="notificationsClose" aria-label="Fermer les notifications">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="panel-body">
                <div class="notifications-list" id="notificationsList">
                    <!-- Notifications will be loaded here -->
                </div>
            </div>
            <div class="panel-footer">
                <a href="{{ route('client.notifications.index') }}" class="view-all page-transition">
                    Voir toutes les notifications
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-icon">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                    </div>
                    <h5 class="modal-title" id="logoutModalLabel">Déconnexion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmLogout">
                        <i class="fas fa-sign-out-alt me-1" aria-hidden="true"></i>
                        Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form - CORRIGÉ: utilise route('logout') et non route('client.logout') -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- App Initialization -->
    <script>
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
            initSessionKeepAlive(); // NOUVEAU: Garde la session active
        }
    </script>

    <!-- Session Keep Alive - NOUVEAU -->
    <script>
        function initSessionKeepAlive() {
            // Ping toutes les 5 minutes pour garder la session active
            setInterval(async () => {
                try {
                    const response = await fetch('/api/session-check', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        console.warn('Session check failed');
                    }
                } catch (error) {
                    console.error('Keep-alive error:', error);
                }
            }, 300000); // 5 minutes
            
            // Rafraîchir le token CSRF avant les requêtes importantes
            window.refreshCsrfToken = async function() {
                try {
                    const response = await fetch('/api/session-check', {
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').content = data.csrf_token;
                        // Mettre à jour aussi les inputs hidden des formulaires
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.csrf_token;
                        });
                    }
                } catch (error) {
                    console.error('CSRF refresh error:', error);
                }
            };
        }
    </script>

    <!-- Professional Toast System -->
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
                    info: '#3b82f6',
                    primary: '#1b5a8d',
                    secondary: '#6b7280'
                };

                if (!this.container) {
                    this.createContainer();
                }
            }

            createContainer() {
                this.container = document.createElement('div');
                this.container.id = 'toastContainer';
                this.container.className = 'toast-container';
                this.container.setAttribute('aria-live', 'polite');
                this.container.setAttribute('aria-atomic', 'true');
                document.querySelector('.app-content').prepend(this.container);
            }

            show(options) {
                const {
                    title = '',
                    message = '',
                    type = 'info',
                    duration = 5000,
                    icon = null,
                    position = 'top-right',
                    actions = [],
                    dismissible = true
                } = options;

                const toastId = `toast-${++this.toastId}`;
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `toast toast-${type} toast-${position}`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');

                const iconMap = {
                    success: 'fas fa-check-circle',
                    error: 'fas fa-times-circle',
                    warning: 'fas fa-exclamation-triangle',
                    info: 'fas fa-info-circle',
                    primary: 'fas fa-bell',
                    secondary: 'fas fa-comment'
                };

                const toastIcon = icon || iconMap[type] || iconMap.info;
                const color = this.colors[type] || this.colors.info;

                toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-icon" style="color: ${color};" aria-hidden="true">
                        <i class="${toastIcon}"></i>
                    </div>
                    <div class="toast-body">
                        ${title ? `<div class="toast-title">${title}</div>` : ''}
                        ${message ? `<div class="toast-message">${message}</div>` : ''}
                        ${actions.length > 0 ? `
                                    <div class="toast-actions">
                                        ${actions.map(action => `
                                    <button class="toast-action" onclick="${action.action}">
                                        ${action.text}
                                    </button>
                                `).join('')}
                                    </div>
                                ` : ''}
                    </div>
                    ${dismissible ? `
                                <button class="toast-close" onclick="toastSystem.dismiss('${toastId}')" aria-label="Fermer">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </button>
                            ` : ''}
                </div>
                ${duration > 0 ? `<div class="toast-progress" style="animation-duration: ${duration}ms;" aria-hidden="true"></div>` : ''}
            `;

                this.container.appendChild(toast);
                this.toasts.push({
                    id: toastId,
                    element: toast
                });

                setTimeout(() => {
                    toast.classList.add('show');
                }, 10);

                if (duration > 0) {
                    setTimeout(() => {
                        this.dismiss(toastId);
                    }, duration);
                }

                return toastId;
            }

            dismiss(toastId) {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.classList.remove('show');
                    toast.classList.add('hiding');

                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                        this.toasts = this.toasts.filter(t => t.id !== toastId);
                    }, 300);
                }
            }

            dismissAll() {
                this.toasts.forEach(toast => {
                    this.dismiss(toast.id);
                });
            }

            success(title, message, options = {}) {
                return this.show({
                    title,
                    message,
                    type: 'success',
                    icon: 'fas fa-check-circle',
                    ...options
                });
            }

            error(title, message, options = {}) {
                return this.show({
                    title,
                    message,
                    type: 'error',
                    icon: 'fas fa-times-circle',
                    ...options
                });
            }

            warning(title, message, options = {}) {
                return this.show({
                    title,
                    message,
                    type: 'warning',
                    icon: 'fas fa-exclamation-triangle',
                    ...options
                });
            }

            info(title, message, options = {}) {
                return this.show({
                    title,
                    message,
                    type: 'info',
                    icon: 'fas fa-info-circle',
                    ...options
                });
            }

            primary(title, message, options = {}) {
                return this.show({
                    title,
                    message,
                    type: 'primary',
                    icon: 'fas fa-bell',
                    ...options
                });
            }
        }

        let toastSystem;
    </script>

    <!-- Notification Badge System -->
    <script>
        const NotificationBadge = {
            badgeElement: document.getElementById('notificationBadge'),

            update: async function() {
                try {
                    const response = await fetch('{{ route('client.notifications.list') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();

                    if (data.success && data.notifications) {
                        const unreadCount = data.notifications.filter(n => !n.read_at).length;
                        this.render(unreadCount);
                    }
                } catch (error) {
                    console.error('Erreur mise à jour badge:', error);
                }
            },

            render: function(count) {
                if (!this.badgeElement) return;

                if (count > 0) {
                    this.badgeElement.textContent = count > 9 ? '9+' : count;
                    this.badgeElement.classList.remove('d-none');
                    this.badgeElement.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        this.badgeElement.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    this.badgeElement.classList.add('d-none');
                }
            },

            decrement: function() {
                const currentText = this.badgeElement.textContent;
                let current = currentText === '9+' ? 10 : parseInt(currentText) || 0;
                if (current > 0) {
                    this.render(current - 1);
                }
            },

            clear: function() {
                this.render(0);
            }
        };

        setInterval(() => {
            if (document.visibilityState === 'visible') {
                NotificationBadge.update();
            }
        }, 30000);

        const notificationsTrigger = document.getElementById('notificationsTrigger');
        if (notificationsTrigger) {
            notificationsTrigger.addEventListener('click', () => {
                NotificationBadge.update();
            });
        }

        document.addEventListener('notificationRead', () => {
            NotificationBadge.decrement();
        });

        document.addEventListener('allNotificationsRead', () => {
            NotificationBadge.clear();
        });
    </script>

    <!-- Navigation System -->
    <script>
        function initNavigation() {
            const menuTrigger = document.getElementById('menuTrigger');
            const sidebar = document.getElementById('appSidebar');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar.classList.add('open');
                sidebar.setAttribute('aria-hidden', 'false');
                sidebarOverlay.style.display = 'block';
                setTimeout(() => {
                    sidebarOverlay.classList.add('active');
                }, 10);
                document.body.style.overflow = 'hidden';
                document.body.classList.add('sidebar-open');
                menuTrigger.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                sidebar.setAttribute('aria-hidden', 'true');
                sidebarOverlay.classList.remove('active');
                setTimeout(() => {
                    sidebarOverlay.style.display = 'none';
                    document.body.style.overflow = '';
                    document.body.classList.remove('sidebar-open');
                }, 300);
                menuTrigger.setAttribute('aria-expanded', 'false');
            }

            if (menuTrigger && sidebar) {
                menuTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    openSidebar();
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    closeSidebar();
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    closeSidebar();
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                const profileAvatar = profileDropdown.querySelector('.profile-avatar');

                profileAvatar.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('open');
                    profileAvatar.setAttribute('aria-expanded', profileDropdown.classList.contains('open'));
                });

                profileAvatar.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        profileDropdown.classList.toggle('open');
                        profileAvatar.setAttribute('aria-expanded', profileDropdown.classList.contains('open'));
                    } else if (e.key === 'Escape' && profileDropdown.classList.contains('open')) {
                        profileDropdown.classList.remove('open');
                        profileAvatar.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!profileDropdown.contains(e.target)) {
                        profileDropdown.classList.remove('open');
                        profileAvatar.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            const searchTrigger = document.getElementById('searchTrigger');
            const searchClose = document.getElementById('searchClose');
            const searchClear = document.getElementById('searchClear');
            const searchInput = document.getElementById('globalSearchInput');
            const searchOverlay = document.getElementById('searchOverlay');

            if (searchTrigger && searchOverlay) {
                searchTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    searchOverlay.classList.add('active');
                    searchOverlay.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        if (searchInput) searchInput.focus();
                    }, 100);
                });
            }

            if (searchClose) {
                searchClose.addEventListener('click', () => {
                    searchOverlay.classList.remove('active');
                    searchOverlay.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    if (searchInput) searchInput.value = '';
                });
            }

            if (searchClear) {
                searchClear.addEventListener('click', () => {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                    searchOverlay.classList.remove('active');
                    searchOverlay.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                    if (searchInput) searchInput.value = '';
                } else if (e.key === '/' && e.ctrlKey) {
                    e.preventDefault();
                    searchTrigger.click();
                }
            });

            searchOverlay.addEventListener('click', (e) => {
                if (e.target === searchOverlay) {
                    searchOverlay.classList.remove('active');
                    searchOverlay.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }
            });

            function trapFocus(element) {
                const focusableElements = element.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                const firstFocusable = focusableElements[0];
                const lastFocusable = focusableElements[focusableElements.length - 1];

                element.addEventListener('keydown', function(e) {
                    if (e.key !== 'Tab') return;

                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusable) {
                            lastFocusable.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastFocusable) {
                            firstFocusable.focus();
                            e.preventDefault();
                        }
                    }
                });
            }

            if (sidebar) {
                trapFocus(sidebar);
            }
        }
    </script>

    <!-- Online Status System -->
    <script>
        function initOnlineStatus() {
            const appStatus = document.getElementById('appStatus');
            const sidebarStatusDot = document.getElementById('sidebarStatusDot');
            const sidebarStatusText = document.getElementById('sidebarStatusText');

            let offlineStartTime = null;
            let offlineNotificationShown = false;
            let checkInterval = null;

            function updateOnlineStatus() {
                const isOnline = navigator.onLine;
                const currentTime = Date.now();

                if (appStatus) {
                    const indicator = appStatus.querySelector('.status-indicator');
                    const text = appStatus.querySelector('.status-text');

                    if (isOnline) {
                        indicator.className = 'status-indicator online';
                        indicator.setAttribute('aria-label', 'En ligne');
                        text.textContent = 'En ligne';
                        text.className = 'status-text online';

                        offlineStartTime = null;
                        offlineNotificationShown = false;

                        if (checkInterval) {
                            clearInterval(checkInterval);
                            checkInterval = null;
                        }

                    } else {
                        indicator.className = 'status-indicator offline';
                        indicator.setAttribute('aria-label', 'Hors ligne');
                        text.textContent = 'Hors ligne';
                        text.className = 'status-text offline';

                        if (!offlineStartTime) {
                            offlineStartTime = currentTime;
                            offlineNotificationShown = false;
                            startOfflineCheck();
                        }
                    }
                }

                if (sidebarStatusDot && sidebarStatusText) {
                    if (isOnline) {
                        sidebarStatusDot.className = 'status-dot online';
                        sidebarStatusText.textContent = 'Connecté';
                    } else {
                        sidebarStatusDot.className = 'status-dot offline';
                        sidebarStatusText.textContent = 'Déconnecté';
                    }
                }

                document.documentElement.dataset.online = isOnline;
            }

            function startOfflineCheck() {
                checkInterval = setInterval(() => {
                    if (!navigator.onLine && offlineStartTime && !offlineNotificationShown) {
                        const currentTime = Date.now();
                        const timeOffline = currentTime - offlineStartTime;
                        const oneHour = 60 * 60 * 1000;

                        if (timeOffline >= oneHour) {
                            if (toastSystem) {
                                toastSystem.warning(
                                    'Connexion perdue depuis 1 heure',
                                    'Vous êtes hors ligne depuis plus d\'une heure. Veuillez vérifier votre connexion internet.', {
                                        duration: 10000,
                                        showCloseButton: true
                                    }
                                );
                            }

                            offlineNotificationShown = true;
                            clearInterval(checkInterval);
                            checkInterval = null;
                        }
                    }
                }, 60000);
            }

            function showReconnectionNotification() {
                if (toastSystem && navigator.onLine) {
                    toastSystem.success(
                        'Connexion rétablie',
                        'Votre connexion internet a été rétablie avec succès.', {
                            duration: 5000,
                            showCloseButton: true
                        }
                    );
                }
            }

            updateOnlineStatus();

            window.addEventListener('online', function() {
                updateOnlineStatus();
                showReconnectionNotification();
            });

            window.addEventListener('offline', function() {
                updateOnlineStatus();
            });

            setInterval(() => {
                const wasOnline = document.documentElement.dataset.online === 'true';
                updateOnlineStatus();

                if (!wasOnline && navigator.onLine) {
                    showReconnectionNotification();
                }
            }, 30000);
        }
    </script>

    <!-- Notifications System -->
    <script>
        function initNotifications() {
            const notificationsTrigger = document.getElementById('notificationsTrigger');
            const notificationsPanel = document.getElementById('notificationsPanel');
            const notificationsClose = document.getElementById('notificationsClose');
            const notificationsOverlay = document.getElementById('notificationsOverlay');

            function openNotificationsPanel() {
                notificationsPanel.classList.add('open');
                notificationsPanel.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                loadNotifications();
                notificationsClose.focus();
            }

            function closeNotificationsPanel() {
                notificationsPanel.classList.remove('open');
                notificationsPanel.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                notificationsTrigger.focus();
            }

            if (notificationsTrigger && notificationsPanel) {
                notificationsTrigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openNotificationsPanel();
                });
            }

            if (notificationsClose) {
                notificationsClose.addEventListener('click', () => {
                    closeNotificationsPanel();
                });
            }

            if (notificationsOverlay) {
                notificationsOverlay.addEventListener('click', () => {
                    closeNotificationsPanel();
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && notificationsPanel.classList.contains('open')) {
                    closeNotificationsPanel();
                }
            });

            async function loadNotifications() {
                const notificationsList = document.getElementById('notificationsList');
                if (!notificationsList) return;

                notificationsList.innerHTML = `
                    <div class="loading-notifications">
                        <div class="spinner" aria-hidden="true"></div>
                        <p>Chargement...</p>
                    </div>
                `;

                try {
                    const response = await fetch('{{ route('client.notifications.list') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        credentials: 'same-origin'
                    });

                    const data = await response.json();

                    if (data.success) {
                        const unreadCount = data.notifications.filter(n => !n.read_at).length;

                        if (window.NotificationBadge) {
                            window.NotificationBadge.render(unreadCount);
                        }

                        if (data.notifications.length > 0) {
                            notificationsList.innerHTML = data.notifications.map(notification => `
                                <div class="notification-item ${notification.read_at ? '' : 'unread'}"
                                     data-id="${notification.id}"
                                     onclick="markNotificationRead(${notification.id}, this)">
                                    <div class="notification-icon" style="background: ${notification.color || '#3b82f6'}">
                                        <i class="${notification.icon || 'fas fa-bell'}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-text">${notification.message}</p>
                                        <span class="notification-time">${notification.time}</span>
                                    </div>
                                    ${!notification.read_at ? '<div class="unread-dot"></div>' : ''}
                                </div>
                            `).join('');
                        } else {
                            notificationsList.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>Aucune notification</p>
                                </div>
                            `;
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    notificationsList.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Erreur de chargement</p>
                        </div>
                    `;
                }
            }

            window.markNotificationRead = async function(id, element) {
                if (element.classList.contains('unread')) {
                    try {
                        const response = await fetch(`/client/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            },
                            credentials: 'same-origin'
                        });

                        if (response.ok) {
                            element.classList.remove('unread');
                            const dot = element.querySelector('.unread-dot');
                            if (dot) dot.remove();

                            document.dispatchEvent(new Event('notificationRead'));
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                    }
                }
            }
        }
    </script>

    <!-- Page Transitions System - CORRIGÉ avec vérification de session -->
    <script>
        function initPageTransitions() {
            const transitionOverlay = document.getElementById('pageTransitionOverlay');

            document.querySelectorAll('.page-transition').forEach(link => {
                link.addEventListener('click', async function(e) {
                    if (this.target === '_blank' ||
                        this.href.includes('logout') ||
                        this.getAttribute('href').startsWith('#') ||
                        this.getAttribute('href') === 'javascript:void(0)') {
                        return;
                    }

                    e.preventDefault();
                    const href = this.href;

                    // Vérifier la session avant de naviguer
                    try {
                        const sessionCheck = await fetch('/api/session-check', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (!sessionCheck.ok) {
                            // Session invalide, rediriger vers login
                            window.location.href = '/login';
                            return;
                        }
                        
                        const sessionData = await sessionCheck.json();
                        if (!sessionData.authenticated) {
                            window.location.href = '/login';
                            return;
                        }
                    } catch (error) {
                        console.error('Session check error:', error);
                        // Continuer quand même, la page cible vérifiera l'auth
                    }

                    if (transitionOverlay) {
                        transitionOverlay.classList.add('active');
                    }

                    setTimeout(() => {
                        window.location.href = href;
                    }, 600);
                });
            });

            window.addEventListener('load', () => {
                if (transitionOverlay) {
                    transitionOverlay.classList.remove('active');
                }
            });

            window.addEventListener('pageshow', () => {
                if (transitionOverlay) {
                    transitionOverlay.classList.remove('active');
                }
            });
        }
    </script>

    <!-- Event Listeners -->
    <script>
        function initEventListeners() {
            toastSystem = new ToastSystem();

            window.toast = {
                success: (title, message, options) => toastSystem.success(title, message, options),
                error: (title, message, options) => toastSystem.error(title, message, options),
                warning: (title, message, options) => toastSystem.warning(title, message, options),
                info: (title, message, options) => toastSystem.info(title, message, options),
                primary: (title, message, options) => toastSystem.primary(title, message, options)
            };

            const logoutTrigger = document.getElementById('logoutTrigger');
            if (logoutTrigger) {
                logoutTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                    logoutModal.show();
                });
            }

            const sidebarLogoutBtn = document.getElementById('sidebarLogoutBtn');
            if (sidebarLogoutBtn) {
                sidebarLogoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
                    logoutModal.show();
                });
            }

            const confirmLogout = document.getElementById('confirmLogout');
            if (confirmLogout) {
                confirmLogout.addEventListener('click', () => {
                    document.getElementById('logout-form').submit();
                });
            }

            $(document).ajaxError(function(event, xhr, settings) {
                if (settings.silent) return;

                let message = 'Une erreur est survenue';
                if (xhr.status === 0) {
                    message = 'Erreur de connexion. Vérifiez votre connexion Internet.';
                } else if (xhr.status === 401) {
                    message = 'Session expirée. Veuillez vous reconnecter.';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 3000);
                } else if (xhr.status === 403) {
                    message = 'Accès refusé.';
                } else if (xhr.status === 422) {
                    message = 'Données invalides. Veuillez vérifier les informations.';
                }

                if (toastSystem) {
                    toastSystem.error('Erreur', message);
                }
            });

            $(document).ajaxComplete(function(event, xhr, settings) {
                if (settings.silent || !xhr.responseJSON) return;

                const data = xhr.responseJSON;
                if (data.message && toastSystem) {
                    const type = data.success ? 'success' : 'error';
                    const title = data.success ? 'Succès' : 'Erreur';

                    toastSystem[type](title, data.message, {
                        duration: data.success ? 3000 : 5000
                    });
                }
            });

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && link.target !== '_blank' &&
                    !link.classList.contains('page-transition') &&
                    link.href.startsWith('http') &&
                    !link.href.includes(window.location.hostname)) {
                    e.preventDefault();
                    if (confirm('Vous allez quitter l\'application. Continuer ?')) {
                        window.open(link.href, '_blank', 'noopener noreferrer');
                    }
                }
            });

            window.testToasts = () => {
                toast.success('Transaction réussie', 'Votre dépôt a été traité');
                setTimeout(() => toast.error('Erreur de paiement', 'Le paiement a été refusé'), 1000);
                setTimeout(() => toast.warning('Solde faible', 'Votre solde est inférieur à 5 000 F'), 2000);
                setTimeout(() => toast.info('Mise à jour', 'Nouvelles fonctionnalités disponibles'), 3000);
                setTimeout(() => toast.primary('Notification', 'Nouveau message reçu'), 4000);
            };

            window.simulateOffline = () => {
                const event = new Event('offline');
                window.dispatchEvent(event);
            };

            window.simulateOnline = () => {
                const event = new Event('online');
                window.dispatchEvent(event);
            };
        }
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ route('service-worker') }}', {
                    scope: '/'
                }).then(function(registration) {
                    console.log('Service Worker enregistré:', registration.scope);
                }).catch(function(error) {
                    console.error('Erreur ServiceWorker:', error);
                });
            });
        }

        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            setTimeout(() => {
                if (deferredPrompt && toastSystem) {
                    toastSystem.primary('Installer l\'application',
                        'Pour une meilleure expérience, installez BHDM Client sur votre appareil.', {
                            duration: 8000,
                            actions: [{
                                text: 'Installer',
                                action: 'installPWA()'
                            }]
                        });
                }
            }, 10000);
        });

        window.installPWA = async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;

                if (outcome === 'accepted') {
                    if (toastSystem) {
                        toastSystem.success('Installation', 'L\'application sera installée bientôt');
                    }
                }

                deferredPrompt = null;
            }
        };
    </script>

    <!-- Global Modal Functions -->
    <script>
        window.showPinModal = function() {
            if (!navigator.onLine) {
                if (window.toast) {
                    window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
                }
                return;
            }

            const modal = document.getElementById('pinSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';

                const forms = modal.querySelectorAll('form');
                forms.forEach(form => form.reset());

                const firstInput = modal.querySelector('input');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 300);
                }
            } else {
                console.error('Modal PIN non trouvé');
                if (window.toast) {
                    window.toast.error('Erreur', 'Impossible d\'ouvrir la gestion du PIN');
                }
            }
        };

        window.showDepositModal = function() {
            if (!navigator.onLine) {
                if (window.toast) {
                    window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
                }
                return;
            }

            const modal = document.getElementById('depositSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        };

        window.showWithdrawModal = function() {
            if (!navigator.onLine) {
                if (window.toast) {
                    window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
                }
                return;
            }

            const balanceElement = document.getElementById('walletBalance');
            let walletBalance = 0;
            if (balanceElement) {
                const balanceText = balanceElement.textContent.replace(/\s/g, '');
                walletBalance = parseInt(balanceText) || 0;
            }

            if (walletBalance < 1000) {
                if (window.toast) {
                    window.toast.error('Solde insuffisant', 'Minimum 1 000 FCFA requis pour un retrait');
                }
                return;
            }

            const modal = document.getElementById('verifyPinSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                const pinInput = document.getElementById('quickPinInput');
                if (pinInput) {
                    pinInput.value = '';
                    pinInput.focus();
                }
            }
        };
    </script>
<script src="https://cdn.kkiapay.me/k.js"></script>
    @stack('scripts')
</body>

</html>