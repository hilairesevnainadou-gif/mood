/* ============================================
   LAYOUT PROFESSIONNEL BHDM CLIENT - PWA MOBILE COMPLET
   ============================================ */

:root {
    /* Nouvelle palette de couleurs professionnelle */
    --primary-50: #e8f4fd;
    --primary-100: #d1e9fb;
    --primary-200: #a3d3f7;
    --primary-300: #75bcf3;
    --primary-400: #47a6ef;
    --primary-500: #1b5a8d;
    --primary-600: #164a77;
    --primary-700: #113a61;
    --primary-800: #0d2a4b;
    --primary-900: #081a35;
    
    --secondary-50: #f8f9fa;
    --secondary-100: #f1f3f5;
    --secondary-200: #e9ecef;
    --secondary-300: #dee2e6;
    --secondary-400: #ced4da;
    --secondary-500: #6c757d;
    --secondary-600: #5a6268;
    --secondary-700: #495057;
    --secondary-800: #343a40;
    --secondary-900: #212529;
    
    /* Couleurs de statut */
    --success-50: #f0fdf4;
    --success-100: #dcfce7;
    --success-200: #bbf7d0;
    --success-300: #86efac;
    --success-400: #4ade80;
    --success-500: #22c55e;
    --success-600: #16a34a;
    --success-700: #15803d;
    --success-800: #166534;
    --success-900: #14532d;
    
    --warning-50: #fffbeb;
    --warning-100: #fef3c7;
    --warning-200: #fde68a;
    --warning-300: #fcd34d;
    --warning-400: #fbbf24;
    --warning-500: #f59e0b;
    --warning-600: #d97706;
    --warning-700: #b45309;
    --warning-800: #92400e;
    --warning-900: #78350f;
    
    --error-50: #fef2f2;
    --error-100: #fee2e2;
    --error-200: #fecaca;
    --error-300: #fca5a5;
    --error-400: #f87171;
    --error-500: #ef4444;
    --error-600: #dc2626;
    --error-700: #b91c1c;
    --error-800: #991b1b;
    --error-900: #7f1d1d;
    
    --info-50: #eff6ff;
    --info-100: #dbeafe;
    --info-200: #bfdbfe;
    --info-300: #93c5fd;
    --info-400: #60a5fa;
    --info-500: #3b82f6;
    --info-600: #2563eb;
    --info-700: #1d4ed8;
    --info-800: #1e40af;
    --info-900: #1e3a8a;
    
    /* Variables de layout PWA Mobile */
    --header-height: 60px;
    --sidebar-width: 85%;
    --sidebar-max-width: 320px;
    --bottom-nav-height: 60px;
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --border-radius-lg: 16px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 25px rgba(0,0,0,0.15);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.2);
    --transition-fast: 200ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reset et base pour PWA Mobile */
.client-body {
    font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    min-height: -webkit-fill-available;
    color: var(--secondary-800);
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

/* Support pour hauteur dynamique sur mobile */
html {
    height: -webkit-fill-available;
}

/* Preloader Professionnel PWA */
.app-preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    height: -webkit-fill-available;
    background: linear-gradient(135deg, var(--primary-900) 0%, var(--primary-700) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity var(--transition-slow);
}

.preloader-content {
    text-align: center;
    color: white;
    max-width: 300px;
    padding: 2rem;
}

.preloader-logo {
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
}

.preloader-logo svg {
    width: 80px;
    height: 80px;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
}

.preloader-text h2 {
    font-family: 'Rajdhani', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #fff 0%, #4aafff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.preloader-text p {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 1.5rem;
}

.preloader-progress {
    width: 100%;
}

.progress-track {
    width: 100%;
    height: 4px;
    background: rgba(255,255,255,0.2);
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #4aafff 0%, #ffffff 100%);
    border-radius: 2px;
    animation: progressLoad 1.5s ease-in-out forwards;
}

@keyframes progressLoad {
    0% { width: 0%; }
    100% { width: 100%; }
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Main App Container PWA */
.app-container {
    display: none;
    min-height: 100vh;
    min-height: -webkit-fill-available;
}

/* Header PWA Mobile */
.app-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--header-height);
    background: white;
    box-shadow: var(--shadow-md);
    z-index: 1002;
    border-bottom: 1px solid var(--secondary-200);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    padding: 0 1rem;
    max-width: 100%;
    margin: 0 auto;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.menu-trigger {
    background: none;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-500);
    transition: all var(--transition-fast);
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.menu-trigger:active {
    background: var(--primary-50);
    transform: scale(0.95);
}

.brand-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-img {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: contain;
}

.brand-text {
    display: flex;
    flex-direction: column;
}

.app-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-700);
    margin: 0;
    line-height: 1.2;
}

