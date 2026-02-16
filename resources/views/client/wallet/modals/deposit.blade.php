<div class="slide-modal" id="depositSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-arrow-down"></i> Faire un dépôt</h3>
            <button class="slide-close" onclick="closeSlide('depositSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <form id="depositForm">
                @csrf

                <!-- Section montant -->
                <div class="form-section">
                    <label class="section-label">
                        <i class="fas fa-money-bill-wave"></i>
                        Montant à déposer
                    </label>

                    <!-- Montants rapides -->
                    <div class="amount-grid">
                        <button type="button" class="amount-card" data-amount="1000">
                            <span class="amount-value">1 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                        <button type="button" class="amount-card" data-amount="5000">
                            <span class="amount-value">5 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                        <button type="button" class="amount-card" data-amount="10000">
                            <span class="amount-value">10 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                        <button type="button" class="amount-card" data-amount="20000">
                            <span class="amount-value">20 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                        <button type="button" class="amount-card" data-amount="50000">
                            <span class="amount-value">50 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                        <button type="button" class="amount-card" data-amount="100000">
                            <span class="amount-value">100 000</span>
                            <span class="amount-currency">FCFA</span>
                        </button>
                    </div>

                    <!-- Input montant personnalisé -->
                    <div class="custom-amount-wrapper">
                        <div class="input-icon-wrapper">
                            <i class="fas fa-pen"></i>
                            <input type="number"
                                   class="form-input"
                                   id="depositAmount"
                                   name="amount"
                                   placeholder="Montant personnalisé"
                                   min="100"
                                   step="100">
                        </div>
                        <span class="input-suffix">FCFA</span>
                    </div>

                    <p class="hint-text">
                        <i class="fas fa-info-circle"></i>
                        Minimum : 100 FCFA • Maximum : 10 000 000 FCFA
                    </p>
                </div>

                <!-- Section téléphone -->
                <div class="form-section">
                    <label class="section-label">
                        <i class="fas fa-mobile-alt"></i>
                        Numéro de paiement
                    </label>

                    <div class="phone-input-wrapper">
                        <span class="phone-prefix">+229</span>
                        <input type="tel"
                               class="form-input phone-input"
                               id="paymentPhone"
                               name="phone"
                               placeholder="01 97 89 90 67"
                               maxlength="10"
                               value="{{ preg_replace('/^(?:\+229|00229|229)/', '', Auth::user()->phone ?? '') }}">
                    </div>

                    <p class="hint-text">
                        <i class="fas fa-shield-alt"></i>
                        Ce numéro sera utilisé pour valider le paiement sur votre téléphone
                    </p>
                </div>

                <!-- Récapitulatif -->
                <div class="summary-card" id="summaryCard" style="display: none;">
                    <div class="summary-row">
                        <span>Montant</span>
                        <strong id="summaryAmount">0 FCFA</strong>
                    </div>
                    <div class="summary-row">
                        <span>Frais</span>
                        <strong class="text-success">0 FCFA</strong>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row total">
                        <span>Total à payer</span>
                        <strong id="summaryTotal">0 FCFA</strong>
                    </div>
                </div>

                <!-- Bouton de paiement -->
                <button type="submit" class="pay-button" id="payButton" disabled>
                    <span class="pay-icon"><i class="fas fa-lock"></i></span>
                    <span class="pay-text">Payer maintenant</span>
                    <span class="pay-amount" id="payButtonAmount">0 F</span>
                </button>

                <!-- Sécurité -->
                <div class="security-badge">
                    <div class="security-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="security-content">
                        <strong>Paiement sécurisé par Kkiapay</strong>
                        <span>PCI DSS compliant • Encryption SSL</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="steps-section">
                    <h4><i class="fas fa-list-ol"></i> Comment ça marche</h4>
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-text">
                            <strong>Saisissez le montant</strong>
                            <span>Choisissez un montant rapide ou personnalisé</span>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-text">
                            <strong>Validez sur téléphone</strong>
                            <span>Recevez une notification pour confirmer</span>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-text">
                            <strong>Crédit instantané</strong>
                            <span>Votre wallet est crédité immédiatement</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="deposit-loading" class="loading-overlay">
    <div class="loading-spinner"></div>
    <p class="loading-text">Traitement en cours...</p>
    <p class="loading-subtext">Veuillez valider sur votre téléphone</p>
