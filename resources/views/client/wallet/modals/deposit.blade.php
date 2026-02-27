<!-- Modal Dépôt Kkiapay - Version Professionnelle -->
<div class="slide-modal" id="depositSlide">
    <div class="slide-content" style="max-width: 480px;">

        <!-- Header -->
        <div class="slide-header" style="background: linear-gradient(135deg, var(--success-500), var(--success-700));">
            <h3><i class="fas fa-arrow-down"></i> Déposer des fonds</h3>
            <button class="slide-close" onclick="closeSlide('depositSlide')" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="slide-body">

            <!-- Étape 1: Formulaire -->
            <div id="depositStep1">

                <!-- Info sécurité -->
                <div class="deposit-info-box">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Paiement sécurisé par Kkiapay</strong>
                        <p>Votre transaction est cryptée et protégée.</p>
                    </div>
                </div>

                <!-- Section Test (visible uniquement en sandbox) -->
                @if(config('services.kkiapay.sandbox', true))
                <div class="test-numbers-box">
                    <div class="test-header" onclick="toggleTestNumbers()">
                        <i class="fas fa-vial"></i>
                        <strong>Mode Test - Numéros de démonstration</strong>
                        <i class="fas fa-chevron-down" id="testToggleIcon"></i>
                    </div>
                    <div class="test-content" id="testContent">
                        <div class="test-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Utilisez ces numéros pour tester sans argent réel</span>
                        </div>
                        <div class="test-nums">
                            <button type="button" class="test-num success" onclick="fillPhone('97000000')">
                                <span class="operator"><i class="fas fa-check-circle"></i> Succès MTN</span>
                                <span class="number">97 00 00 00</span>
                            </button>
                            <button type="button" class="test-num error" onclick="fillPhone('61000001')">
                                <span class="operator"><i class="fas fa-times-circle"></i> Échec MTN</span>
                                <span class="number">61 00 00 01</span>
                            </button>
                        </div>
                        <div class="test-hint">
                            <i class="fas fa-key"></i>
                            <span>Code OTP: <strong>123456</strong></span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Formulaire -->
                <form id="depositForm" onsubmit="return false;">
                    @csrf

                    <!-- Montant -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Montant à déposer
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-money-bill-wave input-icon"></i>
                            <input type="number"
                                   class="form-control"
                                   id="depositAmount"
                                   name="amount"
                                   placeholder="1000"
                                   min="100"
                                   step="100"
                                   required>
                        </div>
                        <div class="amount-selector">
                            <button type="button" class="amount-btn" onclick="selectAmount(1000)">1 000</button>
                            <button type="button" class="amount-btn" onclick="selectAmount(5000)">5 000</button>
                            <button type="button" class="amount-btn" onclick="selectAmount(10000)">10 000</button>
                            <button type="button" class="amount-btn" onclick="selectAmount(20000)">20 000</button>
                            <button type="button" class="amount-btn" onclick="selectAmount(50000)">50 000</button>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Numéro Mobile Money
                        </label>
                        <div class="phone-input-wrapper">
                            <span class="phone-prefix">+229</span>
                            <input type="tel"
                                   class="form-control phone-input"
                                   id="depositPhone"
                                   name="phone"
                                   placeholder="97000000"
                                   maxlength="8"
                                   required
                                   inputmode="numeric"
                                   pattern="[0-9]*">
                        </div>
                        <small class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            8 chiffres sans l'indicatif +229. Ex: 97000000
                        </small>
                    </div>

                    <!-- Récapitulatif -->
                    <div class="deposit-summary" id="summaryCard" style="display: none;">
                        <div class="summary-row">
                            <span>Montant:</span>
                            <strong id="summaryAmount">0 FCFA</strong>
                        </div>
                        <div class="summary-row">
                            <span>Frais:</span>
                            <strong class="text-free">Gratuit</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Total à payer:</span>
                            <strong id="summaryTotal">0 FCFA</strong>
                        </div>
                    </div>

                    <!-- Bouton principal -->
                    <button type="button"
                            class="btn-pay"
                            id="btnPay"
                            onclick="startDeposit()"
                            disabled>
                        <i class="fas fa-credit-card"></i>
                        <span>Payer maintenant</span>
                    </button>
                </form>
            </div>

            <!-- Étape 2: Traitement -->
            <div id="depositStep2" style="display: none;">
                <div class="processing-state">
                    <div class="spinner-large">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                    <h4>Paiement en cours...</h4>
                    <p>Validez sur votre téléphone avec le code <strong>123456</strong></p>

                    <div class="transaction-info">
                        <div class="info-row">
                            <span>Montant:</span>
                            <strong id="processAmount">-</strong>
                        </div>
                        <div class="info-row">
                            <span>Référence:</span>
                            <strong id="processRef">-</strong>
                        </div>
                        <div class="info-row">
                            <span>Numéro:</span>
                            <strong id="processPhone">-</strong>
                        </div>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <p class="status-text" id="statusText">Connexion à Kkiapay...</p>

                    <button type="button" class="btn-check" onclick="checkDepositStatus()">
                        <i class="fas fa-sync"></i> Vérifier manuellement
                    </button>
                </div>
            </div>

            <!-- Étape 3: Résultat -->
            <div id="depositStep3" style="display: none;">
                <div class="result-state" id="resultState">
                    <div class="result-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 id="resultTitle">Paiement réussi !</h4>
                    <p id="resultMessage">Votre compte a été crédité.</p>

                    <div class="transaction-details" id="transactionDetails"></div>

                    <button type="button" class="btn-done" onclick="closeSlide('depositSlide'); refreshBalance();">
                        <i class="fas fa-check"></i> Terminé
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* ========== STYLES SPÉCIFIQUES AU MODAL DÉPÔT ========== */

