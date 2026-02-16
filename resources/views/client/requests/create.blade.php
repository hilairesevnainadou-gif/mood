@extends('layouts.client')

@section('title', 'Nouvelle Demande de Financement')

@section('content')
<meta name="theme-color" content="#1b5a8d">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<style>
/* [CSS identique à la version précédente - inchangé] */
:root {
    --primary: #1b5a8d;
    --primary-dark: #164a77;
    --primary-light: #e0f2fe;
    --success: #10b981;
    --warning: #f59e0b;
    --error: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-600: #4b5563;
    --gray-800: #1f2937;
    --safe-top: env(safe-area-inset-top, 0px);
    --safe-bottom: env(safe-area-inset-bottom, 0px);
    --bottom-nav-height: 70px;
}

* {
    -webkit-tap-highlight-color: transparent;
    box-sizing: border-box;
}

.pro-create-container {
    background: #f5f7fa;
    min-height: 100vh;
    padding-bottom: calc(140px + var(--safe-bottom) + var(--bottom-nav-height));
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    position: relative;
}

.pro-header {
    background: linear-gradient(135deg, var(--primary) 0%, #0f3a5c 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + var(--safe-top));
    display: flex;
    align-items: center;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.pro-header h1 {
    font-size: 1.25rem;
    color: white;
    font-weight: 700;
    margin: 0;
    flex: 1;
}

.pro-back {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
}

.pro-back:active {
    background: rgba(255,255,255,0.25);
    transform: scale(0.95);
}

.pro-create-content {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 800px;
    margin: 0 auto;
    padding-bottom: 2rem;
}

.pro-section {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid transparent;
    transition: all 0.2s;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.pro-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pro-section-title::before {
    content: '';
    width: 4px;
    height: 20px;
    background: var(--primary);
    border-radius: 2px;
}

.pro-section-subtitle {
    font-size: 0.9rem;
    color: var(--gray-600);
    margin-bottom: 1rem;
    margin-top: -0.5rem;
}

.pro-funding-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.pro-funding-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    background: white;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.pro-funding-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary);
    transform: scaleY(0);
    transition: transform 0.2s;
}

.pro-funding-card:active {
    transform: scale(0.98);
}

.pro-funding-card.active {
    border-color: var(--primary);
    background: var(--primary-light);
    box-shadow: 0 4px 12px rgba(27,90,141,0.15);
}

.pro-funding-card.active::before {
    transform: scaleY(1);
}

.pro-funding-radio {
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.radio-dot {
    width: 22px;
    height: 22px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    transition: all 0.2s;
    position: relative;
    background: white;
}

.pro-funding-card.active .radio-dot {
    border-color: var(--primary);
    background: var(--primary);
}

.pro-funding-card.active .radio-dot::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
}

.pro-funding-content {
    flex: 1;
    min-width: 0;
}

.pro-funding-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.pro-funding-header h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    line-height: 1.3;
    flex: 1;
}

.pro-funding-amount {
    background: var(--primary);
    color: white;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
}

.pro-funding-badge {
    background: var(--success);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.pro-funding-desc {
    font-size: 0.9rem;
    color: var(--gray-600);
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
}

.pro-funding-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--gray-600);
    flex-wrap: wrap;
}

.pro-funding-meta i {
    margin-right: 0.25rem;
    opacity: 0.7;
}

.immediate-payment-badge {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #92400e;
    background: #fef3c7;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    border: 1px solid #fde68a;
}

.immediate-payment-badge i {
    color: var(--warning);
}

.pro-funding-custom {
    border-color: #d1fae5;
    background: #f0fdf4;
}

.pro-funding-custom.active {
    border-color: var(--success);
    background: #d1fae5;
}

.pro-funding-custom::before {
    background: var(--success);
}

.pro-funding-custom .radio-dot {
    border-color: var(--success);
}

.pro-funding-custom.active .radio-dot {
    background: var(--success);
    border-color: var(--success);
}

