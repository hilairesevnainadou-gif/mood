<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion Admin | BHDM</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #0f172a;
            --admin-primary-light: #1e293b;
            --admin-accent: #3b82f6;
            --admin-accent-hover: #2563eb;
            --admin-danger: #ef4444;
            --admin-success: #10b981;
            --admin-bg: #f1f5f9;
            --admin-card: #ffffff;
            --admin-text: #1e293b;
            --admin-text-muted: #64748b;
            --admin-border: #e2e8f0;
            --admin-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.2);
            --admin-shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body.admin-auth-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--admin-primary) 0%, #1e1b4b 50%, #312e81 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Effet de fond animé */
        body.admin-auth-body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* Carte d'authentification */
        .admin-auth-card {
            background: var(--admin-card);
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--admin-shadow-lg);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .admin-auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .brand-logo img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .admin-auth-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--admin-text);
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .admin-auth-header p {
            color: var(--admin-text-muted);
            font-size: 0.95rem;
            margin: 0;
        }

        /* Alertes */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger {
            background: #fef2f2;
            color: var(--admin-danger);
        }

        .alert::before {
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 1.1rem;
        }

        .alert-danger::before {
            content: '\f071';
        }

        /* Formulaire */
        .admin-auth-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--admin-text);
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--admin-text-muted);
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid var(--admin-border);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s ease;
            background: #fff;
            color: var(--admin-text);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-control:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--admin-accent);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--admin-text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: var(--admin-text);
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--admin-border);
            border-radius: 6px;
            cursor: pointer;
            appearance: none;
            position: relative;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .form-check-input:checked {
            background: var(--admin-accent);
            border-color: var(--admin-accent);
        }

        .form-check-input:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #fff;
            font-size: 0.7rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--admin-text-muted);
            cursor: pointer;
            user-select: none;
        }

        /* Bouton */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:active::after {
            width: 300px;
            height: 300px;
        }

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--admin-border);
        }

        .auth-footer p {
            color: var(--admin-text-muted);
            font-size: 0.85rem;
            margin: 0;
        }

        .auth-footer a {
            color: var(--admin-accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: var(--admin-accent-hover);
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body.admin-auth-body {
                padding: 16px;
                background: var(--admin-primary);
            }

            .admin-auth-card {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .brand-logo {
                width: 64px;
                height: 64px;
            }

            .brand-logo img {
                width: 40px;
                height: 40px;
            }

            .admin-auth-header h1 {
                font-size: 1.5rem;
            }

            .admin-auth-header p {
                font-size: 0.9rem;
            }
        }

        /* Loading state */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="admin-auth-body">
    <div class="admin-auth-card">
        <div class="admin-auth-header">
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="BHDM">
            </div>
            <h1>Back Office Admin</h1>
            <p>Connectez-vous pour gérer la plateforme</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="admin-auth-form" id="loginForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="admin@bhdm.com"
                        required
                        autocomplete="email"
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword()" tabindex="-1">
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Se souvenir de moi
                    </label>
                </div>
                <a href="#" style="font-size: 0.85rem; color: var(--admin-accent); text-decoration: none; font-weight: 500;">
                    Mot de passe oublié ?
                </a>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                <span>Se connecter</span>
            </button>
        </form>

        <div class="auth-footer">
            <p>&copy; {{ date('Y') }} BHDM. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<span>Connexion...</span>';
        });

        // Prevent zoom on input focus (iOS)
        document.addEventListener('touchstart', function() {}, {passive: true});

        // Add ripple effect to button
        document.querySelector('.btn-login').addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                left: ${x}px;
                top: ${y}px;
                width: 20px;
                height: 20px;
                margin-left: -10px;
                margin-top: -10px;
            `;

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });

        // Add ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
