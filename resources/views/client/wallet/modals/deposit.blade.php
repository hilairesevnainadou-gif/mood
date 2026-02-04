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
                <div class="mtn-form-group">
                    <label class="mtn-form-label">Montant à déposer</label>

                    <!-- Montants rapides -->
                    <div class="amount-selector">
                        <button type="button" class="amount-option" data-amount="1000">
                            <span class="amount">1 000</span>
                            <span class="currency">F</span>
                        </button>
                        <button type="button" class="amount-option" data-amount="5000">
                            <span class="amount">5 000</span>
                            <span class="currency">F</span>
                        </button>
                        <button type="button" class="amount-option" data-amount="10000">
                            <span class="amount">10 000</span>
                            <span class="currency">F</span>
                        </button>
                        <button type="button" class="amount-option" data-amount="20000">
                            <span class="amount">20 000</span>
                            <span class="currency">F</span>
                        </button>
                        <button type="button" class="amount-option" data-amount="50000">
                            <span class="amount">50 000</span>
                            <span class="currency">F</span>
                        </button>
                        <button type="button" class="amount-option" data-amount="100000">
                            <span class="amount">100 000</span>
                            <span class="currency">F</span>
                        </button>
                    </div>

                    <!-- Input montant personnalisé -->
                    <div class="input-with-icon" style="margin-top: 15px;">
                        <i class="fas fa-money-bill-wave"></i>
                        <input type="number"
                               class="mtn-form-control"
                               id="depositAmount"
                               name="amount"
                               placeholder="Ou saisir un montant personnalisé"
                               min="1000"
                               step="100"
                               required>
                    </div>
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Montant minimum : 1 000 FCFA
                    </small>
                </div>

                <!-- Section Mobile Money -->
                <div class="mtn-form-group" style="margin-top: 2rem;">
                    <div class="section-title">
                        <i class="fas fa-mobile-alt"></i>
                        <h4>Informations Mobile Money</h4>
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="mtn-form-group" style="margin-bottom: 1.5rem;">
                        <label class="mtn-form-label">
                            <i class="fas fa-phone"></i>
                            Numéro de téléphone
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel"
                                   class="mtn-form-control"
                                   name="phone_number"
                                   placeholder="Ex: 07 00 00 00 00"
                                   pattern="[0-9\s]{10,20}"
                                   required>
                        </div>
                        <small class="form-hint">
                            Entrez votre numéro Mobile Money (Orange, MTN, Moov ou Wave)
                        </small>
                    </div>

                    <!-- Opérateur - SELECT CUSTOM PROFESSIONNEL -->
                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-sim-card"></i>
                            Opérateur Mobile Money
                        </label>

                        <!-- Container custom select -->
                        <div class="custom-select-container">
                            <div class="custom-select-trigger" id="operatorTrigger">
                                <span class="select-placeholder">Choisir votre opérateur</span>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>

                            <!-- Options customisées -->
                            <div class="custom-select-options" id="operatorOptions">
                                <input type="hidden" name="mobile_operator" id="mobileOperatorInput" required>

                                <div class="custom-select-option" data-value="orange">
                                    <div class="option-content">
                                        <div class="operator-badge orange">
                                            <i class="fas fa-fire"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Orange Money</div>
                                            <div class="option-description">07, 05, 04</div>
                                        </div>
                                        <i class="fas fa-check option-check"></i>
                                    </div>
                                </div>

                                <div class="custom-select-option" data-value="mtn">
                                    <div class="option-content">
                                        <div class="operator-badge mtn">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">MTN Mobile Money</div>
                                            <div class="option-description">06, 05</div>
                                        </div>
                                        <i class="fas fa-check option-check"></i>
                                    </div>
                                </div>

                                <div class="custom-select-option" data-value="moov">
                                    <div class="option-content">
                                        <div class="operator-badge moov">
                                            <i class="fas fa-leaf"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Moov Money</div>
                                            <div class="option-description">01, 02</div>
                                        </div>
                                        <i class="fas fa-check option-check"></i>
                                    </div>
                                </div>

                                <div class="custom-select-option" data-value="wave">
                                    <div class="option-content">
                                        <div class="operator-badge wave">
                                            <i class="fas fa-water"></i>
                                        </div>
                                        <div class="option-text">
                                            <div class="option-title">Wave</div>
                                            <div class="option-description">09</div>
                                        </div>
                                        <i class="fas fa-check option-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <small class="form-hint">
                            Sélectionnez votre opérateur Mobile Money
                        </small>
                    </div>
                </div>

                <!-- Bouton de confirmation -->
                <div class="mtn-form-group" style="margin-top: 2rem;">
                    <button type="submit" class="mtn-btn primary confirm-btn">
                        <i class="fas fa-mobile-alt"></i>
                        <span class="btn-text">Confirmer le dépôt</span>
                        <span class="btn-amount" id="confirmAmount">0 F</span>
                    </button>
                </div>

                <!-- Étapes du processus -->
                <div class="process-steps">
                    <h5>
                        <i class="fas fa-list-ol"></i>
                        Étapes du dépôt :
                    </h5>
                    <div class="steps-container">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <div class="step-title">Saisissez le montant</div>
                                <div class="step-description">Choisissez ou entrez le montant à déposer</div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <div class="step-title">Entrez vos informations</div>
                                <div class="step-description">Renseignez votre numéro et opérateur Mobile Money</div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <div class="step-title">Validez le paiement</div>
                                <div class="step-description">Confirmez et validez sur votre téléphone</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="security-info">
                    <div class="security-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="security-text">
                        <strong>Paiement 100% sécurisé</strong>
                        <small>Transaction cryptée et protégée</small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques pour le modal de dépôt */
.slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-base);
    padding: 1rem;
}

.slide-modal.show {
    opacity: 1;
    visibility: visible;
}

.slide-content {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    overflow: hidden;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
}

.slide-header h3 {
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
}

.slide-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.slide-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.slide-body {
    padding: 1.5rem;
    max-height: calc(100vh - 150px);
    overflow-y: auto;
}

/* ===== SELECT CUSTOM PROFESSIONNEL ===== */
.custom-select-container {
    position: relative;
    width: 100%;
}

.custom-select-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1rem;
    background: white;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all var(--transition-fast);
    user-select: none;
    min-height: 52px;
}

.custom-select-trigger:hover {
    border-color: var(--primary-400);
    background: var(--secondary-50);
}

.custom-select-trigger.active {
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
    background: white;
}

.select-placeholder {
    color: var(--secondary-500);
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.select-placeholder.has-value {
    color: var(--secondary-800);
    font-weight: 500;
}

.select-placeholder .operator-badge {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.select-arrow {
    color: var(--secondary-500);
    transition: transform var(--transition-fast);
    font-size: 0.9rem;
}

.custom-select-trigger.active .select-arrow {
    transform: rotate(180deg);
    color: var(--primary-500);
}

/* Options menu */
.custom-select-options {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 2px solid var(--primary-100);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all var(--transition-fast);
    max-height: 300px;
    overflow-y: auto;
}

.custom-select-options.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Options */
.custom-select-option {
    padding: 0.5rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.custom-select-option:hover {
    background: var(--secondary-50);
}

.custom-select-option.selected {
    background: var(--primary-50);
}

.option-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--border-radius-sm);
}

/* Badges opérateurs */
.operator-badge {
    width: 40px;
    height: 40px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.operator-badge.orange {
    background: linear-gradient(135deg, #FF7900 0%, #FFA64D 100%);
    box-shadow: 0 2px 8px rgba(255, 121, 0, 0.3);
}

.operator-badge.mtn {
    background: linear-gradient(135deg, #FFCC00 0%, #FFE066 100%);
    box-shadow: 0 2px 8px rgba(255, 204, 0, 0.3);
}

.operator-badge.moov {
    background: linear-gradient(135deg, #00B050 0%, #66D19E 100%);
    box-shadow: 0 2px 8px rgba(0, 176, 80, 0.3);
}

.operator-badge.wave {
    background: linear-gradient(135deg, #0078D7 0%, #4DA6FF 100%);
    box-shadow: 0 2px 8px rgba(0, 120, 215, 0.3);
}

/* Texte des options */
.option-text {
    flex: 1;
    min-width: 0;
}

.option-title {
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.option-description {
    font-size: 0.8rem;
    color: var(--secondary-600);
    font-weight: 500;
}

/* Checkmark */
.option-check {
    color: var(--primary-500);
    opacity: 0;
    transition: opacity var(--transition-fast);
    flex-shrink: 0;
    font-size: 0.9rem;
}

.custom-select-option.selected .option-check {
    opacity: 1;
}

/* ===== STYLES EXISTANTS ===== */
.amount-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.amount-option {
    background: white;
    border: 2px solid var(--secondary-300);
    border-radius: var(--border-radius);
    padding: 1rem 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--secondary-700);
    cursor: pointer;
    transition: all var(--transition-fast);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70px;
    outline: none;
}

.amount-option:hover {
    background: var(--secondary-100);
    border-color: var(--primary-400);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.amount-option.selected {
    background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%);
    border-color: var(--success-600);
    color: white;
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.amount-option .amount {
    font-size: 1.25rem;
    font-weight: 700;
    display: block;
    margin-bottom: 0.25rem;
}

.amount-option .currency {
    font-size: 0.9rem;
    font-weight: 500;
}

.mtn-form-group {
    margin-bottom: 1.5rem;
}

.mtn-form-label {
    display: block;
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mtn-form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: all var(--transition-fast);
    background: white;
    color: var(--secondary-800);
}

.mtn-form-control:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

.input-with-icon {
    position: relative;
}

.input-with-icon i:first-child {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary-500);
    z-index: 1;
}

.input-with-icon input {
    padding-left: 45px !important;
    width: 100%;
}

/* Bouton de confirmation amélioré */
.confirm-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
    overflow: hidden;
    position: relative;
}

.confirm-btn:hover {
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.confirm-btn:active {
    transform: translateY(0);
}

.confirm-btn i {
    font-size: 1.2rem;
}

.btn-text {
    flex: 1;
    text-align: left;
    padding: 0 1rem;
}

.btn-amount {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-sm);
    font-weight: 700;
    font-size: 1.2rem;
    backdrop-filter: blur(10px);
}

/* Form hints */
.form-hint {
    color: var(--secondary-500);
    font-size: 0.85rem;
    display: block;
    margin-top: 8px;
    padding-left: 5px;
    line-height: 1.4;
}

.form-hint i {
    margin-right: 0.25rem;
}

/* Process steps */
.process-steps {
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--info-50);
    border-radius: var(--border-radius);
    border: 1px solid var(--info-200);
}

.process-steps h5 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--info-700);
    margin-bottom: 20px;
    font-size: 1rem;
    font-weight: 600;
}

.steps-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.75rem;
    background: white;
    border-radius: var(--border-radius);
    border-left: 4px solid var(--info-400);
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--info-500) 0%, var(--info-700) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-title {
    font-weight: 600;
    color: var(--secondary-800);
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.step-description {
    font-size: 0.85rem;
    color: var(--secondary-600);
    line-height: 1.4;
}

/* Security info */
.security-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--warning-50);
    border-radius: var(--border-radius);
    border: 1px solid var(--warning-200);
}

