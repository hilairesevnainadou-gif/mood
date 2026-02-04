{{-- resources/views/client/wallet/modals/transfer.blade.php --}}
<div class="slide-modal" id="transferSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-exchange-alt"></i> Transférer de l'argent</h3>
            <button class="slide-close" onclick="closeSlide('transferSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="slide-body">
            <form id="transferForm">
                @csrf
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-id-card"></i>
                        Numéro du portefeuille destinataire
                    </label>
                    <input type="text" 
                           class="mtn-form-control" 
                           name="recipient_wallet" 
                           placeholder="Ex: WALLET-2401-000001"
                           required>
                </div>
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-money-bill-wave"></i>
                        Montant à transférer
                    </label>
                    <input type="number" 
                           class="mtn-form-control" 
                           name="amount" 
                           placeholder="Montant" 
                           min="100"
                           max="{{ $wallet->balance ?? 0 }}"
                           required>
                    <small style="color: var(--mtn-gray);">Minimum: 100 FCFA</small>
                </div>
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-user"></i>
                        Nom du destinataire (optionnel)
                    </label>
                    <input type="text" 
                           class="mtn-form-control" 
                           name="recipient_name" 
                           placeholder="Nom complet">
                </div>
                
                <div class="mtn-form-group">
                    <label class="mtn-form-label">
                        <i class="fas fa-comment"></i>
                        Raison du transfert (optionnel)
                    </label>
                    <textarea class="mtn-form-control" 
                              name="reason" 
                              rows="3" 
                              placeholder="Ex: Remboursement, Cadeau..."></textarea>
                </div>
                
                <div class="mtn-form-group">
                    <button type="submit" class="mtn-btn primary">
                        <i class="fas fa-paper-plane"></i>
                        Transférer maintenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>