/* Info box */
.deposit-info-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: var(--success-50);
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    border-left: 4px solid var(--success-500);
}

.deposit-info-box i {
    color: var(--success-500);
    font-size: 1.5rem;
    margin-top: 2px;
}

.deposit-info-box strong {
    display: block;
    color: var(--success-700);
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.deposit-info-box p {
    margin: 0;
    color: var(--success-600);
    font-size: 0.85rem;
}

/* Test numbers */
.test-numbers-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px dashed #f59e0b;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
    overflow: hidden;
}

.test-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    cursor: pointer;
    color: #92400e;
    font-size: 0.9rem;
    font-weight: 600;
}

.test-header i:first-child {
    color: #f59e0b;
}

.test-header .fa-chevron-down {
    margin-left: auto;
    transition: transform 0.3s;
}

.test-header.active .fa-chevron-down {
    transform: rotate(180deg);
}

.test-content {
    display: none;
    padding: 0 16px 16px;
}

.test-content.show {
    display: block;
}

.test-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: var(--border-radius);
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: #78350f;
}

.test-nums {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.test-num {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    background: white;
    border: 2px solid transparent;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.2s;
}

.test-num.success {
    border-color: var(--success-500);
}

.test-num.success:hover {
    background: var(--success-500);
    color: white;
}

.test-num.error {
    border-color: var(--error-500);
}

.test-num.error:hover {
    background: var(--error-500);
    color: white;
}

.test-num .operator {
    font-size: 0.85rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.test-num .number {
    font-size: 1.1rem;
    font-weight: 700;
    font-family: monospace;
}

.test-hint {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #fbbf24;
    font-size: 0.9rem;
    color: #78350f;
}

/* Form elements */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--secondary-700);
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary-400);
    font-size: 1rem;
}

.form-control {
    width: 100%;
    padding: 12px 14px 12px 44px;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: all 0.2s;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--success-500);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Phone input */
.phone-input-wrapper {
    display: flex;
    align-items: center;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: all 0.2s;
}

.phone-input-wrapper:focus-within {
    border-color: var(--success-500);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.phone-prefix {
    padding: 12px 16px;
    background: var(--secondary-100);
    color: var(--secondary-700);
    font-weight: 700;
    font-size: 1.1rem;
    border-right: 2px solid var(--secondary-200);
    white-space: nowrap;
}

.phone-input {
    flex: 1;
    border: none !important;
    padding: 12px 14px !important;
    font-size: 1.1rem;
    letter-spacing: 2px;
    font-family: monospace;
}

.phone-input:focus {
    box-shadow: none !important;
}

.form-hint {
    display: block;
    margin-top: 8px;
    color: var(--secondary-500);
    font-size: 0.8rem;
}

/* Amount selector */
.amount-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.amount-btn {
    padding: 8px 16px;
    border: 2px solid var(--secondary-200);
    border-radius: 20px;
    background: white;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
    color: var(--secondary-700);
}

.amount-btn:hover {
    border-color: var(--success-500);
    color: var(--success-600);
}

.amount-btn.active {
    background: var(--success-500);
    border-color: var(--success-500);
    color: white;
}

/* Summary */
.deposit-summary {
    background: var(--secondary-50);
    padding: 16px;
    border-radius: var(--border-radius);
    margin: 20px 0;
    border: 2px solid var(--secondary-200);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: var(--secondary-600);
}

.summary-row.total {
    border-top: 2px solid var(--secondary-200);
    padding-top: 12px;
    margin-top: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--secondary-900);
}

