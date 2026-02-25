<div class="slide-modal" id="depositSlide">
    <div class="slide-content">
        <div class="slide-header" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
            <h3><i class="fas fa-arrow-down"></i> Déposer des fonds</h3>
            <button class="slide-close" onclick="closeSlide('depositSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <form id="depositForm" onsubmit="return false;">
                @csrf

                <!-- Étape 1 : Montant -->
                <div id="depositStep1">
                    <div class="deposit-info-box">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Paiement sécurisé</strong>
                            <p>Votre transaction est protégée par Kkiapay. Le montant sera crédité instantanément.</p>
                        </div>
                    </div>

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
                                   placeholder="Montant"
                                   min="100"
                                   step="100"
                                   required>
                        </div>

                        <div class="amount-limits">
                            <small>Minimum: 100 FCFA</small>
                            <small><i class="fas fa-lock"></i> Paiement sécurisé</small>
                        </div>

                        <div class="amount-selector">
                            <button type="button" class="amount-option" data-amount="1000">1 000</button>
                            <button type="button" class="amount-option" data-amount="5000">5 000</button>
                            <button type="button" class="amount-option" data-amount="10000">10 000</button>
                            <button type="button" class="amount-option" data-amount="20000">20 000</button>
                            <button type="button" class="amount-option" data-amount="50000">50 000</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Numéro de téléphone (Mobile Money)
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel"
                                   class="form-control"
                                   id="depositPhone"
                                   name="phone"
                                   placeholder="Ex: 07 XX XX XX XX"
                                   maxlength="10"
                                   required>
                        </div>
                        <small class="form-text text-muted">Format: 10 chiffres sans espaces</small>
                    </div>

                    <div class="deposit-summary" id="summaryCard" style="display: none;">
                        <div class="summary-row">
                            <span>Montant:</span>
                            <strong id="depositSummaryAmount">0 FCFA</strong>
                        </div>
                        <div class="summary-row">
                            <span>Frais:</span>
                            <strong class="text-success">Gratuit</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Total à payer:</span>
                            <strong id="depositSummaryTotal">0 FCFA</strong>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-full" id="payButton" onclick="processDeposit()" disabled>
                        <i class="fas fa-credit-card"></i> Payer maintenant
                    </button>

                    <div class="payment-methods-display">
                        <small class="text-muted">Paiement sécurisé par</small>
                        <div class="payment-logos">
                            <span class="payment-logo">Orange Money</span>
                            <span class="payment-logo">MTN Mobile Money</span>
                            <span class="payment-logo">Moov Money</span>
                            <span class="payment-logo">Wave</span>
                        </div>
                    </div>
                </div>

                <!-- Étape 2 : Traitement -->
                <div id="depositStep2" style="display: none;">
                    <div class="processing-box">
                        <div class="processing-spinner">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </div>
                        <h4>Traitement du paiement...</h4>
                        <p>Veuillez compléter le paiement sur votre téléphone</p>
                        <div class="processing-details">
                            <div class="detail-row">
                                <span>Montant:</span>
                                <strong id="processingAmount">-</strong>
                            </div>
                            <div class="detail-row">
                                <span>Référence:</span>
                                <strong id="processingRef">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Étape 3 : Résultat -->
                <div id="depositStep3" style="display: none;">
                    <div class="result-box" id="resultBox">
                        <div class="result-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 id="resultTitle">Paiement réussi !</h4>
                        <p id="resultMessage">Votre compte a été crédité avec succès.</p>
                        <div class="result-details" id="resultDetails"></div>
                        <button type="button" class="btn btn-primary btn-full" onclick="closeSlide('depositSlide')">
                            <i class="fas fa-check"></i> Terminé
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Styles du modal dépôt */
.deposit-info-box {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    margin-bottom: 20px;
}

.deposit-info-box i {
    color: #22c55e;
    font-size: 1.25rem;
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
}

