<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SSEController extends Controller
{
    private $clients = [];
    private $lastId = 0;
    private $maxConnections = 100;

    /**
     * Établir une connexion SSE
     *
     * @return \Illuminate\Http\Response
     */
    public function stream(Request $request)
    {
        // Vérifier l'authentification
        if (!Auth::check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user = Auth::user();
        
        return response()->stream(function () use ($user, $request) {
            // Configuration des headers SSE
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');
            
            // Désactiver la limite d'exécution
            set_time_limit(0);
            
            // Désactiver la mise en tampon de sortie
            if (ob_get_level() > 0) {
                ob_end_flush();
            }
            
            // Générer un ID client unique
            $clientId = 'client_' . ++$this->lastId . '_' . $user->id;
            
            // Limiter le nombre de connexions
            if (count($this->clients) >= $this->maxConnections) {
                $this->sendEvent([
                    'event' => 'error',
                    'data' => [
                        'message' => 'Nombre maximum de connexions atteint',
                        'code' => 'MAX_CONNECTIONS'
                    ]
                ]);
                return;
            }
            
            // Enregistrer le client
            $this->clients[$clientId] = [
                'id' => $clientId,
                'user_id' => $user->id,
                'connected_at' => now(),
                'last_activity' => time(),
                'ip' => $request->ip()
            ];
            
            Log::info('SSE Client connecté', [
                'client_id' => $clientId,
                'user_id' => $user->id,
                'total_clients' => count($this->clients)
            ]);
            
            // Message de bienvenue
            $this->sendEvent([
                'event' => 'connected',
                'data' => [
                    'clientId' => $clientId,
                    'userId' => $user->id,
                    'timestamp' => now()->toISOString(),
                    'message' => 'Connexion SSE établie'
                ]
            ]);
            
            // Envoyer les notifications non lues
            $this->sendUnreadNotifications($user->id, $clientId);
            
            // Boucle principale de maintien de la connexion
            while (true) {
                // Vérifier si le client est toujours connecté
                if (connection_aborted()) {
                    $this->removeClient($clientId);
                    break;
                }
                
                // Vérifier l'inactivité (timeout de 5 minutes)
                if (time() - $this->clients[$clientId]['last_activity'] > 300) {
                    $this->sendEvent([
                        'event' => 'timeout',
                        'data' => ['message' => 'Connexion expirée']
                    ]);
                    $this->removeClient($clientId);
                    break;
                }
                
                // Ping toutes les 30 secondes
                if (time() - $this->clients[$clientId]['last_activity'] >= 30) {
                    $this->clients[$clientId]['last_activity'] = time();
                    
                    $this->sendEvent([
                        'event' => 'ping',
                        'data' => [
                            'timestamp' => now()->toISOString(),
                            'clientId' => $clientId
                        ]
                    ]);
                }
                
                // Vérifier les nouvelles notifications
                $this->checkNewNotifications($user->id, $clientId);
                
                // Vérifier les événements système
                $this->checkSystemEvents($clientId);
                
                // Pause pour éviter une surcharge CPU
                usleep(1000000); // 1 seconde
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Envoyer un événement au client
     */
    private function sendEvent(array $event): void
    {
        $data = json_encode($event['data']);
        
        echo "event: {$event['event']}\n";
        echo "data: {$data}\n\n";
        
        flush();
        
        // Log en développement
        if (config('app.debug')) {
            Log::debug('SSE Event envoyé', [
                'event' => $event['event'],
                'data_size' => strlen($data)
            ]);
        }
    }

    /**
     * Envoyer les notifications non lues
     */
    private function sendUnreadNotifications(int $userId, string $clientId): void
    {
        try {
            $notifications = Notification::where('user_id', $userId)
                ->where('read', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($notifications as $notification) {
                $this->sendEvent([
                    'event' => 'notification',
                    'data' => [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'created_at' => $notification->created_at->toISOString(),
                        'is_unread' => true
                    ]
                ]);
                
                // Marquer comme lu
                $notification->update(['read' => true]);
            }
            
            if ($notifications->count() > 0) {
                Log::info('Notifications envoyées', [
                    'client_id' => $clientId,
                    'count' => $notifications->count()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'error' => $e->getMessage(),
                'client_id' => $clientId
            ]);
        }
    }

    /**
     * Vérifier les nouvelles notifications
     */
    private function checkNewNotifications(int $userId, string $clientId): void
    {
        static $lastCheck = null;
        
        if ($lastCheck === null || time() - $lastCheck >= 10) { // Vérifier toutes les 10 secondes
            $lastCheck = time();
            
            try {
                $newNotifications = Notification::where('user_id', $userId)
                    ->where('read', false)
                    ->when($lastCheck, function ($query) use ($lastCheck) {
                        return $query->where('created_at', '>', now()->subSeconds($lastCheck));
                    })
                    ->get();
                
                foreach ($newNotifications as $notification) {
                    $this->sendEvent([
                        'event' => 'notification',
                        'data' => [
                            'id' => $notification->id,
                            'type' => $notification->type,
                            'title' => $notification->title,
                            'message' => $notification->message,
                            'created_at' => $notification->created_at->toISOString(),
                            'is_new' => true
                        ]
                    ]);
                    
                    // Marquer comme lu
                    $notification->update(['read' => true]);
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors de la vérification des notifications', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Vérifier les événements système
     */
    private function checkSystemEvents(string $clientId): void
    {
        static $lastStats = null;
        
        // Envoyer les statistiques toutes les minutes
        if ($lastStats === null || time() - $lastStats >= 60) {
            $lastStats = time();
            
            $this->sendEvent([
                'event' => 'stats',
                'data' => [
                    'active_connections' => count($this->clients),
                    'server_time' => now()->toISOString(),
                    'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB'
                ]
            ]);
        }
    }

    /**
     * Supprimer un client
     */
    private function removeClient(string $clientId): void
    {
        if (isset($this->clients[$clientId])) {
            $userData = $this->clients[$clientId];
            unset($this->clients[$clientId]);
            
            Log::info('SSE Client déconnecté', [
                'client_id' => $clientId,
                'user_id' => $userData['user_id'],
                'duration' => time() - strtotime($userData['connected_at']),
                'total_clients' => count($this->clients)
            ]);
        }
    }

    /**
     * Envoyer une notification à un utilisateur spécifique
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array'
        ]);
        
        // Créer la notification dans la base de données
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'data' => $request->data ?? [],
            'read' => false
        ]);
        
        // Trouver tous les clients de cet utilisateur
        $sentTo = [];
        foreach ($this->clients as $clientId => $client) {
            if ($client['user_id'] == $request->user_id) {
                $sentTo[] = $clientId;
                
                // Envoyer en temps réel (nécessiterait une architecture différente avec Redis)
                // Pour l'instant, la notification sera envoyée lors de la prochaine vérification
            }
        }
        
        return response()->json([
            'success' => true,
            'notification' => $notification,
            'sent_to' => $sentTo,
            'total_recipients' => count($sentTo)
        ]);
    }

    /**
     * Obtenir les statistiques des connexions
     */
    public function connections()
    {
        $connections = [];
        
        foreach ($this->clients as $clientId => $client) {
            $connections[] = [
                'client_id' => $clientId,
                'user_id' => $client['user_id'],
                'connected_at' => $client['connected_at'],
                'duration' => now()->diffInSeconds($client['connected_at']),
                'ip' => $client['ip']
            ];
        }
        
        return response()->json([
            'total_connections' => count($this->clients),
            'max_connections' => $this->maxConnections,
            'connections' => $connections,
            'server_time' => now()->toISOString()
        ]);
    }

    /**
     * Vérifier la santé du service SSE
     */
    public function health()
    {
        return response()->json([
            'status' => 'healthy',
            'active_connections' => count($this->clients),
            'max_connections' => $this->maxConnections,
            'server_time' => now()->toISOString(),
            'uptime' => $this->lastId > 0 ? 'running' : 'idle'
        ]);
    }

    /**
     * Nettoyer les connexions inactives
     */
    public function cleanup()
    {
        $cleaned = 0;
        $now = time();
        
        foreach ($this->clients as $clientId => $client) {
            if ($now - strtotime($client['connected_at']) > 300) { // 5 minutes d'inactivité
                unset($this->clients[$clientId]);
                $cleaned++;
            }
        }
        
        return response()->json([
            'cleaned' => $cleaned,
            'remaining' => count($this->clients),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Méthode destructrice pour nettoyer à la fin du script
     */
    public function __destruct()
    {
        // Nettoyer toutes les connexions
        $this->clients = [];
    }
}