.text-free {
    color: var(--success-600);
    font-weight: 600;
}

/* Buttons */
.btn-pay, .btn-done {
    width: 100%;
    padding: 14px;
    border-radius: var(--border-radius);
    border: none;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-pay {
    background: linear-gradient(135deg, var(--success-500), var(--success-700));
    color: white;
}

.btn-pay:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

.btn-pay:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: var(--secondary-400);
}

.btn-done {
    background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
    color: white;
    margin-top: 20px;
}

/* Processing state */
.processing-state {
    text-align: center;
    padding: 30px 20px;
}

.spinner-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--success-500), var(--success-700));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: white;
    font-size: 2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
}

.processing-state h4 {
    color: var(--secondary-900);
    margin-bottom: 8px;
    font-size: 1.25rem;
}

.processing-state p {
    color: var(--secondary-600);
    margin-bottom: 20px;
}

.transaction-info {
    background: var(--secondary-50);
    padding: 16px;
    border-radius: var(--border-radius);
    text-align: left;
    max-width: 280px;
    margin: 0 auto 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-row span {
    color: var(--secondary-500);
}

.info-row strong {
    color: var(--secondary-900);
    font-weight: 600;
}

/* Progress bar */
.progress-bar {
    width: 100%;
    height: 4px;
    background: var(--secondary-200);
    border-radius: 2px;
    margin: 20px 0;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-500), var(--success-700));
    width: 0%;
    animation: progress 30s linear;
}

@keyframes progress {
    0% { width: 0%; }
    100% { width: 100%; }
}

.status-text {
    color: var(--secondary-500);
    font-size: 0.9rem;
    text-align: center;
    margin-bottom: 16px;
}

.btn-check {
    padding: 10px 20px;
    border-radius: var(--border-radius);
    border: 1px solid var(--secondary-300);
    background: white;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 auto;
    color: var(--secondary-700);
    transition: all 0.2s;
}

.btn-check:hover {
    background: var(--secondary-50);
    border-color: var(--secondary-400);
}

/* Result state */
.result-state {
    text-align: center;
    padding: 30px 20px;
}

.result-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 2.5rem;
}

.result-state.success .result-icon {
    background: var(--success-100);
    color: var(--success-600);
}

.result-state.error .result-icon {
    background: var(--error-100);
    color: var(--error-600);
}

.result-state h4 {
    color: var(--secondary-900);
    margin-bottom: 8px;
    font-size: 1.25rem;
}

.result-state p {
    color: var(--secondary-600);
    margin-bottom: 20px;
}

.transaction-details {
    background: var(--secondary-50);
    padding: 16px;
    border-radius: var(--border-radius);
    text-align: left;
    max-width: 320px;
    margin: 0 auto 20px;
}

