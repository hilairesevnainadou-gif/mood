@extends('layouts.inscription-layout')

@section('title', 'Réinitialisation du mot de passe')
@section('body_class', 'mode-connexion')

@section('auth_nav_button')
    <a href="{{ route('login') }}" class="btn-auth-nav">
        <i class="fas fa-sign-in-alt"></i>
        <span class="d-none d-sm-inline">Connexion</span>
    </a>
@endsection

@section('page_styles')
<style>
/* ============================================
   RESET PASSWORD - Styles spécifiques
   ============================================ */

/* Container principal qui prend toute la hauteur disponible */
.auth-box {
    width: 100%;
    max-width: 1200px;
    display: flex;
    min-height: calc(100vh - 140px);
    max-height: calc(100vh - 100px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border-radius: 20px;
    overflow: hidden;
    background: white;
    margin: 20px auto;
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

.reset-info {
    max-width: 420px;
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.reset-info-hero {
    margin-bottom: 30px;
}

.reset-info-hero h1 {
    font-family: var(--bh-font-heading);
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 12px;
    color: white;
}

.reset-info-hero p {
    font-size: 1rem;
    line-height: 1.5;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
}

.reset-info-steps {
    margin-bottom: 30px;
}

.reset-step {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.reset-step-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.reset-step div strong {
    display: block;
    font-weight: 600;
    margin-bottom: 2px;
    color: white;
    font-size: 0.9rem;
}

.reset-step div span {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.75);
}

.reset-info-security {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 20px;
    border-left: 3px solid var(--bh-accent);
    margin-top: auto;
}

.reset-info-security > i {
    color: var(--bh-accent);
    font-size: 1.1rem;
    margin-bottom: 8px;
    opacity: 0.8;
}

.reset-info-security p {
    font-size: 0.9rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
}

/* ============================================
   RESET PASSWORD - CÔTÉ DROIT (FORMULAIRE)
   ============================================ */
.auth-box-right {
    flex: 1;
    background: white;
    padding: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
}

.reset-form-wrapper {
    width: 100%;
    max-width: 380px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* LOGO MOBILE/TABLETTE */
.reset-logo-mobile {
    display: none;
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--bh-gray-light);
}

.reset-logo-mobile img {
    height: 50px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
}

.reset-logo-mobile .logo-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--bh-primary);
}

.reset-logo-mobile .logo-fallback i {
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

.reset-form-header {
    text-align: center;
    margin-bottom: 20px;
}

.reset-form-header h2 {
    font-family: var(--bh-font-heading);
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--bh-primary-dark);
    margin-bottom: 6px;
}

.reset-form-header p {
    color: var(--bh-gray);
    margin: 0;
    font-size: 0.9rem;
}

/* Alertes */
.reset-alert {
    border-radius: 8px;
    border: none;
    padding: 10px 14px;
    margin-bottom: 16px;
    font-size: 0.85rem;
}

.reset-alert.alert-danger {
    background: #fef2f2;
    color: #dc2626;
}

.reset-alert.alert-success {
    background: #f0fdf4;
    color: #16a34a;
}

/* Formulaire */
.reset-form .form-group {
    margin-bottom: 1rem;
}

.reset-form label {
    font-weight: 600;
    color: var(--bh-gray-dark);
    margin-bottom: 5px;
    font-size: 0.85rem;
}

.reset-form .input-group-text {
    background: var(--bh-light);
    border: 2px solid var(--bh-gray-light);
    border-right: none;
    color: var(--bh-gray);
    padding: 10px 12px;
    font-size: 0.9rem;
}

.reset-form .form-control {
    border: 2px solid var(--bh-gray-light);
    border-left: none;
    padding: 10px 12px;
    font-size: 0.95rem;
    border-radius: 0 8px 8px 0;
    height: auto;
}

.reset-form .form-control:focus {
    border-color: var(--bh-primary);
    box-shadow: none;
}

.reset-form .input-group:focus-within .input-group-text {
    border-color: var(--bh-primary);
    color: var(--bh-primary);
}

.reset-form .btn-outline-secondary {
    border: 2px solid var(--bh-gray-light);
    border-left: none;
    color: var(--bh-gray);
    border-radius: 0 8px 8px 0;
    padding: 10px 12px;
}

.reset-form .btn-outline-secondary:hover {
    background: var(--bh-light);
    color: var(--bh-primary);
}

.password-strength {
    margin-top: 5px;
    height: 4px;
    background: var(--bh-gray-light);
    border-radius: 2px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.password-strength-bar.weak { width: 33%; background: var(--bh-danger); }
.password-strength-bar.medium { width: 66%; background: var(--bh-warning); }
.password-strength-bar.strong { width: 100%; background: var(--bh-success); }

.password-strength-text {
    font-size: 0.75rem;
    margin-top: 4px;
    color: var(--bh-gray);
}

.reset-requirements {
    background: var(--bh-light);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 1rem;
    font-size: 0.8rem;
}

.reset-requirements ul {
    margin: 0;
    padding-left: 1.2rem;
    color: var(--bh-gray);
}

.reset-requirements li {
    margin-bottom: 4px;
}

.reset-requirements li.valid {
    color: var(--bh-success);
}

.reset-requirements li.valid::marker {
    content: "✓ ";
    color: var(--bh-success);
}

.btn-reset-submit {
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

.btn-reset-submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(27, 90, 141, 0.4);
    color: white;
}

.btn-reset-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.reset-divider {
    display: flex;
    align-items: center;
    margin: 16px 0;
    color: var(--bh-gray);
    font-size: 0.8rem;
}

.reset-divider::before,
.reset-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--bh-gray-light);
}

