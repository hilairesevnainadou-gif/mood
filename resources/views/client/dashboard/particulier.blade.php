@extends('layouts.client')

@section('title', 'Tableau de bord - Particulier')

@section('content')
<div class="pwa-dashboard particulier-dashboard">
    <!-- En-tête PWA Particulier -->
    <div class="pwa-header-card">
        <div class="pwa-user-greeting">
            <div class="pwa-user-avatar">
                @if(Auth::user()->profile_photo)
                    <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}">
                @else
                    <div class="avatar-placeholder" style="background-color: #{{ substr(md5(Auth::id()), 0, 6) }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="pwa-user-info">
                <h2><span id="greeting-message"></span>{{ Auth::user()->first_name ?? Auth::user()->name }} !</h2>
                <p class="user-status">
                    <span class="status-dot bg-success"></span>
                    Membre Particulier • {{ $user->profession ?? 'Développement personnel' }}
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

    <!-- Statistiques principales en grille PWA -->
    <div class="pwa-stats-grid">
        <div class="pwa-stat-card">
            <div class="stat-icon bg-primary-gradient">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-content">
                <h3>{{ number_format($particulierStats['personal_loans']['total_approved'] ?? 0) }}</h3>
                <p>Prêts approuvés</p>
                <small>{{ $particulierStats['personal_loans']['count'] ?? 0 }} demande(s)</small>
            </div>
        </div>

        <div class="pwa-stat-card">
            <div class="stat-icon bg-success-gradient">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['certificates_earned'] ?? 0 }}</h3>
                <p>Certificats</p>
                <small>Formations complétées</small>
            </div>
        </div>

        <div class="pwa-stat-card">
            <div class="stat-icon bg-info-gradient">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['average_training_progress'] ?? 0 }}%</h3>
                <p>Progression</p>
                <small>Moyenne formations</small>
            </div>
        </div>

        <div class="pwa-stat-card">
            <div class="stat-icon bg-warning-gradient">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $particulierStats['documents_by_type']->sum() ?? 0 }}</h3>
                <p>Documents</p>
                <small>Fichiers uploadés</small>
            </div>
        </div>
    </div>

    <!-- Portefeuille rapide PWA -->
    <div class="pwa-wallet-card">
        <div class="wallet-header">
            <h3>
                <i class="fas fa-wallet me-2"></i>
                Mon Portefeuille
            </h3>
            <a href="{{ route('client.wallet') }}" class="wallet-action">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="wallet-balance">
            <div class="balance-amount">
                <span class="currency">FCFA</span>
                <h2>{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
            </div>
            <div class="balance-change {{ $generalStats['wallet_change'] >= 0 ? 'positive' : 'negative' }}">
                <i class="fas fa-arrow-{{ $generalStats['wallet_change'] >= 0 ? 'up' : 'down' }}"></i>
                <span>{{ abs($generalStats['wallet_change']) }}% ce mois</span>
            </div>
        </div>
        <div class="wallet-actions">
            <a href="{{ route('client.wallet') }}?action=deposit" class="wallet-btn deposit-btn">
                <i class="fas fa-plus-circle"></i>
                Dépôt
            </a>
            <a href="{{ route('client.wallet') }}?action=withdraw" class="wallet-btn withdraw-btn">
                <i class="fas fa-minus-circle"></i>
                Retrait
            </a>
            <a href="{{ route('client.wallet.transactions') }}" class="wallet-btn history-btn">
                <i class="fas fa-history"></i>
                Historique
            </a>
        </div>
    </div>

    <!-- Compétences développées -->
    @if(count($particulierStats['skills_developed']) > 0)
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
            <span class="skill-badge more-skills">
                +{{ count($particulierStats['skills_developed']) - 5 }} autres
            </span>
            @endif
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
            @foreach(array_slice($particulierStats['learning_path'], 0, 3) as $index => $path)
            <div class="path-step">
                <div class="step-number">{{ $index + 1 }}</div>
                <div class="step-content">
                    <h4>{{ $path }}</h4>
                    <p>Formations disponibles</p>
                </div>
                <div class="step-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Dernières demandes PWA -->
    <div class="pwa-requests-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-history"></i>
                Dernières demandes
            </h3>
            <a href="{{ route('client.requests.index') }}" class="see-all">Tout voir</a>
        </div>

        @if($requests->count() > 0)
        <div class="pwa-requests-list">
            @foreach($requests as $request)
            <div class="pwa-request-item">
                <div class="request-type-icon">
                    @switch($request->category)
                        @case('loan') <i class="fas fa-hand-holding-usd text-primary"></i> @break
                        @case('grant') <i class="fas fa-gift text-success"></i> @break
                        @case('training') <i class="fas fa-graduation-cap text-info"></i> @break
                        @default <i class="fas fa-file-alt text-secondary"></i>
                    @endswitch
                </div>
                <div class="request-details">
                    <h4>{{ Str::limit($request->title, 30) }}</h4>
                    <div class="request-meta">
                        <span class="amount">{{ number_format($request->amount_requested) }} FCFA</span>
                        <span class="date">{{ $request->created_at->format('d/m') }}</span>
                    </div>
                </div>
                <div class="request-status">
                    <span class="status-badge status-{{ $request->status }}">
                        {{ ucfirst($request->status) }}
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

    <!-- Bouton flottant principal avec étiquettes claires -->
    <div class="pwa-floating-action-container">
        <!-- Bouton principal flottant -->
        <div class="pwa-floating-action-button" id="mainFab">
            <i class="fas fa-plus"></i>
            <span class="fab-label">Actions rapides</span>
        </div>

        <!-- Sous-boutons avec étiquettes -->
        <div class="fab-sub-buttons">
            <a href="{{ route('client.requests.create') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon request-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span class="fab-sub-label">Nouvelle demande</span>
                </div>
            </a>
            <a href="{{ route('client.trainings') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon training-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="fab-sub-label">Formations</span>
                </div>
            </a>
            <a href="{{ route('client.documents.upload') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon document-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <span class="fab-sub-label">Documents</span>
                </div>
            </a>
            <a href="{{ route('client.support.create') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon support-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <span class="fab-sub-label">Support</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Overlay pour fermer le menu FAB -->
    <div class="fab-overlay" id="fabOverlay"></div>

    <!-- Espace pour la navigation bottom -->
    <div class="pwa-bottom-spacer"></div>
</div>

<style>
/* Styles PWA Particulier */
.pwa-dashboard {
    padding: 16px;
    padding-bottom: 120px;
    min-height: 100vh;
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.particulier-dashboard {
    background: linear-gradient(180deg, #f5f7fa 0%, #e3e8f0 100%);
}

/* En-tête PWA */
.pwa-header-card {
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    border-radius: 20px;
    padding: 20px;
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(27, 90, 141, 0.2);
    display: none; /* Caché par défaut, affiché seulement à la connexion */
}

.show-greeting .pwa-header-card {
    display: block;
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
    border: 3px solid rgba(255, 255, 255, 0.3);
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
    font-size: 1.4rem;
    font-weight: 600;
}

.user-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 5px;
    opacity: 0.9;
    font-size: 0.9rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

/* Bannière de bienvenue */
.pwa-welcome-banner {
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    border-radius: 20px;
    padding: 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(27, 90, 141, 0.3);
}

.banner-content h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.banner-content p {
    margin: 5px 0 0 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.banner-icon {
    font-size: 3rem;
    opacity: 0.8;
}

/* Grille de statistiques PWA */
.pwa-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.pwa-stat-card {
    background: white;
    border-radius: 16px;
    padding: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.pwa-stat-card:active {
    transform: scale(0.98);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 1.5rem;
    color: white;
}

.bg-primary-gradient { background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%); }
.bg-success-gradient { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
.bg-info-gradient { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); }
.bg-warning-gradient { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

.stat-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-content p {
    margin: 4px 0 0 0;
    color: #64748b;
    font-size: 0.9rem;
}

.stat-content small {
    color: #94a3b8;
    font-size: 0.8rem;
}

/* Carte portefeuille PWA */
.pwa-wallet-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.wallet-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.wallet-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
}

.wallet-action {
    color: #1b5a8d;
    font-size: 1.2rem;
    text-decoration: none;
}

.wallet-balance {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 20px;
}

.balance-amount {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.currency {
    color: #64748b;
    font-size: 1rem;
}

.balance-amount h2 {
    margin: 0;
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

.balance-change {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.balance-change.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.balance-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.wallet-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.wallet-btn {
    padding: 12px;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: 500;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    transition: transform 0.2s;
}

.wallet-btn:active {
    transform: scale(0.95);
}

.deposit-btn { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
.withdraw-btn { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }
.history-btn { background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%); }

/* Section compétences */
.pwa-skills-section {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.section-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
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
    font-size: 0.85rem;
    color: #475569;
}

.more-skills {
    background: #e0f2fe;
    color: #1b5a8d;
}

/* Parcours d'apprentissage */
.pwa-learning-path {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.see-all {
    color: #1b5a8d;
    text-decoration: none;
    font-size: 0.9rem;
}

.path-steps {
    margin-top: 15px;
}

.path-step {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f1f5f9;
}

.path-step:last-child {
    border-bottom: none;
}

.step-number {
    width: 30px;
    height: 30px;
    background: #1b5a8d;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.step-content {
    flex: 1;
}

.step-content h4 {
    margin: 0;
    font-size: 1rem;
    color: #1e293b;
}

.step-content p {
    margin: 4px 0 0 0;
    color: #64748b;
    font-size: 0.85rem;
}

.step-action {
    color: #cbd5e1;
}

/* Demandes récentes PWA */
.pwa-requests-section {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.pwa-requests-list {
    margin-top: 15px;
}

.pwa-request-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: transform 0.2s;
}

.pwa-request-item:active {
    transform: scale(0.98);
}

.request-type-icon {
    font-size: 1.5rem;
}

.request-details {
    flex: 1;
}

.request-details h4 {
    margin: 0;
    font-size: 1rem;
    color: #1e293b;
}

.request-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
}

.amount {
    color: #1b5a8d;
    font-weight: 600;
    font-size: 0.9rem;
}

.date {
    color: #94a3b8;
    font-size: 0.85rem;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-pending { background: #fef3c7; color: #d97706; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-processing { background: #dbeafe; color: #1e40af; }
.status-rejected { background: #fee2e2; color: #b91c1c; }

/* État vide */
.pwa-empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    width: 60px;
    height: 60px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #94a3b8;
    margin: 0 auto 15px;
}

.pwa-empty-state h4 {
    margin: 0 0 10px 0;
    color: #1e293b;
}

.pwa-empty-state p {
    color: #64748b;
    margin-bottom: 20px;
}

/* === BOUTON FLOTTANT (FAB) AMÉLIORÉ AVEC ÉTIQUETTES === */
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

/* Bouton principal FAB */
.pwa-floating-action-button {
    width: 64px;
    height: 64px;
    border-radius: 32px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 10px 30px rgba(27, 90, 141, 0.4);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1002;
    border: 3px solid white;
}

.pwa-floating-action-button:hover {
    transform: scale(1.1);
    box-shadow: 0 15px 35px rgba(27, 90, 141, 0.5);
}

.pwa-floating-action-button.active {
    transform: rotate(45deg);
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
}

/* Étiquette du bouton principal */
.fab-label {
    position: absolute;
    right: 70px;
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    pointer-events: none;
    z-index: 1003;
    font-weight: 500;
}

.pwa-floating-action-button:hover .fab-label {
    opacity: 1;
    visibility: visible;
    right: 80px;
}

/* Sous-boutons */
.fab-sub-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1001;
    min-width: 220px;
}

.fab-sub-buttons.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.fab-sub-btn {
    display: block;
    text-decoration: none;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    opacity: 0;
    transform: translateX(50px);
    border: 1px solid #e5e7eb;
}

.fab-sub-buttons.show .fab-sub-btn {
    opacity: 1;
    transform: translateX(0);
}

.fab-sub-btn:nth-child(1) { transition-delay: 0.1s; }
.fab-sub-btn:nth-child(2) { transition-delay: 0.2s; }
.fab-sub-btn:nth-child(3) { transition-delay: 0.3s; }
.fab-sub-btn:nth-child(4) { transition-delay: 0.4s; }

.fab-sub-btn:hover {
    transform: translateX(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.fab-sub-content {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px;
}

.fab-sub-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
    flex-shrink: 0;
}

.request-icon { background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%); }
.training-icon { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
.document-icon { background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); }
.support-icon { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }

.fab-sub-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: #1e293b;
    flex: 1;
}

.fab-sub-btn:hover .fab-sub-label {
    color: #1b5a8d;
}

/* Overlay pour fermer le menu FAB */
.fab-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(2px);
}

.fab-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Espace pour bottom nav */
.pwa-bottom-spacer {
    height: 120px;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pwa-stat-card,
.pwa-wallet-card,
.pwa-skills-section,
.pwa-learning-path,
.pwa-requests-section {
    animation: fadeInUp 0.5s ease-out;
}

/* Media queries pour PWA */
@media (display-mode: standalone) {
    .pwa-dashboard {
        padding-top: env(safe-area-inset-top);
        padding-bottom: calc(env(safe-area-inset-bottom) + 120px);
    }

    .pwa-floating-action-container {
        bottom: calc(env(safe-area-inset-bottom) + 90px);
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .particulier-dashboard {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    }

    .pwa-stat-card,
    .pwa-wallet-card,
    .pwa-skills-section,
    .pwa-learning-path,
    .pwa-requests-section {
        background: #1e293b;
        color: white;
    }

    .pwa-wallet-card,
    .pwa-stat-card,
    .pwa-skills-section,
    .pwa-learning-path,
    .pwa-requests-section {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .stat-content h3,
    .balance-amount h2,
    .section-header h3,
    .step-content h4,
    .request-details h4 {
        color: white;
    }

    .skill-badge {
        background: #334155;
        color: #cbd5e1;
    }

    .pwa-request-item {
        background: #334155;
    }

    .pwa-floating-action-button {
        border-color: #1e293b;
    }

    /* Adaptation du FAB pour dark mode */
    .fab-sub-btn {
        background: #1e293b;
        border-color: #374151;
    }

    .fab-sub-label {
        color: #e5e7eb;
    }

    .fab-sub-btn:hover .fab-sub-label {
        color: #60a5fa;
    }

    .fab-label {
        background: rgba(255, 255, 255, 0.95);
        color: #1f2937;
    }
}

/* Responsive pour mobile */
@media (max-width: 480px) {
    .pwa-floating-action-container {
        bottom: 80px;
        right: 15px;
    }

    .pwa-floating-action-button {
        width: 60px;
        height: 60px;
        font-size: 1.6rem;
    }

    .fab-sub-buttons {
        min-width: 200px;
    }

    .fab-sub-content {
        padding: 14px;
        gap: 12px;
    }

    .fab-sub-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .fab-sub-label {
        font-size: 0.9rem;
    }

    /* Masquer l'étiquette sur mobile */
    .fab-label {
        display: none;
    }
}

/* Pour les très petits écrans */
@media (max-width: 360px) {
    .pwa-floating-action-container {
        bottom: 70px;
        right: 10px;
    }

    .fab-sub-buttons {
        min-width: 180px;
    }

    .fab-sub-content {
        padding: 12px;
        gap: 10px;
    }

    .fab-sub-icon {
        width: 36px;
        height: 36px;
        font-size: 1.1rem;
    }

    .fab-sub-label {
        font-size: 0.85rem;
    }
}

/* Support tactile amélioré */
@media (hover: none) and (pointer: coarse) {
    .pwa-floating-action-button:active {
        transform: scale(0.95);
    }

    .fab-sub-btn:active {
        transform: scale(0.98) !important;
    }

    /* Masquer les effets hover sur mobile tactile */
    .pwa-floating-action-button:hover {
        transform: none;
    }

    .fab-sub-btn:hover {
        transform: none;
    }
}

/* Animation d'apparition progressive pour le FAB */
@keyframes fabAppear {
    from {
        opacity: 0;
        transform: scale(0.5) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.pwa-floating-action-button {
    animation: fabAppear 0.5s ease-out 0.3s both;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du message de salutation
    function updateGreeting() {
        const now = new Date();
        const hour = now.getHours();
        let greeting = '';

        if (hour >= 5 && hour < 12) {
            greeting = 'Bonjour, ';
        } else if (hour >= 12 && hour < 18) {
            greeting = 'Bonjour, ';
        } else {
            greeting = 'Bonsoir, ';
        }

        document.getElementById('greeting-message').textContent = greeting;

        // Afficher la carte de salutation seulement si c'est la première fois depuis la connexion
        const hasSeenGreeting = sessionStorage.getItem('hasSeenGreeting');
        if (!hasSeenGreeting) {
            document.querySelector('.pwa-dashboard').classList.add('show-greeting');
            sessionStorage.setItem('hasSeenGreeting', 'true');

            // Masquer après 5 secondes
            setTimeout(() => {
                document.querySelector('.pwa-dashboard').classList.remove('show-greeting');
            }, 5000);
        }
    }

    updateGreeting();

    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.pwa-stat-card, .pwa-wallet-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
    });

    // Gestion du bouton flottant (FAB)
    const mainFab = document.getElementById('mainFab');
    const fabSubButtons = document.querySelector('.fab-sub-buttons');
    const fabOverlay = document.getElementById('fabOverlay');

    let fabOpen = false;

    function toggleFabMenu() {
        fabOpen = !fabOpen;

        if (fabOpen) {
            mainFab.classList.add('active');
            fabSubButtons.classList.add('show');
            fabOverlay.classList.add('active');

            // Animer chaque sous-bouton individuellement
            const subButtons = document.querySelectorAll('.fab-sub-btn');
            subButtons.forEach((btn, index) => {
                btn.style.transitionDelay = (index * 0.1) + 's';
            });

            // Vibration (si supporté)
            if ('vibrate' in navigator) {
                navigator.vibrate([30]);
            }

            // Empêcher le défilement derrière l'overlay
            document.body.style.overflow = 'hidden';
        } else {
            mainFab.classList.remove('active');
            fabSubButtons.classList.remove('show');
            fabOverlay.classList.remove('active');

            // Réinitialiser les délais de transition
            const subButtons = document.querySelectorAll('.fab-sub-btn');
            subButtons.forEach(btn => {
                btn.style.transitionDelay = '0s';
            });

            // Rétablir le défilement
            document.body.style.overflow = '';
        }
    }

    mainFab.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleFabMenu();
    });

    fabOverlay.addEventListener('click', () => {
        if (fabOpen) {
            toggleFabMenu();
        }
    });

    // Fermer le menu FAB en cliquant sur un sous-bouton
    document.querySelectorAll('.fab-sub-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (fabOpen) {
                // Petit délai pour voir l'animation
                setTimeout(() => {
                    toggleFabMenu();
                }, 100);
            }
        });
    });

    // Fermer le menu FAB en cliquant en dehors
    document.addEventListener('click', (e) => {
        if (fabOpen &&
            !mainFab.contains(e.target) &&
            !fabSubButtons.contains(e.target) &&
            !fabOverlay.contains(e.target)) {
            toggleFabMenu();
        }
    });

    // Fermer le menu FAB en faisant défiler
    let lastScrollTop = 0;
    const scrollThreshold = 5;

    window.addEventListener('scroll', () => {
        if (fabOpen) {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (Math.abs(currentScroll - lastScrollTop) > scrollThreshold) {
                toggleFabMenu();
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        }
    });

    // Refresh pull-to-refresh
    let touchStartY = 0;
    const dashboard = document.querySelector('.pwa-dashboard');

    dashboard.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    });

    dashboard.addEventListener('touchend', function(e) {
        const touchEndY = e.changedTouches[0].clientY;
        if (touchStartY - touchEndY > 100 && window.scrollY === 0) {
            // Pull to refresh
            location.reload();
        }
    });

    // Vibration sur les interactions importantes (si supporté)
    const importantButtons = document.querySelectorAll('.wallet-btn');
    importantButtons.forEach(button => {
        button.addEventListener('click', function() {
            if ('vibrate' in navigator) {
                navigator.vibrate(30);
            }
        });
    });

    // Mettre à jour l'heure en temps réel
    function updateTime() {
        const timeElements = document.querySelectorAll('.current-time');
        const now = new Date();
        timeElements.forEach(el => {
            el.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        });
    }

    setInterval(updateTime, 60000);
    updateTime();

    // Mettre à jour la salutation périodiquement (au cas où la page reste ouverte)
    setInterval(updateGreeting, 600000); // Toutes les 10 minutes

    // Animation d'apparition des éléments
    function animateOnScroll() {
        const elements = document.querySelectorAll('.pwa-stat-card, .pwa-wallet-card, .pwa-skills-section, .pwa-learning-path');

        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = "1";
                element.style.transform = "translateY(0)";
            }
        });
    }

    // Initialiser les styles d'animation
    document.querySelectorAll('.pwa-stat-card, .pwa-wallet-card, .pwa-skills-section, .pwa-learning-path').forEach(el => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    });

    window.addEventListener('scroll', animateOnScroll);
    // Déclencher une première fois au chargement
    setTimeout(animateOnScroll, 100);
});
</script>
@endpush
@endsection
