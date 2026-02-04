@extends('layouts.client-pwa')

@section('title', 'Vérification d\'email - BHDM')

@section('content')
<div class="pwa-verification-container">
    <!-- En-tête PWA -->
    <div class="pwa-verification-header">
        <div class="header-back">
            <a href="{{ route('home') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="header-title">
            <h1>Vérification d'email</h1>
        </div>
        <div class="header-actions">
            <button class="refresh-btn" id="refreshVerification">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="pwa-verification-content">
        <!-- Animation de vérification -->
        <div class="verification-animation">
            <div class="verification-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="verification-pulse"></div>
        </div>

        <!-- Titre et message -->
        <div class="verification-message">
            <h2>Vérification requise</h2>
            <p>Pour accéder à votre compte, veuillez vérifier votre adresse email.</p>
            
            <div class="email-display">
                <i class="fas fa-envelope"></i>
                <span>{{ Auth::user()->email }}</span>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('message'))
        <div class="pwa-alert success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        @if (session('warning'))
        <div class="pwa-alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('warning') }}</span>
        </div>
        @endif

        <!-- Étapes -->
        <div class="verification-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-info">
                    <h4>Consultez votre boîte email</h4>
                    <p>Ouvrez votre messagerie à l'adresse indiquée ci-dessus</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-info">
                    <h4>Ouvrez l'email BHDM</h4>
                    <p>Cherchez l'email avec l'objet "Vérification de votre compte"</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-info">
                    <h4>Cliquez sur le lien</h4>
                    <p>Cliquez sur le bouton "Vérifier mon email" dans l'email</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-info">
                    <h4>Retournez ici</h4>
                    <p>Revenez sur cette page pour accéder à votre compte</p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="verification-actions">
            <form method="POST" action="{{ route('verification.send') }}" class="w-100">
                @csrf
                <button type="submit" class="pwa-btn primary">
                    <i class="fas fa-paper-plane"></i>
                    <span>Renvoyer l'email de vérification</span>
                </button>
            </form>

            <button id="checkVerificationBtn" class="pwa-btn secondary">
                <i class="fas fa-sync-alt"></i>
                <span>Vérifier maintenant</span>
            </button>

            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="pwa-btn outline">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

        <!-- FAQ -->
        <div class="verification-faq">
            <h3><i class="fas fa-question-circle"></i> Questions fréquentes</h3>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Je n'ai pas reçu l'email</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <ul>
                        <li>Vérifiez votre dossier spam/courriers indésirables</li>
                        <li>Assurez-vous que l'adresse est correcte</li>
                        <li>Attendez quelques minutes</li>
                        <li>Contactez le support si nécessaire</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Changer d'adresse email</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Contactez notre support :</p>
                    <a href="mailto:support@bhdm.com" class="support-link">
                        <i class="fas fa-envelope"></i>
                        support@bhdm.com
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Message de succès (caché) -->
    <div id="verificationSuccess" class="verification-success">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="success-message">
            <h3>Email vérifié !</h3>
            <p>Redirection vers votre tableau de bord...</p>
        </div>
    </div>
</div>

<style>
/* Styles PWA pour la vérification */
.pwa-verification-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
    padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
}

.pwa-verification-header {
    background: var(--client-primary);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-back .back-btn {
    color: white;
    font-size: 1.2rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.header-back .back-btn:hover {
    background: rgba(255,255,255,0.1);
}

.header-title h1 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    color: white;
}

.refresh-btn {
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.refresh-btn:hover {
    background: rgba(255,255,255,0.2);
    transform: rotate(90deg);
}

.pwa-verification-content {
    padding: 20px;
    max-width: 500px;
    margin: 0 auto;
}

.verification-animation {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 20px auto 30px;
}

.verification-icon {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--client-primary) 0%, var(--client-accent) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    z-index: 2;
    position: relative;
    animation: bounce 2s infinite;
}

.verification-pulse {
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: rgba(27, 90, 141, 0.2);
    border-radius: 50%;
    z-index: 1;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.05); opacity: 0.3; }
    100% { transform: scale(0.95); opacity: 0.7; }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.verification-message {
    text-align: center;
    margin-bottom: 30px;
}

.verification-message h2 {
    color: var(--client-dark);
    margin-bottom: 10px;
    font-size: 1.5rem;
}

.verification-message p {
    color: #666;
    margin-bottom: 20px;
    font-size: 1rem;
}

