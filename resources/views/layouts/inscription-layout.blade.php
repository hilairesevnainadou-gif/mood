<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="@yield('meta_description', 'BHDM - Banque Humanitaire du Développement Mondial')">
    <meta name="theme-color" content="#1b5a8d">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - BHDM</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- iOS PWA Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BHDM">

    <!-- Styles communs -->
    <style>
        :root {
            --bh-primary: #1b5a8d;
            --bh-primary-dark: #0a1f44;
            --bh-primary-light: #4aafff;
            --bh-accent: #ff5a58;
            --bh-accent-light: #ff8a8a;
            --bh-success: #28a745;
            --bh-warning: #ffc107;
            --bh-danger: #dc3545;
            --bh-info: #17a2b8;
            --bh-light: #f8f9fa;
            --bh-gray: #6c757d;
            --bh-gray-dark: #343a40;
            --bh-gray-light: #e9ecef;
            --bh-font-heading: 'Rajdhani', sans-serif;
            --bh-font-body: 'Poppins', sans-serif;
            --bh-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --bh-shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.12);
            --bh-border-radius: 12px;
            --bh-border-radius-lg: 20px;
            --bh-transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; scroll-behavior: smooth; height: 100%; }

        body {
            font-family: var(--bh-font-body);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            flex-direction: column;
        }

        /* ============================================
           MODE INSCRIPTION (Step Wizard)
           ============================================ */
        body.mode-inscription {
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            color: var(--bh-gray-dark);
        }

        /* ============================================
           MODE CONNEXION (Split Screen)
           ============================================ */
        body.mode-connexion {
            background: linear-gradient(135deg, #0a1f44 0%, #1b5a8d 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* ============================================
           HEADER COMMUN
           ============================================ */
        .auth-header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: var(--bh-shadow);
            position: sticky;
            top: 0;
            z-index: 1030;
            width: 100%;
        }

        body.mode-connexion .auth-header {
            position: fixed;
            top: 20px;
            left: 20px;
            right: 20px;
            width: auto;
            max-width: calc(1200px - 40px);
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 0.75rem 1.25rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 1040;
        }

        /* HEADER CACHÉ SUR MOBILE/TABLETTE EN MODE CONNEXION */
        @media (max-width: 992px) {
            body.mode-connexion .auth-header {
                display: none;
            }
            
            body.mode-connexion {
                padding: 0;
            }
        }

        .auth-header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
            transition: opacity 0.3s;
        }

        body.mode-connexion .brand-section { color: var(--bh-primary-dark); }

        /* LOGO IMAGE DANS LE HEADER */
        .brand-logo-img {
            height: 45px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
        }

        /* Fallback si image ne charge pas */
        .brand-logo-fallback {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-family: var(--bh-font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.85rem;
            color: var(--bh-gray);
            margin: 0;
        }

        .auth-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-auth-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--bh-primary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            border: 2px solid var(--bh-primary);
            transition: var(--bh-transition);
            background: transparent;
        }

        .btn-auth-nav:hover {
            background: var(--bh-primary);
            color: white;
            transform: translateY(-2px);
        }

        /* ============================================
           CONTENU PRINCIPAL
           ============================================ */
        .main-content {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        body.mode-connexion .main-content {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 100px; /* Espace pour le header fixe sur desktop */
            min-height: 100vh;
        }

        /* Sur mobile/tablette, pas de padding pour le header caché */
        @media (max-width: 992px) {
            body.mode-connexion .main-content {
                padding-top: 0;
            }
        }

        /* ============================================
           CONTAINER INSCRIPTION (Step Wizard)
           ============================================ */
        .inscription-wrapper {
            flex: 1;
            padding: 2rem 1rem;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: calc(100vh - 200px);
        }

        .inscription-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease;
        }

        .inscription-card {
            background: white;
            border-radius: var(--bh-border-radius-lg);
            box-shadow: var(--bh-shadow-lg);
            overflow: hidden;
            border: none;
        }

        .inscription-card-header {
            background: linear-gradient(135deg, var(--bh-primary-dark) 0%, var(--bh-primary) 100%);
            color: white;
            padding: 2.5rem;
            text-align: center;
        }

        .inscription-card-header h2 {
            font-family: var(--bh-font-heading);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .inscription-card-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .inscription-card-body { padding: 2.5rem; }

        /* Progression */
        .step-progress { margin-bottom: 2.5rem; }
        .step-progress-bar {
            height: 6px;
            background: var(--bh-gray-light);
            border-radius: 3px;
            position: relative;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .step-progress-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
            border-radius: 3px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-indicators {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .step-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
            cursor: pointer;
            transition: var(--bh-transition);
        }

        .step-number {
            width: 45px;
            height: 45px;
            background: white;
            border: 3px solid var(--bh-gray-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--bh-gray);
            margin-bottom: 0.5rem;
            transition: var(--bh-transition);
            font-size: 1.1rem;
        }

        .step-indicator.active .step-number {
            background: var(--bh-primary);
            border-color: var(--bh-primary);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(27, 90, 141, 0.3);
        }

        .step-indicator.completed .step-number {
            background: var(--bh-success);
            border-color: var(--bh-success);
            color: white;
        }

        .step-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--bh-gray);
            text-align: center;
            transition: var(--bh-transition);
        }

        .step-indicator.active .step-label { color: var(--bh-primary); font-weight: 600; }
        .step-indicator.completed .step-label { color: var(--bh-success); }

        /* Formulaire multi-étapes */
        .form-step {
            display: none;
            animation: slideIn 0.4s ease;
        }
        .form-step.active { display: block; }

        .step-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .step-header h3 {
            font-family: var(--bh-font-heading);
            font-size: 1.75rem;
            color: var(--bh-primary-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .step-header p { color: var(--bh-gray); font-size: 1.05rem; margin: 0; }

        .step-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid var(--bh-gray-light);
            gap: 1rem;
        }

        /* ============================================
           CONTAINER CONNEXION (Split Screen)
           ============================================ */
        .auth-box {
            width: 100%;
            max-width: 1200px;
            display: flex;
            min-height: 600px;
            max-height: 90vh;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            overflow: hidden;
            background: white;
            margin: 0 auto;
        }

        .auth-box-left {
            flex: 1;
            background: white;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bh-gray-dark);
        }

        .auth-box-right {
            flex: 1;
            background: white;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .auth-footer {
            background: var(--bh-primary-dark);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: auto;
            width: 100%;
        }

        body.mode-connexion .auth-footer {
            display: none;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--bh-transition);
            font-size: 0.95rem;
        }
        .footer-links a:hover { color: white; text-decoration: underline; }

        .footer-copyright {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
        }

        /* ============================================
           ANIMATIONS
           ============================================ */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateX(30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* ============================================
           RESPONSIVE GLOBAL
           ============================================ */
        @media (max-width: 1200px) {
            body.mode-connexion .auth-header {
                left: 20px;
                right: 20px;
                max-width: none;
            }
        }

        @media (max-width: 992px) {
            .auth-box {
                flex-direction: column;
                max-width: 500px;
                max-height: none;
            }
            
            .auth-box-left {
                display: none;
            }
            
            .auth-box-right {
                padding: 2rem;
            }
            
            body.mode-connexion {
                padding: 0;
                background: white;
            }
            
            body.mode-connexion .auth-header {
                position: relative;
                top: 0;
                left: 0;
                right: 0;
                margin-bottom: 20px;
                max-width: 500px;
            }
            
            body.mode-connexion .main-content {
                padding-top: 0;
            }
        }

        @media (max-width: 768px) {
            .auth-header { padding: 1rem; }
            .brand-logo-img { height: 40px; }
            .brand-text h1 { font-size: 1.25rem; }
            .brand-text p { display: none; }
            
            .inscription-card-header { padding: 1.5rem; }
            .inscription-card-header h2 { font-size: 1.5rem; }
            .inscription-card-body { padding: 1.5rem; }
            
            .step-indicators { gap: 0.5rem; }
            .step-label { font-size: 0.75rem; }
            .step-number { width: 35px; height: 35px; font-size: 0.9rem; }
            .step-navigation { flex-direction: column-reverse; }
            .btn-step { width: 100%; justify-content: center; }
            
            .auth-box-right {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            body.mode-connexion {
                padding: 0;
                background: white;
            }
            
            body.mode-connexion .auth-header {
                border-radius: 0;
                margin-bottom: 0;
                padding: 0.75rem 1rem;
            }
            
            .auth-box {
                border-radius: 0;
                box-shadow: none;
                min-height: calc(100vh - 70px);
            }
            
            .auth-box-right {
                padding: 1.5rem 1rem;
                align-items: flex-start;
            }
        }

        /* Utilitaires spécifiques au contenu */
        @yield('page_styles')
    </style>

    @stack('styles')
</head>
<body class="@yield('body_class', 'mode-inscription')">

    <!-- Header -->
    <header class="auth-header">
        <div class="auth-header-container">
            <a href="{{ url('/') }}" class="brand-section">
                <!-- Logo Image avec fallback -->
                <img src="{{ asset('images/logo.png') }}" 
                     alt="BHDM Logo" 
                     class="brand-logo-img"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <!-- Fallback icône si image indisponible -->
                <div class="brand-logo-fallback" style="display: none;">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                
                <!-- Texte caché sur mobile -->
                <div class="brand-text d-none d-md-block">
                    <h1>BHDM</h1>
                    <p>Banque Humanitaire du Développement Mondial</p>
                </div>
            </a>

            <div class="auth-links">
                @hasSection('auth_nav_button')
                    @yield('auth_nav_button')
                @else
                    <a href="{{ route('login') }}" class="btn-auth-nav">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="d-none d-sm-inline">Connexion</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="main-content">
        @yield('content_wrapper')
    </main>

    <!-- Footer (uniquement en mode inscription) -->
    @section('footer')
    <footer class="auth-footer">
        <div class="auth-footer-content">
            <div class="footer-links">
                <a href="{{ route('terms') }}">Conditions d'utilisation</a>
                <a href="{{ route('privacy') }}">Politique de confidentialité</a>
                <a href="{{ route('faq') }}">FAQ</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
            <p class="footer-copyright">
                &copy; {{ date('Y') }} BHDM - Banque Humanitaire du Développement Mondial. Tous droits réservés.
            </p>
        </div>
    </footer>
    @show

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>