.reset-divider span {
    padding: 0 12px;
}

.btn-reset-secondary {
    width: 100%;
    margin-bottom: 0.75rem;
    padding: 10px;
    font-weight: 500;
    border-radius: 10px;
    font-size: 0.9rem;
}

.btn-reset-link {
    color: var(--bh-gray);
    text-decoration: none;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-reset-link:hover {
    color: var(--bh-primary);
    gap: 0.75rem;
    text-decoration: none;
}

/* Token invalide message */
.token-invalid {
    text-align: center;
    padding: 2rem;
}

.token-invalid i {
    font-size: 4rem;
    color: var(--bh-danger);
    margin-bottom: 1rem;
}

.token-invalid h3 {
    font-family: var(--bh-font-heading);
    color: var(--bh-primary-dark);
    margin-bottom: 0.5rem;
}

.token-invalid p {
    color: var(--bh-gray);
    margin-bottom: 1.5rem;
}

/* Responsive */
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
    
    .reset-form-wrapper {
        max-width: 100%;
        justify-content: flex-start;
        padding-top: 20px;
    }
    
    .reset-logo-mobile {
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
    
    .reset-form-header h2 {
        font-size: 1.4rem;
    }
    
    .reset-info-hero h1 {
        font-size: 1.6rem;
    }
}
</style>
@endsection

@section('content_wrapper')
<div class="auth-box">
    {{-- CÔTÉ GAUCHE - Desktop uniquement --}}
    <div class="auth-box-left">
        <div class="reset-info">
            <div class="reset-info-hero">
                <h1>Réinitialisez votre mot de passe</h1>
                <p>Créez un nouveau mot de passe sécurisé pour protéger votre compte BHDM.</p>
            </div>

            <div class="reset-info-steps">
                <div class="reset-step">
                    <div class="reset-step-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <strong>Sécurité renforcée</strong>
                        <span>Minimum 8 caractères avec chiffres et symboles</span>
                    </div>
                </div>
                <div class="reset-step">
                    <div class="reset-step-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <strong>Token temporaire</strong>
                        <span>Ce lien expire dans 60 minutes</span>
                    </div>
                </div>
                <div class="reset-step">
                    <div class="reset-step-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <strong>Confirmation</strong>
                        <span>Un email vous sera envoyé après changement</span>
                    </div>
                </div>
            </div>

            <div class="reset-info-security">
                <i class="fas fa-info-circle"></i>
                <p>Après réinitialisation, vous serez automatiquement connecté à votre compte. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
            </div>
        </div>
    </div>

    {{-- CÔTÉ DROIT - Formulaire --}}
    <div class="auth-box-right">
        <div class="reset-form-wrapper">
            {{-- LOGO MOBILE/TABLETTE --}}
            <div class="reset-logo-mobile">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="BHDM Logo" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <div class="logo-fallback" style="display: none;">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>BHDM</span>
                </div>
            </div>

            <div class="reset-form-header">
                <h2>Nouveau mot de passe</h2>
                <p>Entrez votre email et créez un mot de passe sécurisé</p>
            </div>

            {{-- Messages --}}
            @if(session('error'))
                <div class="alert reset-alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert reset-alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert reset-alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Vérification si token invalide --}}
            @if(isset($tokenInvalid) && $tokenInvalid)
                <div class="token-invalid">
                    <i class="fas fa-times-circle"></i>
                    <h3>Lien invalide ou expiré</h3>
                    <p>Ce lien de réinitialisation n'est plus valide. Veuillez demander un nouveau lien.</p>
                    <a href="{{ route('password.forgot') }}" class="btn btn-primary">
                        <i class="fas fa-redo me-2"></i>Demander un nouveau lien
                    </a>
                </div>
            @else
                <form method="POST" action="{{ route('password.update') }}" class="reset-form" id="resetForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
                                   class="form-control" placeholder="votre@email.com" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password"
                                   class="form-control" placeholder="Nouveau mot de passe" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'toggleIcon1')" aria-label="Afficher/masquer le mot de passe">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="strengthText"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" placeholder="Confirmez le mot de passe" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')" aria-label="Afficher/masquer la confirmation">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="reset-requirements">
                        <ul>
                            <li id="req-length">Minimum 8 caractères</li>
                            <li id="req-number">Au moins un chiffre</li>
                            <li id="req-special">Au moins un caractère spécial</li>
                            <li id="req-match">Les mots de passe correspondent</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-reset-submit" id="resetBtn">
                        <span class="btn-text"><i class="fas fa-key me-2"></i>Réinitialiser le mot de passe</span>
                        <span class="btn-loader" style="display: none;"><i class="fas fa-spinner fa-spin me-2"></i>Traitement...</span>
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="btn-reset-link">
                        <i class="fas fa-arrow-left"></i>Retour à la connexion
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection

