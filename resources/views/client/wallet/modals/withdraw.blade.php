<!-- Modal Retrait -->
<div class="slide-modal" id="withdrawSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-arrow-up"></i> Demande de retrait</h3>
            <button class="slide-close" onclick="closeSlide('withdrawSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <form id="withdrawForm" onsubmit="return false;">
                @csrf

                <!-- Étape 1 : Informations du retrait -->
                <div id="withdrawStep1">
                    <div class="withdraw-info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Demande soumise à validation</strong>
                            <p>Votre demande sera traitée par notre équipe sous 24-48h.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Montant à retirer
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-money-bill-wave input-icon"></i>
                            <input type="number"
                                   class="form-control"
                                   id="withdrawAmount"
                                   name="amount"
                                   placeholder="Montant"
                                   min="1000"
                                   max="{{ $wallet->balance ?? 0 }}"
                                   step="100"
                                   required>
                        </div>

                        <div class="amount-limits">
                            <small>Minimum: 1 000 FCFA</small>
                            <small>
                                <i class="fas fa-wallet"></i>
                                Solde: <span id="displayBalance">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</span> FCFA
                            </small>
                        </div>

                        <div class="amount-selector">
                            <button type="button" class="amount-option" data-amount="5000">5 000</button>
                            <button type="button" class="amount-option" data-amount="10000">10 000</button>
                            <button type="button" class="amount-option" data-amount="20000">20 000</button>
                            <button type="button" class="amount-option" data-amount="50000">50 000</button>
                            <button type="button" class="amount-option" data-amount="all">Tout</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Méthode de retrait</label>
                        <div class="payment-methods">
                            <label class="payment-method active">
                                <input type="radio" name="withdraw_method" value="mobile_money" checked>
                                <div class="method-icon mobile">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">Mobile Money</div>
                                    <div class="method-desc">Orange, MTN, Moov, Wave</div>
                                </div>
                                <i class="fas fa-check-circle check-icon"></i>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="withdraw_method" value="bank_transfer">
                                <div class="method-icon bank">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">Virement bancaire</div>
                                    <div class="method-desc">Vers compte bancaire</div>
                                </div>
                                <i class="fas fa-check-circle check-icon"></i>
                            </label>
                        </div>
                    </div>

                    <!-- Champs Mobile Money -->
                    <div id="mobileFields" class="withdraw-fields">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i>
                                Numéro de téléphone
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel"
                                       class="form-control"
                                       id="phoneNumber"
                                       name="phone_number"
                                       placeholder="Ex: 07 XX XX XX XX"
                                       maxlength="10"
                                       required>
                            </div>
                            <small class="form-text text-muted">Format: 10 chiffres sans espaces</small>
                        </div>
                    </div>

                    <!-- Champs Virement Bancaire -->
                    <div id="bankFields" class="withdraw-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Nom du bénéficiaire
                            </label>
                            <input type="text"
                                   class="form-control"
                                   name="account_name"
                                   placeholder="Nom complet du bénéficiaire">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-credit-card"></i>
                                Numéro de compte
                            </label>
                            <input type="text"
                                   class="form-control"
                                   name="account_number"
                                   placeholder="Numéro de compte bancaire">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-landmark"></i>
                                Banque
                            </label>
                            <input type="text"
                                   class="form-control"
                                   name="bank_name"
                                   placeholder="Nom de la banque">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-sticky-note"></i>
                            Motif (optionnel)
                        </label>
                        <textarea class="form-control"
                                  name="note"
                                  rows="2"
                                  placeholder="Raison du retrait..."></textarea>
                    </div>

                    <div class="withdraw-summary">
                        <div class="summary-row">
                            <span>Montant demandé:</span>
                            <strong id="summaryAmount">0 FCFA</strong>
                        </div>
                        <div class="summary-row">
                            <span>Frais de traitement:</span>
                            <strong class="text-success">Gratuit</strong>
                        </div>
                        <div class="summary-row total">
                            <span>À recevoir:</span>
                            <strong id="summaryTotal">0 FCFA</strong>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-full" onclick="goToStep2()">
                        Continuer
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Étape 2 : Vérification PIN -->
                <div id="withdrawStep2" style="display: none;">
                    <div class="step-indicator">
                        <div class="step completed">1</div>
                        <div class="step-line"></div>
                        <div class="step active">2</div>
                    </div>

                    <div class="password-verification-box">
                        <div class="security-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Vérification de sécurité</h4>
                        <p>Pour confirmer votre demande de retrait, veuillez saisir votre code PIN à 6 chiffres.</p>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>
                                Code PIN (6 chiffres)
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password"
                                       class="form-control"
                                       id="pinInput"
                                       name="pin"
                                       placeholder="••••••"
                                       maxlength="6"
                                       inputmode="numeric"
                                       pattern="\d{6}"
                                       autocomplete="off"
                                       required>
                                <button type="button" class="toggle-password" onclick="togglePinVisibility(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Entrez votre code PIN sécurisé</small>
                        </div>

                        <div class="withdraw-recap">
                            <div class="recap-item">
                                <span>Montant:</span>
                                <strong id="recapAmount">-</strong>
                            </div>
                            <div class="recap-item">
                                <span>Méthode:</span>
                                <strong id="recapMethod">-</strong>
                            </div>
                            <div class="recap-item" id="recapPhoneRow" style="display: none;">
                                <span>Téléphone:</span>
                                <strong id="recapPhone">-</strong>
                            </div>
                            <div class="recap-item" id="recapBankRow" style="display: none;">
                                <span>Banque:</span>
                                <strong id="recapBank">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary" onclick="goToStep1()">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </button>
                        <button type="button" class="btn btn-primary" id="submitWithdrawBtn" onclick="submitWithdrawForm()">
                            <i class="fas fa-paper-plane"></i>
                            Confirmer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Styles du modal retrait */
.slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1100;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.slide-modal.show {
    display: block;
    opacity: 1;
}

.slide-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 450px;
    height: 100%;
    background: white;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: -4px 0 20px rgba(0,0,0,0.1);
}

.slide-modal.show .slide-content {
    transform: translateX(0);
}

.slide-header {
    padding: 20px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.slide-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.slide-close {
    background: rgba(255,255,255,0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.slide-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.slide-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.withdraw-info-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    margin-bottom: 20px;
}

.withdraw-info-box i {
    color: #3b82f6;
    font-size: 1.25rem;
    margin-top: 2px;
}

.withdraw-info-box strong {
    display: block;
    color: #1e40af;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.withdraw-info-box p {
    margin: 0;
    color: #3b82f6;
    font-size: 0.85rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 0.95rem;
}

.form-text {
    display: block;
    margin-top: 6px;
    font-size: 0.85rem;
}

.text-muted {
    color: #6b7280;
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.form-control {
    width: 100%;
    padding: 12px 16px 12px 45px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s;
    box-sizing: border-box;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Style spécial pour le PIN */
#pinInput {
    font-size: 1.5rem;
    letter-spacing: 0.5rem;
    text-align: center;
    padding-left: 16px;
    font-family: monospace;
}

.amount-limits {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 0.85rem;
    color: #6b7280;
}

.amount-limits i {
    color: #3b82f6;
}

.amount-selector {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.amount-option {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s;
}

.amount-option:hover,
.amount-option.selected {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #1d4ed8;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    background: white;
}

.payment-method:hover {
    border-color: #93c5fd;
}

.payment-method.active {
    border-color: #3b82f6;
    background: #eff6ff;
}

.payment-method input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.method-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.method-icon.mobile {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
}

.method-icon.bank {
    background: linear-gradient(135deg, #10b981, #059669);
}

.method-info {
    flex: 1;
}

.method-name {
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.method-desc {
    font-size: 0.85rem;
    color: #6b7280;
}

.check-icon {
    color: #3b82f6;
    opacity: 0.3;
    transition: opacity 0.2s;
}

.payment-method.active .check-icon {
    opacity: 1;
}

.withdraw-fields {
    animation: fadeIn 0.3s ease;
}

.withdraw-summary {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin: 20px 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #e5e7eb;
    font-size: 0.95rem;
    color: #4b5563;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row.total {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    padding-top: 12px;
    margin-top: 8px;
    border-top: 2px solid #e5e7eb;
    border-bottom: none;
}

.text-success {
    color: #16a34a;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    font-size: 1rem;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.btn-full {
    width: 100%;
}

/* Étape 2 */
.step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    gap: 8px;
}

.step {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    background: #e5e7eb;
    color: #6b7280;
}

.step.completed {
    background: #22c55e;
    color: white;
}

.step.active {
    background: #3b82f6;
    color: white;
}

.step-line {
    width: 40px;
    height: 2px;
    background: #e5e7eb;
}

.password-verification-box {
    text-align: center;
    padding: 24px;
    background: #f9fafb;
    border-radius: 16px;
    margin-bottom: 24px;
}

.security-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: white;
    font-size: 1.5rem;
}

.password-verification-box h4 {
    margin-bottom: 8px;
    color: #111827;
}

.password-verification-box p {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 20px;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    font-size: 1rem;
    padding: 4px;
    z-index: 10;
}

.toggle-password:hover {
    color: #3b82f6;
}

.withdraw-recap {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    text-align: left;
}

.recap-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 0.9rem;
    color: #4b5563;
}

.recap-item strong {
    color: #111827;
}

.btn-group {
    display: flex;
    gap: 12px;
}

.btn-group .btn {
    flex: 1;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 480px) {
    .slide-content {
        max-width: 100%;
    }

    .amount-selector {
        gap: 6px;
    }

    .amount-option {
        padding: 6px 12px;
        font-size: 0.85rem;
    }

    .btn-group {
        flex-direction: column;
    }

    #pinInput {
        font-size: 1.25rem;
        letter-spacing: 0.3rem;
    }
}
</style>

<script>
/**
 * Gestionnaire du modal de retrait
 * Utilise le PIN (pas le mot de passe) pour la validation
 * Pas de détection d'opérateur mobile
 */
(function() {
    'use strict';

    const WithdrawModal = {
        isSubmitting: false,
        initialized: false,
        currentBalance: parseFloat('{{ $wallet->balance ?? 0 }}'),

        init: function() {
            if (this.initialized) return;
            this.initialized = true;
            this.cacheElements();
            this.bindEvents();
            this.updateSummary(0);
        },

        cacheElements: function() {
            this.elements = {
                form: document.getElementById('withdrawForm'),
                amountInput: document.getElementById('withdrawAmount'),
                amountOptions: document.querySelectorAll('#withdrawSlide .amount-option'),
                methodInputs: document.querySelectorAll('#withdrawSlide input[name="withdraw_method"]'),
                mobileFields: document.getElementById('mobileFields'),
                bankFields: document.getElementById('bankFields'),
                phoneInput: document.getElementById('phoneNumber'),
                step1: document.getElementById('withdrawStep1'),
                step2: document.getElementById('withdrawStep2'),
                submitBtn: document.getElementById('submitWithdrawBtn'),
                pinInput: document.getElementById('pinInput'),
                displayBalance: document.getElementById('displayBalance'),

                // Récap étape 2
                recapAmount: document.getElementById('recapAmount'),
                recapMethod: document.getElementById('recapMethod'),
                recapPhoneRow: document.getElementById('recapPhoneRow'),
                recapPhone: document.getElementById('recapPhone'),
                recapBankRow: document.getElementById('recapBankRow'),
                recapBank: document.getElementById('recapBank'),
            };
        },

        bindEvents: function() {
            const self = this;
            const elems = this.elements;

            // Sélection rapide des montants
            elems.amountOptions.forEach(option => {
                option.addEventListener('click', function() {
                    elems.amountOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');

                    let amount = this.getAttribute('data-amount');

                    if (amount === 'all') {
                        amount = self.currentBalance;
                    } else {
                        amount = parseFloat(amount) || 0;
                    }

                    // Limiter au solde disponible
                    if (amount > self.currentBalance) {
                        amount = self.currentBalance;
                        self.showToast('Montant ajusté au solde disponible', 'warning');
                    }

                    if (elems.amountInput) {
                        elems.amountInput.value = amount > 0 ? amount : '';
                        self.updateSummary(amount);
                    }
                });
            });

            // Saisie manuelle du montant
            if (elems.amountInput) {
                elems.amountInput.addEventListener('input', function() {
                    const amount = parseFloat(this.value) || 0;
                    self.updateSummary(amount);
                    elems.amountOptions.forEach(opt => opt.classList.remove('selected'));
                });

                elems.amountInput.addEventListener('blur', function() {
                    const max = self.currentBalance;
                    let val = parseFloat(this.value) || 0;

                    if (val > max) {
                        this.value = max;
                        self.updateSummary(max);
                        self.showToast('Montant ajusté au solde disponible', 'warning');
                    }

                    if (val < 1000 && val > 0) {
                        this.value = 1000;
                        self.updateSummary(1000);
                        self.showToast('Montant minimum: 1 000 FCFA', 'warning');
                    }
                });
            }

            // Changement de méthode de retrait
            elems.methodInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.querySelectorAll('#withdrawSlide .payment-method').forEach(method => {
                        method.classList.remove('active');
                    });
                    this.closest('.payment-method').classList.add('active');

                    if (this.value === 'mobile_money') {
                        if (elems.mobileFields) elems.mobileFields.style.display = 'block';
                        if (elems.bankFields) elems.bankFields.style.display = 'none';
                        self.setRequired(elems.mobileFields, true);
                        self.setRequired(elems.bankFields, false);
                    } else {
                        if (elems.mobileFields) elems.mobileFields.style.display = 'none';
                        if (elems.bankFields) elems.bankFields.style.display = 'block';
                        self.setRequired(elems.mobileFields, false);
                        self.setRequired(elems.bankFields, true);
                    }
                });
            });

            // Formatage téléphone (chiffres uniquement, max 10)
            if (elems.phoneInput) {
                elems.phoneInput.addEventListener('input', function(e) {
                    // Supprimer tout sauf les chiffres
                    let value = this.value.replace(/\D/g, '').slice(0, 10);
                    this.value = value;
                });
            }

            // Fermeture sur clic extérieur
            const modal = document.getElementById('withdrawSlide');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        self.close();
                    }
                });
            }

            // Gestion du PIN (chiffres uniquement)
            if (elems.pinInput) {
                elems.pinInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '').slice(0, 6);
                });

                // Soumission sur Enter
                elems.pinInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        self.submit();
                    }
                });
            }
        },

        setRequired: function(container, required) {
            if (!container) return;
            const fields = container.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                if (required) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                    field.value = ''; // Vider les champs non utilisés
                }
            });
        },

        updateSummary: function(amount) {
            const formatted = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
            const summaryAmount = document.getElementById('summaryAmount');
            const summaryTotal = document.getElementById('summaryTotal');
            if (summaryAmount) summaryAmount.textContent = formatted;
            if (summaryTotal) summaryTotal.textContent = formatted;
        },

        goToStep2: function() {
            const elems = this.elements;
            const amount = parseFloat(elems.amountInput?.value) || 0;
            const methodInput = document.querySelector('#withdrawSlide input[name="withdraw_method"]:checked');
            const method = methodInput?.value;

            // Validations
            if (!amount || amount < 1000) {
                this.showToast('Veuillez entrer un montant valide (minimum 1 000 FCFA)', 'warning');
                return;
            }

            if (amount > this.currentBalance) {
                this.showToast('Le montant ne peut pas dépasser votre solde disponible', 'error');
                return;
            }

            // Validation selon méthode
            if (method === 'mobile_money') {
                const phone = elems.phoneInput?.value?.trim();
                if (!phone || phone.length !== 10) {
                    this.showToast('Veuillez entrer un numéro de téléphone valide (10 chiffres)', 'warning');
                    elems.phoneInput?.focus();
                    return;
                }
            } else if (method === 'bank_transfer') {
                const accountName = document.querySelector('#withdrawSlide input[name="account_name"]')?.value?.trim();
                const accountNumber = document.querySelector('#withdrawSlide input[name="account_number"]')?.value?.trim();
                const bankName = document.querySelector('#withdrawSlide input[name="bank_name"]')?.value?.trim();

                if (!accountName || !accountNumber || !bankName) {
                    this.showToast('Veuillez remplir tous les champs bancaires', 'warning');
                    return;
                }
            }

            // Mise à jour du récapitulatif
            if (elems.recapAmount) {
                elems.recapAmount.textContent = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
            }
            if (elems.recapMethod) {
                elems.recapMethod.textContent = method === 'mobile_money' ? 'Mobile Money' : 'Virement bancaire';
            }

            // Affichage conditionnel des détails
            if (method === 'mobile_money') {
                const phone = elems.phoneInput?.value;
                if (elems.recapPhone) elems.recapPhone.textContent = phone || '-';
                if (elems.recapPhoneRow) elems.recapPhoneRow.style.display = 'flex';
                if (elems.recapBankRow) elems.recapBankRow.style.display = 'none';
            } else {
                const bankName = document.querySelector('#withdrawSlide input[name="bank_name"]')?.value;
                if (elems.recapBank) elems.recapBank.textContent = bankName || '-';
                if (elems.recapPhoneRow) elems.recapPhoneRow.style.display = 'none';
                if (elems.recapBankRow) elems.recapBankRow.style.display = 'flex';
            }

            // Transition
            if (elems.step1) elems.step1.style.display = 'none';
            if (elems.step2) elems.step2.style.display = 'block';

            // Focus sur le PIN
            setTimeout(() => {
                if (elems.pinInput) elems.pinInput.focus();
            }, 100);
        },

        goToStep1: function() {
            const elems = this.elements;

            if (elems.step2) elems.step2.style.display = 'none';
            if (elems.step1) elems.step1.style.display = 'block';

            // Réinitialiser le bouton
            if (elems.submitBtn) {
                elems.submitBtn.disabled = false;
                elems.submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Confirmer';
            }

            this.isSubmitting = false;
        },

        submit: async function() {
            if (this.isSubmitting) return;

            const elems = this.elements;
            const pin = elems.pinInput?.value;

            // Validation PIN
            if (!pin || pin.length !== 6) {
                this.showToast('Veuillez entrer votre code PIN à 6 chiffres', 'warning');
                if (elems.pinInput) elems.pinInput.focus();
                return;
            }

            this.isSubmitting = true;
            if (elems.submitBtn) {
                elems.submitBtn.disabled = true;
                elems.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            }

            try {
                const formData = new FormData(elems.form);

                // Debug
                console.log('=== ENVOI RETRAIT ===');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }

                const response = await fetch('{{ route("client.wallet.withdraw") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value
                    },
                    body: formData
                });

                const data = await response.json();
                console.log('=== RÉPONSE ===', data);

                if (response.ok && data.success) {
                    this.showToast(data.message || 'Demande soumise avec succès !', 'success');

                    // Mettre à jour le solde affiché si fourni
                    if (data.new_balance !== undefined) {
                        this.currentBalance = parseFloat(data.new_balance);
                        if (elems.displayBalance) {
                            elems.displayBalance.textContent = new Intl.NumberFormat('fr-FR').format(this.currentBalance);
                        }
                        // Mettre à jour aussi le max de l'input
                        if (elems.amountInput) {
                            elems.amountInput.max = this.currentBalance;
                        }
                    }

                    this.close();

                    // Recharger la page après 1.5s pour voir la nouvelle transaction
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Erreur lors de la soumission');
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.showToast(error.message || 'Erreur de connexion. Veuillez réessayer.', 'error');

                if (elems.submitBtn) {
                    elems.submitBtn.disabled = false;
                    elems.submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Confirmer';
                }
                this.isSubmitting = false;

                // Si erreur PIN, vider le champ et focus
                if (error.message?.toLowerCase().includes('pin')) {
                    if (elems.pinInput) {
                        elems.pinInput.value = '';
                        elems.pinInput.focus();
                    }
                }
            }
        },

        open: function() {
            const modal = document.getElementById('withdrawSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                this.goToStep1();

                // Réinitialiser le formulaire
                if (this.elements.form) {
                    this.elements.form.reset();
                }
                this.updateSummary(0);
                this.elements.amountOptions?.forEach(opt => opt.classList.remove('selected'));

                // Réinitialiser les étapes
                if (this.elements.mobileFields) this.elements.mobileFields.style.display = 'block';
                if (this.elements.bankFields) this.elements.bankFields.style.display = 'none';

                // Réactiver Mobile Money par défaut
                const mobileRadio = document.querySelector('#withdrawSlide input[value="mobile_money"]');
                if (mobileRadio) {
                    mobileRadio.checked = true;
                    mobileRadio.closest('.payment-method').classList.add('active');
                    document.querySelectorAll('#withdrawSlide .payment-method').forEach(m => {
                        if (!m.contains(mobileRadio)) m.classList.remove('active');
                    });
                }
            }
        },

        close: function() {
            const modal = document.getElementById('withdrawSlide');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';

                if (this.isSubmitting) return; // Ne pas réinitialiser si soumission en cours

                if (this.elements.form) {
                    this.elements.form.reset();
                }
                this.goToStep1();
                this.updateSummary(0);
                this.elements.amountOptions?.forEach(opt => opt.classList.remove('selected'));
            }
        },

        showToast: function(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
            } else if (typeof window.Swal !== 'undefined') {
                window.Swal.fire({
                    icon: type === 'error' ? 'error' : (type === 'success' ? 'success' : 'info'),
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert(message);
            }
        }
    };

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            WithdrawModal.init();
        });
    } else {
        WithdrawModal.init();
    }

    // Exposition des fonctions globales
    window.goToStep2 = function() { WithdrawModal.goToStep2(); };
    window.goToStep1 = function() { WithdrawModal.goToStep1(); };
    window.submitWithdrawForm = function() { WithdrawModal.submit(); };

    window.togglePinVisibility = function(btn) {
        const input = btn?.parentElement?.querySelector('input');
        const icon = btn?.querySelector('i');
        if (!input || !icon) return;

        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
    };

    window.closeSlide = window.closeSlide || function(slideId) {
        if (slideId === 'withdrawSlide') {
            WithdrawModal.close();
        } else {
            const modal = document.getElementById(slideId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }
    };

    window.showWithdrawModal = window.showWithdrawModal || function() {
        WithdrawModal.open();
    };

    // Exposer l'objet pour debug si nécessaire
    window.WithdrawModal = WithdrawModal;

})();
</script>
