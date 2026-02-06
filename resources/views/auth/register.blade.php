@extends('layouts.inscription-layout')

@section('title', 'Inscription')
@section('body_class', 'mode-inscription')

@section('auth_nav_button')
    <a href="{{ route('login') }}" class="btn-auth-nav">
        <i class="fas fa-sign-in-alt"></i>
        <span>Déjà inscrit ? Connexion</span>
    </a>
@endsection

@section('content_wrapper')
<div class="inscription-wrapper">
    <div class="inscription-container">
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

                <!-- Messages d'erreur globaux -->
                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}" id="inscriptionForm" novalidate>
                    @csrf

                    <!-- ÉTAPE 1: Choix du profil -->
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

                    <!-- ÉTAPE 2: Informations -->
                    <div class="form-step" data-step="2">
                        <div class="step-header">
                            <h3><i class="fas fa-id-card text-primary me-2"></i>Informations de contact</h3>
                            <p>Ces informations permettront de sécuriser votre compte</p>
                        </div>

                        <!-- FORMULAIRE PARTICULIER -->
                        <div id="formParticulier" class="{{ old('account_type') == 'entreprise' ? 'd-none' : '' }}">
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Identité personnelle</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="p_name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}" required placeholder="Prénom et Nom" onblur="saveUserData()">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="p_email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                   value="{{ old('email') }}" required placeholder="exemple@email.com" onblur="saveUserData()">
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Téléphone <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" id="p_phone" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone') }}" required placeholder="+225 XX XX XX XX" onblur="saveUserData()">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Pays <span class="text-danger">*</span></label>
                                            <select name="country" id="p_country" class="form-select form-select-lg @error('country') is-invalid @enderror" required onchange="saveUserData()">
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
                                            <input type="text" name="city" id="p_city" class="form-control form-control-lg @error('city') is-invalid @enderror"
                                                   value="{{ old('city') }}" required onblur="saveUserData()">
                                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                                            <input type="text" name="address" id="p_address" class="form-control form-control-lg @error('address') is-invalid @enderror"
                                                   value="{{ old('address') }}" required placeholder="Quartier, rue..." onblur="saveUserData()">
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

                        <!-- FORMULAIRE ENTREPRISE -->
                        <div id="formEntreprise" class="{{ old('account_type') != 'entreprise' ? 'd-none' : '' }}">
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Responsable du compte</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="e_name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}" required onblur="saveUserData()">
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Fonction <span class="text-danger">*</span></label>
                                            <input type="text" name="position" id="e_position" class="form-control form-control-lg @error('position') is-invalid @enderror"
                                                   value="{{ old('position') }}" required placeholder="DG, Manager..." onblur="saveUserData()">
                                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Email professionnel <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="e_email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                                   value="{{ old('email') }}" required onblur="saveUserData()">
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Téléphone <span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" id="e_phone" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                                   value="{{ old('phone') }}" required onblur="saveUserData()">
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
                                            <input type="text" name="company_name" id="e_company_name" class="form-control form-control-lg @error('company_name') is-invalid @enderror"
                                                   value="{{ old('company_name') }}" required onblur="saveUserData()">
                                            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Forme juridique <span class="text-danger">*</span></label>
                                            <select name="company_type" id="e_company_type" class="form-select form-select-lg @error('company_type') is-invalid @enderror" required onchange="saveUserData()">
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
                                            <select name="sector" id="e_sector" class="form-select form-select-lg @error('sector') is-invalid @enderror" required onchange="saveUserData()">
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
                                            <select name="country" id="e_country" class="form-select form-select-lg @error('country') is-invalid @enderror" required onchange="saveUserData()">
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
                                            <input type="text" name="city" id="e_city" class="form-control form-control-lg @error('city') is-invalid @enderror"
                                                   value="{{ old('city') }}" required onblur="saveUserData()">
                                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Adresse <span class="text-danger">*</span></label>
                                            <input type="text" name="address" id="e_address" class="form-control form-control-lg @error('address') is-invalid @enderror"
                                                   value="{{ old('address') }}" required placeholder="Quartier, rue..." onblur="saveUserData()">
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
                                                <input type="password" name="password" id="pwd3" class="form-control form-control-lg @error('password') is-invalid @enderror" required minlength="8">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pwd3')"><i class="fas fa-eye"></i></button>
                                            </div>
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="text-muted">Minimum 8 caractères</small>
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

                    <!-- ÉTAPE 3: PROJET (Entreprise uniquement) -->
                    <div class="form-step" data-step="3" id="step3Container">
                        <div class="step-header">
                            <h3><i class="fas fa-lightbulb text-primary me-2"></i>Votre projet</h3>
                            <p id="step3Subtitle">Définissez votre besoin de financement</p>
                        </div>

                        <div id="projectContent">
                            <!-- Injecté par JS -->
                        </div>
                    </div>

                    <!-- ÉTAPE 4: RÉCAPITULATIF -->
                    <div class="form-step" data-step="4">
                        <div class="step-header">
                            <h3><i class="fas fa-check-circle text-success me-2"></i>Vérification</h3>
                            <p>Vérifiez vos informations avant de créer votre compte</p>
                        </div>

                        <div class="card mb-4 border-2 border-primary shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Récapitulatif</h5>
                            </div>
                            <div class="card-body p-0">
                                <div id="recap-container" class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <tbody id="recap-tbody">
                                            <!-- Injecté par JS -->
                                        </tbody>
                                    </table>
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
    </div>
