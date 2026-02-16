@extends('layouts.inscription-layout')

@section('title', 'Connexion')
@section('body_class', 'mode-connexion')

@section('auth_nav_button')
    <a href="{{ route('register') }}" class="btn-auth-nav">
        <i class="fas fa-user-plus"></i>
        <span class="d-none d-sm-inline">Créer un compte</span>
    </a>
@endsection

@section('page_styles')
<style>
/* ============================================
   LOGIN - ADAPTATION TAILLE LAYOUT PRINCIPAL
   ============================================ */

/* Container principal qui prend toute la hauteur disponible */
.auth-box {
    width: 100%;
    max-width: 1200px;
    display: flex;
    min-height: calc(100vh - 140px); /* Hauteur viewport moins header et marges */
    max-height: calc(100vh - 100px); /* Limite max pour éviter débordement */
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border-radius: 20px;
    overflow: hidden;
    background: white;
    margin: 20px auto; /* Espace autour de la box */
}

/* Côté gauche - Info */
.auth-box-left {
    flex: 1;
    background: linear-gradient(160deg, #0a1f44 0%, #1b5a8d 50%, #0d2b4e 100%);
    padding: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
}

/* Pattern décoratif */
.auth-box-left::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.03;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

.login-info {
    max-width: 420px;
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.login-info-hero {
    margin-bottom: 30px;
}

.login-info-hero h1 {
    font-family: var(--bh-font-heading);
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 12px;
    color: white;
}

.login-info-hero p {
    font-size: 1rem;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
}

.login-info-features {
    margin-bottom: 30px;
}

.login-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.login-feature-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.login-feature-icon.success { background: rgba(74, 222, 128, 0.2); color: #4ade80; }
.login-feature-icon.warning { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
.login-feature-icon.info { background: rgba(96, 165, 250, 0.2); color: #60a5fa; }
.login-feature-icon.pink { background: rgba(244, 114, 182, 0.2); color: #f472b6; }

.login-feature div strong {
    display: block;
    font-weight: 600;
    margin-bottom: 2px;
    color: white;
    font-size: 0.9rem;
}

.login-feature div span {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.75);
}

.login-info-testimonial {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 20px;
    border-left: 3px solid var(--bh-accent);
    margin-top: auto; /* Pousse en bas si espace disponible */
}

.login-info-testimonial > i {
    color: var(--bh-accent);
    font-size: 1.1rem;
    margin-bottom: 8px;
    opacity: 0.8;
}

.login-info-testimonial p {
    font-size: 0.9rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.95);
    font-style: italic;
    margin-bottom: 12px;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 10px;
}

.testimonial-author i {
    font-size: 1.8rem;
    color: rgba(255, 255, 255, 0.5);
}

.testimonial-author div {
    display: flex;
    flex-direction: column;
}

.testimonial-author strong {
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
}

.testimonial-author span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.75rem;
}

/* ============================================
   LOGIN - CÔTÉ DROIT (FORMULAIRE)
   ============================================ */
.auth-box-right {
    flex: 1;
    background: white;
    padding: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto; /* Scroll si contenu trop grand */
}

.login-form-wrapper {
    width: 100%;
    max-width: 380px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* LOGO MOBILE/TABLETTE */
.login-logo-mobile {
    display: none;
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--bh-gray-light);
}

.login-logo-mobile img {
    height: 50px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
}

.login-logo-mobile .logo-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--bh-primary);
}

.login-logo-mobile .logo-fallback i {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.login-form-header {
    text-align: center;
    margin-bottom: 20px;
}

.login-form-header h2 {
    font-family: var(--bh-font-heading);
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--bh-primary-dark);
    margin-bottom: 6px;
}

.login-form-header p {
    color: var(--bh-gray);
    margin: 0;
    font-size: 0.9rem;
}

/* Alertes */
.login-alert {
    border-radius: 8px;
    border: none;
    padding: 10px 14px;
    margin-bottom: 16px;
    font-size: 0.85rem;
}

.login-alert.alert-danger {
    background: #fef2f2;
    color: #dc2626;
}

.login-alert.alert-success {
    background: #f0fdf4;
    color: #16a34a;
}

/* Formulaire */
.login-form .form-group {
    margin-bottom: 1rem;
}

.login-form label {
    font-weight: 600;
    color: var(--bh-gray-dark);
    margin-bottom: 5px;
    font-size: 0.85rem;
}

.login-form .input-group-text {
    background: var(--bh-light);
    border: 2px solid var(--bh-gray-light);
    border-right: none;
    color: var(--bh-gray);
    padding: 10px 12px;
    font-size: 0.9rem;
}

.login-form .form-control {
    border: 2px solid var(--bh-gray-light);
    border-left: none;
    padding: 10px 12px;
    font-size: 0.95rem;
    border-radius: 0 8px 8px 0;
    height: auto;
}

.login-form .form-control:focus {
    border-color: var(--bh-primary);
    box-shadow: none;
}

.login-form .input-group:focus-within .input-group-text {
    border-color: var(--bh-primary);
    color: var(--bh-primary);
}

.login-form .btn-outline-secondary {
    border: 2px solid var(--bh-gray-light);
    border-left: none;
    color: var(--bh-gray);
    border-radius: 0 8px 8px 0;
    padding: 10px 12px;
}

.login-form .btn-outline-secondary:hover {
    background: var(--bh-light);
    color: var(--bh-primary);
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    font-size: 0.85rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.login-options .form-check-label {
    color: var(--bh-gray);
    font-weight: 400;
}

.login-options a {
    color: var(--bh-primary);
    text-decoration: none;
    font-weight: 500;
}

.login-options a:hover {
    text-decoration: underline;
}

.btn-login-submit {
    background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-dark) 100%);
    border: none;
    padding: 12px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(27, 90, 141, 0.3);
    color: white;
    width: 100%;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.btn-login-submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(27, 90, 141, 0.4);
    color: white;
}

