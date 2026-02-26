<!-- Modal Dépôt Kkiapay -->
<div class="slide-modal" id="depositSlide">
    <div class="slide-content">
        <div class="slide-header" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
            <h3><i class="fas fa-arrow-down"></i> Déposer des fonds</h3>
            <button class="slide-close" onclick="closeDepositModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <form id="depositForm" onsubmit="return false;">
                @csrf

                <!-- Étape 1 : Formulaire -->
                <div id="depositStep1">
                    <div class="deposit-info-box">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Paiement sécurisé</strong>
                            <p>Votre transaction est protégée par Kkiapay.</p>
                        </div>
                    </div>

                    <!-- Section Numéros de Test Kkiapay -->
                    <div class="test-numbers-box">
                        <div class="test-header" onclick="toggleTestNumbers()">
                            <i class="fas fa-vial"></i>
                            <strong>Numéros de test Kkiapay</strong>
                            <i class="fas fa-chevron-down" id="testToggleIcon"></i>
                        </div>
                        <div class="test-content" id="testContent">
                            <div class="test-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Format: <strong>8 chiffres sans indicatif</strong> (ex: 97000000)</span>
                            </div>
                            <div class="test-nums">
                                <button type="button" class="test-num" onclick="fillPhone('97000000')">
                                    <span class="operator">Test Succès MTN</span>
                                    <span class="number">97 00 00 00</span>
                                </button>
                                <button type="button" class="test-num" onclick="fillPhone('61000000')">
                                    <span class="operator">Test MTN Bénin</span>
                                    <span class="number">61 00 00 00</span>
                                </button>
                                <button type="button" class="test-num" onclick="fillPhone('61000001')">
                                    <span class="operator">Test Échec MTN</span>
                                    <span class="number">61 00 00 01</span>
                                </button>
                            </div>
                            <div class="test-hint">
                                <i class="fas fa-key"></i>
                                <span>Code OTP: <strong>123456</strong> | Secret: <strong>1234</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Montant à déposer (FCFA)
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-money-bill-wave input-icon"></i>
                            <input type="number"
                                   class="form-control"
                                   id="depositAmount"
                                   name="amount"
                                   placeholder="100"
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
                            Entrez les <strong>8 chiffres</strong> sans l'indicatif.
                            <br>Ex: Pour <strong>01 97 00 00 00</strong>, entrez <strong>97000000</strong>
                        </small>
                    </div>

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

                    <button type="button"
                            class="btn-pay"
                            id="btnPay"
                            onclick="startPayment()"
                            disabled>
                        <i class="fas fa-credit-card"></i>
                        <span>Payer maintenant</span>
                    </button>
                </div>

                <!-- Étape 2 : Traitement -->
                <div id="depositStep2" style="display: none;">
                    <div class="processing-state">
                        <div class="spinner-large">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </div>
                        <h4>Traitement en cours...</h4>
                        <p>Validez le paiement sur votre téléphone</p>

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

                        <div class="otp-hint">
                            <i class="fas fa-key"></i>
                            <span>Entrez le code <strong>123456</strong> sur votre téléphone</span>
                        </div>

                        <button type="button" class="btn-check" onclick="verifyPayment()">
                            <i class="fas fa-sync"></i> Vérifier le statut
                        </button>
                    </div>
                </div>

                <!-- Étape 3 : Résultat -->
                <div id="depositStep3" style="display: none;">
                    <div class="result-state" id="resultState">
                        <div class="result-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 id="resultTitle">Paiement réussi !</h4>
                        <p id="resultMessage">Votre compte a été crédité.</p>

                        <div class="transaction-details" id="transactionDetails"></div>

                        <button type="button" class="btn-done" onclick="closeDepositModal()">
                            <i class="fas fa-check"></i> Terminé
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ========== MODAL STYLES ========== */
.slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    align-items: flex-end;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.slide-modal.active {
    display: flex;
    opacity: 1;
}

