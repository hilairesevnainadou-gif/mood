<!-- Modal PIN -->
<div class="slide-modal" id="pinSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-key"></i> Gérer le code PIN</h3>
            <button class="slide-close" onclick="closeSlide('pinSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <!-- Section : Changer le PIN -->
            <div class="mtn-form-group">
                <h4 style="margin-bottom: 20px; color: var(--secondary-800); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exchange-alt"></i>
                    Changer le code PIN
                </h4>
                <form id="pinForm">
                    @csrf

                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-lock"></i>
                            PIN actuel
                        </label>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-500);"></i>
                            <input type="password"
                                   class="mtn-form-control"
                                   name="current_pin"
                                   placeholder="6 chiffres"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   style="padding-left: 45px;">
                        </div>
                        <small style="color: var(--secondary-500); font-size: 0.85rem; display: block; margin-top: 5px;">
                            Laissez vide si c'est votre première configuration
                        </small>
                    </div>

                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-key"></i>
                            Nouveau PIN
                        </label>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fas fa-key" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-500);"></i>
                            <input type="password"
                                   class="mtn-form-control"
                                   name="new_pin"
                                   placeholder="6 chiffres"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   required
                                   style="padding-left: 45px;">
                        </div>
                        <div class="pin-strength" style="margin-top: 10px;">
                            <div style="font-size: 0.85rem; color: var(--secondary-600); margin-bottom: 5px;">Force du PIN : <span id="pinStrengthText">Faible</span></div>
                            <div class="strength-bar">
                                <div class="strength-fill weak" id="pinStrengthBar"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-check-circle"></i>
                            Confirmer le PIN
                        </label>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fas fa-check-circle" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-500);"></i>
                            <input type="password"
                                   class="mtn-form-control"
                                   name="new_pin_confirmation"
                                   placeholder="6 chiffres"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   required
                                   style="padding-left: 45px;">
                        </div>
                    </div>

                    <div class="mtn-form-group">
                        <div style="background: var(--warning-50); padding: 15px; border-radius: 10px; margin-bottom: 15px; border: 1px solid var(--warning-200);">
                            <small style="color: var(--warning-700); display: block; margin-bottom: 5px; font-weight: 600;">
                                <i class="fas fa-shield-alt"></i>
                                Conseils de sécurité
                            </small>
                            <ul style="margin: 0; padding-left: 20px; color: var(--secondary-600); font-size: 0.85rem;">
                                <li>Utilisez 6 chiffres différents</li>
                                <li>Évitez les séquences simples (123456, 000000)</li>
                                <li>Ne partagez jamais votre PIN</li>
                                <li>Changez votre PIN régulièrement</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mtn-form-group">
                        <button type="submit" class="main-action-btn" style="width: 100%;">
                            <i class="fas fa-save"></i>
                            Enregistrer le nouveau PIN
                        </button>
                    </div>
                </form>
            </div>

            <hr style="margin: 30px 0; border: none; height: 1px; background: var(--secondary-200);">

            <!-- Section : Vérifier le PIN -->
            <div class="mtn-form-group">
                <h4 style="margin-bottom: 20px; color: var(--secondary-800); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i>
                    Vérifier le PIN
                </h4>
                <form id="verifyPinForm">
                    @csrf

                    <div class="mtn-form-group">
                        <label class="mtn-form-label">
                            <i class="fas fa-lock"></i>
                            Entrez votre PIN
                        </label>
                        <div class="input-with-icon" style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-500);"></i>
                            <input type="password"
                                   class="mtn-form-control"
                                   name="pin"
                                   placeholder="6 chiffres"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   required
                                   style="padding-left: 45px;">
                        </div>
                    </div>

                    <div class="mtn-form-group">
                        <button type="submit" class="main-action-btn" style="width: 100%; background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);">
                            <i class="fas fa-check"></i>
                            Vérifier le PIN
                        </button>
                    </div>
                </form>
            </div>

            <hr style="margin: 30px 0; border: none; height: 1px; background: var(--secondary-200);">

            <!-- Section : Informations de sécurité -->
            <div class="mtn-form-group">
                <h4 style="margin-bottom: 15px; color: var(--secondary-800); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-shield-alt"></i>
                    Sécurité du compte
                </h4>

                <div style="background: var(--primary-50); padding: 20px; border-radius: 10px; border: 1px solid var(--primary-200);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; color: var(--secondary-800);">Niveau de sécurité</div>
                            <div style="font-size: 0.9rem; color: var(--secondary-600);">
                                @if($wallet->security_level === 'high')
                                    <span style="color: var(--success-600); font-weight: 600;">Élevé</span>
                                @elseif($wallet->security_level === 'medium')
                                    <span style="color: var(--warning-600); font-weight: 600;">Moyen</span>
                                @else
                                    <span style="color: var(--secondary-600); font-weight: 600;">Bas</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; color: var(--secondary-800);">Dernier changement</div>
                            <div style="font-size: 0.9rem; color: var(--secondary-600);">
                                @if($wallet->pin_changed_at)
                                    {{ $wallet->pin_changed_at->format('d/m/Y H:i') }}
                                @else
                                    Jamais changé
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div style="font-weight: 500; color: var(--secondary-800);">PIN oublié ?</div>
                            <div style="font-size: 0.9rem; color: var(--secondary-600);">
                                Contactez le support pour réinitialiser votre PIN
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section : Déverrouiller pour transaction -->
            <div class="mtn-form-group" style="margin-top: 20px;">
                <div style="background: var(--secondary-50); padding: 15px; border-radius: 10px; text-align: center; border: 1px solid var(--secondary-200);">
                    <div style="font-weight: 600; color: var(--secondary-800); margin-bottom: 10px; font-size: 0.95rem;">
                        <i class="fas fa-unlock-alt"></i> Déverrouillage rapide
                    </div>
                    <div style="font-size: 0.85rem; color: var(--secondary-600); margin-bottom: 15px;">
                        Vérifiez votre PIN pour déverrouiller les transactions
                    </div>
                    <button type="button" class="balance-refresh-btn" onclick="verifyPinForWithdrawal()" style="width: 100%; justify-content: center;">
                        <i class="fas fa-lock-open"></i>
                        Déverrouiller pour retrait
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script spécifique au modal PIN
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on est dans le modal PIN
    const pinModal = document.getElementById('pinSlide');
    if (!pinModal) return;

    // Écouter l'ouverture du modal PIN
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const modal = mutation.target;
                if (modal.classList.contains('show')) {
                    initPinModal();
                }
            }
        });
    });

    observer.observe(pinModal, { attributes: true });
});

