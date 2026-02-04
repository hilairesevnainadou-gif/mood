// resources/js/email-verification.js
class EmailVerification {
    constructor() {
        this.checkInterval = null;
        this.maxChecks = 60; // 10 minutes à 10 secondes d'intervalle
        this.checkCount = 0;
        this.init();
    }

    init() {
        if (!this.shouldRun()) return;
        
        this.setupUI();
        this.startChecking();
        this.bindEvents();
    }

    shouldRun() {
        // Vérifier si l'utilisateur est connecté et n'a pas vérifié son email
        return window.authUser && !window.authUser.email_verified_at;
    }

    setupUI() {
        // Créer une notification
        this.notification = document.createElement('div');
        this.notification.className = 'email-verification-status';
        this.notification.innerHTML = `
            <div class="status-content">
                <i class="fas fa-envelope status-icon"></i>
                <div class="status-text">
                    <strong>En attente de vérification d'email</strong>
                    <small>Vérification automatique en cours...</small>
                </div>
                <button class="status-refresh-btn">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        `;
        
        // Ajouter le CSS
        this.addStyles();
        
        // Ajouter à la page
        document.body.appendChild(this.notification);
    }

    addStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .email-verification-status {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: linear-gradient(135deg, #ffc107 0%, #ffd860 100%);
                border-radius: 10px;
                padding: 15px;
                box-shadow: 0 5px 20px rgba(255, 193, 7, 0.3);
                z-index: 9999;
                max-width: 350px;
                animation: slideUp 0.5s ease;
            }
            
            .status-content {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .status-icon {
                font-size: 1.5rem;
                color: #333;
            }
            
            .status-text {
                flex: 1;
                color: #333;
            }
            
            .status-text strong {
                display: block;
                font-size: 0.95rem;
            }
            
            .status-text small {
                font-size: 0.85rem;
                opacity: 0.8;
            }
            
            .status-refresh-btn {
                background: rgba(255, 255, 255, 0.3);
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .status-refresh-btn:hover {
                background: rgba(255, 255, 255, 0.5);
                transform: rotate(180deg);
            }
            
            @keyframes slideUp {
                from { transform: translateY(100px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            
            .status-verified {
                background: linear-gradient(135deg, #28a745 0%, #5cd85c 100%);
            }
        `;
        document.head.appendChild(style);
    }

    startChecking() {
        this.checkInterval = setInterval(() => {
            this.checkVerification();
            this.checkCount++;
            
            if (this.checkCount >= this.maxChecks) {
                this.stopChecking();
                this.updateStatus('max_checks_reached');
            }
        }, 10000); // Toutes les 10 secondes
        
        // Premier check immédiat
        setTimeout(() => this.checkVerification(), 1000);
    }

    async checkVerification() {
        try {
            const response = await axios.get('/api/user/verification-status');
            
            if (response.data.verified) {
                this.onVerificationSuccess();
            }
        } catch (error) {
            console.error('Email verification check failed:', error);
        }
    }

    onVerificationSuccess() {
        this.stopChecking();
        this.updateStatus('verified');
        
        // Afficher message de succès
        this.showSuccessMessage();
        
        // Rediriger après 3 secondes
        setTimeout(() => {
            const redirectUrl = sessionStorage.getItem('verification_redirect') || '/client/dashboard';
            window.location.href = redirectUrl;
        }, 3000);
    }

    updateStatus(status) {
        switch(status) {
            case 'verified':
                this.notification.className = 'email-verification-status status-verified';
                this.notification.innerHTML = `
                    <div class="status-content">
                        <i class="fas fa-check-circle status-icon"></i>
                        <div class="status-text">
                            <strong>Email vérifié !</strong>
                            <small>Redirection en cours...</small>
                        </div>
                    </div>
                `;
                break;
            case 'max_checks_reached':
                this.notification.innerHTML = `
                    <div class="status-content">
                        <i class="fas fa-clock status-icon"></i>
                        <div class="status-text">
                            <strong>Vérification expirée</strong>
                            <small>Veuillez recharger la page</small>
                        </div>
                    </div>
                `;
                break;
        }
    }

    showSuccessMessage() {
        const message = document.createElement('div');
        message.className = 'verification-success-message';
        message.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>Email vérifié avec succès !</span>
        `;
        document.body.appendChild(message);
        
        setTimeout(() => message.remove(), 3000);
    }

    stopChecking() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.status-refresh-btn')) {
                this.checkVerification();
            }
        });
        
        // Nettoyer à la déconnexion
        window.addEventListener('beforeunload', () => {
            this.stopChecking();
        });
    }
}

// Initialiser lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new EmailVerification();
});