</div>

@if(config('services.kkiapay.sandbox', true))
<!-- Mode Test -->
{{-- <div class="sandbox-banner">
    <i class="fas fa-flask"></i>
    <div class="sandbox-content">
        <strong>Mode Test (Sandbox)</strong>
        <p>Utilisez : <strong>97000000</strong> | OTP: <strong>123456</strong> | Code: <strong>1234</strong></p>
    </div>
</div> --}}
@endif

<style>
/* ===== Modal Slide ===== */
.slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-modal.show {
    opacity: 1;
    visibility: visible;
}

.slide-modal.show .slide-content {
    transform: translateY(0);
}

.slide-content {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    background: #ffffff;
    border-radius: 24px 24px 0 0;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    flex-shrink: 0;
}

.slide-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.slide-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.slide-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.slide-body {
    padding: 24px;
    overflow-y: auto;
    flex: 1;
}

/* ===== Form Sections ===== */
.form-section {
    margin-bottom: 24px;
}

.section-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.section-label i {
    color: #667eea;
}

/* ===== Amount Grid ===== */
.amount-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.amount-card {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.amount-card:hover {
    border-color: #667eea;
    background: #eef2ff;
    transform: translateY(-2px);
}

.amount-card.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transform: translateY(-2px);
}

.amount-value {
    font-size: 1.125rem;
    font-weight: 700;
}

.amount-currency {
    font-size: 0.75rem;
    font-weight: 500;
    opacity: 0.8;
}

/* ===== Custom Amount ===== */
.custom-amount-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.input-icon-wrapper {
    position: relative;
    flex: 1;
}

.input-icon-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 1rem;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s;
    background: white;
    color: #111827;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-icon-wrapper .form-input {
    padding-left: 44px;
}

.input-suffix {
    position: absolute;
    right: 16px;
    color: #6b7280;
    font-weight: 500;
    font-size: 0.875rem;
}

/* ===== Phone Input ===== */
.phone-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.phone-input-wrapper:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.phone-prefix {
    background: #f3f4f6;
    padding: 14px 16px;
    font-weight: 600;
    color: #374151;
    border-right: 2px solid #e5e7eb;
    font-size: 1rem;
}

.phone-input {
    border: none !important;
    border-radius: 0 !important;
    flex: 1;
}

.phone-input:focus {
    box-shadow: none !important;
}

/* ===== Summary Card ===== */
.summary-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e5e7eb;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 0.9375rem;
}

.summary-row span {
    color: #6b7280;
}

.summary-row strong {
    color: #111827;
    font-weight: 600;
}

.summary-row.total {
    font-size: 1.125rem;
    margin-bottom: 0;
    padding-top: 12px;
    border-top: 2px solid #e5e7eb;
}

.summary-row.total strong {
    color: #667eea;
    font-size: 1.25rem;
}

.text-success {
    color: #10b981 !important;
}

.summary-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 12px 0;
}

/* ===== Pay Button ===== */
.pay-button {
    width: 100%;
    padding: 18px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 16px;
    font-size: 1.125rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
    margin-bottom: 20px;
}

.pay-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
}

.pay-button:active:not(:disabled) {
    transform: translateY(0);
}

.pay-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #9ca3af;
    box-shadow: none;
}

.pay-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pay-text {
    flex: 1;
    text-align: center;
}

.pay-amount {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 1rem;
}

/* ===== Security Badge ===== */
.security-badge {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #fef3c7;
    border-radius: 12px;
    margin-bottom: 24px;
    border: 1px solid #fde68a;
}

.security-icon {
    width: 48px;
    height: 48px;
    background: #f59e0b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.security-content {
    flex: 1;
}