.slide-content {
    background: white;
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    border-radius: 20px 20px 0 0;
    overflow: hidden;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.slide-modal.active .slide-content {
    transform: translateY(0);
}

.slide-header {
    padding: 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.slide-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.slide-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.slide-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.slide-body {
    padding: 20px;
    overflow-y: auto;
    max-height: calc(90vh - 76px);
}

/* ========== TEST NUMBERS BOX ========== */
.test-numbers-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px dashed #f59e0b;
    border-radius: 12px;
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
    border-radius: 8px;
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
    padding: 10px 14px;
    background: white;
    border: 1px solid #fbbf24;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.test-num:hover {
    background: #fbbf24;
    border-color: #f59e0b;
}

.test-num .operator {
    font-size: 0.75rem;
    color: #78350f;
    font-weight: 500;
}

.test-num .number {
    font-size: 1rem;
    color: #92400e;
    font-weight: 600;
    font-family: monospace;
}

.test-num:hover .operator,
.test-num:hover .number {
    color: white;
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

/* ========== FORM STYLES ========== */
.deposit-info-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f0fdf4;
    border-radius: 12px;
    margin-bottom: 20px;
}

.deposit-info-box i {
    color: #22c55e;
    font-size: 1.5rem;
    margin-top: 2px;
}

.deposit-info-box strong {
    display: block;
    color: #166534;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.deposit-info-box p {
    margin: 0;
    color: #22c55e;
    font-size: 0.85rem;
    line-height: 1.4;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #374151;
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
    color: #9ca3af;
    font-size: 1rem;
}

.form-control {
    width: 100%;
    padding: 12px 14px 12px 44px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Phone input with prefix */
.phone-input-wrapper {
    display: flex;
    align-items: center;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.2s;
}

.phone-input-wrapper:focus-within {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.phone-prefix {
    padding: 12px 16px;
    background: #f3f4f6;
    color: #374151;
    font-weight: 700;
    font-size: 1.1rem;
    border-right: 2px solid #e5e7eb;
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
    color: #6b7280;
    font-size: 0.8rem;
    line-height: 1.4;
}

.form-hint strong {
    color: #22c55e;
}

.amount-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.amount-btn {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    background: white;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.amount-btn:hover {
    border-color: #22c55e;
    color: #22c55e;
}

.amount-btn.active {
    background: #22c55e;
    border-color: #22c55e;
    color: white;
}

.deposit-summary {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin: 20px 0;
    border: 2px solid #e5e7eb;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #6b7280;
}

.summary-row.total {
    border-top: 2px solid #e5e7eb;
    padding-top: 12px;
    margin-top: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
}

.text-free {
    color: #22c55e;
}

/* ========== BUTTONS ========== */
.btn-pay, .btn-done {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
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
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
}

.btn-pay:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

.btn-pay:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-done {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    margin-top: 20px;
}

.btn-check {
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: white;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 20px auto 0;
}

.btn-check:hover {
    background: #f9fafb;
}

/* ========== PROCESSING STATE ========== */
.processing-state {
    text-align: center;
    padding: 30px 20px;
}

.spinner-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: white;
    font-size: 2rem;
}

.processing-state h4 {
    color: #111827;
    margin-bottom: 8px;
    font-size: 1.25rem;
}

.processing-state p {
    color: #6b7280;
    margin-bottom: 20px;
}

.transaction-info {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    text-align: left;
    max-width: 280px;
    margin: 0 auto 16px;
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
    color: #6b7280;
}

.info-row strong {
    color: #111827;
    font-weight: 600;
}

.otp-hint {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: #fef3c7;
    border-radius: 8px;
    margin-bottom: 16px;
    color: #92400e;
    font-size: 0.9rem;
}

/* ========== RESULT STATE ========== */
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
    background: #f0fdf4;
    color: #22c55e;
}

.result-state.error .result-icon {
    background: #fef2f2;
    color: #ef4444;
}

.result-state h4 {
    color: #111827;
    margin-bottom: 8px;
    font-size: 1.25rem;
}

.result-state p {
    color: #6b7280;
    margin-bottom: 20px;
}

.transaction-details {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    text-align: left;
    max-width: 320px;
    margin: 0 auto 20px;
}

/* ========== ANIMATIONS ========== */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 480px) {
    .slide-body {
        padding: 16px;
    }

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
(function() {
    'use strict';

    // Variables privées
    let currentTx = null;
    let isProcessing = false;

    // Configuration Kkiapay depuis Laravel config
    const KKIAPAY_CONFIG = {
        key: '{{ config("services.kkiapay.public_key", "") }}',
        sandbox: {{ config("services.kkiapay.sandbox", true) ? 'true' : 'false' }},
        callback: '{{ route("kkiapay.callback") }}'
    };

    // Récupération du téléphone utilisateur depuis la base de données
    const USER_RAW = {
        phone: '{{ auth()->user()->phone ?? "" }}',
        name: '{{ auth()->user()->name ?? "" }}',
        email: '{{ auth()->user()->email ?? "" }}'
    };

    // Numéros de test officiels Kkiapay (format sans indicatif pour l'affichage)
    const TEST_PHONES = {
        success: '97000000',  // Test succès MTN Bénin
        failure: '61000001',  // Test échec
        mtn: '61000000'       // Test MTN
    };

    /**
     * Extrait les 8 derniers chiffres du numéro (sans indicatif)
     * Supporte: +22997000000, 22997000000, 0197000000, 97000000
     */
    function extractPhoneNumber(phone) {
        if (!phone) return '';

        // Supprimer espaces et tirets
        phone = phone.replace(/[\s\-]/g, '');

        // Si commence par +229 ou 00229
        if (phone.startsWith('+229')) return phone.substring(4);
        if (phone.startsWith('00229')) return phone.substring(5);

        // Si commence par 229 et a 11 chiffres
        if (phone.startsWith('229') && phone.length === 11) return phone.substring(3);

        // Si commence par 01 (format Bénin local) et a 10 chiffres
        if (phone.startsWith('01') && phone.length === 10) return phone.substring(2);

        // Si c'est déjà 8 chiffres
        if (phone.length === 8 && /^\d{8}$/.test(phone)) return phone;

        return '';
    }

    /**
     * Formate pour l'affichage local: 01 XX XX XX XX
     */
    function formatPhoneDisplay(phoneRaw) {
        if (phoneRaw && phoneRaw.length === 8) {
            const withPrefix = '01' + phoneRaw;
            return withPrefix.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
        }
        return phoneRaw || 'Non défini';
    }

    /**
     * Retourne le numéro au format Kkiapay complet (229XXXXXXXX)
     * C'est ce format que Kkiapay attend dans le paramètre 'phone'
     */
    function getKkiapayPhone(phoneInput) {
        // Si un numéro est saisi manuellement, l'utiliser
        if (phoneInput && phoneInput.length === 8) {
            return '229' + phoneInput;
        }

        // Sinon utiliser le téléphone de l'utilisateur
        const userPhone = extractPhoneNumber(USER_RAW.phone);

        // Si mode sandbox et pas de téléphone valide, utiliser test
        if (KKIAPAY_CONFIG.sandbox && (!userPhone || userPhone.length !== 8)) {
            console.log('Mode sandbox: utilisation numéro test');
            return '229' + TEST_PHONES.success; // 22997000000
        }

        // Sinon utiliser le téléphone de l'utilisateur avec préfixe 229
        if (userPhone && userPhone.length === 8) {
            return '229' + userPhone; // Ex: 22997000000 ou 22966185598
        }

        console.warn('Numéro de téléphone invalide');
        return '';
    }

    // ========== INITIALISATION ==========
    document.addEventListener('DOMContentLoaded', function() {
        initDepositModal();
    });

    function initDepositModal() {
        const amountInput = document.getElementById('depositAmount');
        const phoneInput = document.getElementById('depositPhone');

        if (amountInput) {
            amountInput.addEventListener('input', updateForm);
        }

        if (phoneInput) {
            // N'accepter que les chiffres, max 8
            phoneInput.addEventListener('input', function(e) {
                // Supprimer tout sauf les chiffres
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
                updateForm();
            });

            // Empêcher la saisie de lettres
            phoneInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        }

        // Fermer au clic extérieur
        const modal = document.getElementById('depositSlide');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this && !isProcessing) {
                    closeDepositModal();
                }
            });
        }

        // Écouter les événements Kkiapay
        setupKkiapayListeners();
    }

    // ========== FONCTIONS GLOBALES ==========

    window.openDepositModal = function() {
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
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeDepositModal = function() {
        if (isProcessing) {
            if (!confirm('Un paiement est en cours. Êtes-vous sûr de vouloir fermer ?')) {
                return;
            }
        }

        const modal = document.getElementById('depositSlide');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        resetForm();
    };

    window.toggleTestNumbers = function() {
        const content = document.getElementById('testContent');
        const header = document.querySelector('.test-header');

        if (content && header) {
            content.classList.toggle('show');
            header.classList.toggle('active');
        }
    };

    window.fillPhone = function(kkiapayNumber) {
        // kkiapayNumber = 8 chiffres sans indicatif (ex: 97000000)
        const input = document.getElementById('depositPhone');
        if (input) {
            input.value = kkiapayNumber;
            updateForm();

            // Feedback visuel
            showToast('Numéro de test sélectionné: +229 ' + formatPhoneDisplay(kkiapayNumber), 'success');

            // Focus sur le montant si vide
            const amountInput = document.getElementById('depositAmount');
            if (!amountInput.value) {
                amountInput.focus();
            }
        }
    };

    window.selectAmount = function(amount) {
        const input = document.getElementById('depositAmount');
        if (!input) return;

        input.value = amount;

        // Mettre à jour visuellement les boutons
        document.querySelectorAll('.amount-btn').forEach(btn => {
            btn.classList.remove('active');
            const btnAmount = parseInt(btn.textContent.replace(/\s/g, ''));
            if (btnAmount === amount) {
                btn.classList.add('active');
            }
        });

        updateForm();
    };

    window.startPayment = async function() {
        if (isProcessing) return;

        const amount = parseFloat(document.getElementById('depositAmount')?.value);
        const phoneRaw = document.getElementById('depositPhone')?.value?.trim();

        // Validation stricte
        if (!amount || amount < 100) {
            alert('Le montant minimum est de 100 FCFA');
            return;
        }

        if (!/^\d{8}$/.test(phoneRaw)) {
            alert('Veuillez entrer exactement 8 chiffres\nEx: 97000000 pour +229 97 00 00 00');
            document.getElementById('depositPhone').focus();
            return;
        }

        // Vérifier SDK Kkiapay
        if (typeof window.openKkiapayWidget !== 'function') {
            alert('Erreur: Le système de paiement n\'est pas chargé. Veuillez rafraîchir la page.');
            console.error('SDK Kkiapay non disponible');
            return;
        }

        isProcessing = true;
        setLoading(true);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            // CORRECTION: Préparer le numéro au format Kkiapay complet (229XXXXXXXX)
            const kkiapayPhone = getKkiapayPhone(phoneRaw);

            if (!kkiapayPhone || kkiapayPhone.length !== 11) {
                throw new Error('Numéro de téléphone invalide. Format attendu: 229XXXXXXXX');
            }

            console.log('Démarrage paiement:', {
                amount: amount,
                phoneRaw: phoneRaw,
                phoneKkiapay: kkiapayPhone,
                mode: KKIAPAY_CONFIG.sandbox ? 'SANDBOX' : 'PRODUCTION'
            });

            // Appel API pour créer la transaction
            const response = await fetch('{{ route("client.wallet.deposit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    phone: phoneRaw, // 8 chiffres pour le backend
                    phone_kkiapay: kkiapayPhone // 11 chiffres avec indicatif
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erreur lors de la création de la transaction');
            }

            // Sauvegarder la transaction courante
            currentTx = {
                reference: data.reference,
                amount: amount,
                phone: phoneRaw, // 8 chiffres
                phoneKkiapay: kkiapayPhone, // 11 chiffres (229XXXXXXXX)
                phoneDisplay: formatPhoneDisplay(phoneRaw) // 01 XX XX XX XX
            };

            // Passer à l'étape traitement
            showStep(2);
            document.getElementById('processAmount').textContent = formatMoney(amount);
            document.getElementById('processRef').textContent = data.reference;
            document.getElementById('processPhone').textContent = currentTx.phoneDisplay;

            // Ouvrir Kkiapay avec le numéro au format complet
            openKkiapay(data, amount, kkiapayPhone);

        } catch (error) {
            console.error('Erreur paiement:', error);
            alert('Erreur: ' + error.message);
            isProcessing = false;
            setLoading(false);
        }
    };

    window.verifyPayment = async function() {
        if (!currentTx?.reference) {
            alert('Aucune transaction en cours');
            return;
        }

        const btn = document.querySelector('.btn-check');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
            btn.disabled = true;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch(`{{ url('/payment/status') }}/${currentTx.reference}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (data.status === 'completed' || data.status === 'success') {
                showResult(true, 'Paiement confirmé !', 'Votre compte a été crédité avec succès.');
                refreshWallet();
            } else if (data.status === 'pending') {
                alert('Paiement toujours en attente. Veuillez valider sur votre téléphone avec le code 123456.');
            } else {
                showResult(false, 'Paiement échoué', 'La transaction n\'a pas pu être complétée.');
            }

        } catch (error) {
            console.error('Erreur vérification:', error);
            alert('Erreur lors de la vérification. Veuillez réessayer.');
        } finally {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-sync"></i> Vérifier le statut';
                btn.disabled = false;
            }
        }
    };

    // ========== FONCTIONS PRIVÉES ==========

    function updateForm() {
        const amount = parseFloat(document.getElementById('depositAmount')?.value) || 0;
        const phoneRaw = document.getElementById('depositPhone')?.value?.trim() || '';

        // Validation: exactement 8 chiffres
        const isPhoneValid = /^\d{8}$/.test(phoneRaw);

        // Mettre à jour le résumé
        const formatted = formatMoney(amount);
        const summaryAmount = document.getElementById('summaryAmount');
        const summaryTotal = document.getElementById('summaryTotal');
        const summaryCard = document.getElementById('summaryCard');

        if (summaryAmount) summaryAmount.textContent = formatted;
        if (summaryTotal) summaryTotal.textContent = formatted;
        if (summaryCard) {
            summaryCard.style.display = amount >= 100 ? 'block' : 'none';
        }

        // Activer/désactiver le bouton
        const isValid = amount >= 100 && isPhoneValid;
        const btnPay = document.getElementById('btnPay');
        if (btnPay) {
            btnPay.disabled = !isValid;
        }
    }

    function openKkiapay(data, amount, phone) {
        // phone doit être au format 229XXXXXXXX (11 chiffres)
        const config = {
            amount: amount,
            key: KKIAPAY_CONFIG.key,
            sandbox: KKIAPAY_CONFIG.sandbox,
            // CORRECTION CRITIQUE: phone au format 229XXXXXXXX
            phone: phone, // Ex: 22997000000
            email: USER_RAW.email,
            firstname: USER_RAW.name,
            lastname: '',
            data: {
                reference: data.reference,
                user_id: {{ auth()->id() ?? 'null' }},
                phone_raw: currentTx?.phone // 8 chiffres pour référence
            },
            callback: KKIAPAY_CONFIG.callback
        };

        console.log('Ouverture Kkiapay:', {
            amount: config.amount,
            phone: config.phone,
            phoneLength: config.phone.length,
            sandbox: config.sandbox,
            mode: config.sandbox ? 'SANDBOX' : 'PRODUCTION'
        });

        window.openKkiapayWidget(config);
    }

    function setupKkiapayListeners() {
        if (typeof window.addSuccessListener !== 'function') {
            console.warn('Listeners Kkiapay non disponibles');
            return;
        }

        window.addSuccessListener(function(response) {
            console.log('✅ Paiement réussi:', response);
            showResult(true, 'Paiement réussi !', 'Votre compte a été crédité avec succès.');
            refreshWallet();
            isProcessing = false;
        });

        window.addFailedListener(function(response) {
            console.log('❌ Paiement échoué:', response);
            showResult(false, 'Paiement échoué', 'La transaction n\'a pas pu être complétée.');
            isProcessing = false;
        });

        window.addKkiapayCloseListener(function() {
            console.log('🔒 Widget Kkiapay fermé');
            if (isProcessing && currentTx) {
                // Laisser l'utilisateur vérifier manuellement
                console.log('Paiement en attente de confirmation');
            }
        });
    }

    function showStep(step) {
        const step1 = document.getElementById('depositStep1');
        const step2 = document.getElementById('depositStep2');
        const step3 = document.getElementById('depositStep3');

        if (step1) step1.style.display = step === 1 ? 'block' : 'none';
        if (step2) step2.style.display = step === 2 ? 'block' : 'none';
        if (step3) step3.style.display = step === 3 ? 'block' : 'none';
    }

    function showResult(success, title, message) {
        isProcessing = false;
        showStep(3);

        const resultState = document.getElementById('resultState');
        const icon = resultState?.querySelector('.result-icon i');
        const titleEl = document.getElementById('resultTitle');
        const messageEl = document.getElementById('resultMessage');
        const detailsEl = document.getElementById('transactionDetails');

        if (resultState) {
            resultState.className = 'result-state ' + (success ? 'success' : 'error');
        }

        if (icon) {
            icon.className = success ? 'fas fa-check-circle' : 'fas fa-times-circle';
        }

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;

        if (detailsEl && currentTx) {
            detailsEl.innerHTML = `
                <div class="info-row">
                    <span>Montant:</span>
                    <strong>${formatMoney(currentTx.amount)}</strong>
                </div>
                <div class="info-row">
                    <span>Numéro:</span>
                    <strong>${currentTx.phoneDisplay}</strong>
                </div>
                <div class="info-row">
                    <span>Référence:</span>
                    <strong>${currentTx.reference}</strong>
                </div>
            `;
        }
    }

    function resetForm() {
        currentTx = null;
        isProcessing = false;

        const form = document.getElementById('depositForm');
        if (form) form.reset();

        document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));

        const summaryCard = document.getElementById('summaryCard');
        if (summaryCard) summaryCard.style.display = 'none';

        const btnPay = document.getElementById('btnPay');
        if (btnPay) {
            btnPay.disabled = true;
            btnPay.innerHTML = '<i class="fas fa-credit-card"></i> <span>Payer maintenant</span>';
        }

        // Fermer la section des numéros de test
        const testContent = document.getElementById('testContent');
        const testHeader = document.querySelector('.test-header');
        if (testContent && testHeader) {
            testContent.classList.remove('show');
            testHeader.classList.remove('active');
        }

        showStep(1);
    }

    function setLoading(loading) {
        const btn = document.getElementById('btnPay');
        if (!btn) return;

        if (loading) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Préparation...</span>';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-credit-card"></i> <span>Payer maintenant</span>';
        }
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
    }

    function refreshWallet() {
        if (typeof window.refreshBalance === 'function') {
            setTimeout(window.refreshBalance, 1000);
        }

        // Événement personnalisé pour la mise à jour du solde
        window.dispatchEvent(new CustomEvent('wallet-deposited', {
            detail: { amount: currentTx?.amount }
        }));
    }

    function showToast(message, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            // Toast fallback intégré
            const existing = document.querySelector('.deposit-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = `deposit-toast ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;

            const styles = document.createElement('style');
            styles.textContent = `
                .deposit-toast {
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: ${type === 'success' ? '#22c55e' : '#ef4444'};
                    color: white;
                    padding: 12px 24px;
                    border-radius: 50px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-weight: 500;
                    z-index: 100000;
                    animation: slideDownToast 0.3s ease;
                }
                @keyframes slideDownToast {
                    from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
                    to { opacity: 1; transform: translateX(-50%) translateY(0); }
                }
            `;

            document.head.appendChild(styles);
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }

})();
</script>
