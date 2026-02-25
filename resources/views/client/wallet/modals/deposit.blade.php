<!-- Modal Dépôt Kkiapay -->
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

                <!-- Étape 1 : Formulaire -->
                <div id="depositStep1">
                    <div class="deposit-info-box">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Paiement sécurisé</strong>
                            <p>Votre transaction est protégée par Kkiapay.</p>
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
                                   placeholder="Montant minimum 100"
                                   min="100"
                                   step="100"
                                   required>
                        </div>

                        <div class="amount-selector">
                            <button type="button" class="amount-option" onclick="setAmount(1000)">1 000</button>
                            <button type="button" class="amount-option" onclick="setAmount(5000)">5 000</button>
                            <button type="button" class="amount-option" onclick="setAmount(10000)">10 000</button>
                            <button type="button" class="amount-option" onclick="setAmount(20000)">20 000</button>
                            <button type="button" class="amount-option" onclick="setAmount(50000)">50 000</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Numéro Mobile Money
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel"
                                   class="form-control"
                                   id="depositPhone"
                                   name="phone"
                                   placeholder="Ex: 0744444444"
                                   maxlength="10"
                                   required>
                        </div>
                        <small style="color: #6b7280; font-size: 0.8rem;">
                            <i class="fas fa-info-circle"></i>
                            Numéros test: 0744444444, 0544444444, 0144444444
                        </small>
                    </div>

                    <div class="deposit-summary" id="summaryCard" style="display: none;">
                        <div class="summary-row">
                            <span>Montant:</span>
                            <strong id="depositSummaryAmount">0 FCFA</strong>
                        </div>
                        <div class="summary-row">
                            <span>Frais:</span>
                            <strong style="color: #22c55e;">Gratuit</strong>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <strong id="depositSummaryTotal">0 FCFA</strong>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-primary btn-full"
                            id="prepareButton"
                            onclick="preparePayment()"
                            disabled>
                        <i class="fas fa-credit-card"></i> Payer maintenant
                    </button>
                </div>

                <!-- Étape 2 : Traitement -->
                <div id="depositStep2" style="display: none;">
                    <div class="processing-box">
                        <div class="processing-spinner">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </div>
                        <h4>En attente du paiement...</h4>
                        <p>Validez la transaction sur votre téléphone</p>
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
                        <button type="button" class="btn btn-secondary" onclick="checkPaymentStatus()" style="margin-top: 15px;">
                            <i class="fas fa-sync"></i> Vérifier le statut
                        </button>
                    </div>
                </div>

                <!-- Étape 3 : Résultat -->
                <div id="depositStep3" style="display: none;">
                    <div class="result-box" id="resultBox">
                        <div class="result-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 id="resultTitle">Paiement réussi !</h4>
                        <p id="resultMessage">Votre compte a été crédité.</p>
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
/* Styles spécifiques au modal de dépôt */
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
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}

.input-with-icon {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.form-control {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #22c55e;
}

.amount-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}