/* Responsive */
@media (max-width: 480px) {
    .phone-prefix {
        padding: 10px 12px;
        font-size: 1rem;
    }

    .phone-input {
        padding: 10px 12px !important;
        font-size: 1rem;
    }

    .test-num {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .amount-selector {
        gap: 6px;
    }

    .amount-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
}
</style>

<script>
/**
 * SYSTÈME DE DÉPÔT KKIAPAY
 * Intégration professionnelle avec webhook
 */

(function() {
    'use strict';

    // Configuration depuis Laravel
    const CONFIG = {
        kkiapayKey: '{{ config("services.kkiapay.public_key") }}',
        sandbox: {{ config("services.kkiapay.sandbox", true) ? 'true' : 'false' }},
        apiUrl: '{{ route("client.wallet.deposit") }}',
        statusUrl: '{{ url("/kkiapay/status") }}',
        csrf: document.querySelector('meta[name="csrf-token"]')?.content
    };

    // État
    let currentTx = null;
    let pollTimer = null;

    // ========== FONCTIONS GLOBALES ==========

    /**
     * Ouvrir le modal (appelé depuis l'index)
     */
    window.showDepositModal = function() {
        if (!navigator.onLine) {
            showToast('Connexion Internet requise', 'error');
            return;
        }

        const modal = document.getElementById('depositSlide');
        if (!modal) {
            console.error('Modal depositSlide non trouvé');
            return;
        }

        resetForm();
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    /**
     * Toggle section test
     */
    window.toggleTestNumbers = function() {
        const content = document.getElementById('testContent');
        const header = document.querySelector('.test-header');
        const icon = document.getElementById('testToggleIcon');

        if (content && header) {
            const isOpen = content.classList.toggle('show');
            header.classList.toggle('active', isOpen);
            if (icon) icon.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    };

    /**
     * Remplir le numéro de téléphone
     */
    window.fillPhone = function(number) {
        const input = document.getElementById('depositPhone');
        if (input) {
            input.value = number;
            validateForm();
            showToast('Numéro sélectionné: +229 ' + formatPhone(number), 'success');
        }
    };

    /**
     * Sélectionner un montant rapide
     */
    window.selectAmount = function(amount) {
        const input = document.getElementById('depositAmount');
        if (!input) return;

        input.value = amount;

        // Mettre à jour les boutons
        document.querySelectorAll('.amount-btn').forEach(btn => {
            const btnAmount = parseInt(btn.textContent.replace(/\s/g, ''));
            btn.classList.toggle('active', btnAmount === amount);
        });

        validateForm();
    };

    /**
     * DÉMARRER LE PAIEMENT
     */
    window.startDeposit = async function() {
        const amount = parseFloat(document.getElementById('depositAmount')?.value);
        const phoneRaw = document.getElementById('depositPhone')?.value?.trim();

        // Validation
        if (!amount || amount < 100) {
            showToast('Le montant minimum est de 100 FCFA', 'error');
            return;
        }

        if (!/^\d{8}$/.test(phoneRaw)) {
            showToast('Veuillez entrer exactement 8 chiffres', 'error');
            document.getElementById('depositPhone').focus();
            return;
        }

        // Vérifier SDK Kkiapay
        if (typeof window.openKkiapayWidget !== 'function') {
            showToast('Erreur: SDK Kkiapay non chargé', 'error');
            console.error('SDK Kkiapay non disponible');
            return;
        }

        try {
            setLoading(true);

            // 1. CRÉER LA TRANSACTION
            const response = await fetch(CONFIG.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CONFIG.csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    phone: phoneRaw
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erreur création transaction');
            }

            // Sauvegarder la transaction
            currentTx = {
                reference: data.reference,
                amount: amount,
                phone: phoneRaw,
                phoneDisplay: '+229 ' + formatPhone(phoneRaw)
            };

            // Afficher étape traitement
            showStep(2);
            document.getElementById('processAmount').textContent = formatMoney(amount);
            document.getElementById('processRef').textContent = data.reference;
            document.getElementById('processPhone').textContent = currentTx.phoneDisplay;
            document.getElementById('statusText').textContent = 'Connexion à Kkiapay...';

            // 2. OUVRIR WIDGET KKIAPAY
            const kkiapayConfig = {
                amount: amount,
                key: CONFIG.kkiapayKey,
                sandbox: CONFIG.sandbox,
                phone: '229' + phoneRaw,  // Format: 229XXXXXXXX
                email: '{{ auth()->user()->email ?? "" }}',
                firstname: '{{ auth()->user()->first_name ?? auth()->user()->name ?? "" }}',
                lastname: '{{ auth()->user()->last_name ?? "" }}',
                data: {
                    reference: data.reference,
                    user_id: {{ auth()->id() ?? 'null' }}
                }
            };

            console.log('Ouverture Kkiapay:', kkiapayConfig);

            window.openKkiapayWidget(kkiapayConfig);

            // 3. DÉMARRER POLLING
            // Le webhook va créditer le wallet, on vérifie toutes les 3s
            startPolling(data.reference);

        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur: ' + error.message, 'error');
            setLoading(false);
        }
    };

    /**
     * Vérifier manuellement le statut
     */
    window.checkDepositStatus = async function() {
        if (!currentTx?.reference) {
            showToast('Aucune transaction en cours', 'error');
            return;
        }

        await checkStatus(currentTx.reference, true);
    };

    // ========== FONCTIONS INTERNES ==========

    /**
     * Polling automatique
     */
    function startPolling(reference) {
        let attempts = 0;
        const maxAttempts = 60; // 3 minutes max

        document.getElementById('statusText').textContent = 'En attente de validation...';

        pollTimer = setInterval(async () => {
            attempts++;

            const done = await checkStatus(reference, false);

            if (done || attempts >= maxAttempts) {
                clearInterval(pollTimer);
                pollTimer = null;

                if (attempts >= maxAttempts && !done) {
                    document.getElementById('statusText').textContent = 'Délai dépassé. Vérifiez plus tard.';
                    showToast('Délai dépassé', 'warning');
                }
            }
        }, 3000);
    }

    /**
     * Vérifier le statut
     */
    async function checkStatus(reference, manual = false) {
        try {
            if (manual) {
                document.getElementById('statusText').textContent = 'Vérification...';
            }

            const response = await fetch(`${CONFIG.statusUrl}/${reference}`, {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (data.status === 'completed' || data.is_credited) {
                // SUCCÈS!
                showResult(true, 'Paiement confirmé !',
                    `${formatMoney(data.amount)} ont été crédités sur votre compte.`);
                refreshBalance();
                return true;

            } else if (data.status === 'failed') {
                // ÉCHEC
                showResult(false, 'Paiement échoué',
                    'La transaction n\'a pas pu être complétée.');
                return true;
            }

            // En cours...
            if (manual) {
                document.getElementById('statusText').textContent = 'Toujours en attente...';
            }
            return false;

        } catch (error) {
            console.error('Erreur vérification:', error);
            if (manual) {
                document.getElementById('statusText').textContent = 'Erreur de vérification';
            }
            return false;
        }
    }

    /**
     * Afficher le résultat
     */
    function showResult(success, title, message) {
        showStep(3);

        const state = document.getElementById('resultState');
        const icon = state.querySelector('.result-icon i');
        const titleEl = document.getElementById('resultTitle');
        const msgEl = document.getElementById('resultMessage');
        const details = document.getElementById('transactionDetails');

        state.className = 'result-state ' + (success ? 'success' : 'error');
        icon.className = success ? 'fas fa-check-circle' : 'fas fa-times-circle';
        titleEl.textContent = title;
        msgEl.textContent = message;

        if (currentTx) {
            details.innerHTML = `
                <div class="info-row"><span>Montant:</span><strong>${formatMoney(currentTx.amount)}</strong></div>
                <div class="info-row"><span>Numéro:</span><strong>${currentTx.phoneDisplay}</strong></div>
                <div class="info-row"><span>Référence:</span><strong>${currentTx.reference}</strong></div>
            `;
        }
    }

    /**
     * Changer d'étape
     */
    function showStep(step) {
        document.getElementById('depositStep1').style.display = step === 1 ? 'block' : 'none';
        document.getElementById('depositStep2').style.display = step === 2 ? 'block' : 'none';
        document.getElementById('depositStep3').style.display = step === 3 ? 'block' : 'none';
    }

    /**
     * Valider le formulaire
     */
    function validateForm() {
        const amount = parseFloat(document.getElementById('depositAmount')?.value) || 0;
        const phone = document.getElementById('depositPhone')?.value?.trim() || '';
        const isValid = amount >= 100 && /^\d{8}$/.test(phone);

        const btn = document.getElementById('btnPay');
        if (btn) btn.disabled = !isValid;

        // Mettre à jour le résumé
        const summary = document.getElementById('summaryCard');
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryTotal = document.getElementById('summaryTotal');

        if (summary && summaryAmount && summaryTotal) {
            summary.style.display = amount >= 100 ? 'block' : 'none';
            const formatted = formatMoney(amount);
            summaryAmount.textContent = formatted;
            summaryTotal.textContent = formatted;
        }
    }

    /**
     * Réinitialiser le formulaire
     */
    function resetForm() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }

        currentTx = null;

        const form = document.getElementById('depositForm');
        if (form) form.reset();

        document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));

        const summary = document.getElementById('summaryCard');
        if (summary) summary.style.display = 'none';

        setLoading(false);
        showStep(1);

        // Fermer la section test
        const testContent = document.getElementById('testContent');
        const testHeader = document.querySelector('.test-header');
        if (testContent && testHeader) {
            testContent.classList.remove('show');
            testHeader.classList.remove('active');
        }
    }

    /**
     * Mettre en chargement
     */
    function setLoading(loading) {
        const btn = document.getElementById('btnPay');
        if (!btn) return;

        btn.disabled = loading;
        btn.innerHTML = loading
            ? '<i class="fas fa-spinner fa-spin"></i> <span>Préparation...</span>'
            : '<i class="fas fa-credit-card"></i> <span>Payer maintenant</span>';
    }

    /**
     * Formater un numéro de téléphone
     */
    function formatPhone(number) {
        return number.replace(/(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4');
    }

    /**
     * Formater un montant
     */
    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
    }

    // ========== INITIALISATION ==========

    document.addEventListener('DOMContentLoaded', function() {
        // Validation en temps réel
        const amountInput = document.getElementById('depositAmount');
        const phoneInput = document.getElementById('depositPhone');

        if (amountInput) amountInput.addEventListener('input', validateForm);
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                // N'accepter que les chiffres
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
                validateForm();
            });
        }
    });

})();
</script>