function initPinModal() {
    // Validation du nouveau PIN avec indicateur de force
    const newPinInput = document.querySelector('#pinForm input[name="new_pin"]');
    const pinStrengthBar = document.getElementById('pinStrengthBar');
    const pinStrengthText = document.getElementById('pinStrengthText');

    if (newPinInput && pinStrengthBar && pinStrengthText) {
        newPinInput.addEventListener('input', function() {
            const pin = this.value;
            let strength = 'weak';
            let strengthClass = 'weak';
            let width = '33%';

            if (pin.length === 6) {
                // Vérifier la complexité du PIN
                const hasRepeating = /^(\d)\1{5}$/.test(pin);
                const isSequential = /012345|123456|234567|345678|456789|987654|876543|765432|654321|543210/.test(pin);
                const isCommon = ['000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999', '123456', '654321'].includes(pin);

                if (hasRepeating || isSequential || isCommon) {
                    strength = 'Faible';
                    strengthClass = 'weak';
                    width = '33%';
                } else if (/^(?=.*(\d)(?!\1))(?=.*(\d)(?!\1)(?!\2))(?=.*(\d)(?!\1)(?!\2)(?!\3)).{6}$/.test(pin)) {
                    // Au moins 3 chiffres différents
                    strength = 'Fort';
                    strengthClass = 'strong';
                    width = '100%';
                } else {
                    strength = 'Moyen';
                    strengthClass = 'medium';
                    width = '66%';
                }
            } else {
                strength = 'Faible';
                strengthClass = 'weak';
                width = '33%';
            }

            pinStrengthBar.className = 'strength-fill ' + strengthClass;
            pinStrengthBar.style.width = width;
            pinStrengthText.textContent = strength;
        });
    }

    // Validation des champs PIN
    const pinInputs = document.querySelectorAll('#pinSlide input[type="password"]');

    pinInputs.forEach(input => {
        input.addEventListener('input', function() {
            // N'autoriser que les chiffres
            this.value = this.value.replace(/\D/g, '');

            // Limiter à 6 chiffres
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Ajouter un bouton pour afficher/masquer le PIN
        const parent = input.parentElement;
        const existingToggle = parent.querySelector('.toggle-pin-visibility');
        if (!existingToggle) {
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'toggle-pin-visibility';
            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            toggleButton.style.position = 'absolute';
            toggleButton.style.right = '15px';
            toggleButton.style.top = '50%';
            toggleButton.style.transform = 'translateY(-50%)';
            toggleButton.style.background = 'none';
            toggleButton.style.border = 'none';
            toggleButton.style.color = 'var(--secondary-500)';
            toggleButton.style.cursor = 'pointer';
            toggleButton.style.fontSize = '1rem';
            toggleButton.style.zIndex = '10';

            toggleButton.addEventListener('click', function() {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });

            parent.style.position = 'relative';
            parent.appendChild(toggleButton);
        }
    });

    // Soumission du formulaire de changement de PIN
    const pinForm = document.getElementById('pinForm');
    if (pinForm) {
        pinForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const newPin = formData.get('new_pin');
            const confirmPin = formData.get('new_pin_confirmation');
            const currentPin = formData.get('current_pin');

            // Validation
            if (newPin.length !== 6) {
                showToast('Le PIN doit contenir exactement 6 chiffres', 'warning');
                return;
            }

            if (newPin !== confirmPin) {
                showToast('Les PIN ne correspondent pas', 'error');
                return;
            }

            // Vérifier que le PIN n'est pas trop simple
            if (isSimplePin(newPin)) {
                showToast('Choisissez un PIN plus sécurisé. Évitez les séquences ou répétitions simples.', 'warning');
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route("client.wallet.set-pin") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('PIN changé avec succès', 'success');
                    pinForm.reset();

                    // Réinitialiser l'indicateur de force
                    if (pinStrengthBar && pinStrengthText) {
                        pinStrengthBar.className = 'strength-fill weak';
                        pinStrengthBar.style.width = '33%';
                        pinStrengthText.textContent = 'Faible';
                    }

                    setTimeout(() => {
                        closeSlide('pinSlide');
                        if (window.toast) {
                            window.toast.success('Sécurité', 'Votre PIN a été mis à jour avec succès');
                        }
                    }, 1500);
                } else {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    showToast(data.message || 'Erreur lors du changement de PIN', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                showToast('Erreur de connexion', 'error');
            }
        });
    }

    // Soumission du formulaire de vérification de PIN
    const verifyPinForm = document.getElementById('verifyPinForm');
    if (verifyPinForm) {
        verifyPinForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const pin = formData.get('pin');

            if (pin.length !== 6) {
                showToast('Le PIN doit contenir exactement 6 chiffres', 'warning');
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route("client.wallet.verify-pin") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour l'état global de vérification du PIN
                    if (window.pinVerified !== undefined) {
                        window.pinVerified = true;
                        window.pinVerificationExpiry = Date.now() + (30 * 60 * 1000);
                    }

                    showToast('PIN vérifié avec succès', 'success');
                    verifyPinForm.reset();

                    // Stocker le token de session
                    if (data.auth_token) {
                        localStorage.setItem('wallet_pin_token', data.auth_token);
                        localStorage.setItem('wallet_pin_expiry', Date.now() + (30 * 60 * 1000));
                    }

                    // Fermer le modal après un délai
                    setTimeout(() => {
                        closeSlide('pinSlide');
                        if (window.pendingWithdrawAction === 'withdraw') {
                            setTimeout(() => {
                                if (window.openWithdrawModal) {
                                    window.openWithdrawModal();
                                }
                            }, 500);
                            window.pendingWithdrawAction = null;
                        }
                    }, 1000);
                } else {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    showToast(data.message || 'PIN incorrect', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                showToast('Erreur de connexion', 'error');
            }
        });
    }
}

