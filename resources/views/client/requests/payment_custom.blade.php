@extends('layouts.client')

@section('title', 'Paiement - ' . $fundingRequest->request_number)

@section('content')
<div class="pwa-payment-container">
    {{-- Header --}}
    <div class="pwa-payment-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <a href="{{ route('client.requests.show', $fundingRequest->id) }}" class="pwa-back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pwa-header-text">
                <h1>Paiement</h1>
                <p>{{ $fundingRequest->request_number }}</p>
            </div>
        </div>
    </div>

    {{-- Récapitulatif --}}
    <div class="pwa-payment-summary">
        <div class="pwa-summary-card">
            <h3>Récapitulatif</h3>
            <div class="pwa-summary-row">
                <span>Demande</span>
                <strong>{{ $fundingRequest->title }}</strong>
            </div>
            <div class="pwa-summary-row">
                <span>Type</span>
                <span class="pwa-badge-custom">Personnalisée</span>
            </div>
            <div class="pwa-summary-row">
                <span>Montant demandé</span>
                <strong>{{ $fundingRequest->formatted_amount_requested }}</strong>
            </div>
            <div class="pwa-summary-row">
                <span>Durée</span>
                <strong>{{ $fundingRequest->duration }} mois</strong>
            </div>
            <div class="pwa-summary-divider"></div>
            <div class="pwa-summary-row pwa-total">
                <span>Frais d'inscription</span>
                <strong class="pwa-amount-due">{{ $fundingRequest->formatted_expected_payment }}</strong>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="pwa-payment-instructions">
        <div class="pwa-instruction-card">
            <i class="fas fa-shield-alt"></i>
            <h4>Paiement sécurisé</h4>
            <p>Le paiement est effectué via <strong>Kkiapay</strong>, notre partenaire de paiement sécurisé.</p>
        </div>
    </div>

    @if(config('services.kkiapay.sandbox', true))
    {{-- Mode Test --}}
    <div class="pwa-sandbox-notice">
        <i class="fas fa-flask"></i>
        <div>
            <strong>Mode Test (Sandbox)</strong>
            <p>Utilisez ces informations pour tester :</p>
            <ul>
                <li><strong>Numéro :</strong> 97000000</li>
                <li><strong>OTP :</strong> 123456</li>
                <li><strong>Code secret :</strong> 1234</li>
            </ul>
            <small>Aucun vrai prélèvement ne sera effectué.</small>
        </div>
    </div>
    @endif

    {{-- Bouton de paiement Kkiapay --}}
    <div class="pwa-payment-action">
        <button type="button" class="pwa-btn-kkiapay" id="kkiapay-button" onclick="openKkiapayPayment()">
            <i class="fas fa-credit-card"></i>
            <span>Payer {{ $fundingRequest->formatted_expected_payment }}</span>
        </button>
        <p class="pwa-payment-note">
            <i class="fas fa-lock"></i>
            Transaction sécurisée par Kkiapay
        </p>
    </div>

    {{-- Formulaire caché pour soumission --}}
    <form id="payment-form" action="{{ route('client.requests.payment.process', $fundingRequest->id) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="kkiapay_transaction" id="kkiapay-transaction-input">
    </form>

    {{-- Loading Overlay --}}
    <div id="payment-loading" class="pwa-loading-overlay">
        <div class="pwa-spinner"></div>
        <p>Traitement du paiement...</p>
        <small>Veuillez ne pas fermer cette page</small>
    </div>
</div>
@endsection

@push('styles')
<style>
.pwa-payment-container {
    min-height: 100vh;
    background: #f5f7fa;
    padding-bottom: 2rem;
}

.pwa-payment-header {
    background: linear-gradient(135deg, #1b5a8d 0%, #113a61 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin-bottom: 1rem;
    position: relative;
}

.pwa-header-bg {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
    background-size: 20px 20px;
}

.pwa-header-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}

.pwa-back-btn {
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

.pwa-back-btn:active {
    background: rgba(255,255,255,0.25);
    transform: scale(0.95);
}

.pwa-header-text h1 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
}

.pwa-header-text p {
    font-size: 0.85rem;
    opacity: 0.9;
    margin: 0;
}

.pwa-payment-summary {
    padding: 0 1rem;
    margin-bottom: 1rem;
}

.pwa-summary-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.pwa-summary-card h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
}

.pwa-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}

.pwa-summary-row span {
    color: #6b7280;
}

.pwa-summary-row strong {
    color: #1f2937;
    font-weight: 600;
}

.pwa-badge-custom {
    background: #f3e8ff;
    color: #7c3aed;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.pwa-summary-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 1rem 0;
}

.pwa-summary-row.pwa-total {
    font-size: 1.1rem;
}

.pwa-amount-due {
    color: #d97706;
    font-size: 1.25rem;
}

.pwa-payment-instructions {
    padding: 0 1rem;
    margin-bottom: 1rem;
}

.pwa-instruction-card {
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.pwa-instruction-card i {
    font-size: 1.5rem;
    color: #0369a1;
    margin-top: 0.125rem;
}

.pwa-instruction-card h4 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0369a1;
    margin: 0 0 0.25rem 0;
}