.amount-option {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    background: white;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.amount-option:hover, .amount-option.active {
    background: #22c55e;
    border-color: #22c55e;
    color: white;
}

.deposit-summary {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin: 20px 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
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

.btn-full {
    width: 100%;
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

.processing-details {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin-top: 20px;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
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
    background: #f0fdf4;
    color: #22c55e;
}

.result-box.error .result-icon {
    background: #fef2f2;
    color: #ef4444;
}

.result-details {
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    margin: 20px 0;
    text-align: left;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.processing-spinner i {
    animation: spin 1s linear infinite;
}
</style>

<script>
// Variables globales pour le dépôt
let currentTransaction = null;
let isProcessing = false;

// Initialisation du formulaire de dépôt
document.addEventListener('DOMContentLoaded', function() {
    initDepositForm();
});

function initDepositForm() {
    const amountInput = document.getElementById('depositAmount');
    const phoneInput = document.getElementById('depositPhone');

    if (amountInput) {
        amountInput.addEventListener('input', function() {
            updateDepositSummary();
            validateDepositForm();
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
            validateDepositForm();
        });
    }
}

// Fonctions globales
window.setAmount = function(amount) {
    const input = document.getElementById('depositAmount');
    if (input) {
        input.value = amount;
        document.querySelectorAll('.amount-option').forEach(btn => {
            btn.classList.remove('active');
            if (btn.textContent.includes(amount.toLocaleString())) {
                btn.classList.add('active');
            }
        });
        updateDepositSummary();
        validateDepositForm();
    }
};

window.updateDepositSummary = function() {
    const amount = parseFloat(document.getElementById('depositAmount')?.value) || 0;
    const formatted = new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';

    const summaryAmount = document.getElementById('depositSummaryAmount');
    const summaryTotal = document.getElementById('depositSummaryTotal');
    const summaryCard = document.getElementById('summaryCard');

    if (summaryAmount) summaryAmount.textContent = formatted;
    if (summaryTotal) summaryTotal.textContent = formatted;
    if (summaryCard) summaryCard.style.display = amount >= 100 ? 'block' : 'none';
};

window.validateDepositForm = function() {
    const amount = parseFloat(document.getElementById('depositAmount')?.value) || 0;
    const phone = document.getElementById('depositPhone')?.value?.trim() || '';
    const isValid = amount >= 100 && phone.length === 10;

    const btn = document.getElementById('prepareButton');
    if (btn) btn.disabled = !isValid;
};

window.preparePayment = async function() {
    if (isProcessing) return;

    const amount = parseFloat(document.getElementById('depositAmount')?.value);
    const phone = document.getElementById('depositPhone')?.value?.trim();

    if (!amount || amount < 100) {
        alert('Montant minimum: 100 FCFA');
        return;
    }
    if (!phone || phone.length !== 10) {
        alert('Numéro invalide (10 chiffres requis)');
        return;
    }

    // Vérifier que le SDK Kkiapay est chargé
    if (typeof window.openKkiapayWidget !== 'function') {
        alert('Erreur: SDK Kkiapay non chargé. Veuillez rafraîchir la page.');
        console.error('SDK Kkiapay non disponible');
        return;
    }

    isProcessing = true;
    const btn = document.getElementById('prepareButton');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Préparation...';
    btn.disabled = true;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch('{{ route("client.wallet.deposit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount, phone })
        });

        const data = await response.json();
        console.log('Réponse serveur:', data);

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Erreur serveur');
        }

        currentTransaction = {
            reference: data.reference,
            amount: amount,
            phone: phone
        };

        // Afficher l'étape traitement
        document.getElementById('depositStep1').style.display = 'none';
        document.getElementById('depositStep2').style.display = 'block';
        document.getElementById('processingAmount').textContent =
            new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        document.getElementById('processingRef').textContent = data.reference;

        // Ouvrir Kkiapay
        window.openKkiapayWidget({
            amount: amount,
            key: "{{ config('services.kkiapay.public_key', '') }}",
            sandbox: {{ config('services.kkiapay.sandbox', true) ? 'true' : 'false' }},
            phone: phone,
            email: "{{ auth()->user()->email ?? '' }}",
            firstname: "{{ auth()->user()->first_name ?? auth()->user()->name ?? '' }}",
            lastname: "{{ auth()->user()->last_name ?? '' }}",
            data: {
                reference: data.reference,
                user_id: {{ auth()->id() ?? 'null' }}
            },
            callback: "{{ route('kkiapay.callback') }}"
        });

    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur: ' + error.message);
        isProcessing = false;
        btn.innerHTML = '<i class="fas fa-credit-card"></i> Payer maintenant';
        btn.disabled = false;
    }
};

window.checkPaymentStatus = async function() {
    if (!currentTransaction?.reference) return;

    try {
        const response = await fetch(`{{ url('/payment/status') }}/${currentTransaction.reference}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const data = await response.json();

        if (data.status === 'completed' || data.status === 'success') {
            showDepositResult(true, 'Paiement confirmé !', data);
        } else if (data.status === 'pending') {
            alert('Paiement toujours en attente. Vérifiez votre téléphone.');
        } else {
            showDepositResult(false, 'Paiement non complété', data);
        }
    } catch (error) {
        console.error('Erreur vérification:', error);
    }
};

window.showDepositResult = function(success, message, data) {
    isProcessing = false;

    document.getElementById('depositStep1').style.display = 'none';
    document.getElementById('depositStep2').style.display = 'none';
    document.getElementById('depositStep3').style.display = 'block';

    const resultBox = document.getElementById('resultBox');
    const icon = resultBox?.querySelector('.result-icon i');

    if (resultBox) resultBox.className = 'result-box ' + (success ? 'success' : 'error');
    if (icon) {
        icon.className = success ? 'fas fa-check-circle' : 'fas fa-times-circle';
        icon.style.color = success ? '#22c55e' : '#ef4444';
    }

    document.getElementById('resultTitle').textContent = success ? 'Paiement réussi !' : 'Paiement échoué';
    document.getElementById('resultMessage').textContent = message;

    const details = document.getElementById('resultDetails');
    if (details && currentTransaction) {
        details.innerHTML = `
            <div class="detail-row">
                <span>Montant:</span>
                <strong>${new Intl.NumberFormat('fr-FR').format(currentTransaction.amount)} FCFA</strong>
            </div>
            <div class="detail-row">
                <span>Référence:</span>
                <strong>${currentTransaction.reference}</strong>
            </div>
        `;
    }

    // Rafraîchir le solde si succès
    if (success && typeof window.refreshBalance === 'function') {
        setTimeout(window.refreshBalance, 1000);
    }
};

window.resetDepositForm = function() {
    currentTransaction = null;
    isProcessing = false;

    document.getElementById('depositForm')?.reset();
    document.getElementById('summaryCard') && (document.getElementById('summaryCard').style.display = 'none');
    document.getElementById('depositStep1') && (document.getElementById('depositStep1').style.display = 'block');
    document.getElementById('depositStep2') && (document.getElementById('depositStep2').style.display = 'none');
    document.getElementById('depositStep3') && (document.getElementById('depositStep3').style.display = 'none');

    const btn = document.getElementById('prepareButton');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-credit-card"></i> Payer maintenant';
        btn.disabled = true;
    }

    document.querySelectorAll('.amount-option').forEach(btn => btn.classList.remove('active'));
};

// Écouter les événements Kkiapay
if (typeof window.addSuccessListener === 'function') {
    window.addSuccessListener(function(data) {
        console.log('✅ Paiement succès:', data);
        showDepositResult(true, 'Votre compte a été crédité avec succès !', data);
    });

    window.addFailedListener(function(data) {
        console.log('❌ Paiement échoué:', data);
        showDepositResult(false, 'Le paiement a échoué. Veuillez réessayer.', data);
    });

    window.addKkiapayCloseListener(function() {
        console.log('🔒 Widget fermé');
        if (isProcessing) {
            setTimeout(checkPaymentStatus, 2000);
        }
    });
}
</script>