.app-status {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.7rem;
}

.status-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-indicator.online {
    background: var(--success-500);
    animation: pulse 2s infinite;
}

.status-indicator.offline {
    background: var(--error-500);
}

.status-text {
    color: var(--secondary-600);
    font-weight: 500;
    font-size: 0.7rem;
}

.status-text.online {
    color: var(--success-600);
}

.status-text.offline {
    color: var(--error-600);
}

/* Header Actions PWA */
.header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quick-actions {
    display: flex;
    gap: 0.25rem;
}

.action-btn {
    position: relative;
    background: none;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--secondary-700);
    transition: all var(--transition-fast);
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.action-btn:active {
    background: var(--secondary-100);
    transform: scale(0.95);
}

.badge-count {
    position: absolute;
    top: 4px;
    right: 4px;
    background: var(--error-500);
    color: white;
    font-size: 0.7rem;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    font-weight: 600;
    animation: pulse 2s infinite;
    border: 2px solid white;
}

/* User Profile PWA */
.user-profile {
    position: relative;
}

.profile-dropdown {
    position: relative;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.profile-avatar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem;
    border-radius: var(--border-radius);
    transition: background var(--transition-fast);
}

.profile-avatar:active {
    background: var(--secondary-100);
}

.avatar-img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.profile-info {
    display: none;
}

.profile-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--secondary-800);
}

.profile-role {
    font-size: 0.75rem;
    color: var(--secondary-600);
}

.dropdown-arrow {
    font-size: 0.8rem;
    color: var(--secondary-500);
    transition: transform var(--transition-fast);
}

.profile-dropdown.open .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    min-width: 200px;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--secondary-200);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all var(--transition-fast);
    z-index: 1001;
}

.profile-dropdown.open .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    color: var(--secondary-700);
    text-decoration: none;
    transition: all var(--transition-fast);
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.dropdown-item:active {
    background: var(--secondary-100);
    color: var(--primary-500);
}

.dropdown-item i {
    width: 20px;
    color: var(--secondary-600);
}

.dropdown-divider {
    height: 1px;
    background: var(--secondary-200);
    margin: 0.5rem 0;
}

.logout-item {
    color: var(--error-600);
}

.logout-item:active {
    color: var(--error-700);
    background: var(--error-50);
}

/* Search Overlay PWA */
.search-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    z-index: 1003;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-100%);
    transition: all var(--transition-base);
}

.search-overlay.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.search-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    padding-top: calc(var(--header-height) + 1rem);
    height: 100%;
}

.search-input-group {
    flex: 1;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary-500);
}

.search-input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: all var(--transition-fast);
    -webkit-appearance: none;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

.search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--secondary-500);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 50%;
    transition: all var(--transition-fast);
}

.search-clear:active {
    background: var(--secondary-200);
    color: var(--secondary-700);
}

.search-close {
    position: absolute;
    top: calc(var(--header-height) + 1rem);
    right: 1rem;
    background: none;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--secondary-700);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.search-close:active {
    background: var(--secondary-100);
    color: var(--primary-500);
}

/* SIDEBAR PWA MOBILE - CORRIGÉE */
.app-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    max-width: var(--sidebar-max-width);
    height: 100%;
    height: -webkit-fill-available;
    background: white;
    box-shadow: var(--shadow-xl);
    border-right: 1px solid var(--secondary-200);
    transform: translateX(-100%);
    transition: transform var(--transition-base);
    z-index: 1001;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding-top: var(--header-height); /* CORRECTION IMPORTANTE */
}

.app-sidebar.open {
    transform: translateX(0);
}

/* Overlay pour fermer la sidebar */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-base);
}

.app-sidebar.open + .sidebar-overlay {
    opacity: 1;
    visibility: visible;
}