.security-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--warning-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--warning-600);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.security-text {
    flex: 1;
}

.security-text strong {
    display: block;
    color: var(--warning-700);
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.security-text small {
    display: block;
    color: var(--secondary-600);
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 768px) {
    .slide-content {
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .slide-body {
        flex: 1;
        max-height: none;
        padding: 1rem;
    }

    .amount-selector {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .amount-option {
        min-height: 60px;
        padding: 0.75rem 0.25rem;
    }

    .confirm-btn {
        padding: 0.875rem 1rem;
        font-size: 1rem;
    }

    .btn-amount {
        font-size: 1rem;
        padding: 0.4rem 0.75rem;
    }
}

@media (max-width: 480px) {
    .amount-selector {
        grid-template-columns: 1fr 1fr;
    }

    .option-content {
        padding: 0.5rem;
    }

    .operator-badge {
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .custom-select-trigger,
    .custom-select-options {
        background: #2d2d2d;
        border-color: #404040;
        color: #e0e0e0;
    }

    .select-placeholder {
        color: #a0a0a0;
    }

    .custom-select-option:hover {
        background: #3d3d3d;
    }

    .custom-select-option.selected {
        background: #1e3a8a;
    }
}
</style>

<script>
// Gestion du modal de dépôt avec select custom
document.addEventListener('DOMContentLoaded', function() {
    initDepositModal();
});

function initDepositModal() {
    // Variables
    let currentAmount = 0;
    let selectedOperator = '';

    // Éléments DOM
    const amountOptions = document.querySelectorAll('#depositSlide .amount-option');
    const depositAmountInput = document.getElementById('depositAmount');
    const depositForm = document.getElementById('depositForm');
    const phoneInput = document.querySelector('#depositSlide input[name="phone_number"]');
    const confirmBtn = document.querySelector('#depositSlide .confirm-btn');
    const confirmAmountEl = document.getElementById('confirmAmount');

    // Éléments pour le custom select
    const operatorTrigger = document.getElementById('operatorTrigger');
    const operatorOptions = document.getElementById('operatorOptions');
    const mobileOperatorInput = document.getElementById('mobileOperatorInput');
    const optionElements = document.querySelectorAll('#depositSlide .custom-select-option');
    const selectPlaceholder = document.querySelector('.select-placeholder');

    // Initialisation
    function initialize() {
        // Sélectionner le premier montant par défaut
        if (amountOptions.length > 0) {
            selectAmount(amountOptions[0]);
        }

        // Gestion des montants
        amountOptions.forEach(option => {
            option.addEventListener('click', function() {
                selectAmount(this);
            });

            option.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectAmount(this);
                }
            });
        });

        // Gestion du montant personnalisé
        if (depositAmountInput) {
            depositAmountInput.addEventListener('input', function() {
                const amount = parseInt(this.value) || 0;
                currentAmount = amount;
                updateSelectedAmounts();
                updateConfirmButton();
            });

            depositAmountInput.addEventListener('blur', function() {
                const amount = parseInt(this.value) || 0;

                if (amount < 1000) {
                    showToast('warning', 'Montant minimum', 'Le montant minimum est de 1 000 FCFA');
                    this.value = 1000;
                    currentAmount = 1000;
                }

                if (currentAmount > 0) {
                    this.value = new Intl.NumberFormat('fr-FR').format(currentAmount);
                }

                updateConfirmButton();
            });
        }

        // Formatage du numéro de téléphone
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 0) {
                    value = value.match(/.{1,2}/g).join(' ');
                }
                this.value = value.slice(0, 14);

                // Détection automatique de l'opérateur
                autoDetectOperator(value);
            });
        }

        // ===== GESTION DU CUSTOM SELECT =====

        // Ouvrir/fermer le select
        operatorTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSelect();
        });

        // Sélectionner une option
        optionElements.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const title = this.querySelector('.option-title').textContent;
                const badgeType = this.querySelector('.operator-badge').className.split(' ')[1];

                selectOperator(value, title, badgeType);
                closeSelect();
            });
        });

        // Fermer le select en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!operatorTrigger.contains(e.target) && !operatorOptions.contains(e.target)) {
                closeSelect();
            }
        });

        // Navigation au clavier
        operatorTrigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSelect();
            } else if (e.key === 'Escape') {
                closeSelect();
            }
        });

        // Validation du formulaire
        function validateForm() {
            // Validation du montant
            if (currentAmount < 1000) {
                showToast('error', 'Montant invalide', 'Le montant minimum est de 1 000 FCFA');
                depositAmountInput?.focus();
                return false;
            }

            // Validation du numéro de téléphone
            const phoneNumber = phoneInput?.value || '';

            if (!phoneNumber || phoneNumber.replace(/\s/g, '').length < 10) {
                showToast('error', 'Numéro invalide', 'Veuillez entrer un numéro de téléphone valide (10 chiffres)');
                phoneInput?.focus();
                return false;
            }

            // Validation de l'opérateur
            if (!selectedOperator) {
                showToast('error', 'Opérateur manquant', 'Veuillez sélectionner votre opérateur Mobile Money');
                operatorTrigger.focus();
                return false;
            }

            return true;
        }

        // Soumission du formulaire
        if (depositForm) {
            depositForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                // Préparation des données
                const formData = new FormData(this);
                const data = {
                    amount: currentAmount,
                    payment_method: 'mobile_money',
                    phone_number: formData.get('phone_number'),
                    mobile_operator: selectedOperator,
                    _token: '{{ csrf_token() }}'
                };

                // État de chargement
                const originalText = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Traitement en cours...</span>';
                confirmBtn.disabled = true;

                try {
                    const response = await fetch('{{ route("client.wallet.deposit") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': data._token
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast('success', 'Dépôt initié',
                            `Votre dépôt de ${new Intl.NumberFormat('fr-FR').format(currentAmount)} F est en cours`);

                        setTimeout(() => {
                            closeSlide('depositSlide');
                            resetForm();
                            if (typeof refreshBalance === 'function') {
                                refreshBalance();
                            }
                        }, 2000);

                    } else {
                        throw new Error(result.message || 'Erreur lors du dépôt');
                    }

                } catch (error) {
                    console.error('Erreur:', error);
                    showToast('error', 'Erreur', error.message);

                    // Restaurer le bouton
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            });
        }
    }

    // ===== FONCTIONS CUSTOM SELECT =====

    function toggleSelect() {
        operatorOptions.classList.toggle('open');
        operatorTrigger.classList.toggle('active');

        if (operatorOptions.classList.contains('open')) {
            operatorTrigger.setAttribute('aria-expanded', 'true');
        } else {
            operatorTrigger.setAttribute('aria-expanded', 'false');
        }
    }

    function openSelect() {
        operatorOptions.classList.add('open');
        operatorTrigger.classList.add('active');
        operatorTrigger.setAttribute('aria-expanded', 'true');
    }

    function closeSelect() {
        operatorOptions.classList.remove('open');
        operatorTrigger.classList.remove('active');
        operatorTrigger.setAttribute('aria-expanded', 'false');
    }

    function selectOperator(value, title, badgeType) {
        selectedOperator = value;
        mobileOperatorInput.value = value;

        // Mettre à jour l'affichage du trigger
        selectPlaceholder.textContent = title;
        selectPlaceholder.classList.add('has-value');

        // Ajouter le badge au trigger
        const existingBadge = selectPlaceholder.querySelector('.operator-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        const badge = document.createElement('div');
        badge.className = `operator-badge ${badgeType}`;
        badge.innerHTML = `<i class="fas fa-${getOperatorIcon(value)}"></i>`;
        selectPlaceholder.insertBefore(badge, selectPlaceholder.firstChild);

        // Mettre à jour la sélection visuelle
        optionElements.forEach(option => {
            option.classList.remove('selected');
            if (option.getAttribute('data-value') === value) {
                option.classList.add('selected');
            }
        });
    }

    function getOperatorIcon(operator) {
        switch(operator) {
            case 'orange': return 'fire';
            case 'mtn': return 'bolt';
            case 'moov': return 'leaf';
            case 'wave': return 'water';
            default: return 'sim-card';
        }
    }

    function autoDetectOperator(phoneNumber) {
        const cleanNumber = phoneNumber.replace(/\s/g, '');

        if (cleanNumber.length >= 2) {
            const prefix = cleanNumber.substring(0, 2);
            let operator = '';
            let title = '';

            if (['07', '05', '04'].includes(prefix)) {
                operator = 'orange';
                title = 'Orange Money';
            } else if (['06', '05'].includes(prefix)) {
                operator = 'mtn';
                title = 'MTN Mobile Money';
            } else if (['01', '02'].includes(prefix)) {
                operator = 'moov';
                title = 'Moov Money';
            } else if (['09'].includes(prefix)) {
                operator = 'wave';
                title = 'Wave';
            }

            if (operator && !selectedOperator) {
                selectOperator(operator, title, operator);
            }
        }
    }

    // ===== FONCTIONS EXISTANTES =====

    function selectAmount(option) {
        const amount = parseInt(option.getAttribute('data-amount'));
        currentAmount = amount;

        if (depositAmountInput) {
            depositAmountInput.value = amount;
        }

        updateSelectedAmounts();
        updateConfirmButton();
    }

    function updateSelectedAmounts() {
        amountOptions.forEach(option => {
            const amount = parseInt(option.getAttribute('data-amount'));
            if (amount === currentAmount) {
                option.classList.add('selected');
                option.setAttribute('aria-selected', 'true');
            } else {
                option.classList.remove('selected');
                option.setAttribute('aria-selected', 'false');
            }
        });
    }

    function updateConfirmButton() {
        if (confirmAmountEl) {
            confirmAmountEl.textContent = new Intl.NumberFormat('fr-FR').format(currentAmount) + ' F';
        }
    }

    function resetForm() {
        currentAmount = 0;
        selectedOperator = '';

        if (depositAmountInput) depositAmountInput.value = '';
        if (phoneInput) phoneInput.value = '';
        if (mobileOperatorInput) mobileOperatorInput.value = '';

        // Réinitialiser le select custom
        selectPlaceholder.textContent = 'Choisir votre opérateur';
        selectPlaceholder.classList.remove('has-value');
        const existingBadge = selectPlaceholder.querySelector('.operator-badge');
        if (existingBadge) existingBadge.remove();

        // Réinitialiser les options
        optionElements.forEach(option => {
            option.classList.remove('selected');
        });

        // Réinitialiser les montants
        amountOptions.forEach(option => {
            option.classList.remove('selected');
            option.setAttribute('aria-selected', 'false');
        });

        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-mobile-alt"></i> <span class="btn-text">Confirmer le dépôt</span> <span class="btn-amount" id="confirmAmount">0 F</span>';
        }

        updateConfirmButton();
    }

    function showToast(type, title, message) {
        if (window.toast) {
            switch(type) {
                case 'error': window.toast.error(title, message); break;
                case 'warning': window.toast.warning(title, message); break;
                case 'success': window.toast.success(title, message); break;
                default: window.toast.info(title, message);
            }
        } else {
            console.log(`${type}: ${title} - ${message}`);
        }
    }

    // Initialiser
    initialize();
}

function showDepositModal() {
    const modal = document.getElementById('depositSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            const amountInput = document.getElementById('depositAmount');
            if (amountInput) amountInput.focus();
        }, 300);
    }
}

function closeSlide(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}
</script>
