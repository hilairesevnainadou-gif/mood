<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors ligne - BHDM</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1b5a8d 0%, #0a1f44 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        
        .offline-container {
            max-width: 500px;
            width: 100%;
        }
        
        .offline-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #ffc107;
            animation: pulse 2s infinite;
        }
        
        h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .offline-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .btn {
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: white;
            color: #1b5a8d;
        }
        
        .btn-primary:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .network-status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ffc107;
            animation: pulse 1s infinite;
        }
        
        .cached-content {
            margin-top: 40px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            text-align: left;
        }
        
        .cached-content h3 {
            margin-top: 0;
            font-size: 1.2rem;
        }
        
        .cached-content ul {
            list-style: none;
            padding: 0;
        }
        
        .cached-content li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .cached-content li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <i class="fas fa-wifi-slash"></i>
        </div>
        
        <h1>Vous êtes hors ligne</h1>
        
        <p>
            La connexion Internet semble interrompue. 
            Certaines fonctionnalités nécessitent une connexion active.
            Voici ce que vous pouvez faire :
        </p>
        
        <div class="offline-actions">
            <button class="btn btn-primary" id="retryBtn">
                <i class="fas fa-sync-alt"></i>
                Réessayer la connexion
            </button>
            
            <button class="btn btn-secondary" id="cachedBtn">
                <i class="fas fa-database"></i>
                Voir le contenu disponible
            </button>
            
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Retour à l'accueil
            </a>
        </div>
        
        <div class="network-status" id="networkStatus">
            <div class="status-indicator"></div>
            <span>En attente de connexion...</span>
        </div>
        
        <div class="cached-content" id="cachedContent" style="display: none;">
            <h3><i class="fas fa-download"></i> Contenu disponible hors ligne</h3>
            <ul id="cachedList">
                <!-- Rempli dynamiquement -->
            </ul>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const retryBtn = document.getElementById('retryBtn');
            const cachedBtn = document.getElementById('cachedBtn');
            const networkStatus = document.getElementById('networkStatus');
            const cachedContent = document.getElementById('cachedContent');
            const cachedList = document.getElementById('cachedList');
            
            // Vérifier périodiquement la connexion
            function checkConnection() {
                fetch('/', { method: 'HEAD', cache: 'no-cache' })
                    .then(() => {
                        // Connexion rétablie
                        networkStatus.innerHTML = `
                            <div style="background: #4CAF50;" class="status-indicator"></div>
                            <span>Connexion rétablie ! Redirection...</span>
                        `;
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    })
                    .catch(() => {
                        // Toujours hors ligne
                        setTimeout(checkConnection, 5000);
                    });
            }
            
            // Bouton de réessai
            retryBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-spinner fa-spin';
                
                checkConnection();
                
                setTimeout(() => {
                    icon.className = 'fas fa-sync-alt';
                }, 2000);
            });
            
            // Afficher le contenu en cache
            cachedBtn.addEventListener('click', function() {
                if (cachedContent.style.display === 'none') {
                    cachedContent.style.display = 'block';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer le contenu';
                    
                    // Charger la liste des URLs en cache
                    if ('caches' in window) {
                        caches.keys().then(cacheNames => {
                            let allUrls = [];
                            
                            Promise.all(
                                cacheNames.map(cacheName =>
                                    caches.open(cacheName).then(cache =>
                                        cache.keys().then(requests =>
                                            requests.map(request => request.url)
                                        )
                                    )
                                )
                            ).then(results => {
                                allUrls = results.flat();
                                
                                // Filtrer et afficher les URLs
                                const uniqueUrls = [...new Set(allUrls)]
                                    .filter(url => url.includes(window.location.origin))
                                    .map(url => url.replace(window.location.origin, ''));
                                
                                cachedList.innerHTML = uniqueUrls
                                    .map(url => `<li><i class="fas fa-check-circle" style="color: #4CAF50;"></i> ${url}</li>`)
                                    .join('');
                                
                                if (uniqueUrls.length === 0) {
                                    cachedList.innerHTML = '<li>Aucun contenu disponible hors ligne</li>';
                                }
                            });
                        });
                    }
                } else {
                    cachedContent.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-database"></i> Voir le contenu disponible';
                }
            });
            
            // Démarrer la vérification de connexion
            checkConnection();
            
            // Écouter les événements de réseau
            window.addEventListener('online', () => {
                networkStatus.innerHTML = `
                    <div style="background: #4CAF50;" class="status-indicator"></div>
                    <span>Connexion rétablie ! Redirection...</span>
                `;
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
            
            window.addEventListener('offline', () => {
                networkStatus.innerHTML = `
                    <div class="status-indicator"></div>
                    <span>En attente de connexion...</span>
                `;
            });
            
            // Vérifier l'état initial
            if (navigator.onLine) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>