.email-display {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: white;
    border-radius: 25px;
    border: 2px solid #e9ecef;
    color: var(--client-dark);
    font-weight: 500;
    font-size: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.email-display i {
    color: var(--client-primary);
}

.pwa-alert {
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideInDown 0.3s ease;
}

.pwa-alert.success {
    background: rgba(40, 167, 69, 0.1);
    color: var(--client-success);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.pwa-alert.warning {
    background: rgba(255, 193, 7, 0.1);
    color: var(--client-warning);
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.verification-steps {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.step {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.step.active {
    border-color: var(--client-primary);
    background: rgba(27, 90, 141, 0.05);
}

.step-number {
    width: 40px;
    height: 40px;
    background: #e9ecef;
    color: #666;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.step.active .step-number {
    background: var(--client-primary);
    color: white;
    animation: pulse 2s infinite;
}

.step-info h4 {
    margin: 0 0 5px 0;
    color: var(--client-dark);
    font-size: 1rem;
}

.step-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.verification-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}

.pwa-btn {
    padding: 16px;
    border-radius: 12px;
    border: none;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    width: 100%;
}

.pwa-btn.primary {
    background: linear-gradient(135deg, var(--client-primary) 0%, var(--client-accent) 100%);
    color: white;
}

.pwa-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(27, 90, 141, 0.2);
}

.pwa-btn.secondary {
    background: #f8f9fa;
    color: var(--client-dark);
    border: 2px solid #e9ecef;
}

.pwa-btn.secondary:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.pwa-btn.outline {
    background: transparent;
    color: var(--client-danger);
    border: 2px solid var(--client-danger);
}

.pwa-btn.outline:hover {
    background: rgba(220, 53, 69, 0.1);
    transform: translateY(-2px);
}

.verification-faq {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.verification-faq h3 {
    color: var(--client-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.verification-faq h3 i {
    color: var(--client-primary);
}

.faq-item {
    margin-bottom: 10px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.faq-question {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    background: #f8f9fa;
    font-weight: 500;
    color: var(--client-dark);
}

.faq-question i {
    transition: transform 0.3s ease;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 15px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.faq-item.active .faq-answer {
    padding: 15px;
    max-height: 500px;
}

.faq-answer ul {
    padding-left: 20px;
    margin: 10px 0;
}

.faq-answer li {
    margin-bottom: 8px;
    color: #666;
}

.support-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--client-primary);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 15px;
    background: rgba(27, 90, 141, 0.1);
    border-radius: 8px;
    margin-top: 10px;
}

.support-link:hover {
    background: rgba(27, 90, 141, 0.2);
}

.verification-success {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.95);
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    z-index: 1000;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--client-success) 0%, #5cd85c 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin-bottom: 20px;
    animation: bounce 1s ease;
}

.success-message {
    text-align: center;
}

.success-message h3 {
    color: var(--client-success);
    margin-bottom: 10px;
    font-size: 1.8rem;
}

.success-message p {
    color: #666;
    font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 576px) {
    .pwa-verification-content {
        padding: 15px;
    }
    
    .verification-animation {
        width: 100px;
        height: 100px;
    }
    
    .verification-icon {
        font-size: 2.5rem;
    }
    
    .email-display {
        font-size: 0.9rem;
        padding: 10px 15px;
    }
    
    .pwa-btn {
        padding: 14px;
        font-size: 0.95rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let checkInterval = null;
    const refreshBtn = document.getElementById('refreshVerification');
    const checkBtn = document.getElementById('checkVerificationBtn');
    const successMessage = document.getElementById('verificationSuccess');
    const steps = document.querySelectorAll('.step');
    
    // Fonction pour vérifier le statut de vérification
    function checkVerificationStatus() {
        axios.get('/api/user/verification-status')
            .then(response => {
                if (response.data.verified) {
                    onVerificationSuccess();
                } else {
                    updateSteps();
                }
            })
            .catch(error => {
                console.error('Erreur de vérification:', error);
            });
    }
    
    // Mise à jour des étapes
    function updateSteps() {
        let activeStep = Math.floor(Math.random() * 4); // Pour l'exemple
        steps.forEach((step, index) => {
            if (index <= activeStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }
    
    // Succès de la vérification
    function onVerificationSuccess() {
        // Afficher le message de succès
        successMessage.style.display = 'flex';
        
        // Arrêter la vérification automatique
        if (checkInterval) {
            clearInterval(checkInterval);
        }
        
        // Animer toutes les étapes
        steps.forEach(step => {
            step.classList.add('active');
            step.querySelector('.step-number').style.background = 'var(--client-success)';
        });
        
        // Rediriger après 3 secondes
        setTimeout(() => {
            window.location.href = '{{ route("client.dashboard") }}';
        }, 3000);
    }
    
    // Démarrer la vérification automatique
    function startAutoCheck() {
        if (!checkInterval) {
            checkInterval = setInterval(checkVerificationStatus, 10000); // Toutes les 10 secondes
        }
        // Premier check immédiat
        checkVerificationStatus();
    }
    
    // Bouton de rafraîchissement
    refreshBtn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        icon.style.animation = 'rotateIcon 1s ease';
        
        checkVerificationStatus();
        
        setTimeout(() => {
            icon.style.animation = '';
        }, 1000);
    });
    
    // Bouton de vérification manuelle
    checkBtn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        icon.className = 'fas fa-spinner fa-spin';
        
        checkVerificationStatus();
        
        setTimeout(() => {
            icon.className = 'fas fa-sync-alt';
        }, 2000);
    });
    
    // Gestion du FAQ
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', function() {
            const item = this.parentElement;
            item.classList.toggle('active');
        });
    });
    
    // Démarrer la vérification automatique
    startAutoCheck();
    
    // Nettoyage
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });
    
    // Animation pour l'icône de rotation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rotateIcon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes slideInDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection