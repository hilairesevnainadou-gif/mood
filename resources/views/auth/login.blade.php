@extends('layouts.inscription-layout')

@section('title', 'Connexion')
@section('body_class', 'mode-connexion')

@section('auth_nav_button')
    <a href="{{ route('register') }}" class="btn-auth-nav">
        <i class="fas fa-user-plus"></i>
        <span>Créer un compte</span>
    </a>
@endsection

@section('footer')
    <!-- Pas de footer en mode connexion -->
@endsection

@section('content_wrapper')
<div class="login-container">
    <!-- Panneau gauche avec fond sombre pour lisibilité -->
    <div class="login-left-panel">
        <div class="login-left-content">
            <!-- Logo -->
            <div class="login-brand">
                <div class="login-brand-logo">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div>
                    <h2>BHDM</h2>
                    <p>Banque Humanitaire du Développement Mondial</p>
                </div>
            </div>

            <!-- Titre principal -->
            <div class="login-hero">
                <h1>Bienvenue sur votre espace client</h1>
                <p>Accédez à votre portefeuille digital, gérez vos demandes de financement et suivez vos projets en temps réel.</p>
            </div>

            <!-- Avantages -->
            <div class="login-features">
                <div class="feature-item">
                    <div class="feature-icon bg-success">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <strong>Espace sécurisé</strong>
                        <span>Connexion cryptée et protégée</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon bg-warning">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <strong>Accès instantané</strong>
                        <span>À vos fonds 24h/24, 7j/7</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon bg-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <strong>Suivi en temps réel</strong>
                        <span>De tous vos projets et investissements</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon bg-pink">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <strong>Support dédié</strong>
                        <span>Une équipe à votre écoute</span>
                    </div>
                </div>
            </div>

            <!-- Témoignage -->
            <div class="login-testimonial">
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

    <!-- Panneau droit avec formulaire -->
    <div class="login-right-panel">
        <div class="login-form-box">
            <!-- Header mobile -->
            <div class="mobile-header d-lg-none">
                <div class="mobile-logo">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>BHDM</span>
                </div>
            </div>

            <div class="form-header">
                <h2>Connexion</h2>
                <p>Entrez vos identifiants pour accéder à votre compte</p>
            </div>

            <!-- Messages -->
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control" placeholder="votre@email.com" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control" placeholder="Votre mot de passe" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="{{ route('password.forgot') }}" class="text-decoration-none">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login" id="loginBtn">
                    <span class="btn-normal"><i class="fas fa-sign-in-alt me-2"></i>Se connecter</span>
                    <span class="btn-loading" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Connexion...</span>
                </button>
            </form>

            <div class="divider"><span>ou</span></div>

            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 mb-3">
                <i class="fas fa-user-plus me-2"></i>Créer un compte gratuitement
            </a>

            <a href="{{ route('home') }}" class="btn btn-link w-100 text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
            </a>
        </div>
    </div>
</div>
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
    btn.querySelector('.btn-normal').style.display = 'none';
    btn.querySelector('.btn-loading').style.display = 'inline';
    btn.disabled = true;
});

@auth
    window.location.href = '{{ route("client.dashboard") }}';
@endauth
</script>
@endpush

@push('styles')
<style>
/* ============================================
   STYLES SPÉCIFIQUES LOGIN - À AJOUTER AU LAYOUT
   ============================================ */

