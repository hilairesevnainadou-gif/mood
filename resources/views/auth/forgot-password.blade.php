@extends('layouts.inscription-layout')

@section('title', 'Mot de passe oublié')

@section('body_class', 'mode-connexion')

@section('auth_nav_button')
    <a href="{{ route('login') }}" class="btn-auth-nav">
        <i class="fas fa-arrow-left"></i>
        <span>Retour à la connexion</span>
    </a>
@endsection

@section('page_styles')
    <style>
        /* ============================================
           CÔTÉ GAUCHE - INFO
        ============================================ */
        .forgot-info {
            max-width: 400px;
        }

        .forgot-info-title {
            font-family: var(--bh-font-heading);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            color: var(--bh-primary-dark);
        }

        .forgot-info-subtitle {
            font-size: 1.05rem;
            color: var(--bh-gray);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .forgot-features {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
        }

        .forgot-features li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            font-size: 0.95rem;
            color: var(--bh-gray-dark);
        }

        .forgot-features li i {
            color: var(--bh-primary);
            font-size: 1.125rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .forgot-info-help {
            padding: 1rem;
            background: var(--bh-light);
            border-radius: 10px;
            border-left: 3px solid var(--bh-accent);
        }

        .forgot-info-help p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--bh-gray-dark);
            line-height: 1.5;
        }

        .forgot-info-help i {
            color: var(--bh-primary);
        }

        .forgot-info-help a {
            color: var(--bh-primary);
            text-decoration: underline;
            font-weight: 500;
        }

        /* ============================================
           CÔTÉ DROIT - FORMULAIRE
        ============================================ */
        .forgot-form-wrapper {
            width: 100%;
            max-width: 380px;
            animation: fadeInUp 0.5s ease;
        }

        .forgot-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 8px 25px rgba(27, 90, 141, 0.25);
        }

        .forgot-form-wrapper h2 {
            font-family: var(--bh-font-heading);
            font-size: 1.5rem;
            color: var(--bh-primary-dark);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .forgot-form-desc {
            color: var(--bh-gray);
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* Alertes */
        .forgot-alert {
            border: none;
            border-radius: 8px;
            padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }

        .forgot-alert.alert-success {
            background: #d4edda;
            color: #155724;
        }

        .forgot-alert.alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .forgot-alert .btn-close {
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        /* Formulaire */
        .forgot-form .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        .forgot-form label {
            font-weight: 500;
            color: var(--bh-gray-dark);
            margin-bottom: 0.375rem;
            font-size: 0.9rem;
        }

        .forgot-form .input-group-text {
            background: var(--bh-light);
            border: 2px solid var(--bh-gray-light);
            border-right: none;
            color: var(--bh-primary);
            padding: 0.75rem;
        }

        .forgot-form .form-control {
            border: 2px solid var(--bh-gray-light);
            border-left: none;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .forgot-form .form-control:focus {
            border-color: var(--bh-primary);
            box-shadow: none;
        }

        .forgot-form .input-group:focus-within .input-group-text {
            border-color: var(--bh-primary);
        }

        .forgot-form .form-control.is-invalid {
            border-color: var(--bh-danger);
        }

        .invalid-feedback {
            font-size: 0.8rem;
        }

        /* Boutons */
        .btn-forgot-submit {
            background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-dark) 100%);
            border: none;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25);
            color: white;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-forgot-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(27, 90, 141, 0.35);
            color: white;
        }

        .btn-forgot-back {
            color: var(--bh-primary);
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: gap 0.3s ease;
        }

        .btn-forgot-back:hover {
            color: var(--bh-primary-dark);
            gap: 0.625rem;
            text-decoration: none;
        }

        /* Sécurité */
        .forgot-security {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--bh-gray-light);
            text-align: center;
            font-size: 0.8rem;
            color: var(--bh-gray);
        }

        .forgot-security i {
            color: var(--bh-success);
            margin-right: 0.375rem;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive spécifique */
        @media (max-width: 992px) {
            .forgot-info {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .forgot-form-wrapper {
                max-width: 100%;
            }

            .forgot-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .forgot-form-wrapper h2 {
                font-size: 1.35rem;
            }

            .btn-forgot-submit {
                padding: 1rem;
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('content_wrapper')
    <div class="auth-box">
        {{-- CÔTÉ GAUCHE --}}
        <div class="auth-box-left">
            <div class="forgot-info">
                <h2 class="forgot-info-title">Mot de passe oublié ?</h2>
                <p class="forgot-info-subtitle">
                    Pas de panique ! Nous allons vous aider à récupérer l'accès à votre compte.
                </p>
                
                <ul class="forgot-features">
                    <li>
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Email envoyé instantanément</span>
                    </li>
                    <li>
                        <i class="fas fa-shield-alt"></i>
                        <span>Processus sécurisé</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Lien valable 60 minutes</span>
                    </li>
                    <li>
                        <i class="fas fa-user-check"></i>
                        <span>Vérification d'identité</span>
                    </li>
                </ul>

                <div class="forgot-info-help">
                    <p>
                        <i class="fas fa-info-circle me-2"></i>
                        Vous n'avez pas reçu l'email ? Vérifiez votre spam ou 
                        <a href="{{ route('contact') }}">contactez-nous</a>.
                    </p>
                </div>
            </div>
        </div>

        {{-- CÔTÉ DROIT --}}
        <div class="auth-box-right">
            <div class="forgot-form-wrapper">
                <div class="forgot-icon">
                    <i class="fas fa-key"></i>
                </div>
                
                <h2>Réinitialisation</h2>
                <p class="forgot-form-desc">
                    Entrez votre adresse email pour recevoir le lien de réinitialisation.
                </p>

                {{-- MESSAGE DE SUCCÈS --}}
                @if (session('success'))
                    <div class="alert forgot-alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- MESSAGE D'ERREUR GÉNÉRAL --}}
                @if (session('error'))
                    <div class="alert forgot-alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- MESSAGE STATUS (pour Laravel Fortify/Breeze) --}}
                @if (session('status'))
                    <div class="alert forgot-alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- ERREURS DE VALIDATION --}}
                @if ($errors->any())
                    <div class="alert forgot-alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erreur !</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="forgot-form">
                    @csrf

                    <div class="mb-3">
                        <label for="email">Adresse email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-at"></i>
                            </span>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="votre@email.com"
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                autocomplete="email"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-forgot-submit">
                        <i class="fas fa-paper-plane me-2"></i>
                        Envoyer le lien
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="btn-forgot-back">
                            <i class="fas fa-arrow-left"></i>
                            Retour à la connexion
                        </a>
                    </div>
                </form>

                
            </div>
        </div>
    </div>
@endsection