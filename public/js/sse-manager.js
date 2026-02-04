// resources/js/sse-manager.js

class SSEManager {
    constructor() {
        this.eventSource = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.reconnectDelay = 1000;
        this.listeners = new Map();
        this.connected = false;
        this.clientId = null;
        this.isAuthenticated = false;
        
        // Vérifier l'authentification
        this.checkAuth();
    }

    async checkAuth() {
        try {
            // Vérifier si l'utilisateur est authentifié via Laravel Sanctum
            const response = await axios.get('/api/user');
            this.isAuthenticated = true;
            return true;
        } catch (error) {
            console.warn('[SSE] Utilisateur non authentifié');
            this.isAuthenticated = false;
            return false;
        }
    }

    connect() {
        // Ne pas connecter si pas authentifié
        if (!this.isAuthenticated) {
            console.log('[SSE] Connexion différée - authentification requise');
            return;
        }

        if (this.eventSource && this.eventSource.readyState !== EventSource.CLOSED) {
            return;
        }

        console.log('[SSE] Tentative de connexion...');
        
        // URL avec token CSRF pour Laravel
        const url = '/api/events';
        
        try {
            this.eventSource = new EventSource(url);
            
            this.eventSource.onopen = (event) => {
                console.log('[SSE] Connexion établie');
                this.connected = true;
                this.reconnectAttempts = 0;
                this.onConnectionChange(true);
            };
            
            this.eventSource.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.handleEvent(data.event || 'message', data.data || data);
                } catch (error) {
                    console.error('[SSE] Erreur de parsing:', error);
                }
            };
            
            // Gestion des événements spécifiques
            this.eventSource.addEventListener('connected', (event) => {
                const data = JSON.parse(event.data);
                this.clientId = data.clientId;
                console.log(`[SSE] Connecté avec ID: ${this.clientId}`);
                
                // Émettre un événement personnalisé
                this.dispatchEvent('connected', data);
                
                // Mettre à jour l'interface
                this.updateUI();
            });
            
            this.eventSource.addEventListener('notification', (event) => {
                const data = JSON.parse(event.data);
                console.log('[SSE] Notification reçue:', data);
                
                // Afficher la notification
                this.showNotification(data);
                
                // Émettre un événement pour les composants Vue/React
                this.dispatchEvent('notification', data);
            });
            
            this.eventSource.addEventListener('ping', (event) => {
                // Maintenir la connexion
                console.debug('[SSE] Ping reçu');
            });
            
            this.eventSource.addEventListener('error', (event) => {
                const data = JSON.parse(event.data);
                console.error('[SSE] Erreur serveur:', data);
                this.dispatchEvent('error', data);
            });
            