.security-content strong {
    display: block;
    color: #92400e;
    font-size: 0.9375rem;
    margin-bottom: 4px;
}

.security-content span {
    color: #b45309;
    font-size: 0.875rem;
}

/* ===== Steps Section ===== */
.steps-section {
    background: #eff6ff;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #dbeafe;
}

.steps-section h4 {
    margin: 0 0 16px 0;
    color: #1e40af;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.step-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}

.step-item:last-child {
    margin-bottom: 0;
}

.step-number {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.step-text {
    flex: 1;
}

.step-text strong {
    display: block;
    color: #1e40af;
    font-size: 0.9375rem;
    margin-bottom: 2px;
}

.step-text span {
    color: #3b82f6;
    font-size: 0.875rem;
}

/* ===== Hint Text ===== */
.hint-text {
    margin: 8px 0 0 0;
    font-size: 0.8125rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.hint-text i {
    color: #9ca3af;
    font-size: 0.875rem;
}

/* ===== Loading Overlay ===== */
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(8px);
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 10000;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255, 255, 255, 0.2);
    border-top-color: #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 24px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.loading-subtext {
    font-size: 0.9375rem;
    opacity: 0.7;
}

/* ===== Sandbox Banner ===== */
.sandbox-banner {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #fef3c7;
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    z-index: 10001;
    max-width: 90%;
}

.sandbox-banner i {
    font-size: 1.5rem;
    color: #d97706;
}

.sandbox-content strong {
    display: block;
    color: #92400e;
    margin-bottom: 4px;
}

.sandbox-content p {
    margin: 0;
    color: #b45309;
    font-size: 0.875rem;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .slide-content {
        max-width: 100%;
        border-radius: 20px 20px 0 0;
    }

    .amount-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .slide-body {
        padding: 20px;
    }
}
</style>

@push('scripts')
<script src="https://cdn.kkiapay.me/k.js"></script>
<script>
// Configuration
const KKIAPAY_CONFIG = {
    key: '{{ config("services.kkiapay.public_key") }}',
    sandbox: {{ config("services.kkiapay.sandbox", true) ? 'true' : 'false' }},
    url: '{{ asset("images/logo.png") }}'
};

const USER_DATA = {
    name: '{{ Auth::user()->name }}',
    email: '{{ Auth::user()->email }}',
    walletId: {{ $wallet->id }}
};

// Numéro brut de la base: 2290197899067 → on enlève le préfixe 229
const RAW_PHONE = '{{ Auth::user()->phone ?? "" }}';

let currentAmount = 0;
let kkiapayListenersConfigured = false;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initDepositModal();

    // Nettoyer le numéro au chargement
    const phoneInput = document.getElementById('paymentPhone');
    if (phoneInput) {
        phoneInput.value = cleanPhoneNumber(RAW_PHONE);
    }
});

/**
 * Nettoie le numéro de téléphone en retirant les indicatifs
 */
function cleanPhoneNumber(phone) {
    if (!phone) return '';

    phone = phone.toString().replace(/[\s\-]/g, '');

    if (phone.startsWith('+229')) {
        phone = phone.substring(4);
    } else if (phone.startsWith('00229')) {
        phone = phone.substring(5);
    } else if (phone.startsWith('229') && phone.length > 10) {
        phone = phone.substring(3);
    }

    return phone;
}

function initDepositModal() {
    const amountCards = document.querySelectorAll('.amount-card');
    const depositAmountInput = document.getElementById('depositAmount');
    const depositForm = document.getElementById('depositForm');
    const phoneInput = document.getElementById('paymentPhone');

    // Gestion des montants rapides
    amountCards.forEach(card => {
        card.addEventListener('click', function() {
            const amount = parseInt(this.getAttribute('data-amount'));
            selectAmount(amount, this);
        });
    });

    // Gestion du montant personnalisé
    if (depositAmountInput) {
        depositAmountInput.addEventListener('input', function() {
            const amount = parseInt(this.value) || 0;
            selectAmount(amount, null);
        });

        depositAmountInput.addEventListener('blur', function() {
            const amount = parseInt(this.value) || 0;
            if (amount > 0 && amount < 100) {
                showToast('warning', 'Montant minimum', 'Le montant minimum est de 100 FCFA');
                this.value = 100;
                selectAmount(100, null);
            }
        });
    }

    // Formatage du numéro de téléphone à la saisie
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            this.value = value;
            updatePayButton();
        });
    }

    // Soumission du formulaire
    if (depositForm) {
        depositForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            openKkiapayPayment();
        });
    }
}