/* Override pour le mode connexion */
body.mode-connexion {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

body.mode-connexion .auth-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin: 20px;
    width: calc(100% - 40px);
    position: relative;
    top: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* Conteneur principal login */
.login-container {
    flex: 1;
    display: flex;
    width: 100%;
    max-width: 1200px;
    min-height: 650px;
    margin: 20px auto;
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
}

/* Panneau gauche - FOND SOMBRE OPAQUE pour lisibilité */
.login-left-panel {
    flex: 1;
    background: linear-gradient(160deg, #0a1f44 0%, #1b5a8d 50%, #0d2b4e 100%);
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
    position: relative;
}

/* Effet décoratif subtil */
.login-left-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.4;
}

.login-left-content {
    position: relative;
    z-index: 1;
}

/* Brand */
.login-brand {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 40px;
}

.login-brand-logo {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bh-primary);
    font-size: 1.8rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.login-brand h2 {
    font-family: var(--bh-font-heading);
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.login-brand p {
    font-size: 0.85rem;
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
}

/* Hero section */
.login-hero {
    margin-bottom: 35px;
}

.login-hero h1 {
    font-family: var(--bh-font-heading);
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 15px;
    color: white;
}

.login-hero p {
    font-size: 1.1rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.85);
    max-width: 90%;
}

/* Features */
.login-features {
    margin-bottom: 35px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    color: white;
}

.feature-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.feature-icon.bg-success { background: rgba(74, 222, 128, 0.2); color: #4ade80; }
.feature-icon.bg-warning { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
.feature-icon.bg-info { background: rgba(96, 165, 250, 0.2); color: #60a5fa; }
.feature-icon.bg-pink { background: rgba(244, 114, 182, 0.2); color: #f472b6; }

.feature-item strong {
    display: block;
    font-weight: 600;
    margin-bottom: 2px;
    color: white;
}

.feature-item span {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.75);
}

/* Testimonial */
.login-testimonial {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 16px;
    padding: 25px;
    border-left: 4px solid var(--bh-accent);
}

.login-testimonial > i {
    color: var(--bh-accent);
    font-size: 1.5rem;
    margin-bottom: 10px;
    opacity: 0.7;
}

.login-testimonial p {
    font-size: 1rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.95);
    font-style: italic;
    margin-bottom: 15px;
}

.testimonial-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.testimonial-author i {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.5);
}

.testimonial-author div {
    display: flex;
    flex-direction: column;
}

.testimonial-author strong {
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
}

.testimonial-author span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
}

/* Panneau droit */
.login-right-panel {
    flex: 1;
    background: #ffffff;
    padding: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-form-box {
    width: 100%;
    max-width: 400px;
}

/* Mobile header */
.mobile-header {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.mobile-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bh-primary);
}

.mobile-logo i {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Form header */
.form-header {
    text-align: center;
    margin-bottom: 25px;
}

.form-header h2 {
    font-family: var(--bh-font-heading);
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--bh-primary-dark);
    margin-bottom: 8px;
}

.form-header p {
    color: #6b7280;
    margin: 0;
}

/* Formulaire */
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.input-group-text {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-right: none;
    padding: 12px 15px;
}

.form-control {
    border: 2px solid #e5e7eb;
    border-left: none;
    padding: 12px 15px;
    font-size: 1rem;
}

.form-control:focus {
    border-color: var(--bh-primary);
    box-shadow: none;
}

.btn-login {
    background: linear-gradient(135deg, var(--bh-primary) 0%, var(--bh-primary-light) 100%);
    border: none;
    padding: 14px;
    font-size: 1.05rem;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(27, 90, 141, 0.3);
}

.btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(27, 90, 141, 0.4);
}

/* Divider */
.divider {
    display: flex;
    align-items: center;
    margin: 25px 0;
    color: #9ca3af;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}

.divider span {
    padding: 0 15px;
    font-size: 0.9rem;
}

/* Alertes */
.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
}

.alert-success {
    background: #f0fdf4;
    color: #16a34a;
}

/* Responsive */
@media (max-width: 992px) {
    body.mode-connexion .auth-header {
        margin: 10px;
        width: calc(100% - 20px);
    }

    .login-container {
        flex-direction: column;
        max-width: 450px;
        margin: 10px auto;
        min-height: auto;
    }

    .login-left-panel {
        display: none;
    }

    .login-right-panel {
        padding: 30px 25px;
    }
}

@media (max-width: 576px) {
    .login-right-panel {
        padding: 25px 20px;
    }

    .form-header h2 {
        font-size: 1.6rem;
    }
}

.d-lg-none {
    display: none;
}

@media (max-width: 992px) {
    .d-lg-none {
        display: block !important;
    }
}
</style>
@endpush