.pwa-instruction-card p {
    font-size: 0.85rem;
    color: #0369a1;
    margin: 0;
    line-height: 1.4;
}

.pwa-sandbox-notice {
    margin: 0 1rem 1rem;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    gap: 0.75rem;
}

.pwa-sandbox-notice > i {
    font-size: 1.25rem;
    color: #d97706;
}

.pwa-sandbox-notice strong {
    color: #92400e;
    font-size: 0.9rem;
}

.pwa-sandbox-notice p {
    color: #92400e;
    font-size: 0.85rem;
    margin: 0.25rem 0;
}

.pwa-sandbox-notice ul {
    margin: 0.5rem 0;
    padding-left: 1.25rem;
    color: #92400e;
    font-size: 0.85rem;
}

.pwa-sandbox-notice li {
    margin-bottom: 0.25rem;
}

.pwa-sandbox-notice small {
    color: #b45309;
    font-size: 0.75rem;
}

.pwa-payment-action {
    padding: 0 1rem;
    margin-top: 2rem;
}

.pwa-btn-kkiapay {
    width: 100%;
    padding: 1.25rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    transition: all 0.2s;
    cursor: pointer;
}

.pwa-btn-kkiapay:active {
    transform: scale(0.98);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

.pwa-btn-kkiapay i {
    font-size: 1.25rem;
}

.pwa-payment-note {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.8rem;
    color: #6b7280;
}

.pwa-payment-note i {
    color: #10b981;
    margin-right: 0.25rem;
}

.pwa-loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.9);
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 9999;
}

.pwa-loading-overlay.active {
    display: flex;
}

.pwa-spinner {
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

.pwa-loading-overlay p {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.pwa-loading-overlay small {
    opacity: 0.7;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.kkiapay.me/k.js"></script>
<script>
const KKIAPAY_CONFIG = {
    key: '{{ config("services.kkiapay.public_key", "") }}',
    sandbox: {{ config("services.kkiapay.sandbox", true) ? 'true' : 'false' }},
    url: '{{ asset("images/logo.png") }}'
};

const USER_PHONE = '{{ Auth::user()->phone ?? "" }}';
const USER_NAME = '{{ Auth::user()->name ?? "" }}';
const USER_EMAIL = '{{ Auth::user()->email ?? "" }}';
const AMOUNT = {{ $fundingRequest->expected_payment }};

function formatPhoneNumber(phone) {
    if (!phone) return '';
    phone = phone.replace(/[\s\-]/g, '');
    if (phone.startsWith('+229')) return phone.substring(4);
    if (phone.startsWith('00229')) return phone.substring(5);
    if (/^[967]\d{7}$/.test(phone)) return phone;
    return '';
}

function getKkiapayPhone() {
    const formatted = formatPhoneNumber(USER_PHONE);
    return formatted || '97000000'; // Numéro de test par défaut
}

function openKkiapayPayment() {
    const loadingOverlay = document.getElementById('payment-loading');

    try {
        if (typeof openKkiapayWidget !== 'function') {
            alert('Erreur: Kkiapay non chargé. Veuillez réessayer.');
            return;
        }

        const config = {
            amount: parseInt(AMOUNT),
            key: KKIAPAY_CONFIG.key,
            url: KKIAPAY_CONFIG.url,
            position: 'center',
            sandbox: KKIAPAY_CONFIG.sandbox,
            phone: getKkiapayPhone(),
            name: USER_NAME,
            email: USER_EMAIL,
            callback: '{{ url("/kkiapay/callback") }}',
            data: JSON.stringify({
                request_id: {{ $fundingRequest->id }},
                request_number: '{{ $fundingRequest->request_number }}',
                timestamp: Date.now()
            })
        };

        console.log('Ouverture Kkiapay:', config);

        // Configurer les listeners une seule fois
        if (!window.kkiapayListenersConfigured) {
            setupKkiapayListeners();
            window.kkiapayListenersConfigured = true;
        }

        openKkiapayWidget(config);

    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message);
    }
}

function setupKkiapayListeners() {
    if (typeof addSuccessListener === 'function') {
        addSuccessListener(function(response) {
            console.log('Paiement réussi:', response);
            handlePaymentSuccess(response);
        });
    }

    if (typeof addFailedListener === 'function') {
        addFailedListener(function(error) {
            console.log('Paiement échoué:', error);
            alert('Le paiement a échoué. Veuillez réessayer.');
        });
    }

    if (typeof addKkiapayCloseListener === 'function') {
        addKkiapayCloseListener(function() {
            console.log('Widget fermé');
        });
    }
}

function handlePaymentSuccess(response) {
    const loadingOverlay = document.getElementById('payment-loading');
    loadingOverlay.classList.add('active');

    // Remplir le formulaire et soumettre
    document.getElementById('kkiapay-transaction-input').value = JSON.stringify(response);
    document.getElementById('payment-form').submit();
}

// Vérifier chargement SDK
window.addEventListener('load', function() {
    if (typeof openKkiapayWidget === 'function') {
        console.log('✓ Kkiapay chargé');
    } else {
        console.warn('✗ Kkiapay non détecté');
        document.getElementById('kkiapay-button').disabled = true;
        document.getElementById('kkiapay-button').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur de chargement';
    }
});
</script>
@endpush