.summary-card {
    background: var(--primary-light);
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--gray-600);
}

.summary-row:last-child {
    margin-bottom: 0;
}

.summary-row strong {
    color: var(--gray-800);
    font-weight: 700;
}

.fee-row {
    color: #92400e;
    font-weight: 600;
    border-top: 2px dashed #cbd5e1;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
    padding-bottom: 0.5rem;
}

.summary-hint {
    display: block;
    margin-top: 0.75rem;
    color: var(--primary);
    font-size: 0.8rem;
    font-style: italic;
    text-align: center;
    padding: 0.5rem;
    background: rgba(255,255,255,0.5);
    border-radius: 6px;
}

.pro-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.pro-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.pro-form-group:last-child {
    margin-bottom: 0;
}

.pro-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--gray-800);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.pro-required {
    color: var(--error);
}

.pro-input-wrap {
    display: flex;
    align-items: center;
    border: 2px solid var(--gray-200);
    border-radius: 10px;
    background: var(--gray-50);
    overflow: hidden;
    transition: all 0.2s;
}

.pro-input-wrap:focus-within {
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(27,90,141,0.1);
}

.pro-input-icon {
    padding: 0 0.875rem;
    color: #9ca3af;
    font-size: 1.1rem;
}

.pro-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 0.875rem;
    font-size: 1rem;
    outline: none;
    color: var(--gray-800);
    width: 100%;
    font-weight: 500;
}

.pro-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.pro-textarea {
    width: 100%;
    border: 2px solid var(--gray-200);
    border-radius: 10px;
    padding: 0.875rem;
    font-size: 1rem;
    background: var(--gray-50);
    resize: vertical;
    min-height: 120px;
    outline: none;
    font-family: inherit;
    transition: all 0.2s;
    color: var(--gray-800);
}

