@extends('layouts.client')

@section('title', 'Tableau de bord - Particulier')

@section('content')
<div class="pwa-dashboard particulier-dashboard" id="dashboard-container">
    <!-- En-tête utilisateur avec salutation dynamique -->
    <div class="pwa-header-card" id="greeting-card">
        <div class="pwa-user-greeting">
            <div class="pwa-user-avatar">
                @if(Auth::user()->profile_photo)
                    <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" loading="lazy">
                @else
                    <div class="avatar-placeholder" style="background: linear-gradient(135deg, #{{ substr(md5(Auth::id()), 0, 6) }} 0%, #{{ substr(md5(Auth::id()), 6, 6) }} 100%)">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="pwa-user-info">
                <h2><span id="greeting-text"></span> {{ Auth::user()->first_name ?? Auth::user()->name }} !</h2>
                <p class="user-status">
                    <span class="status-dot bg-success"></span>
                    Membre Particulier • {{ $particulierStats['profession'] ?? 'Non spécifié' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bannière de bienvenue -->
    <div class="pwa-welcome-banner">
        <div class="banner-content">
            <h3>Votre développement personnel</h3>
            <p>Accédez à vos formations, prêts et subventions pour évoluer</p>
        </div>
        <div class="banner-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
    </div>

    <!-- Grille de statistiques principales -->
    <div class="pwa-stats-grid">
        <!-- Total reçu -->
        <div class="pwa-stat-card" onclick="navigateTo('{{ route('client.wallet.index') }}')">
            <div class="stat-icon bg-primary-gradient">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-content">
                <h3>{{ number_format($particulierStats['total_received_all'] ?? 0, 0, ',', ' ') }}</h3>
                <p>Total reçu</p>
                <small>FCFA versés</small>
            </div>
        </div>

        <!-- Certificats -->
        <div class="pwa-stat-card" onclick="navigateTo('{{ route('client.trainings') }}')">
            <div class="stat-icon bg-success-gradient">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['certificates_earned'] ?? 0 }}</h3>
                <p>Certificats</p>
                <small>Formations complétées</small>
            </div>
        </div>

        <!-- Progression -->
        <div class="pwa-stat-card" onclick="navigateTo('{{ route('client.trainings') }}')">
            <div class="stat-icon bg-info-gradient">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['average_training_progress'] ?? 0 }}%</h3>
                <p>Progression</p>
                <small>Moyenne formations</small>
            </div>
        </div>

        <!-- Documents -->
        <div class="pwa-stat-card" onclick="navigateTo('{{ route('client.documents.index') }}')">
            <div class="stat-icon bg-warning-gradient">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['documents_validated'] ?? 0 }}</h3>
                <p>Documents</p>
                <small>{{ $particulierStats['documents_pending'] ?? 0 }} en attente</small>
            </div>
        </div>
    </div>

    <!-- Carte Portefeuille -->
    <div class="pwa-wallet-card">
        <div class="wallet-header">
            <h3>
                <i class="fas fa-wallet me-2"></i>
                Mon Portefeuille
            </h3>
            <a href="{{ route('client.wallet.index') }}" class="wallet-action" aria-label="Voir le portefeuille">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="wallet-balance">
            <div class="balance-amount">
                <span class="currency">FCFA</span>
                <h2>{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
            </div>
            @if(isset($generalStats['wallet_change']))
            <div class="balance-change {{ ($generalStats['wallet_change']['direction'] ?? 'up') === 'up' ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $generalStats['wallet_change']['direction'] ?? 'up' }}"></i>
                <span>{{ abs($generalStats['wallet_change']['percentage'] ?? 0) }}% ce mois</span>
            </div>
            @endif
        </div>

        <div class="wallet-actions">
            <a href="{{ route('client.wallet.index') }}?action=deposit" class="wallet-btn deposit-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Dépôt</span>
            </a>
            <a href="{{ route('client.wallet.index') }}?action=withdraw" class="wallet-btn withdraw-btn">
                <i class="fas fa-minus-circle"></i>
                <span>Retrait</span>
            </a>
            <a href="{{ route('client.wallet.transactions') }}" class="wallet-btn history-btn">
                <i class="fas fa-history"></i>
                <span>Historique</span>
            </a>
        </div>
    </div>

    <!-- Section Financements détaillée -->
    <div class="pwa-stats-detail-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-chart-pie"></i>
                Mes financements
            </h3>
            <span class="success-rate">{{ $particulierStats['success_rate'] ?? 0 }}% succès</span>
        </div>

        <!-- Prêts standards -->
        <div class="funding-category">
            <h4><i class="fas fa-university"></i> Prêts standards</h4>
            <div class="stats-detail-grid">
                <div class="stat-detail-item">
                    <span class="detail-value">{{ $particulierStats['predefined_requests']['count'] ?? 0 }}</span>
                    <span class="detail-label">Total demandes</span>
                </div>
                <div class="stat-detail-item highlight">
                    <span class="detail-value">{{ number_format($particulierStats['predefined_requests']['total_received'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="detail-label">Montant reçu</span>
                    <span class="detail-approved">{{ $particulierStats['predefined_requests']['approved'] ?? 0 }} approuvées</span>
                </div>
                <div class="stat-detail-item">
                    <span class="detail-value">{{ number_format($particulierStats['predefined_requests']['total_approved'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="detail-label">En attente de versement</span>
                </div>
                <div class="stat-detail-item">
                    <span class="detail-value">{{ $particulierStats['predefined_requests']['pending'] ?? 0 }}</span>
                    <span class="detail-label">En cours de traitement</span>
                </div>
            </div>
        </div>

        <!-- Demandes personnalisées -->
        <div class="funding-category">
            <h4><i class="fas fa-lightbulb"></i> Demandes personnalisées</h4>
            <div class="stats-detail-grid">
                <div class="stat-detail-item">
                    <span class="detail-value">{{ $particulierStats['custom_requests']['count'] ?? 0 }}</span>
                    <span class="detail-label">Total demandes</span>
                </div>
                <div class="stat-detail-item highlight">
                    <span class="detail-value">{{ number_format($particulierStats['custom_requests']['total_received'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="detail-label">Montant reçu</span>
                    <span class="detail-approved">{{ $particulierStats['custom_requests']['approved'] ?? 0 }} approuvées</span>
                </div>
                <div class="stat-detail-item">
                    <span class="detail-value">{{ number_format($particulierStats['custom_requests']['total_approved'] ?? 0, 0, ',', ' ') }}</span>
                    <span class="detail-label">En attente de versement</span>
                </div>
                <div class="stat-detail-item">
                    <span class="detail-value">{{ $particulierStats['custom_requests']['pending'] ?? 0 }}</span>
                    <span class="detail-label">En cours de traitement</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Compétences développées -->
    @if(!empty($particulierStats['skills_developed']))
    <div class="pwa-skills-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-star"></i>
                Compétences développées
            </h3>
        </div>
        <div class="skills-container">
            @foreach(array_slice($particulierStats['skills_developed'], 0, 5) as $skill)
                <span class="skill-badge">{{ $skill }}</span>
            @endforeach
            @if(count($particulierStats['skills_developed']) > 5)
                <span class="skill-badge more-skills" onclick="showAllSkills()">
                    +{{ count($particulierStats['skills_developed']) - 5 }} autres
                </span>
            @endif
        </div>
        <!-- Modal caché pour toutes les compétences -->
        <div id="skills-modal" class="skills-modal" style="display: none;">
            <div class="skills-modal-content">
                <div class="modal-header">
                    <h4>Toutes mes compétences</h4>
                    <button onclick="closeSkillsModal()" class="close-btn">&times;</button>
                </div>
                <div class="skills-list">
                    @foreach($particulierStats['skills_developed'] as $skill)
                        <span class="skill-badge">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Parcours recommandé -->
    <div class="pwa-learning-path">
        <div class="section-header">
            <h3>
                <i class="fas fa-road"></i>
                Parcours recommandé
            </h3>
            <a href="{{ route('client.trainings') }}" class="see-all">Voir tout</a>
        </div>
        <div class="path-steps">
            @forelse(array_slice($particulierStats['learning_path'] ?? [], 0, 3) as $index => $path)
                <div class="path-step" onclick="navigateTo('{{ route('client.trainings') }}')">
                    <div class="step-number">{{ $index + 1 }}</div>
                    <div class="step-content">
                        <h4>{{ $path }}</h4>
                        <p>Formations disponibles</p>
                    </div>
                    <div class="step-action">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            @empty
                <div class="empty-path">
                    <p>Aucun parcours recommandé pour le moment</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Formations en cours -->
    @if(($particulierStats['enrolled_trainings'] ?? 0) > 0)
    <div class="pwa-training-progress">
        <div class="section-header">
            <h3>
                <i class="fas fa-graduation-cap"></i>
                Formations en cours
            </h3>
            <span class="training-count">{{ $particulierStats['enrolled_trainings'] }} active(s)</span>
        </div>
        <div class="progress-overview">
            <div class="progress-circle" style="--progress: {{ $particulierStats['average_training_progress'] ?? 0 }}%">
                <span class="progress-value">{{ $particulierStats['average_training_progress'] ?? 0 }}%</span>
            </div>
            <div class="progress-info">
                <p>{{ $particulierStats['completed_trainings'] ?? 0 }} formation(s) terminée(s)</p>
                <a href="{{ route('client.trainings') }}" class="btn btn-sm btn-primary">Continuer</a>
            </div>
        </div>
    </div>
    @endif

    <!-- Dernières demandes -->
    <div class="pwa-requests-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-history"></i>
                Dernières demandes
            </h3>
            <a href="{{ route('client.requests.index') }}" class="see-all">Tout voir</a>
        </div>

        @if(isset($requests) && $requests->count() > 0)
            <div class="pwa-requests-list">
                @foreach($requests as $request)
                    <div class="pwa-request-item" onclick="navigateTo('{{ route('client.requests.show', $request->id) }}')">
                        <div class="request-type-icon">
                            @switch($request->type)
                                @case('loan')
                                    <i class="fas fa-hand-holding-usd text-primary"></i>
                                    @break
                                @case('grant')
                                    <i class="fas fa-gift text-success"></i>
                                    @break
                                @case('training')
                                    <i class="fas fa-graduation-cap text-info"></i>
                                    @break
                                @default
                                    <i class="fas fa-file-alt text-secondary"></i>
                            @endswitch
                        </div>
                        <div class="request-details">
                            <h4>{{ Str::limit($request->title, 30) }}</h4>
                            <div class="request-meta">
                                <span class="amount">{{ number_format($request->amount_requested ?? 0, 0, ',', ' ') }} FCFA</span>
                                <span class="date">{{ $request->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="request-status">
                            <span class="status-badge status-{{ $request->status }}">
                                {{ $request->status_label ?? ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="pwa-empty-state">
                <div class="empty-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h4>Aucune demande</h4>
                <p>Vous n'avez pas encore soumis de demande</p>
                <a href="{{ route('client.requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Créer une demande
                </a>
            </div>
        @endif
    </div>

    <!-- Notifications récentes -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="pwa-notifications-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-bell"></i>
                Notifications
                @if($notifications->whereNull('read_at')->count() > 0)
                    <span class="notification-badge">{{ $notifications->whereNull('read_at')->count() }}</span>
                @endif
            </h3>
            <a href="{{ route('client.notifications.index') }}" class="see-all">Tout voir</a>
        </div>
        <div class="notifications-list">
            @foreach($notifications->take(5) as $notification)
                <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}"
                     onclick="navigateTo('{{ route('client.notifications.index') }}')">
                    <div class="notification-icon">
                        <i class="fas fa-{{ $notification->data['icon'] ?? 'info-circle' }}"></i>
                    </div>
                    <div class="notification-content">
                        <p>{{ $notification->data['message'] ?? $notification->title }}</p>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if(!$notification->read_at)
                        <span class="unread-dot"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Bouton flottant d'action (FAB) -->
    <div class="pwa-floating-action-container">
        <button class="pwa-floating-action-button" id="mainFab" aria-label="Menu rapide" aria-expanded="false">
            <i class="fas fa-plus"></i>
            <span class="fab-label">Actions rapides</span>
        </button>

        <nav class="fab-sub-buttons" aria-hidden="true">
            <a href="{{ route('client.requests.create') }}" class="fab-sub-btn" aria-label="Nouvelle demande">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon request-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span class="fab-sub-label">Nouvelle demande</span>
                </div>
            </a>
            <a href="{{ route('client.trainings') }}" class="fab-sub-btn" aria-label="Formations">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon training-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="fab-sub-label">Formations</span>
                </div>
            </a>
            <a href="{{ route('client.documents.upload.form') }}" class="fab-sub-btn" aria-label="Documents">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon document-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <span class="fab-sub-label">Documents</span>
                </div>
            </a>
            <a href="{{ route('client.support.create') }}" class="fab-sub-btn" aria-label="Support">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon support-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <span class="fab-sub-label">Support</span>
                </div>
            </a>
        </nav>
    </div>

    <div class="fab-overlay" id="fabOverlay"></div>
    <div class="pwa-bottom-spacer"></div>
</div>

<style>
/* ============================================
   VARIABLES CSS & CONFIGURATION
   ============================================ */
:root {
    --primary-color: #1b5a8d;
    --primary-dark: #2c5282;
    --success-color: #10b981;
    --success-light: #34d399;
    --info-color: #0ea5e9;
    --info-light: #38bdf8;
    --warning-color: #f59e0b;
    --warning-light: #fbbf24;
    --danger-color: #ef4444;
    --danger-light: #f87171;
    --gray-color: #64748b;
    --gray-light: #94a3b8;
    --bg-color: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition-fast: 150ms ease;
    --transition-normal: 300ms ease;
    --transition-slow: 500ms ease;
}

/* ============================================
   LAYOUT DE BASE
   ============================================ */
.pwa-dashboard {
    padding: 16px;
    padding-bottom: 120px;
    min-height: 100vh;
    background: linear-gradient(180deg, #f5f7fa 0%, #e3e8f0 100%);
    position: relative;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* ============================================
   EN-TÊTE UTILISATEUR
   ============================================ */
.pwa-header-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-xl);
    padding: 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    display: none;
    animation: slideDown 0.5s ease-out;
}

.show-greeting .pwa-header-card {
    display: block;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pwa-user-greeting {
    display: flex;
    align-items: center;
    gap: 15px;
}

.pwa-user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
}

.pwa-user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.pwa-user-info h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.3;
}

.user-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 5px 0 0 0;
    opacity: 0.9;
    font-size: 0.875rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--success-color);
    display: inline-block;
}

/* ============================================
   BANNIÈRE DE BIENVENUE
   ============================================ */
.pwa-welcome-banner {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-xl);
    padding: 24px 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(27, 90, 141, 0.3);
    position: relative;
    overflow: hidden;
}

.pwa-welcome-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.banner-content {
    position: relative;
    z-index: 1;
}

.banner-content h3 {
    margin: 0 0 8px 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.banner-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
    max-width: 200px;
}

.banner-icon {
    font-size: 3rem;
    opacity: 0.8;
    position: relative;
    z-index: 1;
}

/* ============================================
   GRILLE DE STATISTIQUES
   ============================================ */
.pwa-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.pwa-stat-card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    padding: 16px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    cursor: pointer;
    border: 1px solid transparent;
}

.pwa-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: rgba(27, 90, 141, 0.1);
}

.pwa-stat-card:active {
    transform: scale(0.98);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 1.25rem;
    color: white;
}

.bg-primary-gradient { background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); }
.bg-success-gradient { background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%); }
.bg-info-gradient { background: linear-gradient(135deg, var(--info-color) 0%, var(--info-light) 100%); }
.bg-warning-gradient { background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%); }

.stat-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
}

.stat-content p {
    margin: 4px 0 0 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

.stat-content small {
    color: var(--gray-light);
    font-size: 0.75rem;
    display: block;
    margin-top: 2px;
}

/* ============================================
   CARTE PORTEFEUILLE
   ============================================ */
.pwa-wallet-card {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0,0,0,0.05);
}

.wallet-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.wallet-header h3 {
    margin: 0;
    font-size: 1rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    font-weight: 600;
}

.wallet-action {
    color: var(--primary-color);
    font-size: 1.1rem;
    text-decoration: none;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background var(--transition-fast);
}

.wallet-action:hover {
    background: rgba(27, 90, 141, 0.1);
}

.wallet-balance {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.balance-amount {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.currency {
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 500;
}

.balance-amount h2 {
    margin: 0;
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.balance-change {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.balance-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.balance-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.wallet-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.wallet-btn {
    padding: 12px 8px;
    border-radius: var(--radius-md);
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: 600;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    transition: all var(--transition-fast);
    border: none;
    cursor: pointer;
}

.wallet-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.wallet-btn:active {
    transform: scale(0.95);
}

.deposit-btn { background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%); }
.withdraw-btn { background: linear-gradient(135deg, var(--danger-color) 0%, var(--danger-light) 100%); }
.history-btn { background: linear-gradient(135deg, var(--gray-color) 0%, var(--gray-light) 100%); }

/* ============================================
   SECTION FINANCEMENTS DÉTAILLÉE
   ============================================ */
.pwa-stats-detail-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.success-rate {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.funding-category {
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #e2e8f0;
}

.funding-category:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.funding-category h4 {
    margin: 0 0 16px 0;
    font-size: 0.95rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.funding-category h4 i {
    color: var(--primary-color);
}

.stats-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.stat-detail-item {
    background: #f8fafc;
    border-radius: var(--radius-md);
    padding: 16px 12px;
    text-align: center;
    transition: all var(--transition-fast);
    border: 1px solid transparent;
}

.stat-detail-item:hover {
    border-color: rgba(27, 90, 141, 0.2);
    transform: translateY(-1px);
}

.stat-detail-item.highlight {
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border: 1px solid var(--success-color);
}

.stat-detail-item.highlight .detail-value {
    color: #065f46;
}

.detail-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 4px;
    line-height: 1.2;
}

.detail-label {
    display: block;
    color: var(--text-secondary);
    font-size: 0.75rem;
    margin-bottom: 4px;
    line-height: 1.3;
}

.detail-approved {
    display: block;
    color: var(--success-color);
    font-size: 0.7rem;
    font-weight: 600;
}

/* ============================================
   SECTION COMPÉTENCES
   ============================================ */
.pwa-skills-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.skill-badge {
    padding: 8px 16px;
    background: #f1f5f9;
    border-radius: 20px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
    transition: all var(--transition-fast);
    cursor: default;
}

.skill-badge:hover {
    background: #e2e8f0;
    transform: translateY(-1px);
}

.skill-badge.more-skills {
    background: #e0f2fe;
    color: var(--primary-color);
    cursor: pointer;
    font-weight: 600;
}

/* Modal compétences */
.skills-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.skills-modal-content {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    max-width: 400px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    animation: modalAppear 0.3s ease;
}

@keyframes modalAppear {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-secondary);
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background var(--transition-fast);
}

.close-btn:hover {
    background: #f1f5f9;
}

.skills-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* ============================================
   PARCOURS D'APPRENTISSAGE
   ============================================ */
.pwa-learning-path {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.see-all {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.see-all:hover {
    text-decoration: underline;
}

.path-steps {
    margin-top: 16px;
}

.path-step {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: background var(--transition-fast);
    margin: 0 -20px;
    padding-left: 20px;
    padding-right: 20px;
}

.path-step:hover {
    background: #f8fafc;
}

.path-step:last-child {
    border-bottom: none;
}

.step-number {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
    min-width: 0;
}

.step-content h4 {
    margin: 0;
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.step-content p {
    margin: 4px 0 0 0;
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.step-action {
    color: #cbd5e1;
    font-size: 0.875rem;
}

.empty-path {
    text-align: center;
    padding: 24px;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

/* ============================================
   FORMATIONS EN COURS
   ============================================ */
.pwa-training-progress {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.training-count {
    background: #e0f2fe;
    color: var(--primary-color);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.progress-overview {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-top: 16px;
}

.progress-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(var(--primary-color) calc(var(--progress) * 1%), #e2e8f0 0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.progress-circle::before {
    content: '';
    width: 64px;
    height: 64px;
    background: white;
    border-radius: 50%;
    position: absolute;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.progress-value {
    position: relative;
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1rem;
    z-index: 1;
}

.progress-info {
    flex: 1;
}

.progress-info p {
    color: var(--text-secondary);
    margin: 0 0 12px 0;
    font-size: 0.9rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 8px 16px;
    border-radius: var(--radius-md);
    text-decoration: none;
    display: inline-block;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* ============================================
   DEMANDES RÉCENTES
   ============================================ */
.pwa-requests-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.pwa-requests-list {
    margin-top: 16px;
}

.pwa-request-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: var(--radius-md);
    margin-bottom: 10px;
    transition: all var(--transition-fast);
    cursor: pointer;
    border: 1px solid transparent;
}

.pwa-request-item:hover {
    background: white;
    border-color: #e2e8f0;
    box-shadow: var(--shadow-sm);
}

.pwa-request-item:active {
    transform: scale(0.99);
}

.request-type-icon {
    font-size: 1.25rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    flex-shrink: 0;
}

.request-details {
    flex: 1;
    min-width: 0;
}

.request-details h4 {
    margin: 0 0 6px 0;
    font-size: 0.9rem;
    color: var(--text-primary);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.request-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.amount {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 0.85rem;
}

.date {
    color: var(--gray-light);
    font-size: 0.75rem;
}

.request-status {
    flex-shrink: 0;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending { background: #fef3c7; color: #d97706; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-funded { background: #dbeafe; color: #1e40af; }
.status-processing { background: #e0f2fe; color: #0369a1; }
.status-rejected { background: #fee2e2; color: #b91c1c; }
.status-completed { background: #f3f4f6; color: #374151; }

/* ============================================
   NOTIFICATIONS
   ============================================ */
.pwa-notifications-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.notification-badge {
    background: var(--danger-color);
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
    font-weight: 700;
}

.notifications-list {
    margin-top: 16px;
}

.notification-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: var(--radius-md);
    margin-bottom: 8px;
    background: #f8fafc;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
}

.notification-item:hover {
    background: #f1f5f9;
}

.notification-item.unread {
    background: #eff6ff;
    border-left: 3px solid var(--primary-color);
}

.notification-item.read {
    opacity: 0.8;
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: #e0f2fe;
    color: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-content p {
    margin: 0 0 4px 0;
    font-size: 0.875rem;
    color: var(--text-primary);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-content small {
    color: var(--gray-light);
    font-size: 0.75rem;
}

.unread-dot {
    width: 8px;
    height: 8px;
    background: var(--primary-color);
    border-radius: 50%;
    flex-shrink: 0;
}

/* ============================================
   ÉTAT VIDE
   ============================================ */
.pwa-empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--gray-light);
    margin: 0 auto 16px;
}

.pwa-empty-state h4 {
    margin: 0 0 8px 0;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.pwa-empty-state p {
    color: var(--text-secondary);
    margin: 0 0 20px 0;
    font-size: 0.9rem;
}

/* ============================================
   BOUTON FLOTTANT (FAB)
   ============================================ */
.pwa-floating-action-container {
    position: fixed;
    bottom: 90px;
    right: 20px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 15px;
}

.pwa-floating-action-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border: 3px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 10px 25px rgba(27, 90, 141, 0.4);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    z-index: 1002;
}

.pwa-floating-action-button:hover {
    transform: scale(1.1);
    box-shadow: 0 15px 35px rgba(27, 90, 141, 0.5);
}

.pwa-floating-action-button.active {
    transform: rotate(45deg);
    background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%);
}

.fab-label {
    position: absolute;
    right: 70px;
    background: rgba(0,0,0,0.85);
    color: white;
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-size: 0.85rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
    pointer-events: none;
    font-weight: 500;
}

.pwa-floating-action-button:hover .fab-label {
    opacity: 1;
    visibility: visible;
    right: 75px;
}

.fab-sub-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.8);
    transition: all var(--transition-normal);
    position: relative;
    z-index: 1001;
    min-width: 220px;
    pointer-events: none;
}

.fab-sub-buttons.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: all;
}

.fab-sub-btn {
    display: block;
    text-decoration: none;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    transition: all var(--transition-fast);
    overflow: hidden;
    opacity: 0;
    transform: translateX(50px);
    border: 1px solid #e5e7eb;
}

.fab-sub-buttons.show .fab-sub-btn {
    opacity: 1;
    transform: translateX(0);
}

.fab-sub-btn:nth-child(1) { transition-delay: 0.05s; }
.fab-sub-btn:nth-child(2) { transition-delay: 0.1s; }
.fab-sub-btn:nth-child(3) { transition-delay: 0.15s; }
.fab-sub-btn:nth-child(4) { transition-delay: 0.2s; }

.fab-sub-btn:hover {
    transform: translateX(-5px);
    box-shadow: var(--shadow-xl);
}

.fab-sub-content {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
}

.fab-sub-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    flex-shrink: 0;
}

.request-icon { background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); }
.training-icon { background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%); }
.document-icon { background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); }
.support-icon { background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%); }

.fab-sub-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    flex: 1;
}

.fab-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
    backdrop-filter: blur(2px);
}

.fab-overlay.active {
    opacity: 1;
    visibility: visible;
}

.pwa-bottom-spacer {
    height: 100px;
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pwa-stat-card, .pwa-wallet-card, .pwa-skills-section,
.pwa-learning-path, .pwa-requests-section, .pwa-stats-detail-section,
.pwa-training-progress, .pwa-notifications-section {
    animation: fadeInUp 0.5s ease-out backwards;
}

.pwa-stat-card:nth-child(1) { animation-delay: 0.1s; }
.pwa-stat-card:nth-child(2) { animation-delay: 0.15s; }
.pwa-stat-card:nth-child(3) { animation-delay: 0.2s; }
.pwa-stat-card:nth-child(4) { animation-delay: 0.25s; }
.pwa-wallet-card { animation-delay: 0.3s; }
.pwa-stats-detail-section { animation-delay: 0.35s; }

@keyframes fabAppear {
    from { opacity: 0; transform: scale(0.5) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.pwa-floating-action-button {
    animation: fabAppear 0.5s ease-out 0.5s both;
}

/* ============================================
   MEDIA QUERIES - PWA & RESPONSIVE
   ============================================ */
@media (display-mode: standalone) {
    .pwa-dashboard {
        padding-top: env(safe-area-inset-top);
        padding-bottom: calc(env(safe-area-inset-bottom) + 120px);
    }

    .pwa-floating-action-container {
        bottom: calc(env(safe-area-inset-bottom) + 90px);
    }

    .pwa-header-card {
        margin-top: env(safe-area-inset-top);
    }
}

@media (max-width: 380px) {
    .pwa-stats-grid {
        grid-template-columns: 1fr;
    }

    .stats-detail-grid {
        grid-template-columns: 1fr;
    }

    .wallet-actions {
        grid-template-columns: 1fr;
    }

    .balance-amount h2 {
        font-size: 1.75rem;
    }
}

@media (max-width: 480px) {
    .pwa-floating-action-container {
        bottom: 80px;
        right: 16px;
    }

    .pwa-floating-action-button {
        width: 56px;
        height: 56px;
        font-size: 1.3rem;
    }

    .fab-sub-buttons {
        min-width: 180px;
    }

    .fab-sub-content {
        padding: 12px;
    }

    .fab-sub-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }

    .fab-sub-label {
        font-size: 0.85rem;
    }

    .fab-label {
        display: none;
    }
}

/* Support tactile amélioré */
@media (hover: none) and (pointer: coarse) {
    .pwa-floating-action-button:hover {
        transform: none;
    }

    .pwa-floating-action-button:active {
        transform: scale(0.95);
    }

    .fab-sub-btn:hover {
        transform: none;
    }

    .fab-sub-btn:active {
        transform: scale(0.98) !important;
    }
}

/* ============================================
   MODE SOMBRE (DARK MODE)
   ============================================ */
@media (prefers-color-scheme: dark) {
    :root {
        --bg-color: #0f172a;
        --card-bg: #1e293b;
        --text-primary: #f1f5f9;
        --text-secondary: #94a3b8;
    }

    .pwa-dashboard {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    }

    .pwa-stat-card, .pwa-wallet-card, .pwa-skills-section,
    .pwa-learning-path, .pwa-requests-section, .pwa-stats-detail-section,
    .pwa-training-progress, .pwa-notifications-section {
        background: var(--card-bg);
        border-color: #334155;
    }

    .stat-content h3, .balance-amount h2, .section-header h3,
    .step-content h4, .request-details h4, .detail-value,
    .pwa-empty-state h4, .banner-content h3 {
        color: var(--text-primary);
    }

    .skill-badge {
        background: #334155;
        color: #cbd5e1;
    }

    .skill-badge.more-skills {
        background: #1e3a8a;
        color: #60a5fa;
    }

    .pwa-request-item, .stat-detail-item, .notification-item {
        background: #334155;
    }

    .notification-item.unread {
        background: #1e3a8a;
        border-left-color: #60a5fa;
    }

    .pwa-floating-action-button {
        border-color: var(--card-bg);
    }

    .fab-sub-btn {
        background: var(--card-bg);
        border-color: #374151;
    }

    .fab-sub-label {
        color: #e5e7eb;
    }

    .fab-sub-btn:hover .fab-sub-label {
        color: #60a5fa;
    }

    .fab-label {
        background: rgba(255,255,255,0.95);
        color: #1f2937;
    }

    .progress-circle::before {
        background: var(--card-bg);
    }

    .funding-category h4 {
        color: #94a3b8;
    }

    .path-step:hover {
        background: #334155;
    }

    .pwa-welcome-banner::before {
        background: rgba(255,255,255,0.05);
    }

    .skills-modal-content {
        background: var(--card-bg);
        color: var(--text-primary);
    }

    .close-btn:hover {
        background: #334155;
    }
}

/* ============================================
   ACCESSIBILITÉ
   ============================================ */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus visible pour navigation clavier */
.pwa-stat-card:focus-visible,
.pwa-request-item:focus-visible,
.path-step:focus-visible,
.notification-item:focus-visible,
.wallet-btn:focus-visible,
.fab-sub-btn:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* ============================================
   UTILITAIRES
   ============================================ */
.text-primary { color: var(--primary-color); }
.text-success { color: var(--success-color); }
.text-info { color: var(--info-color); }
.text-secondary { color: var(--text-secondary); }

.bg-success { background-color: var(--success-color); }

.me-2 { margin-right: 0.5rem; }
</style>

@push('scripts')
<script>
(function() {
    'use strict';

    // ==========================================
    // GESTION DE LA SALUTATION
    // ==========================================
    function initGreeting() {
        const hour = new Date().getHours();
        let greeting = 'Bonsoir';

        if (hour >= 5 && hour < 18) {
            greeting = 'Bonjour';
        }

        const greetingElement = document.getElementById('greeting-text');
        if (greetingElement) {
            greetingElement.textContent = greeting + ',';
        }

        // Afficher la carte de salutation une seule fois par session
        const hasSeenGreeting = sessionStorage.getItem('hasSeenGreeting');
        const dashboard = document.getElementById('dashboard-container');

        if (!hasSeenGreeting && dashboard) {
            dashboard.classList.add('show-greeting');
            sessionStorage.setItem('hasSeenGreeting', 'true');

            setTimeout(() => {
                dashboard.classList.remove('show-greeting');
            }, 5000);
        }
    }

    // ==========================================
    // GESTION DU FAB (Floating Action Button)
    // ==========================================
    function initFab() {
        const mainFab = document.getElementById('mainFab');
        const fabSubButtons = document.querySelector('.fab-sub-buttons');
        const fabOverlay = document.getElementById('fabOverlay');

        if (!mainFab || !fabSubButtons || !fabOverlay) return;

        let isOpen = false;

        function toggleFab() {
            isOpen = !isOpen;

            mainFab.classList.toggle('active', isOpen);
            fabSubButtons.classList.toggle('show', isOpen);
            fabOverlay.classList.toggle('active', isOpen);
            mainFab.setAttribute('aria-expanded', isOpen);
            fabSubButtons.setAttribute('aria-hidden', !isOpen);

            document.body.style.overflow = isOpen ? 'hidden' : '';
        }

        function closeFab() {
            if (isOpen) toggleFab();
        }

        mainFab.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleFab();
        });

        fabOverlay.addEventListener('click', closeFab);

        // Fermer au clic sur un sous-bouton
        fabSubButtons.querySelectorAll('.fab-sub-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(closeFab, 100);
            });
        });

        // Fermer au clic extérieur
        document.addEventListener('click', (e) => {
            if (isOpen && !mainFab.contains(e.target) && !fabSubButtons.contains(e.target)) {
                closeFab();
            }
        });

        // Fermer avec la touche Echap
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen) {
                closeFab();
            }
        });
    }

    // ==========================================
    // NAVIGATION
    // ==========================================
    window.navigateTo = function(url) {
        if (url) {
            window.location.href = url;
        }
    };

    // ==========================================
    // MODAL COMPÉTENCES
    // ==========================================
    window.showAllSkills = function() {
        const modal = document.getElementById('skills-modal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeSkillsModal = function() {
        const modal = document.getElementById('skills-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Fermer le modal en cliquant à l'extérieur
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('skills-modal');
        if (modal && e.target === modal) {
            closeSkillsModal();
        }
    });

    // ==========================================
    // INITIALISATION
    // ==========================================
    document.addEventListener('DOMContentLoaded', () => {
        initGreeting();
        initFab();
    });

    // Support pour les pages chargées via Turbo/Turbolinks
    document.addEventListener('turbo:load', () => {
        initGreeting();
        initFab();
    });
})();
</script>
@endpush
@endsection