// Fonction utilitaire pour vérifier si un PIN est trop simple
function isSimplePin(pin) {
    const simplePatterns = [
        '000000', '111111', '222222', '333333', '444444',
        '555555', '666666', '777777', '888888', '999999',
        '123456', '654321', '121212', '112233', '111222'
    ];

    // Vérifier les séquences
    if (/^(\d)\1{5}$/.test(pin)) return true; // Tous les mêmes chiffres
    if (/012345|123456|234567|345678|456789/.test(pin)) return true; // Séquence ascendante
    if (/987654|876543|765432|654321|543210/.test(pin)) return true; // Séquence descendante

    return simplePatterns.includes(pin);
}

// Fonction utilitaire pour afficher des toasts
function showToast(message, type = 'info') {
    if (window.toast) {
        switch(type) {
            case 'success':
                window.toast.success(message);
                break;
            case 'error':
                window.toast.error(message);
                break;
            case 'warning':
                window.toast.warning(message);
                break;
            default:
                window.toast.info(message);
        }
    } else {
        // Fallback simple
        console.log(`${type}: ${message}`);
    }
}
</script>

<style>
/* Styles spécifiques au modal PIN */
#pinSlide .input-with-icon {
    position: relative;
}

#pinSlide .toggle-pin-visibility {
    background: none;
    border: none;
    color: var(--secondary-500);
    cursor: pointer;
    font-size: 1rem;
    padding: 5px;
    transition: color 0.3s ease;
    z-index: 10;
}

#pinSlide .toggle-pin-visibility:hover {
    color: var(--primary-500);
}

#pinSlide .security-level {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    margin-left: 10px;
}

#pinSlide .security-level.high {
    background: var(--success-100);
    color: var(--success-600);
}

#pinSlide .security-level.medium {
    background: var(--warning-100);
    color: var(--warning-600);
}

#pinSlide .security-level.low {
    background: var(--error-100);
    color: var(--error-600);
}

#pinSlide .pin-strength {
    margin-top: 10px;
}

#pinSlide .strength-bar {
    height: 5px;
    background: var(--secondary-200);
    border-radius: 3px;
    overflow: hidden;
    margin-top: 5px;
}

#pinSlide .strength-fill {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
    border-radius: 3px;
}

#pinSlide .strength-fill.weak {
    background: var(--error-500);
}

#pinSlide .strength-fill.medium {
    background: var(--warning-500);
}

#pinSlide .strength-fill.strong {
    background: var(--success-500);
}

#pinSlide .slide-body {
    padding-bottom: 2rem;
}

#pinSlide .mtn-form-control {
    padding-right: 45px !important;
}

#pinSlide .mtn-form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--secondary-700);
}
</style>
