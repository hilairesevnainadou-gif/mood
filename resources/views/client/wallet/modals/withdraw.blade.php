<div class="slide-modal" id="withdrawSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-arrow-up"></i> Faire un retrait</h3>
            <button class="slide-close" onclick="closeSlide('withdrawSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="slide-body">
            <form id="withdrawForm">
                @csrf
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-money-bill-wave"></i>
                        Montant à retirer
                    </label>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fas fa-money-bill-wave" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--mtn-gray);"></i>
                        <input type="number" 
                               class="mtn-form-control" 
                               id="withdrawAmount" 
                               name="amount" 
                               placeholder="Montant" 
                               min="1000"
                               max="{{ $wallet->balance ?? 0 }}"
                               step="100"
                               required
                               style="padding-left: 45px;">
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                        <small style="color: var(--mtn-gray); font-size: 0.85rem;">
                            Minimum: 1 000 FCFA
                        </small>
                        <small style="color: var(--mtn-dark); font-weight: 500; font-size: 0.85rem;">
                            <i class="fas fa-wallet"></i>
                            Solde: {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} FCFA
                        </small>
                    </div>
                    
                    <div class="amount-selector" style="margin-top: 15px;">
                        <button type="button" class="amount-option" data-amount="5000">5 000</button>
                        <button type="button" class="amount-option" data-amount="10000">10 000</button>
                        <button type="button" class="amount-option" data-amount="20000">20 000</button>
                        <button type="button" class="amount-option" data-amount="50000">50 000</button>
                        <button type="button" class="amount-option" data-amount="{{ $wallet->balance ?? 0 }}">Tout</button>
                    </div>
                </div>
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">Méthode de retrait</label>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="withdraw_method" value="mobile_money" class="d-none" checked>
                            <div class="method-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="method-info">
                                <div class="method-name">Mobile Money</div>
                                <div class="method-desc">Orange, MTN, Moov</div>
                            </div>
                            <i class="fas fa-check-circle"></i>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="withdraw_method" value="bank_transfer" class="d-none">
                            <div class="method-icon" style="background: linear-gradient(135deg, #28a745, #5cd85c);">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="method-info">
                                <div class="method-name">Virement bancaire</div>
                                <div class="method-desc">Vers compte bancaire</div>
                            </div>
                            <i class="fas fa-check-circle" style="opacity: 0.3;"></i>
                        </label>
                    </div>
                </div>
                
                <div id="withdrawMobileFields" class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-phone"></i>
                        Numéro de téléphone
                    </label>
                    <div class="input-with-icon" style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--mtn-gray);"></i>
                        <input type="tel" 
                               class="mtn-form-control" 
                               name="phone_number" 
                               placeholder="Ex: 07 00 00 00 00"
                               required
                               style="padding-left: 45px;">
                    </div>
                    
                    <div class="mtn-form-group" style="margin-top: 15px;">
                        <label class="mtn-form-label">
                            <i class="fas fa-sim-card"></i>
                            Opérateur mobile
                        </label>
                        <select class="mtn-form-control" name="mobile_operator" required>
                            <option value="">Choisir un opérateur</option>
                            <option value="orange">Orange Money</option>
                            <option value="mtn">MTN Mobile Money</option>
                            <option value="moov">Moov Money</option>
                            <option value="wave">Wave</option>
                        </select>
                    </div>
                </div>
                
                <div id="withdrawBankFields" class="mtn-form-group" style="display: none;">
                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-user"></i>
                            Nom du compte
                        </label>
                        <input type="text" 
                               class="mtn-form-control" 
                               name="account_name" 
                               placeholder="Nom complet"
                               required>
                    </div>
                    
                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-credit-card"></i>
                            Numéro de compte
                        </label>
                        <input type="text" 
                               class="mtn-form-control" 
                               name="account_number" 
                               placeholder="Numéro de compte"
                               required>
                    </div>
                    
                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-landmark"></i>
                            Nom de la banque
                        </label>
                        <input type="text" 
                               class="mtn-form-control" 
                               name="bank_name" 
                               placeholder="Nom de la banque"
                               required>
                    </div>
                    
                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-code-branch"></i>
                            Code banque (optionnel)
                        </label>
                        <input type="text" 
                               class="mtn-form-control" 
                               name="bank_code" 
                               placeholder="Code banque">
                    </div>
                </div>
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-sticky-note"></i>
                        Note (optionnel)
                    </label>
                    <textarea class="mtn-form-control" 
                              name="note" 
                              rows="2" 
                              placeholder="Ex: Pour frais de transport..."></textarea>
                </div>
                
                <div class="mtn-form-group">
                    <div style="background: var(--mtn-yellow-light); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="color: var(--mtn-gray); font-size: 0.9rem;">Frais de retrait:</span>
                            <span style="color: var(--mtn-dark); font-weight: 500;">0 FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--mtn-gray); font-size: 0.9rem;">À recevoir:</span>
                            <span style="color: var(--mtn-success); font-weight: 600;" id="amountToReceive">0 FCFA</span>
                        </div>
                    </div>
                </div>
                
                <div class="mtn-form-group">
                    <button type="submit" class="mtn-btn primary">
                        <i class="fas fa-check-circle"></i>
                        Confirmer le retrait
                    </button>
                </div>
                
                <div style="text-align: center; margin-top: 20px; padding: 15px; background: var(--mtn-yellow-light); border-radius: 10px;">
                    <small style="color: var(--mtn-yellow-dark); display: block; margin-bottom: 5px;">
                        <i class="fas fa-info-circle"></i>
                        Délai de traitement
                    </small>
                    <small style="color: var(--mtn-gray); font-size: 0.8rem;">
                        Mobile Money: Instantané • Virement bancaire: 24-48h
                    </small>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des options de montant pour retrait
    const amountOptions = document.querySelectorAll('#withdrawSlide .amount-option');
    const withdrawAmountInput = document.getElementById('withdrawAmount');
    const amountToReceive = document.getElementById('amountToReceive');
    const walletBalance = {{ $wallet->balance ?? 0 }};
    
    amountOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la classe selected de toutes les options
            amountOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la classe selected à l'option cliquée
            this.classList.add('selected');
            
            // Mettre à jour l'input avec la valeur
            let amount = this.getAttribute('data-amount');
            
            // Si "Tout" est sélectionné, utiliser le solde complet
            if (this.textContent === 'Tout') {
                amount = walletBalance;
            }
            
            withdrawAmountInput.value = amount;
            
            // Calculer le montant à recevoir (montant - frais)
            calculateAmountToReceive(amount);
            
            // Focus sur l'input
            withdrawAmountInput.focus();
        });
    });
    
    // Calcul du montant à recevoir en temps réel
    withdrawAmountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        calculateAmountToReceive(amount);
    });
    
    function calculateAmountToReceive(amount) {
        const fees = 0; // Pas de frais pour le moment
        const netAmount = amount - fees;
        
        if (amountToReceive) {
            amountToReceive.textContent = new Intl.NumberFormat('fr-FR').format(netAmount) + ' FCFA';
        }
    }
    
    // Gestion du changement de méthode de retrait
    const withdrawMethods = document.querySelectorAll('#withdrawSlide input[name="withdraw_method"]');
    const mobileFields = document.getElementById('withdrawMobileFields');
    const bankFields = document.getElementById('withdrawBankFields');
    
    withdrawMethods.forEach(method => {
        method.addEventListener('change', function() {
            const value = this.value;
            
            // Mettre à jour les icônes de vérification
            document.querySelectorAll('#withdrawSlide .payment-method .fa-check-circle').forEach(icon => {
                icon.style.opacity = '0.3';
            });
            
            // Activer l'icône de la méthode sélectionnée
            const parent = this.closest('.payment-method');
            parent.querySelector('.fa-check-circle').style.opacity = '1';
            
            // Afficher/masquer les champs correspondants
            if (value === 'mobile_money') {
                mobileFields.style.display = 'block';
                bankFields.style.display = 'none';
            } else if (value === 'bank_transfer') {
                mobileFields.style.display = 'none';
                bankFields.style.display = 'block';
            }
        });
    });
    
    // Formatage du numéro de téléphone
    const phoneInput = document.querySelector('#withdrawSlide input[name="phone_number"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = value.match(/.{1,2}/g).join(' ');
            }
            this.value = value;
        });
    }
    
    // Validation du montant maximum
    withdrawAmountInput.addEventListener('blur', function() {
        const amount = parseFloat(this.value) || 0;
        const maxAmount = parseFloat(this.getAttribute('max')) || walletBalance;
        
        if (amount > maxAmount) {
            showToast('Le montant ne peut pas dépasser votre solde', 'warning');
            this.value = maxAmount;
            calculateAmountToReceive(maxAmount);
        }
        
        if (amount < 1000) {
            showToast('Le montant minimum est de 1 000 FCFA', 'warning');
            this.value = 1000;
            calculateAmountToReceive(1000);
        }
    });
});
</script>