.pro-textarea:focus {
    border-color: var(--primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(27,90,141,0.1);
}

.pro-hint {
    font-size: 0.8rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.pro-char-counter {
    text-align: right;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    color: var(--gray-600);
    font-weight: 500;
}

.pro-char-counter .valid {
    color: var(--success);
}

.pro-char-counter .invalid {
    color: var(--error);
}

.pro-error-msg {
    color: var(--error);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding: 0.75rem;
    background: #fef2f2;
    border-radius: 8px;
    border-left: 3px solid var(--error);
}

.pro-error-text {
    color: var(--error);
    font-size: 0.8rem;
    margin-top: 0.25rem;
    display: block;
    font-weight: 500;
}

.is-invalid {
    border-color: var(--error) !important;
}

.pro-footer-sticky {
    position: fixed;
    bottom: calc(var(--bottom-nav-height) + var(--safe-bottom));
    left: 0;
    right: 0;
    padding: 1rem;
    padding-bottom: calc(1rem + var(--safe-bottom));
    background: white;
    border-top: 1px solid var(--gray-200);
    z-index: 1000;
    box-shadow: 0 -4px 6px rgba(0,0,0,0.05);
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.98);
    max-width: 800px;
    margin: 0 auto;
    border-radius: 16px 16px 0 0;
}

.pro-btn-primary {
    width: 100%;
    padding: 1rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.2s;
    opacity: 0.5;
    cursor: not-allowed;
    box-shadow: 0 4px 6px -1px rgba(27,90,141,0.2);
}

.pro-btn-primary:not(:disabled) {
    opacity: 1;
    cursor: pointer;
}

.pro-btn-primary:not(:disabled):active {
    transform: translateY(1px);
    background: var(--primary-dark);
    box-shadow: 0 2px 4px -1px rgba(27,90,141,0.2);
}

.pwa-toast {
    position: fixed;
    top: 80px;
    left: 50%;
    transform: translateX(-50%) translateY(-100px);
    background: var(--gray-800);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    z-index: 100000;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
    max-width: 90%;
    text-align: center;
    pointer-events: none;
}

.pwa-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.pwa-toast.success { background: var(--success); }
.pwa-toast.error { background: var(--error); }

@media (max-width: 640px) {
    .pro-form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .pro-funding-header {
        flex-direction: column;
    }

    .pro-funding-amount {
        align-self: flex-start;
    }

    .pro-footer-sticky {
        max-width: 100%;
        border-radius: 0;
    }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.loading {
    animation: pulse 1.5s infinite;
}

.payment-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 99998;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.payment-loading-overlay.active {
    display: flex;
}

.payment-loading-overlay .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Badge mode sandbox */
.sandbox-badge {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #f59e0b;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 10000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>

<!-- Badge Sandbox -->
@if(config('services.kkiapay.sandbox', true))
<div class="sandbox-badge">
    <i class="fas fa-flask"></i> MODE TEST (SANDBOX)
</div>
@endif

<!-- Overlay de chargement -->
<div id="payment-loading" class="payment-loading-overlay">
    <div class="spinner"></div>
    <p>Initialisation du paiement...</p>
    <small style="margin-top: 1rem; opacity: 0.8;">
        Utilisez le numéro de test: <strong>97000000</strong><br>
        Code OTP: <strong>123456</strong>
    </small>
</div>

<div class="pro-create-container">
    {{-- Header --}}
    <div class="pro-header">
        <a href="{{ route('client.requests.index') }}" class="pro-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Nouvelle Demande</h1>
    </div>

    <form action="{{ route('client.requests.store') }}" method="POST" id="create-form" novalidate>
        @csrf
        <input type="hidden" name="is_custom" id="is_custom_input" value="0">
        <input type="hidden" name="funding_type_id" id="funding_type_id_input" value="">

        <div class="pro-create-content">

            {{-- Étape 1: Choix du type --}}
            <div class="pro-section">
                <h3 class="pro-section-title">1. Choisissez votre formule</h3>
                <p class="pro-section-subtitle">Sélectionnez une offre ou créez une demande personnalisée</p>

                <div class="pro-funding-list">
                    @forelse($types as $type)
                    @php
                        $amount = $type->amount ?? $type->grant_amount ?? 0;
                        $fee = $type->registration_fee ?? 0;
                        $duration = $type->duration_months ?? 12;
                    @endphp
                    <div class="pro-funding-card"
                         id="card-{{ $type->id }}"
                         data-type-id="{{ $type->id }}"
                         data-amount="{{ $amount }}"
                         data-duration="{{ $duration }}"
                         data-fee="{{ $fee }}"
                         onclick="selectFundingType({{ $type->id }}, false)">

                        <div class="pro-funding-radio">
                            <div class="radio-dot"></div>
                        </div>

                        <div class="pro-funding-content">
                            <div class="pro-funding-header">
                                <h4>{{ $type->name }}</h4>
                                <span class="pro-funding-amount">{{ number_format($amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <p class="pro-funding-desc">{{ $type->description }}</p>
                            <div class="pro-funding-meta">
                                <span><i class="fas fa-calendar"></i> {{ $duration }} mois</span>
                                <span><i class="fas fa-receipt"></i> Frais: {{ number_format($fee, 0, ',', ' ') }} F</span>
                            </div>
                            <div class="immediate-payment-badge" style="display: none;">
                                <i class="fas fa-bolt"></i> Paiement immédiat requis
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="pro-error-msg">
                        <i class="fas fa-exclamation-circle"></i> Aucune offre disponible pour le moment.
                    </div>
                    @endforelse

                    {{-- Option Personnalisée --}}
                    <div class="pro-funding-card pro-funding-custom"
                         id="card-custom"
                         onclick="selectFundingType(null, true)">
                        <div class="pro-funding-radio">
                            <div class="radio-dot"></div>
                        </div>
                        <div class="pro-funding-content">
                            <div class="pro-funding-header">
                                <h4>Demande Personnalisée</h4>
                                <span class="pro-funding-badge">Sur mesure</span>
                            </div>
                            <p class="pro-funding-desc">Définissez vous-même le montant et la durée selon vos besoins spécifiques.</p>
                            <div class="pro-funding-meta">
                                <span><i class="fas fa-sliders-h"></i> Montant libre</span>
                                <span><i class="fas fa-clock"></i> Validation manuelle</span>
                            </div>
                        </div>
                    </div>
                </div>

                @error('funding_type_id')
                    <div class="pro-error-msg mt-3" style="margin-top: 1rem;">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Étape 2: Détails du financement --}}
            <div class="pro-section" id="funding-details-section" style="display: none;">
                <h3 class="pro-section-title">2. Montant et durée</h3>

                <div id="type-summary" style="display: none;"></div>

                <div class="pro-form-row">
                    <div class="pro-form-group">
                        <label class="pro-label" for="amount_requested">
                            Montant demandé (FCFA) <span class="pro-required">*</span>
                        </label>
                        <div class="pro-input-wrap">
                            <span class="pro-input-icon"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" name="amount_requested" id="amount_requested"
                                   class="pro-input @error('amount_requested') is-invalid @enderror"
                                   placeholder="Ex: 2500000"
                                   min="1000"
                                   step="1000"
                                   value="{{ old('amount_requested') }}"
                                   required>
                        </div>
                        @error('amount_requested')
                            <span class="pro-error-text">{{ $message }}</span>
                        @enderror
                        <span class="pro-hint">
                            <i class="fas fa-info-circle"></i> Minimum: 1 000 FCFA
                        </span>
                    </div>

                    <div class="pro-form-group">
                        <label class="pro-label" for="duration">
                            Durée (mois) <span class="pro-required">*</span>
                        </label>
                        <div class="pro-input-wrap">
                            <span class="pro-input-icon"><i class="fas fa-calendar-alt"></i></span>
                            <input type="number" name="duration" id="duration"
                                   class="pro-input @error('duration') is-invalid @enderror"
                                   placeholder="12"
                                   min="6"
                                   max="60"
                                   value="{{ old('duration') }}"
                                   required>
                        </div>
                        @error('duration')
                            <span class="pro-error-text">{{ $message }}</span>
                        @enderror
                        <span class="pro-hint">
                            <i class="fas fa-clock"></i> Entre 6 et 60 mois
                        </span>
                    </div>
                </div>
            </div>

            {{-- Étape 3: Informations projet --}}
            <div class="pro-section">
                <h3 class="pro-section-title">3. À propos de votre projet</h3>

                <div class="pro-form-group" style="margin-bottom: 1.5rem;">
                    <label class="pro-label" for="project_title">
                        Titre du projet <span class="pro-required">*</span>
                    </label>
                    <input type="text" name="title" id="project_title"
                           class="pro-input @error('title') is-invalid @enderror"
                           placeholder="Ex: Extension de ma boutique de commerce"
                           value="{{ old('title') }}"
                           required
                           maxlength="255">
                    @error('title')
                        <span class="pro-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pro-form-group">
                    <label class="pro-label" for="project_desc">
                        Description détaillée <span class="pro-required">*</span>
                    </label>
                    <textarea name="description" id="project_desc" rows="5"
                              class="pro-textarea @error('description') is-invalid @enderror"
                              placeholder="Décrivez votre projet, vos objectifs et comment vous utiliserez les fonds..."
                              required
                              minlength="50">{{ old('description') }}</textarea>
                    <div class="pro-char-counter">
                        <span id="char-count" class="{{ old('description') && strlen(old('description')) >= 50 ? 'valid' : 'invalid' }}">
                            {{ strlen(old('description')) }}
                        </span> / 50 caractères minimum
                    </div>
                    @error('description')
                        <span class="pro-error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Info sécurité --}}
            <div class="pro-section" style="background: #f0f9ff; border: 1px solid #bae6fd;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fas fa-shield-alt" style="color: var(--primary); font-size: 1.5rem; margin-top: 0.2rem;"></i>
                    <div>
                        <h4 style="margin: 0 0 0.5rem 0; color: var(--primary); font-size: 0.95rem;">Paiement sécurisé</h4>
                        <p style="margin: 0; color: var(--gray-600); font-size: 0.85rem; line-height: 1.4;">
                            Pour les formules prédéfinies, le paiement des frais d'inscription est immédiat via notre partenaire sécurisé Kkiapay.
                            Les demandes personnalisées sont gratuites à la soumission, le paiement sera demandé après validation de votre dossier.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Info Sandbox --}}
            @if(config('services.kkiapay.sandbox', true))
            <div class="pro-section" style="background: #fef3c7; border: 1px solid #fde68a;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fas fa-vial" style="color: #92400e; font-size: 1.5rem; margin-top: 0.2rem;"></i>
                    <div>
                        <h4 style="margin: 0 0 0.5rem 0; color: #92400e; font-size: 0.95rem;">Mode Test (Sandbox)</h4>
                        <p style="margin: 0; color: #92400e; font-size: 0.85rem; line-height: 1.4;">
                            <strong>Numéro de test:</strong> 97000000<br>
                            <strong>Code OTP:</strong> 123456<br>
                            <strong>Code secret:</strong> 1234<br>
                            <small>Aucun vrai prélèvement ne sera effectué.</small>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Espace supplémentaire --}}
            <div style="height: 120px;"></div>

        </div>

        {{-- Footer sticky avec bouton --}}
        <div class="pro-footer-sticky">
            <button type="submit" class="pro-btn-primary" id="submit-btn" disabled>
                <span>Soumettre la demande</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>