@push('scripts')
<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Validation de la force du mot de passe
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    // Mise à jour des indicateurs
    document.getElementById('req-length').classList.toggle('valid', password.length >= 8);
    document.getElementById('req-number').classList.toggle('valid', password.match(/[0-9]/));
    document.getElementById('req-special').classList.toggle('valid', password.match(/[^a-zA-Z0-9]/));
    
    // Mise à jour de la barre
    strengthBar.className = 'password-strength-bar';
    if (strength === 0) {
        strengthBar.style.width = '0';
        strengthText.textContent = '';
    } else if (strength === 1) {
        strengthBar.classList.add('weak');
        strengthText.textContent = 'Faible';
        strengthText.style.color = 'var(--bh-danger)';
    } else if (strength === 2) {
        strengthBar.classList.add('medium');
        strengthText.textContent = 'Moyen';
        strengthText.style.color = 'var(--bh-warning)';
    } else {
        strengthBar.classList.add('strong');
        strengthText.textContent = 'Fort';
        strengthText.style.color = 'var(--bh-success)';
    }
    
    checkMatch();
});

document.getElementById('password_confirmation').addEventListener('input', checkMatch);

function checkMatch() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const matchReq = document.getElementById('req-match');
    
    if (confirm.length > 0) {
        matchReq.classList.toggle('valid', password === confirm);
        matchReq.textContent = password === confirm ? 'Les mots de passe correspondent' : 'Les mots de passe ne correspondent pas';
    } else {
        matchReq.classList.remove('valid');
        matchReq.textContent = 'Les mots de passe correspondent';
    }
}

// Soumission du formulaire
document.getElementById('resetForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
        return;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères.');
        return;
    }
    
    const btn = document.getElementById('resetBtn');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline';
    btn.disabled = true;
});
</script>
@endpush