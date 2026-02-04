@extends('layouts.inscription-layout')

@section('title', 'Inscription')

@section('content')

<style>
:root {
    --toast-success: #10b981;
    --toast-error: #ef4444;
    --toast-warning: #f59e0b;
    --toast-info: #3b82f6;
    --modal-overlay: rgba(0, 0, 0, 0.6);
}

.pwa-toast-container {
    position: fixed;
    top: 100px;
    right: 20px;
    z-index: 10000;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
}

.pwa-toast {
    background: white;
    color: var(--gray-800);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 300px;
    max-width: 400px;
    transform: translateX(120%);
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    pointer-events: auto;
    border-left: 4px solid;
    font-weight: 500;
}

.pwa-toast.show { transform: translateX(0); }
.pwa-toast.success { border-left-color: var(--toast-success); }
.pwa-toast.error { border-left-color: var(--toast-error); }
.pwa-toast.warning { border-left-color: var(--toast-warning); }
.pwa-toast.info { border-left-color: var(--toast-info); }

.pwa-toast-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.pwa-toast.success .pwa-toast-icon { background: #d1fae5; color: var(--toast-success); }
.pwa-toast.error .pwa-toast-icon { background: #fee2e2; color: var(--toast-error); }

.pwa-toast-content { flex: 1; font-size: 0.95rem; }

.swal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: var(--modal-overlay);
    backdrop-filter: blur(5px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.swal-overlay.show { opacity: 1; visibility: visible; }

.swal-modal {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: modalPop 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes modalPop {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.swal-icon { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
.swal-icon.error { background: #fee2e2; color: #dc2626; }
.swal-icon.warning { background: #fef3c7; color: #d97706; }
.swal-icon.success { background: #d1fae5; color: #059669; }

.swal-title { text-align: center; font-size: 1.5rem; font-weight: 700; color: var(--gray-800); margin-bottom: 0.75rem; }
.swal-text { text-align: center; color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.5; }
.swal-footer { display: flex; gap: 0.75rem; justify-content: center; }

.swal-button { padding: 0.875rem 2rem; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; font-size: 1rem; }
.swal-button-primary { background: var(--bh-primary); color: white; }
.swal-button-secondary { background: #f3f4f6; color: var(--gray-700); }

.field-error { border-color: #ef4444 !important; background-color: #fef2f2 !important; }
.field-success { border-color: #10b981 !important; background-color: #f0fdf4 !important; }

.error-tooltip {
    background: #ef4444; color: white; padding: 0.5rem 0.75rem;
    border-radius: 8px; font-size: 0.85rem; margin-top: 0.5rem;
    display: flex; align-items: center; gap: 0.5rem;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
@keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); } 20%, 40%, 60%, 80% { transform: translateX(5px); } }
.shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

.funding-type-option { cursor: pointer; transition: all 0.2s; border: 2px solid #e5e7eb; margin-bottom: 1rem; }
.funding-type-option:hover { border-color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.funding-type-option.active { border-color: var(--bh-primary); background: #f0f9ff; position: relative; }
.funding-type-option.active::before {
    content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
    position: absolute; top: 10px; right: 10px;
    width: 24px; height: 24px; background: var(--bh-primary); color: white;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem;
}

.funding-type-option.border-success { border-color: #d1fae5; }
.funding-type-option.border-success:hover { border-color: #a7f3d0; }
.funding-type-option.border-success.active { border-color: #10b981; background: #f0fdf4; }
.funding-type-option.border-success.active::before { background: #10b981; }

.badge-custom { background: #10b981; color: white; }
.badge-predefined { background: var(--bh-primary); color: white; }

.summary-card-dynamic {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    display: none;
}
.summary-card-dynamic.show { display: block; animation: slideDown 0.3s ease; }

.btn-loading { position: relative; color: transparent !important; pointer-events: none; }
.btn-loading::after {
    content: ''; position: absolute; width: 20px; height: 20px;
    top: 50%; left: 50%; margin-left: -10px; margin-top: -10px;
    border: 2px solid #ffffff; border-radius: 50%; border-top-color: transparent;
    animation: spinner 0.8s linear infinite;
}
@keyframes spinner { to { transform: rotate(360deg); } }

.recap-table { width: 100%; }
.recap-table td { padding: 0.75rem; border-bottom: 1px solid #e5e7eb; }
.recap-table tr:last-child td { border-bottom: none; }
.recap-table td:first-child { color: #6b7280; width: 40%; }
.recap-table td:last-child { font-weight: 600; color: #111827; }

.auth-switch {
    display: flex;
    justify-content: flex-end;
    margin: 0 0 1rem;
    font-size: 0.95rem;
    color: var(--bh-gray);
    gap: 0.5rem;
}

.auth-switch a {
    color: var(--bh-primary);
    font-weight: 600;
    text-decoration: none;
}

.auth-switch a:hover {
    text-decoration: underline;
}
</style>

<!-- Toast Container -->
<div class="pwa-toast-container" id="toastContainer"></div>

<!-- Modal -->
<div class="swal-overlay" id="swalModal">
    <div class="swal-modal">
        <div class="swal-icon" id="swalIcon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="swal-title" id="swalTitle">Erreur</div>
        <div class="swal-text" id="swalText">Message</div>
        <div class="swal-footer">
            <button class="swal-button swal-button-secondary" id="swalCancel" style="display:none;">Annuler</button>
            <button class="swal-button swal-button-primary" id="swalConfirm">OK</button>
        </div>
    </div>
</div>

<div class="auth-switch">
    <span>Déjà inscrit ?</span>
    <a href="{{ route('login') }}">Se connecter</a>
</div>

<div class="inscription-card">
    <div class="inscription-card-header">
        <h2>Créer votre compte</h2>
        <p>Rejoignez la BHDM et accédez à des solutions de financement adaptées à vos projets</p>
    </div>

    <div class="inscription-card-body">
        <!-- Progression -->
        <div class="step-progress">
            <div class="step-progress-bar">
                <div class="step-progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <div class="step-indicators">
                <div class="step-indicator active" data-step="1"><div class="step-number">1</div><span class="step-label">Profil</span></div>
                <div class="step-indicator" data-step="2"><div class="step-number">2</div><span class="step-label">Identité</span></div>
                <div class="step-indicator" data-step="3"><div class="step-number">3</div><span class="step-label" id="step3Label">Projet</span></div>
                <div class="step-indicator" data-step="4"><div class="step-number">4</div><span class="step-label">Validation</span></div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="inscriptionForm" novalidate>
            @csrf

            <!-- ÉTAPE 1 -->
            <div class="form-step active" data-step="1">
                <div class="step-header">
                    <h3><i class="fas fa-user-astronaut text-primary me-2"></i>Qui êtes-vous ?</h3>
                    <p>Sélectionnez votre profil pour personnaliser votre inscription</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 account-type-card {{ old('account_type') == 'particulier' ? 'selected border-primary' : '' }}"
                             data-type="particulier" onclick="selectProfile('particulier')">
                            <div class="card-body p-4 text-center position-relative">
                                <div class="account-type-select"><i class="fas fa-check"></i></div>
                                <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h4>Particulier</h4>
                                <p class="text-muted">Entrepreneur individuel ou freelance</p>
                            </div>
                        </div>
                        <input type="radio" name="account_type" value="particulier" id="type_particulier" class="d-none" {{ old('account_type') == 'particulier' ? 'checked' : '' }}>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 account-type-card {{ old('account_type') == 'entreprise' ? 'selected border-primary' : '' }}"
                             data-type="entreprise" onclick="selectProfile('entreprise')">
                            <div class="card-body p-4 text-center position-relative">
                                <div class="account-type-select"><i class="fas fa-check"></i></div>
                                <div class="bg-dark bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h4>Entreprise</h4>
                                <p class="text-muted">PME, Startup ou Organisation</p>
                            </div>
                        </div>
                        <input type="radio" name="account_type" value="entreprise" id="type_entreprise" class="d-none" {{ old('account_type') == 'entreprise' ? 'checked' : '' }}>
                    </div>
                </div>

                @error('account_type')
                    <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                @enderror
            </div>

            <!-- ÉTAPE 2 -->
            <div class="form-step" data-step="2">
                <div class="step-header">
                    <h3><i class="fas fa-id-card text-primary me-2"></i>Informations de contact</h3>
                    <p>Ces informations permettront de sécuriser votre compte</p>
                </div>

                <!-- Particulier -->
                <div id="formParticulier" class="{{ old('account_type') == 'entreprise' ? 'd-none' : '' }}">
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Identité personnelle</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required placeholder="Prénom et Nom">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required placeholder="exemple@email.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" required placeholder="+225 XX XX XX XX">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pays <span class="text-danger">*</span></label>
                                    <select name="country" class="form-select form-select-lg @error('country') is-invalid @enderror" required>
                                        <option value="">Choisir un pays</option>
                                        <option value="senegal" {{ old('country') == 'senegal' ? 'selected' : '' }}>Sénégal</option>
                                        <option value="cote_ivoire" {{ old('country') == 'cote_ivoire' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                        <option value="mali" {{ old('country') == 'mali' ? 'selected' : '' }}>Mali</option>
                                        <option value="burkina_faso" {{ old('country') == 'burkina_faso' ? 'selected' : '' }}>Burkina Faso</option>
                                        <option value="benin" {{ old('country') == 'benin' ? 'selected' : '' }}>Bénin</option>
                                        <option value="togo" {{ old('country') == 'togo' ? 'selected' : '' }}>Togo</option>
                                    </select>
                                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ville <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control form-control-lg @error('city') is-invalid @enderror"
                                           value="{{ old('city') }}" required>
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control form-control-lg @error('address') is-invalid @enderror"
                                           value="{{ old('address') }}" required placeholder="Quartier, rue...">
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h5 class="text-primary mb-3"><i class="fas fa-shield-alt me-2"></i>Sécurité</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mot de passe <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="pwd1" class="form-control form-control-lg @error('password') is-invalid @enderror" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pwd1')"><i class="fas fa-eye"></i></button>
                                    </div>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Confirmation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="pwd2" class="form-control form-control-lg" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pwd2')"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entreprise -->
                <div id="formEntreprise" class="{{ old('account_type') != 'entreprise' ? 'd-none' : '' }}">
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Responsable du compte</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fonction <span class="text-danger">*</span></label>
                                    <input type="text" name="position" class="form-control form-control-lg @error('position') is-invalid @enderror"
                                           value="{{ old('position') }}" required placeholder="DG, Manager...">
                                    @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email professionnel <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Téléphone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light mb-4 border-start border-4 border-primary">
                        <div class="card-body">
                            <h5 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Informations de l'entreprise</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nom de l'entreprise <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control form-control-lg @error('company_name') is-invalid @enderror"
                                           value="{{ old('company_name') }}" required>
                                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Forme juridique <span class="text-danger">*</span></label>
                                    <select name="company_type" class="form-select form-select-lg @error('company_type') is-invalid @enderror" required>
                                        <option value="">Sélectionner</option>
                                        <option value="SARL" {{ old('company_type') == 'SARL' ? 'selected' : '' }}>SARL</option>
                                        <option value="SA" {{ old('company_type') == 'SA' ? 'selected' : '' }}>SA</option>
                                        <option value="Entreprise Individuelle" {{ old('company_type') == 'Entreprise Individuelle' ? 'selected' : '' }}>Entreprise Individuelle</option>
                                        <option value="Startup" {{ old('company_type') == 'Startup' ? 'selected' : '' }}>Startup</option>
                                        <option value="Association" {{ old('company_type') == 'Association' ? 'selected' : '' }}>Association</option>
                                        <option value="ONG" {{ old('company_type') == 'ONG' ? 'selected' : '' }}>ONG</option>
                                    </select>
                                    @error('company_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Secteur d'activité <span class="text-danger">*</span></label>
                                    <select name="sector" class="form-select form-select-lg @error('sector') is-invalid @enderror" required>
                                        <option value="">Sélectionner</option>
                                        <option value="agriculture" {{ old('sector') == 'agriculture' ? 'selected' : '' }}>Agriculture</option>
                                        <option value="commerce" {{ old('sector') == 'commerce' ? 'selected' : '' }}>Commerce</option>
                                        <option value="services" {{ old('sector') == 'services' ? 'selected' : '' }}>Services</option>
                                        <option value="technologie" {{ old('sector') == 'technologie' ? 'selected' : '' }}>Technologie</option>
                                        <option value="industrie" {{ old('sector') == 'industrie' ? 'selected' : '' }}>Industrie</option>
                                    </select>
                                    @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pays <span class="text-danger">*</span></label>
                                    <select name="country" class="form-select form-select-lg @error('country') is-invalid @enderror" required>
                                        <option value="">Sélectionner</option>
                                        <option value="senegal" {{ old('country') == 'senegal' ? 'selected' : '' }}>Sénégal</option>
                                        <option value="cote_ivoire" {{ old('country') == 'cote_ivoire' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                        <option value="mali" {{ old('country') == 'mali' ? 'selected' : '' }}>Mali</option>
                                        <option value="burkina_faso" {{ old('country') == 'burkina_faso' ? 'selected' : '' }}>Burkina Faso</option>
                                        <option value="benin" {{ old('country') == 'benin' ? 'selected' : '' }}>Bénin</option>
                                        <option value="togo" {{ old('country') == 'togo' ? 'selected' : '' }}>Togo</option>
                                    </select>
                                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ville <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control form-control-lg @error('city') is-invalid @enderror"
                                           value="{{ old('city') }}" required>
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control form-control-lg @error('address') is-invalid @enderror"
                                           value="{{ old('address') }}" required placeholder="Quartier, rue...">
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h5 class="text-primary mb-3"><i class="fas fa-shield-alt me-2"></i>Sécurité</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mot de passe <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="pwd3" class="form-control form-control-lg @error('password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pwd3')"><i class="fas fa-eye"></i></button>
                                    </div>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Confirmation <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="pwd4" class="form-control form-control-lg" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pwd4')"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ÉTAPE 3: PROJET -->
            <div class="form-step" data-step="3" id="step3Container">
                <div class="step-header">
                    <h3><i class="fas fa-lightbulb text-primary me-2"></i>Votre projet</h3>
                    <p id="step3Subtitle">Définissez votre besoin de financement</p>
                </div>

                <div id="projectContent">
                    <!-- Contenu injecté par JS selon le type -->
                </div>
            </div>

            <!-- ÉTAPE 4 -->
            <div class="form-step" data-step="4">
                <div class="step-header">
                    <h3><i class="fas fa-check-circle text-success me-2"></i>Vérification</h3>
                    <p>Vérifiez vos informations avant de créer votre compte</p>
                </div>

                <div class="card mb-4 border-2 border-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Récapitulatif de votre inscription</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="recap-container" class="p-3">
                            <!-- Rempli dynamiquement -->
                        </div>
                    </div>
                </div>

                <div class="card border-danger border-2 mb-3">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" value="1"
                                   {{ old('terms') ? 'checked' : '' }} required style="width: 1.5em; height: 1.5em;">
                            <label class="form-check-label fw-bold ms-2" for="terms">
                                J'accepte les <a href="{{ route('terms') }}" target="_blank">Conditions Générales</a>
                                et la <a href="{{ route('privacy') }}" target="_blank">Politique de Confidentialité</a>
                                <span class="text-danger">*</span>
                            </label>
                            @error('terms')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="step-navigation">
                <button type="button" class="btn btn-outline-primary btn-lg" id="btnPrev" onclick="prevStep()">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </button>
                <div class="flex-grow-1 text-center text-muted" id="stepCounter">
                    Étape <span class="fw-bold text-primary">1</span> sur 4
                </div>
                <button type="button" class="btn btn-primary btn-lg px-5" id="btnNext" onclick="nextStep()">
                    Suivant<i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button type="submit" class="btn btn-success btn-lg px-5 d-none" id="btnSubmit">
                    <i class="fas fa-check me-2"></i>Créer mon compte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template pour particulier étape 3 -->
<template id="templateParticulierStep3">
    <div class="text-center py-5">
        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
            <i class="fas fa-check"></i>
        </div>
        <h4>Presque terminé !</h4>
        <p class="text-muted">En tant que particulier, vous pourrez définir vos projets après l'activation de votre compte.</p>
        <div class="alert alert-info mt-3 mx-auto" style="max-width: 500px;">
            <i class="fas fa-info-circle me-2"></i>
            Passez à l'étape suivante pour finaliser votre inscription.
        </div>
    </div>
</template>

<!-- Template pour entreprise étape 3 -->
<template id="templateEntrepriseStep3">
    <div class="pro-funding-list mb-4">
        <label class="form-label fw-bold mb-3"><i class="fas fa-hand-holding-usd me-2"></i>Choisissez votre formule (optionnel)</label>

        @if(isset($fundingTypes) && count($fundingTypes) > 0)
            @foreach($fundingTypes as $type)
            @php
                $amount = $type->amount ?? $type->grant_amount ?? 0;
                $fee = $type->registration_fee ?? 0;
                $duration = $type->duration_months ?? 12;
            @endphp
            <div class="card funding-type-option mb-3"
                 onclick="selectFundingOption({{ $type->id }}, false, {{ $amount }}, {{ $duration }}, {{ $fee }}, '{{ $type->name }}')"
                 id="funding-card-{{ $type->id }}" data-id="{{ $type->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $type->name }}</h6>
                            <p class="text-muted small mb-0">{{ $type->description }}</p>
                        </div>
                        <span class="badge badge-predefined">{{ number_format($amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex gap-3 text-muted small">
                        <span><i class="fas fa-calendar me-1"></i> {{ $duration }} mois</span>
                        <span><i class="fas fa-receipt me-1"></i> Frais: {{ number_format($fee, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucune offre disponible pour le moment.
            </div>
        @endif

        <div class="card funding-type-option border-success"
             onclick="selectFundingOption('custom', true, 0, 0, 0, 'Demande personnalisée')"
             id="funding-card-custom">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1 fw-bold text-success"><i class="fas fa-sliders-h me-2"></i>Demande personnalisée</h6>
                        <p class="text-muted small mb-0">Définissez vous-même le montant et la durée selon vos besoins spécifiques.</p>
                    </div>
                    <span class="badge badge-custom">Sur mesure</span>
                </div>
                <div class="d-flex gap-3 text-muted small">
                    <span><i class="fas fa-money-bill-wave me-1"></i> Montant libre</span>
                    <span><i class="fas fa-clock me-1"></i> Durée flexible</span>
                </div>
            </div>
        </div>

        <input type="hidden" name="funding_type_id" id="funding_type_input" value="">
        <input type="hidden" name="is_custom_funding" id="is_custom_input" value="0">
    </div>

    <div id="summary-predefined" class="summary-card-dynamic">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Formule choisie:</span>
            <strong id="summary-name">-</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Montant suggéré:</span>
            <strong id="summary-amount">-</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Durée:</span>
            <strong id="summary-duration">-</strong>
        </div>
        <div class="alert alert-warning mb-0 mt-2 py-2">
            <small><i class="fas fa-pencil-alt me-1"></i> Vous pouvez ajuster le montant et la durée ci-dessous</small>
        </div>
    </div>

    <div id="funding-details-form" class="card bg-light d-none">
        <div class="card-body">
            <h6 class="text-primary mb-3"><i class="fas fa-edit me-2"></i>Détails de votre demande</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Montant demandé (FCFA) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                        <input type="number" name="funding_needed" id="input_amount"
                               class="form-control form-control-lg" min="1000" step="1000">
                    </div>
                    <small class="text-muted">Minimum: 1 000 FCFA</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Durée (mois) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="number" name="duration" id="input_duration"
                               class="form-control form-control-lg" min="6" max="60">
                    </div>
                    <small class="text-muted">Entre 6 et 60 mois</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Nom du projet</label>
                    <input type="text" name="project_name" class="form-control" placeholder="Ex: Extension de l'unité de production">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Description du projet <span class="text-danger">*</span></label>
                    <textarea name="project_description" id="project_desc" rows="3"
                              class="form-control"
                              placeholder="Décrivez votre projet, vos objectifs et comment vous utiliserez les fonds..."
                              minlength="50"></textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted">Minimum 50 caractères</small>
                        <small class="text-muted"><span id="char-count" class="fw-bold">0</span> caractères</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note :</strong> Cette demande est facultative. Vous pouvez la compléter plus tard depuis votre espace membre.
    </div>
</template>

@endsection

@push('scripts')
<script>
let currentStep = 1;
let accountType = '{{ old('account_type', '') }}';
const totalSteps = 4;

// Vérifier si l'utilisateur est déjà connecté - REDIRECTION IMMÉDIATE
@auth
    window.location.href = '{{ route("client.dashboard") }}';
@endauth

// Toast notification
function showToast(message, type = 'error', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `pwa-toast ${type}`;

    const icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };

    toast.innerHTML = `
        <div class="pwa-toast-icon"><i class="fas fa-${icons[type]}"></i></div>
        <div class="pwa-toast-content">${message}</div>
        <button class="pwa-toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, duration);
}

// Modal
function showModal(options) {
    const modal = document.getElementById('swalModal');
    document.getElementById('swalIcon').innerHTML = `<i class="fas fa-${options.icon === 'error' ? 'times' : options.icon === 'warning' ? 'exclamation-triangle' : 'check'}"></i>`;
    document.getElementById('swalIcon').className = `swal-icon ${options.icon || 'info'}`;
    document.getElementById('swalTitle').textContent = options.title || 'Information';
    document.getElementById('swalText').textContent = options.text || '';

    const cancelBtn = document.getElementById('swalCancel');
    cancelBtn.style.display = options.showCancelButton ? 'inline-block' : 'none';
    cancelBtn.textContent = options.cancelButtonText || 'Annuler';
    document.getElementById('swalConfirm').textContent = options.confirmButtonText || 'OK';

    document.getElementById('swalConfirm').onclick = () => { modal.classList.remove('show'); if(options.onConfirm) options.onConfirm(); };
    cancelBtn.onclick = () => { modal.classList.remove('show'); if(options.onCancel) options.onCancel(); };

    modal.classList.add('show');
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Détection erreurs serveur pour afficher la bonne étape
    @if($errors->has('account_type'))
        currentStep = 1;
    @elseif($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('password') || $errors->has('position') || $errors->has('company_name') || $errors->has('company_type') || $errors->has('sector') || $errors->has('city') || $errors->has('address') || $errors->has('country'))
        currentStep = 2;
    @elseif($errors->has('funding_type_id') || $errors->has('funding_needed') || $errors->has('duration') || $errors->has('project_description'))
        currentStep = 3;
    @elseif($errors->has('terms'))
        currentStep = 4;
    @endif

    if (accountType) {
        selectProfile(accountType, false);
    } else {
        // Par défaut, masquer les deux formulaires jusqu'à sélection
        document.getElementById('formParticulier').classList.add('d-none');
        document.getElementById('formEntreprise').classList.add('d-none');
    }

    updateUI();
});

function selectProfile(type, autoNext = true) {
    accountType = type;

    // Réinitialiser tous les champs disabled
    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.disabled = false;
    });

    // Afficher/masquer les formulaires
    const particulierForm = document.getElementById('formParticulier');
    const entrepriseForm = document.getElementById('formEntreprise');

    if (type === 'particulier') {
        particulierForm.classList.remove('d-none');
        entrepriseForm.classList.add('d-none');
        document.getElementById('step3Label').textContent = 'Confirmation';

        // Désactiver les champs entreprise
        entrepriseForm.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
        });
    } else {
        particulierForm.classList.add('d-none');
        entrepriseForm.classList.remove('d-none');
        document.getElementById('step3Label').textContent = 'Projet';

        // Désactiver les champs particulier
        particulierForm.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
        });
    }

    // Mise à jour visuelle cartes
    document.querySelectorAll('.account-type-card').forEach(card => {
        card.classList.remove('selected', 'border-primary');
        card.querySelector('.account-type-select').style.opacity = '0';
    });

    const selected = document.querySelector(`[data-type="${type}"]`);
    if (selected) {
        selected.classList.add('selected', 'border-primary');
        selected.querySelector('.account-type-select').style.opacity = '1';
        document.getElementById(`type_${type}`).checked = true;
    }

    // Injecter le contenu de l'étape 3 selon le type
    injectStep3Content(type);

    if (autoNext && currentStep === 1) setTimeout(() => nextStep(), 400);
}

function injectStep3Content(type) {
    const container = document.getElementById('projectContent');
    container.innerHTML = '';

    if (type === 'particulier') {
        const template = document.getElementById('templateParticulierStep3');
        container.appendChild(template.content.cloneNode(true));
        // Désactiver les champs de projet pour particulier
        container.querySelectorAll('input, textarea').forEach(el => el.disabled = true);
    } else {
        const template = document.getElementById('templateEntrepriseStep3');
        container.appendChild(template.content.cloneNode(true));

        // Réinitialiser les sélections
        document.querySelectorAll('.funding-type-option').forEach(card => card.classList.remove('active'));
        document.getElementById('funding_type_input').value = '';
        document.getElementById('is_custom_input').value = '0';
        document.getElementById('funding-details-form').classList.add('d-none');
        document.getElementById('summary-predefined').classList.remove('show');

        // Compteur caractères
        const desc = document.getElementById('project_desc');
        if (desc) {
            desc.addEventListener('input', function() {
                const count = this.value.length;
                const counter = document.getElementById('char-count');
                if (counter) {
                    counter.textContent = count;
                    if (count >= 50) {
                        counter.classList.add('text-success');
                        counter.classList.remove('text-danger');
                    } else {
                        counter.classList.remove('text-success');
                        counter.classList.add('text-danger');
                    }
                }
            });
        }
    }
}

function selectFundingOption(id, isCustom, amount, duration, fee, name) {
    document.querySelectorAll('.funding-type-option').forEach(card => card.classList.remove('active'));
    document.getElementById(`funding-card-${id}`).classList.add('active');

    document.getElementById('funding_type_input').value = id;
    document.getElementById('is_custom_input').value = isCustom ? '1' : '0';

    const detailsForm = document.getElementById('funding-details-form');
    detailsForm.classList.remove('d-none');

    if (!isCustom) {
        document.getElementById('input_amount').value = amount;
        document.getElementById('input_duration').value = duration;

        document.getElementById('summary-name').textContent = name;
        document.getElementById('summary-amount').textContent = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        document.getElementById('summary-duration').textContent = duration + ' mois';
        document.getElementById('summary-predefined').classList.add('show');

        document.getElementById('summary-predefined').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        document.getElementById('input_amount').value = '';
        document.getElementById('input_duration').value = '';
        document.getElementById('summary-predefined').classList.remove('show');

        setTimeout(() => document.getElementById('input_amount').focus(), 300);
    }
}

function saveStep2Data() {
    window.userData = {};

    if (accountType === 'particulier') {
        const form = document.getElementById('formParticulier');
        window.userData.name = form.querySelector('input[name="name"]')?.value;
        window.userData.email = form.querySelector('input[name="email"]')?.value;
        window.userData.phone = form.querySelector('input[name="phone"]')?.value;
        window.userData.city = form.querySelector('input[name="city"]')?.value;
        window.userData.country = form.querySelector('select[name="country"]')?.value;
        window.userData.address = form.querySelector('input[name="address"]')?.value;
        window.userData.type = 'Particulier';
    } else {
        const form = document.getElementById('formEntreprise');
        window.userData.name = form.querySelector('input[name="name"]')?.value;
        window.userData.email = form.querySelector('input[name="email"]')?.value;
        window.userData.phone = form.querySelector('input[name="phone"]')?.value;
        window.userData.position = form.querySelector('input[name="position"]')?.value;
        window.userData.company_name = form.querySelector('input[name="company_name"]')?.value;
        window.userData.company_type = form.querySelector('select[name="company_type"]')?.value;
        window.userData.sector = form.querySelector('select[name="sector"]')?.value;
        window.userData.city = form.querySelector('input[name="city"]')?.value;
        window.userData.country = form.querySelector('select[name="country"]')?.value;
        window.userData.address = form.querySelector('input[name="address"]')?.value;
        window.userData.type = 'Entreprise';
    }
}

function nextStep() {
    // Validation étape 1
    if (currentStep === 1) {
        if (!accountType) {
            showModal({
                icon: 'warning',
                title: 'Profil requis',
                text: 'Veuillez sélectionner un type de compte (Particulier ou Entreprise) pour continuer.',
                confirmButtonText: 'Compris'
            });
            return;
        }
    }

    // Validation étape 2
    if (currentStep === 2) {
        if (!validerEtape2()) return;
        saveStep2Data();
    }

    // Validation étape 3 (uniquement pour entreprise et si un type est sélectionné)
    if (currentStep === 3 && accountType === 'entreprise') {
        const fundingType = document.getElementById('funding_type_input')?.value;
        if (fundingType && !validerEtape3()) return;
    }

    if (currentStep < totalSteps) {
        currentStep++;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function validerEtape2() {
    const container = document.getElementById(accountType === 'entreprise' ? 'formEntreprise' : 'formParticulier');
    const fields = container.querySelectorAll('[required]');
    let firstError = null;
    let errorCount = 0;

    fields.forEach(field => {
        // Ne valider que les champs non disabled
        if (!field.disabled && !field.value.trim()) {
            field.classList.add('is-invalid');
            if (!firstError) firstError = field;
            errorCount++;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validation email
    const emailField = container.querySelector('input[type="email"]');
    if (emailField && !emailField.disabled && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            emailField.classList.add('is-invalid');
            showToast('Veuillez entrer un email valide', 'error');
            if (!firstError) firstError = emailField;
            errorCount++;
        }
    }

    // Validation mot de passe
    const pwd = container.querySelector('input[name="password"]');
    const pwdConfirm = container.querySelector('input[name="password_confirmation"]');
    if (pwd && pwdConfirm && !pwd.disabled) {
        if (pwd.value.length < 8) {
            pwd.classList.add('is-invalid');
            showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
            if (!firstError) firstError = pwd;
            return false;
        }
        if (pwd.value !== pwdConfirm.value) {
            pwdConfirm.classList.add('is-invalid');
            showToast('Les mots de passe ne correspondent pas', 'error');
            if (!firstError) firstError = pwdConfirm;
            return false;
        }
    }

    if (errorCount > 0) {
        showToast(`Veuillez remplir tous les champs obligatoires`, 'error');
        if (firstError) {
            firstError.focus();
            firstError.classList.add('shake');
            setTimeout(() => firstError.classList.remove('shake'), 500);
        }
        return false;
    }
    return true;
}

function validerEtape3() {
    const amount = document.getElementById('input_amount')?.value;
    const duration = document.getElementById('input_duration')?.value;
    const desc = document.getElementById('project_desc');

    if (!amount || amount < 1000) {
        showToast('Le montant minimum est de 1 000 FCFA', 'error');
        document.getElementById('input_amount')?.focus();
        return false;
    }

    if (!duration || duration < 6 || duration > 60) {
        showToast('La durée doit être entre 6 et 60 mois', 'error');
        document.getElementById('input_duration')?.focus();
        return false;
    }

    if (desc && desc.value.length < 50) {
        showToast('La description doit faire au moins 50 caractères', 'error');
        desc.focus();
        return false;
    }

    return true;
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Réactiver tous les champs quand on revient en arrière
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = false;
        });

        // Réappliquer la logique de désactivation selon le type
        if (accountType) {
            selectProfile(accountType, false);
        }
    }
}

function updateUI() {
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressFill').style.width = progress + '%';

    document.querySelectorAll('.form-step').forEach((el, idx) => {
        el.classList.toggle('active', idx + 1 === currentStep);
    });

    document.querySelectorAll('.step-indicator').forEach((el, idx) => {
        el.classList.remove('active', 'completed');
        if (idx + 1 < currentStep) el.classList.add('completed');
        if (idx + 1 === currentStep) el.classList.add('active');
    });

    document.getElementById('btnPrev'). style.display = currentStep === 1 ? 'none' : 'inline-block';
    document.getElementById('btnNext').classList.toggle('d-none', currentStep === totalSteps);
    document.getElementById('btnSubmit').classList.toggle('d-none', currentStep !== totalSteps);
    document.getElementById('stepCounter').innerHTML = `Étape <span class="fw-bold text-primary">${currentStep}</span> sur ${totalSteps}`;

    if (currentStep === 4) generateRecap();
}

function generateRecap() {
    const container = document.getElementById('recap-container');
    const data = window.userData || {};

    let html = '<table class="table recap-table">';

    html += `<tr class="table-light"><td colspan="2" class="bg-light"><strong><i class="fas fa-user-tag me-2"></i>Type de compte</strong></td></tr>`;
    html += `<tr><td>Profil</td><td><span class="badge bg-${accountType === 'entreprise' ? 'dark' : 'primary'}">${data.type || accountType}</span></td></tr>`;

    html += `<tr class="table-light"><td colspan="2" class="bg-light"><strong><i class="fas fa-user me-2"></i>Identité</strong></td></tr>`;
    html += `<tr><td>Nom complet</td><td>${data.name || '-'}</td></tr>`;
    html += `<tr><td>Email</td><td>${data.email || '-'}</td></tr>`;
    html += `<tr><td>Téléphone</td><td>${data.phone || '-'}</td></tr>`;

    if (accountType === 'entreprise') {
        html += `<tr class="table-light"><td colspan="2" class="bg-light"><strong><i class="fas fa-building me-2"></i>Entreprise</strong></td></tr>`;
        html += `<tr><td>Raison sociale</td><td>${data.company_name || '-'}</td></tr>`;
        html += `<tr><td>Forme juridique</td><td>${data.company_type || '-'}</td></tr>`;
        html += `<tr><td>Secteur</td><td>${data.sector || '-'}</td></tr>`;
        html += `<tr><td>Fonction</td><td>${data.position || '-'}</td></tr>`;

        const fundingType = document.getElementById('funding_type_input')?.value;
        if (fundingType) {
            html += `<tr class="table-light"><td colspan="2" class="bg-light"><strong><i class="fas fa-lightbulb me-2"></i>Projet de financement</strong></td></tr>`;

            if (fundingType === 'custom') {
                html += `<tr><td>Type</td><td><span class="badge bg-success">Personnalisée</span></td></tr>`;
            } else {
                const card = document.getElementById(`funding-card-${fundingType}`);
                const typeName = card?.querySelector('h6')?.textContent || 'Formule sélectionnée';
                html += `<tr><td>Formule</td><td>${typeName}</td></tr>`;
            }

            const amount = document.getElementById('input_amount')?.value;
            const duration = document.getElementById('input_duration')?.value;

            if (amount) {
                html += `<tr><td>Montant demandé</td><td><strong class="text-primary">${new Intl.NumberFormat('fr-FR').format(amount)} FCFA</strong></td></tr>`;
            }
            if (duration) {
                html += `<tr><td>Durée</td><td>${duration} mois</td></tr>`;
            }
        }
    }

    html += `<tr class="table-light"><td colspan="2" class="bg-light"><strong><i class="fas fa-map-marker-alt me-2"></i>Localisation</strong></td></tr>`;
    html += `<tr><td>Adresse</td><td>${data.address || '-'}</td></tr>`;
    html += `<tr><td>Ville</td><td>${data.city || '-'}</td></tr>`;
    html += `<tr><td>Pays</td><td>${data.country || '-'}</td></tr>`;

    html += '</table>';
    container.innerHTML = html;
}

function togglePass(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Soumission finale
document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        showModal({
            icon: 'warning',
            title: 'Conditions non acceptées',
            text: 'Vous devez accepter les Conditions Générales d\'Utilisation pour créer votre compte.',
            confirmButtonText: 'J\'ai compris'
        });
        return;
    }

    // Désactiver les champs du formulaire non sélectionné
    if (accountType === 'particulier') {
        const entrepriseForm = document.getElementById('formEntreprise');
        entrepriseForm.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
            if (el.name && el.name !== '_token') el.value = '';
        });

        document.querySelectorAll('#projectContent input, #projectContent textarea').forEach(el => {
            el.disabled = true;
            el.value = '';
        });
    } else if (accountType === 'entreprise') {
        const particulierForm = document.getElementById('formParticulier');
        particulierForm.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = true;
            if (el.name && el.name !== '_token') el.value = '';
        });

        const fundingType = document.getElementById('funding_type_input')?.value;
        if (!fundingType) {
            document.querySelectorAll('#funding-details-form input, #funding-details-form textarea').forEach(el => {
                el.disabled = true;
                el.value = '';
            });
        }
    }

    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.classList.add('btn-loading');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...';
});
</script>
@endpush