</div>

<!-- Script Kkiapay SDK -->
<script src="https://cdn.kkiapay.me/k.js"></script>

<script>
// Variables globales
let selectedTypeData = null;
let isSubmitting = false;

// Configuration Kkiapay depuis Laravel config
const KKIAPAY_CONFIG = {
    key: '{{ config("services.kkiapay.public_key", "") }}',
    sandbox: {{ config("services.kkiapay.sandbox", true) ? 'true' : 'false' }},
    url: '{{ asset("images/logo.png") }}',
    callback: '{{ url("/kkiapay/callback") }}'
};

// CORRECTION: Séparer l'indicatif du numéro de téléphone
const USER_RAW = {
    phone: '{{ Auth::user()->phone ?? "" }}', // Ex: +22966185598 ou 66185598
    name: '{{ Auth::user()->name ?? "" }}',
    email: '{{ Auth::user()->email ?? "" }}'
};

// Numéro de test officiel Kkiapay pour sandbox
const TEST_PHONE_NUMBER = '97000000';

/**
 * Extrait le numéro sans indicatif pays
 * Supporte: +22966185598, 0022966185598, 66185598
 */
function extractPhoneNumber(phone) {
    if (!phone) return '';

    // Supprimer les espaces et tirets
    phone = phone.replace(/[\s\-]/g, '');

    // Supprimer l'indicatif +229 ou 00229
    if (phone.startsWith('+229')) {
        return phone.substring(4);
    }
    if (phone.startsWith('00229')) {
        return phone.substring(5);
    }
    if (phone.startsWith('229') && phone.length > 8) {
        return phone.substring(3);
    }

    // Si commence déjà par 9, 6, 7 (numéro local BJ), retourner tel quel
    if (/^[967]/.test(phone) && phone.length === 8) {
        return phone;
    }

    return phone;
}