.btn-login-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.login-divider {
    display: flex;
    align-items: center;
    margin: 16px 0;
    color: var(--bh-gray);
    font-size: 0.8rem;
}

.login-divider::before,
.login-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--bh-gray-light);
}

.login-divider span {
    padding: 0 12px;
}

.btn-login-secondary {
    width: 100%;
    margin-bottom: 0.75rem;
    padding: 10px;
    font-weight: 500;
    border-radius: 10px;
    font-size: 0.9rem;
}

.btn-login-link {
    color: var(--bh-gray);
    text-decoration: none;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-login-link:hover {
    color: var(--bh-primary);
    gap: 0.75rem;
    text-decoration: none;
}

/* Sécurité footer */
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

/* ============================================
   RESPONSIVE ADAPTATIONS
   ============================================ */
@media (max-width: 1200px) {
    .auth-box {
        margin: 15px;
        min-height: calc(100vh - 120px);
        max-height: calc(100vh - 80px);
    }
}

@media (max-width: 992px) {
    body.mode-connexion {
        padding: 0;
        background: white;
    }
    
    body.mode-connexion .auth-header {
        display: none;
    }
    
    .auth-box {
        flex-direction: column;
        max-width: 500px;
        min-height: 100vh;
        max-height: none;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
    }
    
    .auth-box-left {
        display: none;
    }
    
    .auth-box-right {
        padding: 2rem 1.5rem;
        align-items: flex-start;
        overflow-y: visible;
    }
    
    .login-form-wrapper {
        max-width: 100%;
        justify-content: flex-start;
        padding-top: 20px;
    }
    
    .login-logo-mobile {
        display: block;
    }
    
    body.mode-connexion .main-content {
        padding-top: 0;
    }
}

@media (max-width: 576px) {
    .auth-box-right {
        padding: 1.5rem 1rem;
    }
    
    .login-form-header h2 {
        font-size: 1.4rem;
    }
    
    .login-info-hero h1 {
        font-size: 1.6rem;
    }
}
</style>
@endsection

@section('content_wrapper')
<div class="auth-box">
    {{-- CÔTÉ GAUCHE - Desktop uniquement --}}
    <div class="auth-box-left">
        <div class="login-info">
            <div class="login-info-hero">
                <h1>Bienvenue sur votre espace financier</h1>
                <p>Accédez à votre portefeuille digital, gérez vos demandes de financement et suivez vos projets en temps réel.</p>
            </div>

            <div class="login-info-features">
                <div class="login-feature">
                    <div class="login-feature-icon success">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <strong>Espace sécurisé</strong>
                        <span>Connexion cryptée et protégée</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon warning">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <strong>Accès instantané</strong>
                        <span>À vos fonds 24h/24, 7j/7</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <strong>Suivi en temps réel</strong>
                        <span>De tous vos projets et investissements</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon pink">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <strong>Support dédié</strong>
                        <span>Une équipe à votre écoute</span>
                    </div>
                </div>
            </div>

            <div class="login-info-testimonial">
                <i class="fas fa-quote-left"></i>
                <p>"Grâce à BHDM, j'ai pu développer mon entreprise agricole et créer 15 emplois dans ma communauté. Un service exceptionnel !"</p>
                <div class="testimonial-author">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <strong>— Fatou Diagne</strong>
                        <span>Entrepreneure, Thiès</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CÔTÉ DROIT - Formulaire --}}
    <div class="auth-box-right">
        <div class="login-form-wrapper">
            {{-- LOGO MOBILE/TABLETTE --}}
            <div class="login-logo-mobile">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="BHDM Logo" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <div class="logo-fallback" style="display: none;">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>BHDM</span>
                </div>
            </div>

            <div class="login-form-header">
                <h2>Connexion</h2>
                <p>Entrez vos identifiants pour accéder à votre compte</p>
            </div>

            {{-- Messages --}}
            @if(session('error'))
                <div class="alert login-alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert login-alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert login-alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="login-form" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control" placeholder="votre@email.com" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password"
                               class="form-control" placeholder="Votre mot de passe" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" aria-label="Afficher/masquer le mot de passe">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="login-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="{{ route('password.forgot') }}">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn btn-login-submit" id="loginBtn">
                    <span class="btn-text"><i class="fas fa-sign-in-alt me-2"></i>Se connecter</span>
                    <span class="btn-loader" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Connexion...</span>
                </button>
            </form>

            <div class="login-divider">
                <span>ou</span>
            </div>

            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-login-secondary">
                <i class="fas fa-user-plus me-2"></i>Créer un compte gratuitement
            </a>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="btn-login-link">
                    <i class="fas fa-arrow-left"></i>Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('loginBtn');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline';
    btn.disabled = true;
});

@auth
    window.location.href = '{{ route("client.dashboard") }}';
@endauth
</script>
@endpush