function selectAmount(amount, selectedCard) {
    currentAmount = amount;

    document.querySelectorAll('.amount-card').forEach(card => {
        card.classList.remove('selected');
    });

    if (selectedCard) {
        selectedCard.classList.add('selected');
        const input = document.getElementById('depositAmount');
        if (input && input.value != amount) {
            input.value = amount;
        }
    } else {
        const matchingCard = document.querySelector(`.amount-card[data-amount="${amount}"]`);
        if (matchingCard) {
            matchingCard.classList.add('selected');
        }
    }

    updateSummary();
    updatePayButton();
}

function updateSummary() {
    const summaryCard = document.getElementById('summaryCard');
    const summaryAmount = document.getElementById('summaryAmount');
    const summaryTotal = document.getElementById('summaryTotal');
    const payButtonAmount = document.getElementById('payButtonAmount');

    if (currentAmount >= 100) {
        summaryCard.style.display = 'block';
        const formatted = new Intl.NumberFormat('fr-FR').format(currentAmount) + ' FCFA';
        summaryAmount.textContent = formatted;
        summaryTotal.textContent = formatted;
        payButtonAmount.textContent = new Intl.NumberFormat('fr-FR').format(currentAmount) + ' F';
    } else {
        summaryCard.style.display = 'none';
        payButtonAmount.textContent = '0 F';
    }
}

function updatePayButton() {
    const phoneInput = document.getElementById('paymentPhone');
    const payButton = document.getElementById('payButton');
    const phone = phoneInput ? phoneInput.value.replace(/\s/g, '') : '';

    const isValid = currentAmount >= 100 && phone.length >= 8;
    payButton.disabled = !isValid;
}

function validateForm() {
    const phoneInput = document.getElementById('paymentPhone');
    const phone = phoneInput ? phoneInput.value.replace(/\s/g, '') : '';

    if (currentAmount < 100) {
        showToast('error', 'Montant invalide', 'Le montant minimum est de 100 FCFA');
        document.getElementById('depositAmount').focus();
        return false;
    }

    if (phone.length < 8) {
        showToast('error', 'Numéro invalide', 'Veuillez saisir un numéro de téléphone valide');
        phoneInput.focus();
        return false;
    }

    return true;
}

function openKkiapayPayment() {
    if (typeof openKkiapayWidget !== 'function') {
        showToast('error', 'Erreur', 'Le service de paiement n\'est pas chargé. Veuillez rafraîchir la page.');
        return;
    }

    const phoneInput = document.getElementById('paymentPhone');
    let phoneNumber = phoneInput.value.replace(/\s/g, '');
    phoneNumber = cleanPhoneNumber(phoneNumber);

    console.log('Numéro original:', phoneInput.value);
    console.log('Numéro nettoyé:', phoneNumber);

    const config = {
        amount: parseInt(currentAmount),
        key: KKIAPAY_CONFIG.key,
        url: KKIAPAY_CONFIG.url,
        position: 'center',
        sandbox: KKIAPAY_CONFIG.sandbox,
        phone: phoneNumber,
        name: USER_DATA.name,
        email: USER_DATA.email,
        callback: '{{ url("/kkiapay/callback") }}',
        data: JSON.stringify({
            wallet_id: USER_DATA.walletId,
            amount: currentAmount,
            type: 'deposit',
            phone: phoneNumber,
            timestamp: Date.now()
        })
    };

    console.log('Configuration Kkiapay:', config);

    if (!kkiapayListenersConfigured) {
        setupKkiapayListeners();
        kkiapayListenersConfigured = true;
    }

    try {
        openKkiapayWidget(config);
    } catch (error) {
        console.error('Erreur ouverture Kkiapay:', error);
        showToast('error', 'Erreur', 'Impossible d\'ouvrir le paiement');
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
        addFailedListener(function(response) {
            console.log('Paiement échoué:', response);
            showToast('error', 'Paiement échoué', response.message || 'Le paiement a été refusé');
            hideLoading();
        });
    }

    if (typeof addPendingListener === 'function') {
        addPendingListener(function(response) {
            console.log('Paiement en attente:', response);
            showLoading('Validation en attente...', 'Veuillez confirmer sur votre téléphone');
        });
    }

    if (typeof addKkiapayCloseListener === 'function') {
        addKkiapayCloseListener(function() {
            console.log('Widget fermé');
            hideLoading();
        });
    }
}