/**
 * Retourne le numéro à utiliser pour Kkiapay
 * En sandbox: utilise le numéro de test
 * En production: extrait le numéro sans indicatif
 */
function getKkiapayPhone() {
    // Si mode sandbox, proposer le numéro de test par défaut
    if (KKIAPAY_CONFIG.sandbox) {
        const userPhone = extractPhoneNumber(USER_RAW.phone);
        // Si l'utilisateur a déjà un numéro valide, l'utiliser, sinon test
        return userPhone && userPhone.length === 8 ? userPhone : TEST_PHONE_NUMBER;
    }

    // Mode production: numéro sans indicatif
    return extractPhoneNumber(USER_RAW.phone);
}

// Debug: afficher la configuration
console.log('Kkiapay Config:', {
    ...KKIAPAY_CONFIG,
    phoneRaw: USER_RAW.phone,
    phoneFormatted: getKkiapayPhone(),
    mode: KKIAPAY_CONFIG.sandbox ? 'SANDBOX' : 'PRODUCTION'
});

// ==================== FONCTIONS DE SÉLECTION ====================

function selectFundingType(typeId, isCustom = false) {
    if (navigator.vibrate) navigator.vibrate(15);

    document.querySelectorAll('.pro-funding-card').forEach(card => {
        card.classList.remove('active');
    });

    const selectedCard = document.getElementById(isCustom ? 'card-custom' : 'card-' + typeId);
    if (selectedCard) selectedCard.classList.add('active');

    document.getElementById('is_custom_input').value = isCustom ? '1' : '0';
    document.getElementById('funding_type_id_input').value = isCustom ? '' : typeId;

    document.querySelectorAll('.immediate-payment-badge').forEach(el => el.style.display = 'none');

    const detailsSection = document.getElementById('funding-details-section');
    detailsSection.style.display = 'block';

    if (!isCustom && selectedCard) {
        const amount = parseFloat(selectedCard.dataset.amount) || 0;
        const duration = parseInt(selectedCard.dataset.duration) || 0;
        const fee = parseFloat(selectedCard.dataset.fee) || 0;

        selectedTypeData = {
            amount: amount,
            duration: duration,
            fee: fee,
            name: selectedCard.querySelector('h4').textContent
        };

        selectedCard.querySelector('.immediate-payment-badge').style.display = 'inline-flex';

        document.getElementById('amount_requested').value = amount;
        document.getElementById('duration').value = duration;

        showSummary(selectedTypeData);
    } else {
        selectedTypeData = null;
        document.getElementById('amount_requested').value = '';
        document.getElementById('duration').value = '';
        document.getElementById('type-summary').style.display = 'none';

        setTimeout(() => {
            document.getElementById('amount_requested').focus();
        }, 300);
    }

    if (window.innerWidth < 768) {
        setTimeout(() => {
            detailsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    checkFormValidity();
}

function showSummary(data) {
    const summaryDiv = document.getElementById('type-summary');
    summaryDiv.innerHTML = `
        <div class="summary-card">
            <div class="summary-row">
                <span>Formule:</span>
                <strong>${data.name}</strong>
            </div>
            <div class="summary-row">
                <span>Montant suggéré:</span>
                <strong>${Math.round(data.amount).toLocaleString('fr-FR')} FCFA</strong>
            </div>
            <div class="summary-row">
                <span>Durée:</span>
                <strong>${data.duration} mois</strong>
            </div>
            <div class="summary-row fee-row">
                <span>Frais d'inscription:</span>
                <strong>${Math.round(data.fee).toLocaleString('fr-FR')} FCFA</strong>
            </div>
            <small class="summary-hint">
                <i class="fas fa-pencil-alt"></i> Vous pouvez ajuster le montant et la durée ci-dessous
            </small>
        </div>
    `;
    summaryDiv.style.display = 'block';
}

// ==================== VALIDATION DU FORMULAIRE ====================

function checkFormValidity() {
    const hasSelectedType = document.getElementById('funding_type_id_input').value !== '' ||
                          document.getElementById('is_custom_input').value === '1';
    const title = document.getElementById('project_title').value.trim().length > 0;
    const description = document.getElementById('project_desc').value.trim().length >= 50;

    const amount = parseFloat(document.getElementById('amount_requested').value) || 0;
    const duration = parseInt(document.getElementById('duration').value) || 0;
    const financialValid = amount >= 1000 && duration >= 6 && duration <= 60;

    const btn = document.getElementById('submit-btn');
    const isValid = hasSelectedType && title && description && financialValid;

    btn.disabled = !isValid || isSubmitting;
    btn.style.opacity = (isValid && !isSubmitting) ? '1' : '0.5';

    if (isValid) {
        const isPredefined = document.getElementById('funding_type_id_input').value !== '';
        const btnText = btn.querySelector('span');
        if (isPredefined && selectedTypeData) {
            const fee = Math.round(selectedTypeData.fee);
            btnText.textContent = `Payer ${fee.toLocaleString('fr-FR')} FCFA`;
        } else {
            btnText.textContent = 'Soumettre pour examen';
        }
    } else {
        btn.querySelector('span').textContent = 'Soumettre la demande';
    }
}

function updateCharCount() {
    const len = document.getElementById('project_desc').value.length;
    const counter = document.getElementById('char-count');
    counter.textContent = len;

    if (len >= 50) {
        counter.classList.add('valid');
        counter.classList.remove('invalid');
    } else {
        counter.classList.add('invalid');
        counter.classList.remove('valid');
    }
    checkFormValidity();
}

// ==================== INTÉGRATION KKIAPAY CORRIGÉE ====================

/**
 * Ouvre le widget Kkiapay avec les paramètres corrigés
 * CORRECTION: Numéro sans indicatif pays
 */
function openKkiapayPayment(amount, motif, requestData) {
    const loadingOverlay = document.getElementById('payment-loading');

    try {
        loadingOverlay.classList.add('active');

        const parsedAmount = parseInt(amount);
        if (isNaN(parsedAmount) || parsedAmount <= 0) {
            throw new Error('Montant invalide: ' + amount);
        }

        // CORRECTION: Préparer le numéro sans indicatif
        const phoneNumber = getKkiapayPhone();

        console.log('Ouverture Kkiapay:', {
            amount: parsedAmount,
            phone: phoneNumber,
            phoneRaw: USER_RAW.phone,
            mode: KKIAPAY_CONFIG.sandbox ? 'SANDBOX' : 'PROD'
        });

        // Configuration selon documentation officielle Kkiapay
        const config = {
            amount: parsedAmount,
            key: KKIAPAY_CONFIG.key,
            url: KKIAPAY_CONFIG.url,
            position: 'center',
            sandbox: KKIAPAY_CONFIG.sandbox,
            // CORRECTION: phone sans indicatif +229
            phone: phoneNumber,
            name: USER_RAW.name,
            email: USER_RAW.email,
            data: JSON.stringify({
                request_data: requestData,
                motif: motif,
                timestamp: Date.now(),
                user_phone_raw: USER_RAW.phone,
                user_phone_formatted: phoneNumber
            }),
            callback: KKIAPAY_CONFIG.callback
        };

        // Vérifier que l'API est disponible
        if (typeof openKkiapayWidget !== 'function') {
            throw new Error('API Kkiapay non disponible. Vérifiez que le SDK est chargé.');
        }

        // Ouvrir le widget
        openKkiapayWidget(config);

        // Configurer les listeners
        setupKkiapayListeners();

        loadingOverlay.classList.remove('active');

    } catch (error) {
        console.error('Erreur Kkiapay:', error);
        loadingOverlay.classList.remove('active');
        showToast('Erreur: ' + error.message, 'error');
        isSubmitting = false;
        checkFormValidity();
    }
}

/**
 * Configure les listeners d'événements Kkiapay
 */
function setupKkiapayListeners() {
    if (window.kkiapayListenersConfigured) return;
    window.kkiapayListenersConfigured = true;

    console.log('Configuration des listeners Kkiapay');

    // Succès
    if (typeof addSuccessListener === 'function') {
        addSuccessListener(function(response) {
            console.log('Paiement réussi:', response);
            handlePaymentSuccess(response);
        });
    }

    // Échec
    if (typeof addFailedListener === 'function') {
        addFailedListener(function(error) {
            console.log('Paiement échoué:', error);
            handlePaymentFailure(error);
        });
    }

    // Fermeture
    if (typeof addKkiapayCloseListener === 'function') {
        addKkiapayCloseListener(function() {
            console.log('Widget fermé');
            if (isSubmitting) {
                isSubmitting = false;
                checkFormValidity();
            }
        });
    }
}

/**
 * Gestion du succès de paiement
 */
function handlePaymentSuccess(response) {
    showToast('Paiement réussi! Finalisation...', 'success');

    const form = document.getElementById('create-form');

    // Supprimer l'ancien input
    const oldInput = form.querySelector('input[name="kkiapay_transaction"]');
    if (oldInput) oldInput.remove();

    // Ajouter les données de transaction
    const paymentInput = document.createElement('input');
    paymentInput.type = 'hidden';
    paymentInput.name = 'kkiapay_transaction';
    paymentInput.value = JSON.stringify(response);
    form.appendChild(paymentInput);

    form.submit();
}

/**
 * Gestion de l'échec de paiement
 */
function handlePaymentFailure(error) {
    showToast('Le paiement a échoué. Vous pouvez réessayer.', 'error');
    isSubmitting = false;
    checkFormValidity();
}

// ==================== GESTION DE LA SOUMISSION ====================

function handleFormSubmit(e) {
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }

    const isPredefined = document.getElementById('funding_type_id_input').value !== '';
    const isCustom = document.getElementById('is_custom_input').value === '1';

    if (isPredefined && selectedTypeData) {
        e.preventDefault();

        const currentAmount = parseFloat(document.getElementById('amount_requested').value) || 0;
        const originalAmount = selectedTypeData.amount;
        const fee = selectedTypeData.fee;

        if (currentAmount !== originalAmount) {
            if (!confirm(`Attention : vous avez modifié le montant de ${originalAmount.toLocaleString('fr-FR')} à ${currentAmount.toLocaleString('fr-FR')} FCFA.\n\nContinuer avec ce montant ?`)) {
                return false;
            }
        }

        if (!fee || fee <= 0) {
            showToast('Erreur: Frais d\'inscription invalides', 'error');
            return false;
        }

        isSubmitting = true;
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.classList.add('loading');

        const requestData = {
            title: document.getElementById('project_title').value.trim(),
            description: document.getElementById('project_desc').value.trim(),
            amount_requested: currentAmount,
            duration: parseInt(document.getElementById('duration').value) || 0,
            funding_type_id: document.getElementById('funding_type_id_input').value,
            is_custom: 0
        };

        openKkiapayPayment(fee, 'Frais inscription ' + selectedTypeData.name, requestData);

    } else if (isCustom) {
        isSubmitting = true;
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.classList.add('loading');
        btn.querySelector('span').textContent = 'Envoi en cours...';
    }
}

// ==================== INITIALISATION ====================

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-form');
    form.addEventListener('submit', handleFormSubmit);

    ['amount_requested', 'duration', 'project_title', 'project_desc'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                if (id === 'project_desc') updateCharCount();
                else checkFormValidity();
            });
        }
    });

    // Vérifier SDK
    window.addEventListener('load', function() {
        if (typeof openKkiapayWidget === 'function') {
            console.log('✓ SDK Kkiapay chargé');
        } else {
            console.warn('✗ SDK Kkiapay non détecté');
        }
    });
});

// ==================== UTILITAIRES ====================

function showToast(message, type = 'success') {
    document.querySelectorAll('.pwa-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `pwa-toast ${type} show`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

window.addEventListener('offline', () => {
    showToast('Mode hors ligne. Vérifiez votre connexion.', 'error');
});

window.addEventListener('online', () => {
    showToast('Connexion rétablie', 'success');
});
</script>

@endsection
