import './bootstrap';
import './sse-manager';

const express = require('express');
const cors = require('cors');
const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// Stockage des connexions SSE
const clients = new Map();
let clientId = 0;

// Route SSE pour les événements en temps réel
app.get('/api/events', (req, res) => {
  // Définir les headers SSE
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.setHeader('X-Accel-Buffering', 'no'); // Important pour Nginx
  res.setHeader('Access-Control-Allow-Origin', '*');
  
  // Forcer le flush des headers
  res.flushHeaders();
  
  // Générer un ID unique pour ce client
  const id = ++clientId;
  clients.set(id, res);
  
  console.log(`[SSE] Nouveau client connecté: ${id}`);
  
  // Envoyer un message de connexion
  const connectMessage = {
    event: 'connected',
    data: { 
      clientId: id, 
      timestamp: new Date().toISOString(),
      message: 'Connexion SSE établie'
    }
  };
  res.write(`data: ${JSON.stringify(connectMessage)}\n\n`);
  
  // Envoyer un ping périodique pour garder la connexion active
  const pingInterval = setInterval(() => {
    const pingMessage = {
      event: 'ping',
      data: { timestamp: new Date().toISOString() }
    };
    
    try {
      res.write(`data: ${JSON.stringify(pingMessage)}\n\n`);
    } catch (error) {
      clearInterval(pingInterval);
      clients.delete(id);
    }
  }, 30000); // Ping toutes les 30 secondes
  
  // Gérer la déconnexion du client
  req.on('close', () => {
    console.log(`[SSE] Client déconnecté: ${id}`);
    clearInterval(pingInterval);
    clients.delete(id);
    res.end();
  });
  
  // Gérer les erreurs
  req.on('error', (err) => {
    console.error(`[SSE] Erreur avec client ${id}:`, err);
    clearInterval(pingInterval);
    clients.delete(id);
    res.end();
  });
});

// Fonction utilitaire pour envoyer des événements à tous les clients
function broadcastEvent(event, data) {
  const message = {
    event,
    data: {
      ...data,
      timestamp: new Date().toISOString()
    }
  };
  
  const eventString = `event: ${event}\ndata: ${JSON.stringify(message)}\n\n`;
  
  clients.forEach((client, clientId) => {
    try {
      client.write(eventString);
    } catch (error) {
      console.error(`[SSE] Erreur d'envoi au client ${clientId}:`, error);
      clients.delete(clientId);
    }
  });
}

// Exemple de route pour déclencher des événements
app.post('/api/events/notify', (req, res) => {
  const { type, message, userId } = req.body;
  
  const eventData = {
    type: type || 'notification',
    message: message || 'Nouvelle notification',
    userId,
    broadcast: true
  };
  
  // Diffuser à tous les clients
  broadcastEvent('notification', eventData);
  
  res.json({ 
    success: true, 
    message: 'Notification envoyée',
    recipients: clients.size
  });
});

// Envoyer un événement à un client spécifique
app.post('/api/events/send/:clientId', (req, res) => {
  const { clientId } = req.params;
  const { event, data } = req.body;
  
  const client = clients.get(parseInt(clientId));
  
  if (!client) {
    return res.status(404).json({ 
      error: 'Client non connecté' 
    });
  }
  
  try {
    const message = {
      event: event || 'message',
      data: {
        ...data,
        timestamp: new Date().toISOString(),
        targeted: true
      }
    };
    
    client.write(`event: ${message.event}\ndata: ${JSON.stringify(message)}\n\n`);
    
    res.json({ 
      success: true, 
      message: 'Événement envoyé'
    });
  } catch (error) {
    res.status(500).json({ 
      error: 'Échec de l\'envoi',
      details: error.message 
    });
  }
});

// Route pour vérifier les connexions actives
app.get('/api/events/connections', (req, res) => {
  const connections = Array.from(clients.keys()).map(id => ({
    clientId: id,
    connectedAt: new Date().toISOString()
  }));
  
  res.json({
    totalConnections: clients.size,
    connections
  });
});

// Exemple d'événements système
setInterval(() => {
  if (clients.size > 0) {
    const systemMessage = {
      event: 'stats',
      data: {
        activeConnections: clients.size,
        memoryUsage: process.memoryUsage(),
        uptime: process.uptime()
      }
    };
    
    broadcastEvent('stats', systemMessage);
  }
}, 60000); // Toutes les minutes

// Autres routes de votre API...
app.get('/api/health', (req, res) => {
  res.json({
    status: 'healthy',
    timestamp: new Date().toISOString(),
    sseConnections: clients.size
  });
});

// Middleware de gestion d'erreurs
app.use((err, req, res, next) => {
  console.error('Erreur serveur:', err);
  
  // Envoyer une notification d'erreur via SSE
  broadcastEvent('error', {
    type: 'server_error',
    message: err.message,
    route: req.path
  });
  
  res.status(500).json({ 
    error: 'Erreur interne du serveur',
    message: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Serveur démarré sur le port ${PORT}`);
  console.log(`SSE disponible sur /api/events`);
});

// Gestion propre de l'arrêt
process.on('SIGTERM', () => {
  console.log('[SSE] Fermeture des connexions...');
  
  // Informer tous les clients de la fermeture
  broadcastEvent('server_shutdown', {
    message: 'Le serveur va s\'arrêter. Reconnexion automatique dans 30 secondes.',
    reconnectIn: 30000
  });
  
  // Fermer toutes les connexions après un délai
  setTimeout(() => {
    clients.forEach((client, id) => {
      client.end();
    });
    clients.clear();
    process.exit(0);
  }, 5000);
});