</div>

<!-- Template particulier étape 3 -->
<template id="templateParticulierStep3">
    <div class="text-center py-5">
        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 3rem;">
            <i class="fas fa-check"></i>
        </div>
        <h4>Presque terminé !</h4>
        <p class="text-muted">En tant que particulier, vous pourrez définir vos projets après activation de votre compte.</p>
        <div class="alert alert-info mt-3 mx-auto" style="max-width: 500px;">
            <i class="fas fa-info-circle me-2"></i>
            Passez à l'étape suivante pour finaliser votre inscription.
        </div>
    </div>
</template>

<!-- Template entreprise étape 3 -->
<template id="templateEntrepriseStep3">
    <div class="mb-4">
        <label class="form-label fw-bold mb-3"><i class="fas fa-hand-holding-usd me-2"></i>Demande de financement (optionnel)</label>

        @if(isset($fundingTypes) && count($fundingTypes) > 0)
            <div class="row g-3 mb-3">
                @foreach($fundingTypes as $type)
                <div class="col-md-6">
                    <div class="card funding-option h-100" onclick="selectFundingType({{ $type->id }}, '{{ $type->name }}', {{ $type->amount ?? 0 }})" id="funding-option-{{ $type->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-bold">{{ $type->name }}</h6>
                                <span class="badge bg-primary">{{ number_format($type->amount ?? 0, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <p class="text-muted small mb-2">{{ Str::limit($type->description, 80) }}</p>
                            <small class="text-muted"><i class="fas fa-calendar me-1"></i> {{ $type->duration_months ?? 12 }} mois</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        <div class="card funding-option border-success" onclick="selectFundingType('custom', 'Demande personnalisée', 0)" id="funding-option-custom">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-success"><i class="fas fa-sliders-h me-2"></i>Demande personnalisée</h6>
                        <p class="text-muted small mb-0">Définissez vous-même le montant et la durée</p>
                    </div>
                    <span class="badge bg-success">Sur mesure</span>
                </div>
            </div>
        </div>

        <input type="hidden" name="funding_type_id" id="funding_type_id" value="">
        <input type="hidden" name="is_custom_funding" id="is_custom_funding" value="0">

        <div id="funding-details" class="mt-4 d-none">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-primary mb-3"><i class="fas fa-edit me-2"></i>Détails du projet</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Montant demandé (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="funding_needed" id="funding_needed" class="form-control" min="1000" step="1000" onchange="saveProjectData()">
                            <small class="text-muted">Minimum: 1 000 FCFA</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durée (mois) <span class="text-danger">*</span></label>
                            <input type="number" name="duration" id="duration" class="form-control" min="6" max="60" onchange="saveProjectData()">
                            <small class="text-muted">Entre 6 et 60 mois</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nom du projet</label>
                            <input type="text" name="project_name" id="project_name" class="form-control" placeholder="Ex: Extension unité de production" onchange="saveProjectData()">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description du projet <span class="text-danger">*</span></label>
                            <textarea name="project_description" id="project_description" rows="3" class="form-control" placeholder="Décrivez votre projet..." minlength="50" onchange="saveProjectData()"></textarea>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Minimum 50 caractères</small>
                                <small class="text-muted"><span id="char-count">0</span> car.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note :</strong> Vous pouvez compléter cette demande plus tard depuis votre espace membre.
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;
let accountType = '{{ old('account_type', '') }}';
let userData = {};
let projectData = {
    funding_type_id: '',
    funding_type_name: '',
    funding_needed: '',
    duration: '',
    project_name: '',
    project_description: ''
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Gestion erreurs serveur pour positionner sur la bonne étape
    @if($errors->has('account_type'))
        currentStep = 1;
    @elseif($errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('password') || $errors->has('company_name') || $errors->has('company_type') || $errors->has('sector') || $errors->has('position'))
        currentStep = 2;
        @if(old('account_type'))
            selectProfile('{{ old('account_type') }}', false);
        @endif
    @elseif($errors->has('funding_needed') || $errors->has('duration') || $errors->has('project_description'))
        currentStep = 3;
        @if(old('account_type') == 'entreprise')
            selectProfile('entreprise', false);
        @endif
    @elseif($errors->has('terms'))
        currentStep = 4;
        @if(old('account_type'))
            selectProfile('{{ old('account_type') }}', false);
        @endif
    @elseif(old('account_type'))
        selectProfile('{{ old('account_type') }}', false);
    @else
        document.getElementById('formParticulier').classList.add('d-none');
        document.getElementById('formEntreprise').classList.add('d-none');
    @endif

    updateUI();
});

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

function selectProfile(type, autoNext = true) {
    accountType = type;

    // Réactiver tous les champs
    document.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);

    const formParticulier = document.getElementById('formParticulier');
    const formEntreprise = document.getElementById('formEntreprise');

    if (type === 'particulier') {
        formParticulier.classList.remove('d-none');
        formEntreprise.classList.add('d-none');
        document.getElementById('step3Label').textContent = 'Confirmation';
        formEntreprise.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
    } else {
        formParticulier.classList.add('d-none');
        formEntreprise.classList.remove('d-none');
        document.getElementById('step3Label').textContent = 'Projet';
        formParticulier.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
    }

    // Style visuel
    document.querySelectorAll('.account-type-card').forEach(card => {
        card.classList.remove('selected', 'border-primary');
        card.querySelector('.account-type-select').style.opacity = '0';
    });

    const selectedCard = document.querySelector(`[data-type="${type}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected', 'border-primary');
        selectedCard.querySelector('.account-type-select').style.opacity = '1';
        document.getElementById(`type_${type}`).checked = true;
    }

    injectStep3Content(type);
    saveUserData();

    if (autoNext && currentStep === 1) {
        setTimeout(() => nextStep(), 300);
    }
}

function injectStep3Content(type) {
    const container = document.getElementById('projectContent');
    container.innerHTML = '';

    if (type === 'particulier') {
        const template = document.getElementById('templateParticulierStep3');
        container.appendChild(template.content.cloneNode(true));
    } else {
        const template = document.getElementById('templateEntrepriseStep3');
        container.appendChild(template.content.cloneNode(true));

        // Compteur de caractères
        setTimeout(() => {
            const desc = document.getElementById('project_description');
            if (desc) {
                desc.addEventListener('input', function() {
                    document.getElementById('char-count').textContent = this.value.length;
                });
            }

            // Restaurer données projet si existantes
            if (projectData.funding_type_id) {
                selectFundingType(projectData.funding_type_id, projectData.funding_type_name, 0);
                document.getElementById('funding_needed').value = projectData.funding_needed;
                document.getElementById('duration').value = projectData.duration;
                document.getElementById('project_name').value = projectData.project_name;
                document.getElementById('project_description').value = projectData.project_description;
            }
        }, 100);
    }
}

function selectFundingType(id, name, amount) {
    document.querySelectorAll('.funding-option').forEach(el => el.classList.remove('selected'));
    document.getElementById(`funding-option-${id}`)?.classList.add('selected');

    document.getElementById('funding_type_id').value = id;
    document.getElementById('is_custom_funding').value = (id === 'custom') ? '1' : '0';

    projectData.funding_type_id = id;
    projectData.funding_type_name = name;

    const detailsDiv = document.getElementById('funding-details');
    if (id) {
        detailsDiv.classList.remove('d-none');
        if (amount > 0) {
            document.getElementById('funding_needed').value = amount;
        }
    }
}

function saveUserData() {
    const prefix = accountType === 'particulier' ? 'p_' : 'e_';

    userData = {
        type: accountType === 'particulier' ? 'Particulier' : 'Entreprise',
        name: document.getElementById(`${prefix}name`)?.value || '',
        email: document.getElementById(`${prefix}email`)?.value || '',
        phone: document.getElementById(`${prefix}phone`)?.value || '',
        city: document.getElementById(`${prefix}city`)?.value || '',
        country: document.getElementById(`${prefix}country`)?.value || '',
        address: document.getElementById(`${prefix}address`)?.value || ''
    };

    if (accountType === 'entreprise') {
        userData.company_name = document.getElementById('e_company_name')?.value || '';
        userData.company_type = document.getElementById('e_company_type')?.value || '';
        userData.sector = document.getElementById('e_sector')?.value || '';
        userData.position = document.getElementById('e_position')?.value || '';
    }
}

function saveProjectData() {
    projectData.funding_needed = document.getElementById('funding_needed')?.value || '';
    projectData.duration = document.getElementById('duration')?.value || '';
    projectData.project_name = document.getElementById('project_name')?.value || '';
    projectData.project_description = document.getElementById('project_description')?.value || '';
}

function validateStep2() {
    const container = document.getElementById(accountType === 'entreprise' ? 'formEntreprise' : 'formParticulier');
    const requiredFields = container.querySelectorAll('[required]');
    let isValid = true;
    let firstError = null;

    requiredFields.forEach(field => {
        if (!field.disabled && !field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
            if (!firstError) firstError = field;
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
            isValid = false;
        }
    }

    // Validation mot de passe
    const pwdField = accountType === 'particulier' ? document.getElementById('pwd1') : document.getElementById('pwd3');
    const pwdConfirmField = accountType === 'particulier' ? document.getElementById('pwd2') : document.getElementById('pwd4');

    if (pwdField && pwdConfirmField && !pwdField.disabled) {
        if (pwdField.value.length < 8) {
            pwdField.classList.add('is-invalid');
            alert('Le mot de passe doit contenir au moins 8 caractères');
            return false;
        }
        if (pwdField.value !== pwdConfirmField.value) {
            pwdConfirmField.classList.add('is-invalid');
            alert('Les mots de passe ne correspondent pas');
            return false;
        }
    }

    if (!isValid && firstError) {
        firstError.focus();
        alert('Veuillez remplir tous les champs obligatoires');
    }

    return isValid;
}

function validateStep3() {
    if (accountType !== 'entreprise') return true;

    const fundingType = document.getElementById('funding_type_id')?.value;
    if (!fundingType) return true; // Optionnel

    const amount = document.getElementById('funding_needed')?.value;
    const duration = document.getElementById('duration')?.value;
    const description = document.getElementById('project_description')?.value;

    if (amount && amount < 1000) {
        alert('Le montant minimum est de 1 000 FCFA');
        return false;
    }
    if (duration && (duration < 6 || duration > 60)) {
        alert('La durée doit être entre 6 et 60 mois');
        return false;
    }
    if (description && description.length < 50) {
        alert('La description doit faire au moins 50 caractères');
        return false;
    }

    return true;
}

function generateRecap() {
    const tbody = document.getElementById('recap-tbody');
    if (!userData.name) saveUserData();

    let html = '';

    // Type de compte
    html += `<tr class="table-primary"><td colspan="2"><strong><i class="fas fa-user-tag me-2"></i>Type de compte</strong></td></tr>`;
    html += `<tr><td width="35%">Profil</td><td><span class="badge bg-${accountType === 'entreprise' ? 'dark' : 'primary'}">${userData.type}</span></td></tr>`;

    // Identité
    html += `<tr class="table-light"><td colspan="2"><strong><i class="fas fa-user me-2"></i>Identité</strong></td></tr>`;
    html += `<tr><td>Nom complet</td><td class="fw-bold">${userData.name || '-'}</td></tr>`;
    html += `<tr><td>Email</td><td>${userData.email || '-'}</td></tr>`;
    html += `<tr><td>Téléphone</td><td>${userData.phone || '-'}</td></tr>`;

    // Entreprise
    if (accountType === 'entreprise') {
        html += `<tr class="table-light"><td colspan="2"><strong><i class="fas fa-building me-2"></i>Entreprise</strong></td></tr>`;
        html += `<tr><td>Raison sociale</td><td class="fw-bold">${userData.company_name || '-'}</td></tr>`;
        html += `<tr><td>Forme juridique</td><td>${userData.company_type || '-'}</td></tr>`;
        html += `<tr><td>Secteur</td><td>${userData.sector || '-'}</td></tr>`;
        html += `<tr><td>Fonction</td><td>${userData.position || '-'}</td></tr>`;

        // Projet
        if (projectData.funding_type_id) {
            html += `<tr class="table-light"><td colspan="2"><strong><i class="fas fa-lightbulb me-2"></i>Projet de financement</strong></td></tr>`;
            html += `<tr><td>Type</td><td>${projectData.funding_type_name}</td></tr>`;
            if (projectData.funding_needed) {
                html += `<tr><td>Montant</td><td class="text-primary fw-bold">${new Intl.NumberFormat('fr-FR').format(projectData.funding_needed)} FCFA</td></tr>`;
            }
            if (projectData.duration) {
                html += `<tr><td>Durée</td><td>${projectData.duration} mois</td></tr>`;
            }
            if (projectData.project_name) {
                html += `<tr><td>Nom du projet</td><td>${projectData.project_name}</td></tr>`;
            }
        }
    }

    // Localisation
    html += `<tr class="table-light"><td colspan="2"><strong><i class="fas fa-map-marker-alt me-2"></i>Localisation</strong></td></tr>`;
    html += `<tr><td>Adresse</td><td>${userData.address || '-'}</td></tr>`;
    html += `<tr><td>Ville</td><td>${userData.city || '-'}</td></tr>`;
    html += `<tr><td>Pays</td><td>${userData.country || '-'}</td></tr>`;

    tbody.innerHTML = html;
}

function nextStep() {
    if (currentStep === 1 && !accountType) {
        alert('Veuillez sélectionner un type de compte');
        return;
    }

    if (currentStep === 2 && !validateStep2()) return;

    if (currentStep === 3 && !validateStep3()) return;

    if (currentStep < totalSteps) {
        currentStep++;
        updateUI();

        if (currentStep === 4) {
            setTimeout(generateRecap, 100);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function updateUI() {
    // Progress bar
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressFill').style.width = progress + '%';

    // Steps visibility
    document.querySelectorAll('.form-step').forEach((el, idx) => {
        el.classList.toggle('active', idx + 1 === currentStep);
    });

    // Indicators
    document.querySelectorAll('.step-indicator').forEach((el, idx) => {
        el.classList.remove('active', 'completed');
        if (idx + 1 < currentStep) el.classList.add('completed');
        if (idx + 1 === currentStep) el.classList.add('active');
    });

    // Buttons
    document.getElementById('btnPrev').style.display = currentStep === 1 ? 'none' : 'inline-block';
    document.getElementById('btnNext').classList.toggle('d-none', currentStep === totalSteps);
    document.getElementById('btnSubmit').classList.toggle('d-none', currentStep !== totalSteps);
    document.getElementById('stepCounter').innerHTML = `Étape <span class="fw-bold text-primary">${currentStep}</span> sur ${totalSteps}`;
}
</script>
@endpush

@push('styles')
<style>
.account-type-card {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid #e5e7eb;
    position: relative;
}
.account-type-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.account-type-card.selected {
    border-color: var(--bh-primary);
    box-shadow: 0 0 0 3px rgba(27, 90, 141, 0.1);
}
.account-type-select {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 28px;
    height: 28px;
    background: var(--bh-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
    font-size: 0.8rem;
}
.account-type-card.selected .account-type-select {
    opacity: 1;
}
.funding-option {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid #e5e7eb;
}
.funding-option:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
}
.funding-option.selected {
    border-color: var(--bh-success);
    background: #f0fdf4;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
.d-none { display: none !important; }
</style>
@endpush