async function handlePaymentSuccess(kkiapayResponse) {
    showLoading('Traitement en cours...', 'Ne fermez pas cette page');

    try {
        let additionalData = {};
        try {
            additionalData = JSON.parse(kkiapayResponse.data || '{}');
        } catch(e) {
            console.warn('Parse data error:', e);
        }

        const returnedPhone = cleanPhoneNumber(kkiapayResponse.phone || additionalData.phone || '');
        const walletId = parseInt(additionalData.wallet_id) || USER_DATA.walletId;

        const payload = {
            transaction_id: kkiapayResponse.transactionId,
            status: 'success',
            amount: parseInt(currentAmount),
            phone: returnedPhone,
            wallet_id: walletId,
            kkiapay_data: kkiapayResponse
        };

        console.log('Envoi au serveur:', payload);

        // URL CORRIGÉE - Route publique sans /client/
        const response = await fetch('/wallet/deposit/callback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        console.log('Response status:', response.status);

        const responseText = await response.text();
        console.log('Response text:', responseText);

        let result;
        try {
            result = JSON.parse(responseText);
        } catch {
            throw new Error('Réponse serveur invalide: ' + responseText.substring(0, 200));
        }

        if (!response.ok) {
            throw new Error(result.message || `Erreur HTTP ${response.status}`);
        }

        if (result.success) {
            showToast('success', 'Dépôt réussi !',
                'Votre compte a été crédité de ' + new Intl.NumberFormat('fr-FR').format(currentAmount) + ' FCFA');

            closeSlide('depositSlide');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToast('warning', 'Attention', result.message);
            hideLoading();
        }

    } catch (error) {
        console.error('Erreur complète:', error);
        showToast('error', 'Erreur', error.message);
        hideLoading();
    }
}

function showLoading(text, subtext) {
    const overlay = document.getElementById('deposit-loading');
    if (overlay) {
        overlay.querySelector('.loading-text').textContent = text || 'Traitement en cours...';
        overlay.querySelector('.loading-subtext').textContent = subtext || 'Veuillez patienter';
        overlay.classList.add('active');
    }
}

function hideLoading() {
    const overlay = document.getElementById('deposit-loading');
    if (overlay) {
        overlay.classList.remove('active');
    }
}

function showDepositModal() {
    const modal = document.getElementById('depositSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            const amountInput = document.getElementById('depositAmount');
            if (amountInput && !amountInput.value) {
                const firstCard = document.querySelector('.amount-card');
                if (firstCard) firstCard.click();
            }
        }, 100);
    }
}

function closeSlide(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        hideLoading();
    }
}

function showToast(type, title, message) {
    if (window.toast && typeof window.toast[type] === 'function') {
        window.toast[type](title, message);
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type === 'error' ? 'error' : type === 'success' ? 'success' : 'info',
            title: title,
            text: message,
            timer: type === 'success' ? 3000 : undefined
        });
    } else {
        alert(`${title}: ${message}`);
    }
}
</script>
@endpush
