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
        // NOUVELLE LOGIQUE : documents soumis (pending ou validated) débloquent l'accès
        $hasSubmittedRequiredDocuments = $user?->hasSubmittedRequiredDocuments() ?? false;
        // Information de validation pour affichage visuel uniquement
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
                <!-- Logo & Brand -->
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

                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Quick Actions -->
                    <div class="quick-actions">
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

        <!-- BANNIERES DE STATUT - LOGIQUE MIS À JOUR -->
        @if (! $hasSubmittedRequiredDocuments)
            <!-- Documents manquants - Bloquant -->
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
            <!-- Documents soumis en attente de validation - ACCÈS COMPLET -->
            <div class="status-banner" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%); border-left: 4px solid #10b981;">
                <div class="status-banner-content">
                    <div>
                        <span class="status-badge status-success">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Documents reçus
                        </span>
                        <p class="status-banner-message">
                            <strong>Bonne nouvelle !</strong> Vos documents sont en cours de validation. 
                            En attendant, vous pouvez utiliser <strong>toutes les fonctionnalités</strong> : créer des demandes, accéder à votre wallet et aux formations.
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
            <!-- Documents validés -->
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
            <!-- Side Navigation - ACCÈS COMPLET DÈS SOUMISSION -->
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
                    <!-- Navigation Principale - ACCÈS DÈS SOUMISSION -->
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
                                
                                <!-- WALLET accessible dès soumission -->
                                <li class="menu-item {{ Request::is('client/wallet*') ? 'active' : '' }}">
                                    <a href="{{ route('client.wallet.index') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-wallet"></i>
                                        </span>
                                        <span class="menu-text">Mon Portefeuille</span>
                                        @if(!$hasValidatedRequiredDocuments)
                                            <span class="badge bg-success ms-auto badge-pulse" style="font-size: 0.5rem;" title="Accessible pendant la validation">●</span>
                                        @endif
                                    </a>
                                </li>
                                
                                <!-- DEMANDES accessible dès soumission -->
                                <li class="menu-item {{ Request::is('client/requests*') ? 'active' : '' }}">
                                    <a href="{{ route('client.requests.index') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-file-contract"></i>
                                        </span>
                                        <span class="menu-text">Mes Demandes</span>
                                        @if($user->fundingRequests()->count() === 0 && $hasSubmittedRequiredDocuments)
                                            <span class="badge bg-success ms-auto badge-pulse" style="font-size: 0.6rem;">NEW</span>
                                        @endif
                                    </a>
                                </li>
                            @else
                                <!-- Seul le profil accessible sans documents -->
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

                    <!-- Contenus - ACCÈS DÈS SOUMISSION -->
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
                                    @if(!$hasSubmittedRequiredDocuments)
                                        <span class="badge bg-warning ms-auto" style="font-size: 0.6rem;">!</span>
                                    @elseif(!$hasValidatedRequiredDocuments)
                                        <span class="badge bg-info ms-auto" style="font-size: 0.5rem;">⏱</span>
                                    @else
                                        <span class="badge bg-success ms-auto" style="font-size: 0.5rem;">✓</span>
                                    @endif
                                </a>
                            </li>
                            
                            @if ($hasSubmittedRequiredDocuments)
                                <!-- FORMATIONS accessible dès soumission -->
                                <li class="menu-item {{ Request::is('client/training*') ? 'active' : '' }}">
                                    <a href="{{ route('client.trainings') }}" class="menu-link page-transition"
                                        role="menuitem">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-graduation-cap"></i>
                                        </span>
                                        <span class="menu-text">Formations</span>
                                        @if(!$hasValidatedRequiredDocuments)
                                            <span class="badge bg-success ms-auto badge-pulse" style="font-size: 0.5rem;" title="Accessible pendant la validation">●</span>
                                        @endif
                                    </a>
                                </li>
                            @else
                                <li class="menu-item disabled" style="opacity: 0.5;">
                                    <span class="menu-link" style="cursor: not-allowed;">
                                        <span class="menu-icon" aria-hidden="true">
                                            <i class="fas fa-graduation-cap"></i>
                                        </span>
                                        <span class="menu-text">Formations</span>
                                        <i class="fas fa-lock ms-auto" style="font-size: 0.7rem;"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Support - Toujours accessible -->
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

        <!-- Bottom Navigation (Mobile) - ACCÈS COMPLET DÈS SOUMISSION -->
        <nav class="app-bottom-nav" id="bottomNav" aria-label="Navigation mobile">
            @if ($hasSubmittedRequiredDocuments)
                <a href="{{ route('client.dashboard') }}"
                    class="nav-item page-transition {{ Request::is('client/dashboard*') ? 'active' : '' }}"
                    aria-label="Accueil">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    <span>Accueil</span>
                </a>
                
                <!-- WALLET accessible dès soumission -->
                <a href="{{ route('client.wallet.index') }}"
                    class="nav-item page-transition {{ Request::is('client/wallet*') ? 'active' : '' }}"
                    aria-label="Portefeuille">
                    <i class="fas fa-wallet" aria-hidden="true"></i>
                    <span>Portefeuille</span>
                </a>
                
                <!-- DEMANDES accessible dès soumission -->
                <a href="{{ route('client.requests.index') }}"
                    class="nav-item page-transition {{ Request::is('client/requests*') ? 'active' : '' }}"
                    aria-label="Demandes">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    <span>Demandes</span>
                    @if($user->fundingRequests()->count() === 0)
                        <span class="badge bg-success position-absolute" style="top: 5px; right: 5px; font-size: 0.5rem; padding: 2px 4px;">NEW</span>
                    @endif
                </a>
                
                <!-- FORMATIONS accessible dès soumission -->
                <a href="{{ route('client.trainings') }}"
                    class="nav-item page-transition {{ Request::is('client/training*') ? 'active' : '' }}"
                    aria-label="Formations">
                    <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    <span>Formations</span>
                </a>
            @else
                <!-- Version limitée sans documents -->
                <a href="{{ route('client.documents.index') }}"
                    class="nav-item page-transition {{ Request::is('client/documents*') ? 'active' : '' }}"
                    aria-label="Documents">
                    <i class="fas fa-folder" aria-hidden="true"></i>
                    <span>Documents</span>
                    <span class="badge bg-warning position-absolute" style="top: 5px; right: 5px; font-size: 0.5rem;">!</span>
                </a>
                
                <a href="{{ route('client.support.index') }}"
                    class="nav-item page-transition {{ Request::is('client/support*') ? 'active' : '' }}"
                    aria-label="Support">
                    <i class="fas fa-headset" aria-hidden="true"></i>
                    <span>Support</span>
                </a>
            @endif
            
            <a href="{{ route('client.profile') }}"
                class="nav-item page-transition {{ Request::is('client/profile*') ? 'active' : '' }}"
                aria-label="Profil">
                @if($user && $user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}?v={{ time() }}"
                         alt="Profil"
                         class="bottom-nav-avatar"
                         onerror="this.onerror=null; this.src='{{ asset('images/avatar.png') }}';">
                @else
                    <img src="{{ asset('images/avatar.png') }}"
                         alt="Profil"
                         class="bottom-nav-avatar">
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

    <!-- Logout Form -->
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
            initSessionKeepAlive();
        }
    </script>

    <!-- Session Keep Alive -->
    <script>
        function initSessionKeepAlive() {
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
            }, 300000);

            window.refreshCsrfToken = async function() {
                try {
                    const response = await fetch('/api/session-check', {
                        credentials: 'same-origin'
                    });
                    const data = await response.json();
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').content = data.csrf_token;
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

    <!-- Page Transitions System -->
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

                    try {
                        const sessionCheck = await fetch('/api/session-check', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            credentials: 'same-origin'
                        });

                        if (!sessionCheck.ok) {
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
                        'Pour une meilleure expérience, installez BHDM sur votre appareil.', {
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