            this.eventSource.onerror = (error) => {
                console.error('[SSE] Erreur de connexion:', error);
                this.connected = false;
                this.onConnectionChange(false);
                this.reconnect();
            };
            
        } catch (error) {
            console.error('[SSE] Erreur lors de la connexion:', error);
            this.reconnect();
        }
    }
    
    reconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('[SSE] Nombre maximum de tentatives atteint');
            this.dispatchEvent('max_reconnect_attempts');
            return;
        }
        
        this.reconnectAttempts++;
        const delay = Math.min(this.reconnectDelay * Math.pow(1.5, this.reconnectAttempts), 30000);
        
        console.log(`[SSE] Reconnexion dans ${delay}ms (tentative ${this.reconnectAttempts})`);
        
        setTimeout(() => {
            if (!this.connected) {
                this.disconnect();
                this.connect();
            }
        }, delay);
    }
    
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
            this.connected = false;
            this.onConnectionChange(false);
            console.log('[SSE] Déconnecté');
        }
    }
    
    onConnectionChange(connected) {
        this.dispatchEvent('connection', { connected });
        
        // Mettre à jour l'UI
        const statusElement = document.getElementById('sse-status');
        if (statusElement) {
            statusElement.textContent = connected ? '🟢 Connecté' : '🔴 Déconnecté';
            statusElement.className = connected ? 'text-success' : 'text-danger';
        }
    }
    
    handleEvent(eventName, data) {
        const callbacks = this.listeners.get(eventName) || [];
        callbacks.forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`[SSE] Erreur dans le callback pour ${eventName}:`, error);
            }
        });
    }
    
    addEventListener(eventName, callback) {
        if (!this.listeners.has(eventName)) {
            this.listeners.set(eventName, []);
        }
        this.listeners.get(eventName).push(callback);
    }
    
    removeEventListener(eventName, callback) {
        if (this.listeners.has(eventName)) {
            const callbacks = this.listeners.get(eventName);
            const index = callbacks.indexOf(callback);
            if (index > -1) {
                callbacks.splice(index, 1);
            }
        }
    }
    
    dispatchEvent(eventName, data) {
        // Émettre un événement DOM
        document.dispatchEvent(new CustomEvent(`sse:${eventName}`, {
            detail: data
        }));
        
        // Appeler les callbacks enregistrés
        this.handleEvent(eventName, data);
    }
    
    showNotification(data) {
        // Vérifier les permissions
        if ('Notification' in window) {
            if (Notification.permission === 'granted') {
                this.createBrowserNotification(data);
            } else if (Notification.permission === 'default') {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        this.createBrowserNotification(data);
                    }
                });
            }
        }
        
        // Notification dans l'interface
        this.createInAppNotification(data);
    }
    
    createBrowserNotification(data) {
        new Notification(data.title || 'BHDM', {
            body: data.message,
            icon: '/images/icons/icon-192.png',
            badge: '/images/icons/icon-72.png',
            tag: 'bhdm-notification',
            timestamp: data.timestamp ? new Date(data.timestamp).getTime() : Date.now()
        });
    }
    
    createInAppNotification(data) {
        // Créer un élément de notification
        const notification = document.createElement('div');
        notification.className = 'notification alert alert-info alert-dismissible fade show';
        notification.setAttribute('role', 'alert');
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-bell me-2"></i>
                <div>
                    <strong class="notification-title">${data.title || 'Notification'}</strong>
                    <div class="notification-message">${data.message}</div>
                    <small class="text-muted">${new Date(data.timestamp).toLocaleTimeString()}</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Ajouter au conteneur
        const container = document.getElementById('notifications-container');
        if (container) {
            container.prepend(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Auto-suppression après 10 secondes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 10000);
        }
    }
    
    updateUI() {
        // Mettre à jour les indicateurs d'état
        const indicators = document.querySelectorAll('.sse-indicator');
        indicators.forEach(indicator => {
            if (this.connected) {
                indicator.classList.remove('offline');
                indicator.classList.add('online');
                indicator.title = 'Connecté en temps réel';
            } else {
                indicator.classList.remove('online');
                indicator.classList.add('offline');
                indicator.title = 'Hors ligne - Reconnexion...';
            }
        });
    }
    
    // Méthode utilitaire pour envoyer des notifications
    async sendNotification(userId, type, title, message) {
        try {
            const response = await axios.post('/api/notifications', {
                user_id: userId,
                type: type,
                title: title,
                message: message
            });
            
            return response.data;
        } catch (error) {
            console.error('[SSE] Erreur d\'envoi de notification:', error);
            throw error;
        }
    }
}

// Exporter pour utilisation globale
window.SSEManager = SSEManager;

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    // Attendre que Laravel/Vue soit chargé
    setTimeout(() => {
        window.sseManager = new SSEManager();
        
        // Écouter les changements d'authentification
        document.addEventListener('auth:changed', () => {
            window.sseManager.checkAuth().then(authenticated => {
                if (authenticated && !window.sseManager.connected) {
                    window.sseManager.connect();
                }
            });
        });
        
        // Connexion automatique si déjà authentifié
        window.sseManager.checkAuth().then(authenticated => {
            if (authenticated) {
                window.sseManager.connect();
            }
        });
        
        // Gestion de la déconnexion réseau
        window.addEventListener('offline', () => {
            console.log('[SSE] Hors ligne');
            window.sseManager.disconnect();
        });
        
        window.addEventListener('online', () => {
            console.log('[SSE] De nouveau en ligne');
            setTimeout(() => {
                window.sseManager.checkAuth().then(authenticated => {
                    if (authenticated) {
                        window.sseManager.connect();
                    }
                });
            }, 1000);
        });
    }, 1000);
});

// Export pour les modules ES6
export default SSEManager;