/* SIDEBAR HEADER - CORRIGÉ */
.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--secondary-200);
    position: relative;
    /* Pas besoin de padding-top supplémentaire ici */
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-large {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

.user-avatar-large .avatar-img {
    width: 56px;
    height: 56px;
    font-size: 1.5rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-details {
    flex: 1;
    min-width: 0; /* Empêche le débordement */
}

.user-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--secondary-800);
    margin: 0 0 0.25rem 0;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-id {
    font-size: 0.85rem;
    color: var(--secondary-600);
    display: block;
    margin-bottom: 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--success-600);
}

.user-status .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--success-500);
    animation: pulse 2s infinite;
}

/* Bouton de fermeture positionné correctement */
.sidebar-close {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: none;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--secondary-600);
    cursor: pointer;
    transition: all var(--transition-fast);
    z-index: 10;
    -webkit-tap-highlight-color: transparent;
}

.sidebar-close:active {
    background: var(--secondary-100);
    color: var(--primary-500);
}

/* Sidebar Menu PWA */
.sidebar-menu {
    flex: 1;
    padding: 1.5rem 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.menu-section {
    margin-bottom: 2rem;
    padding: 0 1.5rem;
}

.section-title {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--secondary-500);
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
    padding-left: 0.5rem;
}

.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    margin-bottom: 0.5rem;
}

.menu-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    color: var(--secondary-700);
    text-decoration: none;
    border-radius: var(--border-radius-sm);
    transition: all var(--transition-fast);
    position: relative;
    -webkit-tap-highlight-color: transparent;
}

.menu-link:active {
    background: var(--secondary-100);
    color: var(--primary-500);
    transform: translateX(4px);
}

.menu-item.active .menu-link {
    background: linear-gradient(135deg, var(--primary-50) 0%, #f0f7ff 100%);
    color: var(--primary-600);
    border-left: 3px solid var(--primary-500);
    font-weight: 600;
}

.menu-icon {
    position: relative;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.menu-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: var(--error-500);
    color: white;
    font-size: 0.7rem;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    font-weight: 600;
    border: 2px solid white;
}

.menu-text {
    font-size: 1rem;
    font-weight: 500;
    flex: 1;
}

/* Sidebar Footer */
.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--secondary-200);
}

.app-info {
    font-size: 0.8rem;
    color: var(--secondary-600);
    text-align: center;
}

.version {
    margin-bottom: 0.25rem;
    font-weight: 500;
}

.copyright {
    opacity: 0.7;
}

/* Main Content PWA */
.app-main {
    margin-top: var(--header-height);
    min-height: calc(100vh - var(--header-height) - var(--bottom-nav-height));
    min-height: calc(-webkit-fill-available - var(--header-height) - var(--bottom-nav-height));
    padding-bottom: var(--bottom-nav-height);
}

.app-content {
    padding: 1rem;
    position: relative;
    min-height: 100%;
}

/* Toast Container PWA */
.toast-container {
    position: fixed;
    top: calc(var(--header-height) + 1rem);
    right: 1rem;
    left: 1rem;
    z-index: 9998;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    pointer-events: none;
}

.toast {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    border-left: 4px solid;
    transform: translateY(-20px);
    opacity: 0;
    transition: all var(--transition-base);
    pointer-events: auto;
    overflow: hidden;
    position: relative;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast.hiding {
    transform: translateY(-20px);
    opacity: 0;
}

.toast-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
}

.toast-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.toast-body {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.toast-message {
    color: var(--secondary-600);
    font-size: 0.875rem;
    line-height: 1.4;
}

.toast-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.toast-action {
    background: none;
    border: 1px solid currentColor;
    color: inherit;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
    -webkit-tap-highlight-color: transparent;
}

.toast-action:active {
    background: currentColor;
    color: white;
}

.toast-close {
    background: none;
    border: none;
    color: var(--secondary-400);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: var(--border-radius-sm);
    transition: all var(--transition-fast);
    flex-shrink: 0;
    -webkit-tap-highlight-color: transparent;
}

.toast-close:active {
    background: var(--secondary-100);
    color: var(--secondary-600);
}

.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--secondary-200);
    transform-origin: left;
    animation: toastProgress linear;
}

@keyframes toastProgress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}

/* Content Wrapper PWA */
.content-wrapper {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--secondary-200);
    min-height: calc(100vh - var(--header-height) - var(--bottom-nav-height) - 2rem);
    padding: 1.5rem;
    margin-bottom: 1rem;
}

/* Bottom Navigation (Mobile PWA) */
.app-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--bottom-nav-height);
    background: white;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-top: 1px solid var(--secondary-200);
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    color: var(--secondary-600);
    text-decoration: none;
    font-size: 0.75rem;
    transition: all var(--transition-fast);
    border-radius: var(--border-radius-sm);
    flex: 1;
    max-width: 80px;
    height: 100%;
    -webkit-tap-highlight-color: transparent;
}

.nav-item:active {
    color: var(--primary-500);
    background: var(--primary-50);
}

.nav-item.active {
    color: var(--primary-600);
    background: var(--primary-50);
}

.nav-item i {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.nav-item span {
    font-size: 0.7rem;
    font-weight: 500;
}

/* Notifications Panel PWA */
.notifications-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    height: -webkit-fill-available;
    z-index: 2000;
    pointer-events: none;
}

.notifications-panel.open {
    pointer-events: auto;
}

.panel-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    opacity: 0;
    transition: opacity var(--transition-base);
}

.notifications-panel.open .panel-overlay {
    opacity: 1;
}

.panel-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 90%;
    max-width: 400px;
    height: 100%;
    height: -webkit-fill-available;
    background: white;
    transform: translateX(100%);
    transition: transform var(--transition-base);
    display: flex;
    flex-direction: column;
}

.notifications-panel.open .panel-content {
    transform: translateX(0);
}

.panel-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--secondary-200);
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: calc(var(--header-height) + 1.5rem);
}

.panel-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.panel-actions {
    display: flex;
    gap: 0.5rem;
}

.panel-close {
    background: rgba(255,255,255,0.2);
    border: none;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    -webkit-tap-highlight-color: transparent;
}

.panel-close:active {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.panel-body {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 1rem;
}

.panel-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--secondary-200);
}

.view-all {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-500);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-fast);
    -webkit-tap-highlight-color: transparent;
}

.view-all:active {
    color: var(--primary-600);
    transform: translateX(4px);
}

/* Notifications List PWA */
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--secondary-50);
    border-radius: var(--border-radius);
    transition: all var(--transition-fast);
    border-left: 4px solid transparent;
    -webkit-tap-highlight-color: transparent;
}

.notification-item:active {
    background: var(--secondary-100);
    transform: translateX(4px);
}

.notification-item.unread {
    background: var(--primary-50);
    border-left-color: var(--primary-500);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-text {
    font-size: 0.875rem;
    color: var(--secondary-700);
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.75rem;
    color: var(--secondary-500);
}

.loading-notifications,
.empty-state,
.error-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--secondary-600);
}

.loading-notifications .spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--secondary-200);
    border-top-color: var(--primary-500);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.empty-state i,
.error-state i {
    font-size: 2.5rem;
    color: var(--secondary-400);
    margin-bottom: 1rem;
}

/* Logout Modal Customization PWA */
#logoutModal .modal-dialog {
    max-width: 90%;
    margin: 1rem auto;
}

#logoutModal .modal-content {
    border-radius: var(--border-radius-lg);
    border: none;
    box-shadow: var(--shadow-xl);
}

#logoutModal .modal-header {
    border-bottom: none;
    padding: 1.5rem 1.5rem 0;
    background: linear-gradient(135deg, var(--error-50) 0%, #fff 100%);
}

#logoutModal .modal-icon {
    width: 48px;
    height: 48px;
    background: var(--error-500);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
}

#logoutModal .modal-title {
    font-weight: 600;
    color: var(--error-700);
    font-size: 1.1rem;
}

#logoutModal .modal-body {
    padding: 1.5rem;
    text-align: center;
    font-size: 1rem;
    color: var(--secondary-700);
}

#logoutModal .modal-footer {
    border-top: 1px solid var(--secondary-200);
    padding: 1rem 1.5rem;
    flex-direction: column;
    gap: 0.5rem;
}

#logoutModal .modal-footer .btn {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
}

/* Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Page Transition Overlay PWA */
.page-transition-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    height: -webkit-fill-available;
    background: linear-gradient(135deg, var(--primary-900) 0%, var(--primary-700) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-base);
}

.page-transition-overlay.active {
    opacity: 1;
    visibility: visible;
}

.transition-content {
    text-align: center;
    color: white;
    max-width: 300px;
    padding: 2rem;
}

