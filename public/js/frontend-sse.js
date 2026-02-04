// frontend-sse.js
class EventSourceManager {
  constructor() {
    this.eventSource = null;
    this.reconnectAttempts = 0;
    this.maxReconnectAttempts = 10;
    this.reconnectDelay = 1000;
    this.listeners = new Map();
    this.connected = false;
    this.clientId = null;
  }

  connect() {
    if (this.eventSource && this.eventSource.readyState !== EventSource.CLOSED) {
      return;
    }

    console.log('[SSE] Tentative de connexion...');
    
    try {
      this.eventSource = new EventSource('/api/events');
      
      this.eventSource.onopen = (event) => {
        console.log('[SSE] Connexion établie');
        this.connected = true;
        this.reconnectAttempts = 0;
        this.onConnectionChange(true);
      };
      
      this.eventSource.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data);
          this.handleEvent(data.event, data.data);
        } catch (error) {
          console.error('[SSE] Erreur de parsing:', error);
        }
      };
      
      // Gestion des événements spécifiques
      this.eventSource.addEventListener('connected', (event) => {
        const data = JSON.parse(event.data);
        this.clientId = data.data.clientId;
        console.log(`[SSE] Connecté avec ID: ${this.clientId}`);
        
        // Émettre un événement personnalisé
        document.dispatchEvent(new CustomEvent('sse:connected', { 
          detail: data 
        }));
      });
      
      this.eventSource.addEventListener('notification', (event) => {
        const data = JSON.parse(event.data);
        console.log('[SSE] Notification reçue:', data);
        
        // Afficher une notification UI
        this.showNotification(data);
        
        // Émettre un événement personnalisé
        document.dispatchEvent(new CustomEvent('sse:notification', { 
          detail: data 
        }));
      });
      
      this.eventSource.addEventListener('ping', (event) => {
        // Répondre au ping pour maintenir la connexion
        console.log('[SSE] Ping reçu');
      });
      
      this.eventSource.addEventListener('stats', (event) => {
        const data = JSON.parse(event.data);
        console.log('[SSE] Stats serveur:', data);
      });
      
      this.eventSource.addEventListener('error', (event) => {
        console.error('[SSE] Erreur reçue:', event.data);
        
        document.dispatchEvent(new CustomEvent('sse:error', { 
          detail: JSON.parse(event.data) 
        }));
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
    document.dispatchEvent(new CustomEvent('sse:connection', {
      detail: { connected }
    }));
    
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
  
  showNotification(data) {
    // Utiliser l'API Notifications du navigateur si disponible
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('BHDM - Notification', {
        body: data.message || 'Nouvelle notification',
        icon: '/images/icons/icon-192.png',
        tag: 'bhdm-notification'
      });
    }
    
    // Ou afficher une notification dans l'interface
    const notificationContainer = document.getElementById('notifications');
    if (notificationContainer) {
      const notification = document.createElement('div');
      notification.className = 'alert alert-info alert-dismissible fade show';
      notification.innerHTML = `
        <strong>${data.type || 'Notification'}</strong>
        <p>${data.message}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      notificationContainer.prepend(notification);
      
      // Auto-remove after 10 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 10000);
    }
  }
  
  sendNotification(type, message) {
    return fetch('/api/events/notify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ type, message })
    });
  }
}

// Utilisation
const sseManager = new EventSourceManager();

// Initialiser la connexion lorsque la page est chargée
document.addEventListener('DOMContentLoaded', () => {
  // Demander la permission pour les notifications
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
  
  // Se connecter aux événements
  sseManager.connect();
  
  // Écouter les événements de déconnexion réseau
  window.addEventListener('offline', () => {
    console.log('[SSE] Hors ligne, déconnexion...');
    sseManager.disconnect();
  });
  
  window.addEventListener('online', () => {
    console.log('[SSE] De nouveau en ligne, reconnexion...');
    setTimeout(() => sseManager.connect(), 1000);
  });
});

// Exposer globalement si nécessaire
window.SSEManager = sseManager;