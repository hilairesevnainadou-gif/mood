<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Back Office Admin')</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <!-- Overlay pour mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-layout" id="adminLayout">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="admin-brand">
                    <div class="brand-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="BHDM" />
                    </div>
                    <div class="brand-text">
                        <strong>BHDM Admin</strong>
                        <span>Back-office</span>
                    </div>
                </div>
                <button class="sidebar-close" id="sidebarClose">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="admin-nav">
                <div class="nav-section">
                    <span class="nav-label">Principal</span>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Gestion</span>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="{{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <span>Transactions</span>
                    </a>

                    {{-- Financement avec sous-menu --}}
                    <div class="nav-item-dropdown {{ request()->routeIs('admin.funding.*') ? 'open' : '' }}">
                        <a href="#" class="nav-dropdown-toggle {{ request()->routeIs('admin.funding.*') ? 'active' : '' }}" onclick="toggleDropdown(event, 'fundingMenu')">
                            <div class="nav-link-content">
                                <i class="fa-solid fa-file-signature"></i>
                                <span>Financement</span>
                                @php
                                    $fundingCount = \App\Models\FundingRequest::whereIn('status', ['submitted', 'under_review', 'pending_committee', 'validated', 'documents_validated'])->count();
                                @endphp
                                @if($fundingCount > 0)
                                    <span class="badge bg-danger">{{ $fundingCount }}</span>
                                @endif
                            </div>
                            <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="nav-dropdown-menu {{ request()->routeIs('admin.funding.*') ? 'show' : '' }}" id="fundingMenu">
                            <a href="{{ route('admin.funding.pending-validation') }}" class="{{ request()->routeIs('admin.funding.pending-validation') ? 'active' : '' }}">
                                <span>En attente de validation</span>
                                @php
                                    $pendingValidationCount = \App\Models\FundingRequest::whereIn('status', ['submitted', 'under_review', 'pending_committee'])->count();
                                @endphp
                                @if($pendingValidationCount > 0)
                                    <span class="badge bg-warning text-dark">{{ $pendingValidationCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.funding.pending-transfers') }}" class="{{ request()->routeIs('admin.funding.pending-transfers') ? 'active' : '' }}">
                                <span>Transferts en attente</span>
                                @php
                                    $pendingTransferCount = \App\Models\FundingRequest::whereIn('status', ['documents_validated', 'transfer_pending'])->count();
                                @endphp
                                @if($pendingTransferCount > 0)
                                    <span class="badge bg-info">{{ $pendingTransferCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.funding.pending-payments') }}" class="{{ request()->routeIs('admin.funding.pending-payments') ? 'active' : '' }}">
                                <span>Paiements en attente</span>
                                @php
                                    $pendingPaymentCount = \App\Models\FundingRequest::whereIn('status', ['validated', 'pending_payment'])->count();
                                @endphp
                                @if($pendingPaymentCount > 0)
                                    <span class="badge bg-primary">{{ $pendingPaymentCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.documents.index') }}" class="{{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Documents</span>
                    </a>

                    {{-- Documents Requis --}}
                    <a href="{{ route('admin.required-documents.index') }}" class="{{ request()->routeIs('admin.required-documents.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Documents Requis</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Services</span>
                    <a href="{{ route('admin.trainings.index') }}" class="{{ request()->routeIs('admin.trainings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Formations</span>
                    </a>
                    <a href="{{ route('admin.support.index') }}" class="{{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-headset"></i>
                        <span>Support</span>
                        @php
                            $unreadSupportCount = \App\Models\SupportTicket::where('status', 'open')
                                ->whereHas('messages', function($q) {
                                    $q->where('is_admin', false)->where('read', false);
                                })->count();
                        @endphp
                        @if($unreadSupportCount > 0)
                            <span class="badge bg-danger">Nouveau</span>
                        @endif
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Système</span>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Rapports</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gear"></i>
                        <span>Paramètres</span>
                    </a>
                </div>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="user-preview">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        <span class="user-role">Super Administrateur</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="breadcrumb">
                        <span>BHDM</span>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>@yield('page-title', 'Administration')</span>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-actions">
                        <button class="action-btn" type="button" title="Rechercher" onclick="toggleSearch()">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button class="action-btn has-notification" type="button" title="Notifications" onclick="toggleNotifications()">
                            <i class="fa-solid fa-bell"></i>
                            @php
                                $notificationCount = $fundingCount + $unreadSupportCount;
                            @endphp
                            @if($notificationCount > 0)
                                <span class="notification-dot"></span>
                            @endif
                        </button>
                        <button class="action-btn" type="button" title="Messages" onclick="toggleMessages()">
                            <i class="fa-solid fa-envelope"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
                @include('admin.partials.flash')
                @yield('content')
            </main>

            <footer class="admin-footer">
                <span>&copy; {{ date('Y') }} BHDM. Tous droits réservés.</span>
                <span>Version 2.0.1</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion du menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const adminLayout = document.getElementById('adminLayout');

            // Ouvrir le menu
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    adminSidebar.classList.add('active');
                    sidebarOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Fermer le menu
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }

            // Fermer en cliquant sur l'overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }

            // Fermer en cliquant sur un lien (mobile)
            const navLinks = document.querySelectorAll('.admin-nav a:not(.nav-dropdown-toggle)');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        closeSidebar();
                    }
                });
            });

            // Fermer avec la touche Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            function closeSidebar() {
                adminSidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Toggle sidebar desktop (collapse)
            let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (isCollapsed && window.innerWidth > 1024) {
                adminLayout.classList.add('sidebar-collapsed');
            }

            // Double clic sur le toggle pour collapse desktop
            sidebarToggle.addEventListener('dblclick', function(e) {
                if (window.innerWidth > 1024) {
                    e.preventDefault();
                    adminLayout.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', adminLayout.classList.contains('sidebar-collapsed'));
                }
            });
        });

        // Toggle dropdown menu
        function toggleDropdown(event, menuId) {
            event.preventDefault();
            const menu = document.getElementById(menuId);
            const toggle = event.currentTarget;

            menu.classList.toggle('show');
            toggle.classList.toggle('open');
        }

        // Fonctions pour les boutons d'action
        function toggleSearch() {
            alert('Fonction de recherche à implémenter');
        }

        function toggleNotifications() {
            alert('Fonction de notifications à implémenter');
        }

        function toggleMessages() {
            alert('Fonction de messages à implémenter');
        }

        // Gestion du touch sur mobile
        document.addEventListener('touchstart', function() {}, {passive: true});

        // Empêcher le zoom sur double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
    @stack('scripts')
</body>
</html>