.three-dots-loader {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.three-dots-loader .dot {
    width: 12px;
    height: 12px;
    background: white;
    border-radius: 50%;
    animation: dotPulse 1.4s infinite ease-in-out;
}

.three-dots-loader .dot:nth-child(1) {
    animation-delay: -0.32s;
}

.three-dots-loader .dot:nth-child(2) {
    animation-delay: -0.16s;
}

.transition-text {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
    animation: fadeInOut 2s infinite;
}

@keyframes dotPulse {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes fadeInOut {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

/* PWA Standalone Mode Optimisations */
@media (display-mode: standalone) {
    .app-header {
        height: calc(var(--header-height) + env(safe-area-inset-top));
        padding-top: env(safe-area-inset-top);
    }
    
    .app-sidebar {
        padding-top: calc(var(--header-height) + env(safe-area-inset-top)); /* CORRECTION */
    }
    
    .panel-header {
        padding-top: calc(var(--header-height) + env(safe-area-inset-top) + 1.5rem);
    }
    
    .app-main {
        margin-top: calc(var(--header-height) + env(safe-area-inset-top));
        min-height: calc(100vh - var(--header-height) - var(--bottom-nav-height) - env(safe-area-inset-top));
        min-height: calc(-webkit-fill-available - var(--header-height) - var(--bottom-nav-height) - env(safe-area-inset-top));
    }
    
    .app-bottom-nav {
        height: calc(var(--bottom-nav-height) + env(safe-area-inset-bottom));
        padding-bottom: env(safe-area-inset-bottom);
    }
    
    .search-container {
        padding-top: calc(var(--header-height) + env(safe-area-inset-top) + 1rem);
    }
    
    .search-close {
        top: calc(var(--header-height) + env(safe-area-inset-top) + 1rem);
    }
    
    .toast-container {
        top: calc(var(--header-height) + env(safe-area-inset-top) + 1rem);
    }
}

/* Dark Mode Support PWA */
@media (prefers-color-scheme: dark) {
    .client-body {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #e0e0e0;
    }
    
    .app-header,
    .app-sidebar,
    .content-wrapper,
    .panel-content,
    .notification-item,
    .dropdown-menu {
        background: #2d2d2d;
        border-color: #404040;
        color: #e0e0e0;
    }
    
    .search-input,
    .search-overlay {
        background: #2d2d2d;
        border-color: #404040;
        color: #e0e0e0;
    }
    
    .search-input::placeholder {
        color: #888;
    }
    
    .app-bottom-nav {
        background: #2d2d2d;
        border-color: #404040;
    }
    
    .menu-link,
    .dropdown-item,
    .nav-item {
        color: #e0e0e0;
    }
    
    .menu-link:active,
    .dropdown-item:active,
    .nav-item:active {
        background: #3d3d3d;
    }
    
    .menu-item.active .menu-link {
        background: #1b5a8d33;
        border-color: var(--primary-500);
    }
    
    .toast {
        background: #2d2d2d;
        color: #e0e0e0;
    }
    
    .user-details h4 {
        color: #e0e0e0;
    }
    
    .user-id {
        color: #a0a0a0;
    }
}

/* Touch optimizations */
* {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    user-select: none;
}

input, textarea {
    -webkit-user-select: text;
    user-select: text;
}

/* Performance optimizations */
.app-sidebar,
.panel-content,
.content-wrapper {
    will-change: transform;
    backface-visibility: hidden;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-track {
    background: var(--secondary-100);
}

::-webkit-scrollbar-thumb {
    background: var(--secondary-300);
    border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary-400);
}

/* Optimisations responsive */
@media (max-width: 360px) {
    .sidebar-header {
        padding: 1rem;
    }
    
    .user-avatar-large .avatar-img {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
    
    .user-details h4 {
        font-size: 1rem;
    }
    
    .user-id {
        font-size: 0.8rem;
    }
    
    .menu-link {
        padding: 0.875rem;
    }
    
    .menu-text {
        font-size: 0.95rem;
    }
}

/* Correction pour les très grands écrans (tablettes) */
@media (min-width: 768px) {
    .app-sidebar {
        max-width: 280px;
    }
}

/* Masquer la navigation bottom sur les tablettes */
@media (min-width: 768px) and (max-height: 1024px) {
    .app-bottom-nav {
        display: none;
    }
    
    .app-main {
        padding-bottom: 0;
        min-height: calc(100vh - var(--header-height));
    }
    
    .content-wrapper {
        min-height: calc(100vh - var(--header-height) - 2rem);
    }
}