.payment-methods-display {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.payment-logos {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}

.payment-logo {
    padding: 4px 12px;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.processing-box {
    text-align: center;
    padding: 40px 20px;
}

.processing-spinner {
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

.processing-box h4 {
    color: #111827;
    margin-bottom: 8px;
}

.processing-box p {
    color: #6b7280;
    margin-bottom: 24px;
}

.processing-details {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    text-align: left;
}

.result-box {
    text-align: center;
    padding: 40px 20px;
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

.result-box.success .result-icon {
    background: #f0fdf4;
    color: #22c55e;
}

.result-box.error .result-icon {
    background: #fef2f2;
    color: #ef4444;
}

.result-box h4 {
    color: #111827;
    margin-bottom: 8px;
}

.result-box p {
    color: #6b7280;
    margin-bottom: 24px;
}

.result-details {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    text-align: left;
}
</style>

<script>
// Script du modal dépôt - PAS de déclaration de walletBalance ici !
(function() {
    'use strict';

    const DepositModal = {
        isProcessing: false,
        initialized: false,
        paymentCompleted: false,

        init: function() {
            if (this.initialized) return;
            this.initialized = true;
            this.cacheElements();
            this.bindEvents();
            console.log('DepositModal initialisé');
        },

        cacheElements: function() {
            this.elements = {
                form: document.getElementById('depositForm'),
                amountInput: document.getElementById('depositAmount'),
                phoneInput: document.getElementById('depositPhone'),
                amountOptions: document.querySelectorAll('#depositSlide .amount-option'),
                payButton: document.getElementById('payButton'),
                summaryCard: document.getElementById('summaryCard'),
                summaryAmount: document.getElementById('depositSummaryAmount'),
                summaryTotal: document.getElementById('depositSummaryTotal'),
                step1: document.getElementById('depositStep1'),
                step2: document.getElementById('depositStep2'),
                step3: document.getElementById('depositStep3'),
                processingAmount: document.getElementById('processingAmount'),
                processingRef: document.getElementById('processingRef'),
                resultBox: document.getElementById('resultBox'),
                resultTitle: document.getElementById('resultTitle'),
                resultMessage: document.getElementById('resultMessage'),
                resultDetails: document.getElementById('resultDetails'),
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

                    const amount = parseFloat(this.getAttribute('data-amount')) || 0;
                    if (elems.amountInput) {
                        elems.amountInput.value = amount;
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
            }

            // Formatage téléphone
            if (elems.phoneInput) {
                elems.phoneInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, '').slice(0, 10);
                    this.value = value;
                    self.validateForm();
                });
            }

            // Fermeture sur clic extérieur
            const modal = document.getElementById('depositSlide');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this && !self.isProcessing) {
                        self.close();
                    }
                });
            }
        },

        updateSummary: function(amount) {
            const formatted = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
            if (this.elements.summaryAmount) this.elements.summaryAmount.textContent = formatted;
            if (this.elements.summaryTotal) this.elements.summaryTotal.textContent = formatted;

            if (amount >= 100) {
                if (this.elements.summaryCard) this.elements.summaryCard.style.display = 'block';
                this.validateForm();
            } else {
                if (this.elements.summaryCard) this.elements.summaryCard.style.display = 'none';
                if (this.elements.payButton) this.elements.payButton.disabled = true;
            }
        },

        validateForm: function() {
            const amount = parseFloat(this.elements.amountInput?.value) || 0;
            const phone = this.elements.phoneInput?.value?.trim() || '';

            const isValid = amount >= 100 && phone.length === 10;
            if (this.elements.payButton) {
                this.elements.payButton.disabled = !isValid;
            }
            return isValid;
        },

        processDeposit: async function() {
            if (this.isProcessing || !this.validateForm()) return;

            const amount = parseFloat(this.elements.amountInput.value);
            const phone = this.elements.phoneInput.value.trim();

            this.isProcessing = true;
            if (this.elements.payButton) {
                this.elements.payButton.disabled = true;
                this.elements.payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Préparation...';
            }

            try {
                // Créer la transaction côté serveur
                const response = await fetch('{{ route("client.wallet.deposit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount,
                        phone: phone
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erreur lors de la préparation du paiement');
                }

                // Afficher l'étape de traitement
                this.showStep(2);
                if (this.elements.processingAmount) {
                    this.elements.processingAmount.textContent = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
                }
                if (this.elements.processingRef) {
                    this.elements.processingRef.textContent = data.reference || '-';
                }

                // Ouvrir Kkiapay dans un nouvel onglet/popup
                this.openKkiapay(data, amount, phone);

            } catch (error) {
                console.error('Erreur dépôt:', error);
                this.showResult(false, error.message || 'Erreur de connexion');
                this.isProcessing = false;
                if (this.elements.payButton) {
                    this.elements.payButton.disabled = false;
                    this.elements.payButton.innerHTML = '<i class="fas fa-credit-card"></i> Payer maintenant';
                }
            }
        },

        openKkiapay: function(transactionData, amount, phone) {
            // Construire l'URL Kkiapay
            const kkiapayUrl = `https://widget.kkiapay.me/?` + new URLSearchParams({
                api_key: "{{ config('services.kkiapay.public_key', '') }}",
                amount: amount,
                phone: phone,
                email: transactionData.user_email || '',
                firstname: transactionData.user_name || '',
                reference: transactionData.reference || '',
                sandbox: "{{ config('services.kkiapay.sandbox', true) ? 'true' : 'false' }}",
                callback_url: "{{ route('kkiapay.callback') }}"
            }).toString();

            // Ouvrir dans une popup
            const popup = window.open(kkiapayUrl, 'kkiapay', 'width=500,height=600,scrollbars=yes');

            if (!popup) {
                // Si popup bloquée, rediriger
                window.location.href = kkiapayUrl;
                return;
            }

            // Vérifier si la popup se ferme
            const checkClosed = setInterval(() => {
                if (popup.closed) {
                    clearInterval(checkClosed);
                    this.checkPaymentStatus(transactionData.reference);
                }
            }, 1000);
        },

        checkPaymentStatus: async function(reference) {
            try {
                // Vérifier le statut du paiement
                const response = await fetch(`{{ route('payment.status', '') }}/${reference}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value
                    }
                });

                const data = await response.json();

                if (data.success && data.status === 'completed') {
                    this.showResult(true, 'Votre compte a été crédité avec succès !', {
                        amount: data.amount,
                        reference: reference
                    });

                    // Mettre à jour le solde
                    if (typeof window.refreshBalance === 'function') {
                        window.refreshBalance();
                    }
                } else if (data.status === 'pending') {
                    this.showResult(false, 'Paiement en attente. Veuillez vérifier votre téléphone.');
                } else {
                    this.showResult(false, 'Le paiement n\'a pas été complété.');
                }
            } catch (error) {
                console.error('Erreur vérification:', error);
                this.showResult(false, 'Impossible de vérifier le statut du paiement.');
            }
        },

        showStep: function(stepNumber) {
            if (this.elements.step1) this.elements.step1.style.display = stepNumber === 1 ? 'block' : 'none';
            if (this.elements.step2) this.elements.step2.style.display = stepNumber === 2 ? 'block' : 'none';
            if (this.elements.step3) this.elements.step3.style.display = stepNumber === 3 ? 'block' : 'none';
        },

        showResult: function(success, message, details) {
            this.paymentCompleted = true;
            this.isProcessing = false;
            this.showStep(3);

            if (this.elements.resultBox) {
                this.elements.resultBox.className = 'result-box ' + (success ? 'success' : 'error');
            }

            const icon = this.elements.resultBox?.querySelector('.result-icon i');
            if (icon) {
                icon.className = success ? 'fas fa-check-circle' : 'fas fa-times-circle';
            }

            if (this.elements.resultTitle) {
                this.elements.resultTitle.textContent = success ? 'Paiement réussi !' : 'Paiement échoué';
            }

            if (this.elements.resultMessage) {
                this.elements.resultMessage.textContent = message;
            }

            if (this.elements.resultDetails && details) {
                this.elements.resultDetails.innerHTML = `
                    <div class="detail-row">
                        <span>Montant:</span>
                        <strong>${new Intl.NumberFormat('fr-FR').format(details.amount)} FCFA</strong>
                    </div>
                    ${details.reference ? `
                    <div class="detail-row">
                        <span>Référence:</span>
                        <strong>${details.reference}</strong>
                    </div>
                    ` : ''}
                `;
            }
        },

        open: function() {
            const modal = document.getElementById('depositSlide');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                this.reset();
            }
        },

        close: function() {
            if (this.isProcessing) return;

            const modal = document.getElementById('depositSlide');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                this.reset();
            }
        },

        reset: function() {
            this.isProcessing = false;
            this.paymentCompleted = false;

            if (this.elements.form) {
                this.elements.form.reset();
            }

            this.elements.amountOptions?.forEach(opt => opt.classList.remove('selected'));
            this.updateSummary(0);
            this.showStep(1);

            if (this.elements.payButton) {
                this.elements.payButton.disabled = true;
                this.elements.payButton.innerHTML = '<i class="fas fa-credit-card"></i> Payer maintenant';
            }
        }
    };

    // Initialisation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            DepositModal.init();
        });
    } else {
        DepositModal.init();
    }

    // Exposition globale
    window.DepositModal = DepositModal;
    window.processDeposit = function() {
        DepositModal.processDeposit();
    };

})();
</script>
