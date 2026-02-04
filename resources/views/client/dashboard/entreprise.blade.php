@extends('layouts.client')

@section('title', 'Tableau de bord - Entreprise BHDM')

@section('content')
<div class="pwa-dashboard enterprise-dashboard">
    <!-- En-tête BHDM avec mission -->
    <div class="bhdm-mission-card">
        <div class="mission-header">
            <div class="bhdm-logo">
                <i class="fas fa-hands-helping"></i>
            </div>
            <div class="mission-title">
                <h3>BHDM - Bureau Humanitaire pour le Développement Mondial</h3>
                <p class="mission-tagline">Créer • Préserver • Soutenir • Accompagner • Promouvoir</p>
            </div>
        </div>
        <div class="mission-quote">
            <i class="fas fa-quote-left"></i>
            <p>Luttons ensemble contre les inégalités et construisons une prospérité partagée</p>
            <i class="fas fa-quote-right"></i>
        </div>
    </div>

    <!-- Statut entreprise -->
    <div class="enterprise-status-card">
        <div class="status-header">
            <div class="enterprise-avatar">
                @if($user->company_logo ?? false)
                    <img src="{{ Storage::url($user->company_logo) }}" alt="{{ $user->company_name }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-fallback">
                        <i class="fas fa-building"></i>
                    </div>
                @else
                    <div class="avatar-fallback">
                        <i class="fas fa-building"></i>
                    </div>
                @endif
                <div class="verification-badge" title="Entreprise vérifiée BHDM">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="enterprise-info">
                <h1>{{ $user->company_name ?? 'Mon Entreprise' }}</h1>
                <div class="enterprise-meta">
                    <span class="meta-item">
                        <i class="fas fa-users"></i>
                        {{ $user->expected_jobs }} {{ $user->expected_jobs == 1 ? 'employé' : 'employés' }}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-industry"></i>
                        {{ $entrepriseStats['sector'] ?? 'Secteur non spécifié' }}
                    </span>
                </div>

            </div>
        </div>
        <div class="status-stats">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $entrepriseStats['total_projects'] ?? 0 }}</h3>
                    <p>Projets BHDM</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $entrepriseStats['projects_by_status']['approved'] ?? 0 }}</h3>
                    <p>Projets approuvés</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $entrepriseStats['total_projects'] > 0 ? round(($entrepriseStats['projects_by_status']['approved'] / $entrepriseStats['total_projects']) * 100, 1) : 0 }}%</h3>
                    <p>Taux succès</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact BHDM -->
    <div class="impact-dashboard">
        <div class="impact-header">
            <h3>
                <i class="fas fa-trophy"></i>
                Votre impact BHDM
            </h3>
            <div class="impact-score">
                <span class="score-badge">Score: {{ $entrepriseStats['bhdm_score'] ?? 85 }}/100</span>
            </div>
        </div>

        <div class="impact-metrics">
            <div class="metric-card" data-metric="economic">
                <div class="metric-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ number_format($entrepriseStats['total_approved'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                    <p>Capital mobilisé</p>
                    <div class="metric-progress">
                        <div class="progress-bar" style="width: {{ min(($entrepriseStats['total_approved'] ?? 0) / 5000000 * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="metric-card" data-metric="social">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $entrepriseStats['jobs_created'] ?? 0 }}</h4>
                    <p>Emplois générés</p>
                    <div class="metric-progress">
                        <div class="progress-bar" style="width: {{ min(($entrepriseStats['jobs_created'] ?? 0) / 50 * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="metric-card" data-metric="development">
                <div class="metric-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $entrepriseStats['training_completed'] ?? 0 }}</h4>
                    <p>Formations suivies</p>
                    <div class="metric-progress">
                        <div class="progress-bar" style="width: {{ min(($entrepriseStats['training_completed'] ?? 0) / 10 * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="impact-actions">
            <a href="{{ route('client.requests.create') }}" class="impact-btn">
                <i class="fas fa-plus-circle"></i>
                Nouveau projet d'impact
            </a>
        </div>
    </div>

    <!-- Portefeuille entreprise -->
    <div class="enterprise-wallet-card">
        <div class="wallet-header">
            <div class="wallet-title">
                <h3>
                    <i class="fas fa-piggy-bank"></i>
                    Votre trésorerie BHDM
                </h3>
                <p class="wallet-subtitle">Fonds disponibles pour le développement</p>
            </div>
            <a href="{{ route('client.wallet') }}" class="wallet-link">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>

        <div class="wallet-content">
            <div class="balance-display">
                <div class="balance-amount">
                    <span class="currency">FCFA</span>
                    <h2>{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
                </div>
                <div class="balance-trend {{ ($generalStats['wallet_change'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($generalStats['wallet_change'] ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($generalStats['wallet_change'] ?? 0) }}% ce mois</span>
                </div>
            </div>

            <div class="wallet-actions">
                <a href="{{ route('client.wallet') }}?action=deposit" class="wallet-action deposit">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <span>Déposer</span>
                </a>
                <a href="{{ route('client.wallet') }}?action=withdraw" class="wallet-action withdraw">
                    <div class="action-icon">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <span>Retirer</span>
                </a>
                <a href="{{ route('client.wallet.transactions') }}" class="wallet-action history">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <span>Historique</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Types de financement BHDM -->
    <div class="funding-types">
        <div class="section-header">
            <h3>
                <i class="fas fa-hand-holding-usd"></i>
                Nos solutions de financement
            </h3>
        </div>

        <div class="funding-cards">
            <div class="funding-card grant">
                <div class="funding-header">
                    <div class="funding-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h4>Subventions d'exploitation</h4>
                </div>
                <div class="funding-body">
                    <p>Aide financière non remboursable pour les charges courantes</p>
                    <ul class="funding-features">
                        <li><i class="fas fa-check"></i> Non remboursable</li>
                        <li><i class="fas fa-check"></i> Charges courantes</li>
                        <li><i class="fas fa-check"></i> Aide immédiate</li>
                    </ul>
                </div>
                <div class="funding-footer">
                    <a href="{{ route('client.requests.create') }}?type=grant" class="apply-btn">
                        <i class="fas fa-paper-plane"></i>
                        Demander
                    </a>
                </div>
            </div>

            <div class="funding-card microcredit">
                <div class="funding-header">
                    <div class="funding-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4>Microcrédits solidaires</h4>
                </div>
                <div class="funding-body">
                    <p>Petits prêts avec accompagnement pour projets personnels ou professionnels</p>
                    <ul class="funding-features">
                        <li><i class="fas fa-check"></i> Taux réduits</li>
                        <li><i class="fas fa-check"></i> Accompagnement inclus</li>
                        <li><i class="fas fa-check"></i> Accessible à tous</li>
                    </ul>
                </div>
                <div class="funding-footer">
                    <a href="{{ route('client.requests.create') }}?type=microcredit" class="apply-btn">
                        <i class="fas fa-paper-plane"></i>
                        Demander
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Projets en cours -->
    <div class="active-projects">
        <div class="section-header">
            <h3>
                <i class="fas fa-tasks"></i>
                Vos projets actifs
            </h3>
            <a href="{{ route('client.requests.index') }}" class="see-all">
                Tout voir
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        @if($requests->count() > 0)
        <div class="projects-list">
            @foreach($requests->take(3) as $project)
            <div class="project-card" onclick="window.location='{{ route('client.requests.show', $project->id) }}'">
                <div class="project-header">
                    <div class="project-badge status-{{ $project->status }}">
                        {{ ucfirst($project->status) }}
                    </div>
                    <div class="project-amount">
                        {{ number_format($project->amount_requested, 0, ',', ' ') }} FCFA
                    </div>
                </div>

                <div class="project-body">
                    <h4>{{ Str::limit($project->title, 40) }}</h4>
                    <p class="project-description">{{ Str::limit($project->description ?? 'Aucune description', 60) }}</p>

                    <div class="project-meta">
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            {{ $project->created_at->format('d/m/Y') }}
                        </span>
                        @if($project->expected_jobs > 0)
                        <span class="meta-item">
                            <i class="fas fa-user-plus"></i>
                            {{ $project->expected_jobs }} emplois
                        </span>
                        @endif
                    </div>
                </div>

                <div class="project-footer">
                    <div class="project-type">
                        @switch($project->category)
                            @case('agriculture') <i class="fas fa-seedling"></i> Agriculture @break
                            @case('technology') <i class="fas fa-laptop-code"></i> Technologie @break
                            @case('commerce') <i class="fas fa-store"></i> Commerce @break
                            @default <i class="fas fa-briefcase"></i> {{ ucfirst($project->category) }}
                        @endswitch
                    </div>
                    <div class="project-actions">
                        <button class="action-btn" onclick="event.stopPropagation(); window.location='{{ route('client.requests.show', $project->id) }}'">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-projects">
            <div class="empty-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <h4>Commencez votre premier projet</h4>
            <p>Créez votre premier projet de développement avec l'accompagnement BHDM</p>
            <a href="{{ route('client.requests.create') }}" class="start-project-btn">
                <i class="fas fa-rocket"></i>
                Lancer un projet
            </a>
        </div>
        @endif
    </div>

    <!-- Accompagnement BHDM -->
    <div class="accompaniment-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-hands-helping"></i>
                Votre accompagnement BHDM
            </h3>
        </div>

        <div class="accompaniment-steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Diagnostic personnalisé</h4>
                    <p>Analyse approfondie de vos besoins et potentiel</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Financement adapté</h4>
                    <p>Solution financière sur mesure pour votre projet</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Suivi & Formation</h4>
                    <p>Accompagnement continu et développement de compétences</p>
                </div>
            </div>
        </div>

        <div class="accompaniment-cta">
            <a href="{{ route('client.support.create') }}" class="cta-btn">
                <i class="fas fa-comments"></i>
                Demander un accompagnement
            </a>
        </div>
    </div>

    <!-- Récompenses et fidélité -->
    <div class="rewards-section">
        <div class="section-header">
            <h3>
                <i class="fas fa-award"></i>
                Votre fidélité récompensée
            </h3>
        </div>

        <div class="rewards-progress">
            <div class="progress-info">
                <div class="current-level">
                    <span class="level-badge">Niveau {{ $entrepriseStats['loyalty_level'] ?? 1 }}</span>
                </div>
                <div class="progress-stats">
                    <span class="points">{{ $entrepriseStats['loyalty_points'] ?? 0 }} points</span>
                    <span class="next-level">Prochain niveau: {{ max(1000 - ($entrepriseStats['loyalty_points'] ?? 0), 0) }} points</span>
                </div>
            </div>

            <div class="progress-bar-container">
                <div class="progress-bar" style="width: {{ min(($entrepriseStats['loyalty_points'] ?? 0) / 1000 * 100, 100) }}%"></div>
            </div>

            <div class="rewards-benefits">
                <div class="benefit-item">
                    <i class="fas fa-percentage"></i>
                    <span>Taux préférentiel</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-rocket"></i>
                    <span>Traitement prioritaire</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-gift"></i>
                    <span>Bonus fidélité</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton flottant principal -->
    <div class="pwa-floating-action-container">
        <!-- Bouton principal flottant -->
        <div class="pwa-floating-action-button" id="mainFab">
            <i class="fas fa-plus"></i>
            <span class="fab-label">Actions rapides</span>
        </div>

        <!-- Sous-boutons -->
        <div class="fab-sub-buttons">
            <a href="{{ route('client.requests.create') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon request-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <span class="fab-sub-label">Nouveau projet</span>
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
                        <i class="fas fa-file-contract"></i>
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
/* ===== STYLES PWA ENTREPRISE BHDM ===== */
.enterprise-dashboard {
    padding: 16px;
    padding-bottom: 120px;
    min-height: 100vh;
    background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
    position: relative;
}

/* Carte mission BHDM */
.bhdm-mission-card {
    background: linear-gradient(135deg, #1b5a8d 0%, #0f3460 100%);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    box-shadow: 0 10px 30px rgba(27, 90, 141, 0.3);
    position: relative;
    overflow: hidden;
}

.bhdm-mission-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.bhdm-mission-card::after {
    content: '';
    position: absolute;
    bottom: -50%;
    left: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.mission-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
}

.bhdm-logo {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
}

.mission-title h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.3;
}

.mission-tagline {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 500;
}

.mission-quote {
    position: relative;
    z-index: 1;
    text-align: center;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    margin-top: 15px;
    backdrop-filter: blur(10px);
}

.mission-quote p {
    margin: 0;
    font-size: 0.95rem;
    font-style: italic;
    line-height: 1.4;
}

.mission-quote i {
    opacity: 0.5;
    font-size: 0.8rem;
}

.mission-quote i:first-child {
    margin-right: 10px;
}

.mission-quote i:last-child {
    margin-left: 10px;
}

/* Statut entreprise */
.enterprise-status-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.status-header {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
}

.enterprise-avatar {
    position: relative;
    width: 70px;
    height: 70px;
    flex-shrink: 0;
}

.enterprise-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 15px;
    object-fit: cover;
}

.avatar-fallback {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}

.verification-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 25px;
    height: 25px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    border: 3px solid white;
}

.enterprise-info {
    flex: 1;
}

.enterprise-info h1 {
    margin: 0 0 8px 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: #1e293b;
}

.enterprise-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 10px;
}

.meta-item {
    font-size: 0.85rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.enterprise-impact {
    margin-top: 10px;
}

.impact-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding-top: 15px;
    border-top: 1px solid #e2e8f0;
}

.stat-item {
    text-align: center;
}

.stat-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: white;
    font-size: 1.2rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-content p {
    margin: 5px 0 0 0;
    font-size: 0.85rem;
    color: #64748b;
}

/* Impact dashboard */
.impact-dashboard {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.impact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.impact-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.score-badge {
    padding: 6px 12px;
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.impact-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.metric-card {
    text-align: center;
    padding: 15px;
    border-radius: 15px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.metric-card:active {
    transform: scale(0.98);
}

.metric-card[data-metric="economic"] {
    background: rgba(27, 90, 141, 0.1);
    border: 1px solid rgba(27, 90, 141, 0.2);
}

.metric-card[data-metric="social"] {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.metric-card[data-metric="development"] {
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.2);
}

.metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.2rem;
}

.metric-card[data-metric="economic"] .metric-icon {
    background: #1b5a8d;
    color: white;
}

.metric-card[data-metric="social"] .metric-icon {
    background: #10b981;
    color: white;
}

.metric-card[data-metric="development"] .metric-icon {
    background: #8b5cf6;
    color: white;
}

.metric-content h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
}

.metric-content p {
    margin: 0 0 10px 0;
    font-size: 0.85rem;
    color: #64748b;
}

.metric-progress {
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    overflow: hidden;
}

.metric-card[data-metric="economic"] .progress-bar {
    background: #1b5a8d;
}

.metric-card[data-metric="social"] .progress-bar {
    background: #10b981;
}

.metric-card[data-metric="development"] .progress-bar {
    background: #8b5cf6;
}

.impact-actions {
    text-align: center;
}

.impact-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.impact-btn:active {
    transform: scale(0.98);
}

/* Portefeuille entreprise */
.enterprise-wallet-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.wallet-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.wallet-title h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.wallet-subtitle {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
}

.wallet-link {
    color: #1b5a8d;
    font-size: 1.1rem;
    text-decoration: none;
}

.wallet-content {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 15px;
    padding: 20px;
}

.balance-display {
    text-align: center;
    margin-bottom: 25px;
}

.balance-amount {
    margin-bottom: 10px;
}

.currency {
    font-size: 0.9rem;
    color: #64748b;
    display: block;
    margin-bottom: 5px;
}

.balance-amount h2 {
    margin: 0;
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

.balance-trend {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.balance-trend.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.balance-trend.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.wallet-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.wallet-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.wallet-action:active {
    transform: scale(0.95);
}

.wallet-action.deposit {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.wallet-action.withdraw {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.wallet-action.history {
    background: rgba(100, 116, 139, 0.1);
    color: #64748b;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.wallet-action.deposit .action-icon {
    background: #10b981;
    color: white;
}

.wallet-action.withdraw .action-icon {
    background: #ef4444;
    color: white;
}

.wallet-action.history .action-icon {
    background: #64748b;
    color: white;
}

.wallet-action span {
    font-size: 0.85rem;
    font-weight: 500;
}

/* Types de financement */
.funding-types {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
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
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.funding-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.funding-card {
    border-radius: 15px;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.funding-card.grant {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.funding-card.microcredit {
    background: linear-gradient(135deg, rgba(27, 90, 141, 0.1) 0%, rgba(27, 90, 141, 0.05) 100%);
    border: 1px solid rgba(27, 90, 141, 0.2);
}

.funding-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.funding-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.funding-card.grant .funding-icon {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
}

.funding-card.microcredit .funding-icon {
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
}

.funding-header h4 {
    margin: 0;
    font-size: 1rem;
    color: #1e293b;
    font-weight: 600;
}

.funding-body p {
    margin: 0 0 12px 0;
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.4;
}

.funding-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.funding-features li {
    font-size: 0.85rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 8px;
}

.funding-features i {
    color: #10b981;
    font-size: 0.8rem;
}

.funding-footer {
    margin-top: 15px;
}

.apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.apply-btn:active {
    transform: scale(0.98);
}

/* Projets en cours */
.active-projects {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.see-all {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    color: #1b5a8d;
    text-decoration: none;
    font-weight: 500;
}

.projects-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.project-card {
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.project-card:active {
    transform: scale(0.98);
    background: #f8fafc;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.project-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.project-badge.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
}

.project-badge.status-processing {
    background: rgba(59, 130, 246, 0.1);
    color: #1d4ed8;
}

.project-badge.status-approved {
    background: rgba(16, 185, 129, 0.1);
    color: #065f46;
}

.project-badge.status-rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #b91c1c;
}

.project-amount {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1b5a8d;
}

.project-body h4 {
    margin: 0 0 8px 0;
    font-size: 1rem;
    color: #1e293b;
    line-height: 1.3;
}

.project-description {
    margin: 0 0 12px 0;
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.4;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.meta-item {
    font-size: 0.8rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
}

.project-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e2e8f0;
}

.project-type {
    font-size: 0.85rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.project-actions .action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #1b5a8d;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.project-actions .action-btn:active {
    background: #1b5a8d;
    color: white;
}

.empty-projects {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: rgba(27, 90, 141, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: #1b5a8d;
}

.empty-projects h4 {
    margin: 0 0 10px 0;
    font-size: 1.2rem;
    color: #1e293b;
}

.empty-projects p {
    color: #64748b;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.start-project-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.start-project-btn:active {
    transform: scale(0.98);
}

/* Accompagnement */
.accompaniment-section {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.accompaniment-steps {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.step-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #1b5a8d;
}

.step-number {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    flex-shrink: 0;
}

.step-content h4 {
    margin: 0 0 5px 0;
    font-size: 0.95rem;
    color: #1e293b;
}

.step-content p {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
}

.accompaniment-cta {
    text-align: center;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    transition: transform 0.3s ease;
}

.cta-btn:active {
    transform: scale(0.98);
}

/* Récompenses */
.rewards-section {
    background: white;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.rewards-progress {
    padding: 20px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 15px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.level-badge {
    padding: 6px 12px;
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.progress-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 3px;
}

.points {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
}

.next-level {
    font-size: 0.8rem;
    color: #64748b;
}

.progress-bar-container {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    margin-bottom: 20px;
    overflow: hidden;
}

.rewards-progress .progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.rewards-benefits {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.benefit-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
}

.benefit-item i {
    font-size: 1.5rem;
    color: #1b5a8d;
}

.benefit-item span {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

/* ===== BOUTON FLOTTANT ===== */
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

.pwa-bottom-spacer {
    height: 120px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 480px) {
    .enterprise-dashboard {
        padding: 12px;
        padding-bottom: 100px;
    }

    .bhdm-mission-card,
    .enterprise-status-card,
    .impact-dashboard,
    .enterprise-wallet-card,
    .funding-types,
    .active-projects,
    .accompaniment-section,
    .rewards-section {
        padding: 16px;
    }

    .impact-metrics {
        grid-template-columns: 1fr;
    }

    .rewards-benefits {
        grid-template-columns: 1fr;
    }

    .pwa-floating-action-container {
        bottom: 80px;
        right: 15px;
    }

    .pwa-floating-action-button {
        width: 60px;
        height: 60px;
        font-size: 1.6rem;
    }

    .fab-label {
        display: none;
    }
}

/* Mode PWA standalone */
@media (display-mode: standalone) {
    .enterprise-dashboard {
        padding-top: calc(16px + env(safe-area-inset-top));
        padding-bottom: calc(120px + env(safe-area-inset-bottom));
    }

    .pwa-floating-action-container {
        bottom: calc(env(safe-area-inset-bottom) + 90px);
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bhdm-mission-card,
.enterprise-status-card,
.impact-dashboard,
.enterprise-wallet-card,
.funding-types,
.active-projects,
.accompaniment-section,
.rewards-section {
    animation: fadeIn 0.5s ease-out;
}

/* Support tactile */
@media (hover: none) and (pointer: coarse) {
    .metric-card:active,
    .project-card:active,
    .impact-btn:active,
    .apply-btn:active,
    .start-project-btn:active,
    .cta-btn:active {
        transform: scale(0.98);
    }

    .pwa-floating-action-button:hover {
        transform: none;
    }

    .fab-sub-btn:hover {
        transform: none;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du dashboard entreprise
    initializeEnterpriseDashboard();

    // Configuration des interactions PWA
    setupPWAInteractions();

    // Suivi de l'engagement
    trackUserEngagement();
});

function initializeEnterpriseDashboard() {
    // Animation de chargement des éléments
    const sections = document.querySelectorAll('.bhdm-mission-card, .enterprise-status-card, .impact-dashboard, .enterprise-wallet-card');
    sections.forEach((section, index) => {
        section.style.animationDelay = `${index * 0.1}s`;
    });

    // Configuration du bouton flottant
    setupFloatingActionButton();

    // Mise à jour en temps réel
    startRealTimeUpdates();
}

function setupFloatingActionButton() {
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

            // Empêcher le défilement
            document.body.style.overflow = 'hidden';

            // Vibration si supportée
            if ('vibrate' in navigator) {
                navigator.vibrate(30);
            }
        } else {
            mainFab.classList.remove('active');
            fabSubButtons.classList.remove('show');
            fabOverlay.classList.remove('active');

            // Rétablir le défilement
            document.body.style.overflow = '';
        }
    }

    mainFab.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleFabMenu();
    });

    fabOverlay.addEventListener('click', () => {
        if (fabOpen) toggleFabMenu();
    });

    // Fermer le menu en cliquant sur un bouton
    document.querySelectorAll('.fab-sub-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (fabOpen) {
                setTimeout(() => toggleFabMenu(), 100);
            }
        });
    });

    // Fermer le menu en faisant défiler
    let lastScrollTop = 0;
    window.addEventListener('scroll', () => {
        if (fabOpen && Math.abs(window.pageYOffset - lastScrollTop) > 5) {
            toggleFabMenu();
        }
        lastScrollTop = window.pageYOffset;
    });
}

function setupPWAInteractions() {
    // Détection du mode PWA
    const isPWA = window.matchMedia('(display-mode: standalone)').matches;

    if (isPWA) {
        // Optimisations spécifiques PWA
        document.body.classList.add('pwa-mode');

        // Gestion de la visibilité
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Mettre à jour les données lorsque l'utilisateur revient
                refreshDashboardData();
            }
        });
    }

    // Service Worker communication
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
            type: 'ENTERPRISE_DASHBOARD_OPENED',
            timestamp: Date.now()
        });
    }
}

function startRealTimeUpdates() {
    // Vérification périodique des mises à jour
    setInterval(() => {
        fetch('/api/enterprise/updates')
            .then(response => response.json())
            .then(data => {
                if (data.hasUpdates) {
                    updateDashboardMetrics(data);
                    showUpdateNotification('Nouvelles données disponibles');
                }
            })
            .catch(() => {
                // Mode hors ligne
                console.log('Mode hors ligne - Utilisation des données en cache');
            });
    }, 60000); // Toutes les minutes

    // Mise à jour initiale
    refreshDashboardData();
}

function refreshDashboardData() {
    // Simuler une mise à jour des données
    const metrics = document.querySelectorAll('.metric-content h4');
    metrics.forEach(metric => {
        const currentValue = parseInt(metric.textContent.replace(/[^0-9]/g, ''));
        if (!isNaN(currentValue)) {
            // Ajouter une petite variation pour simuler l'actualisation
            const newValue = currentValue + Math.floor(Math.random() * 10);
            metric.textContent = formatNumber(newValue) + (metric.textContent.includes('FCFA') ? ' FCFA' : '');

            // Mettre à jour la barre de progression
            const progressBar = metric.parentElement.querySelector('.progress-bar');
            if (progressBar) {
                const newWidth = Math.min(newValue / 100 * 100, 100);
                progressBar.style.width = `${newWidth}%`;
            }
        }
    });

    // Mettre à jour le score de fidélité
    const loyaltyPoints = document.querySelector('.points');
    if (loyaltyPoints) {
        const currentPoints = parseInt(loyaltyPoints.textContent);
        if (!isNaN(currentPoints)) {
            const newPoints = currentPoints + Math.floor(Math.random() * 5);
            loyaltyPoints.textContent = `${newPoints} points`;

            // Mettre à jour la barre de progression
            const progressBar = document.querySelector('.rewards-progress .progress-bar');
            if (progressBar) {
                const newWidth = Math.min(newPoints / 1000 * 100, 100);
                progressBar.style.width = `${newWidth}%`;
            }
        }
    }
}

function updateDashboardMetrics(data) {
    // Mettre à jour les métriques spécifiques
    if (data.total_approved) {
        const metric = document.querySelector('[data-metric="economic"] h4');
        if (metric) metric.textContent = formatNumber(data.total_approved) + ' FCFA';
    }

    if (data.jobs_created) {
        const metric = document.querySelector('[data-metric="social"] h4');
        if (metric) metric.textContent = formatNumber(data.jobs_created);
    }
}

function formatNumber(value) {
    return new Intl.NumberFormat('fr-FR').format(value);
}

function trackUserEngagement() {
    let engagementScore = 0;
    const lastVisit = localStorage.getItem('lastEnterpriseVisit');
    const now = Date.now();

    // Calculer le score d'engagement
    if (lastVisit) {
        const daysSinceLastVisit = Math.floor((now - lastVisit) / (1000 * 60 * 60 * 24));
        if (daysSinceLastVisit === 0) {
            engagementScore = 100; // Visite quotidienne
        } else if (daysSinceLastVisit <= 3) {
            engagementScore = 80; // Visite régulière
        } else if (daysSinceLastVisit <= 7) {
            engagementScore = 60; // Visite hebdomadaire
        } else {
            engagementScore = 40; // Visite occasionnelle
        }
    } else {
        engagementScore = 100; // Première visite
    }

    // Stocker la date de visite
    localStorage.setItem('lastEnterpriseVisit', now);

    // Mettre à jour l'affichage de l'engagement
    updateEngagementDisplay(engagementScore);

    // Envoyer les données d'engagement au serveur
    sendEngagementData(engagementScore);
}

function updateEngagementDisplay(score) {
    // Mettre à jour un indicateur visuel de l'engagement
    const engagementIndicator = document.createElement('div');
    engagementIndicator.className = 'engagement-indicator';
    engagementIndicator.innerHTML = `
        <div class="engagement-bar" style="width: ${score}%"></div>
    `;

    // Ajouter au dashboard
    const missionCard = document.querySelector('.bhdm-mission-card');
    if (missionCard) {
        missionCard.appendChild(engagementIndicator);
    }
}

function sendEngagementData(score) {
    // Stocker uniquement localement sans appel API
    storeEngagementDataLocally(score);

    // Journaliser pour le débogage
    console.log(`Engagement score: ${score}`);

    // Option: synchroniser plus tard si connecté
    if (navigator.onLine) {
        // Vous pourriez essayer une route client existante
        setTimeout(() => {
            fetch('/client/api/check-permission', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).catch(e => console.log('API non disponible'));
        }, 1000);
    }
}

function storeEngagementDataLocally(score) {
    const engagementData = JSON.parse(localStorage.getItem('engagementData') || '[]');
    engagementData.push({
        score: score,
        timestamp: Date.now()
    });
    localStorage.setItem('engagementData', JSON.stringify(engagementData));
}

function showUpdateNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'pwa-update-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-sync-alt"></i>
            <div>
                <strong>Actualisation</strong>
                <p>${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.querySelector('.enterprise-dashboard').prepend(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Styles supplémentaires pour l'engagement
const engagementStyles = document.createElement('style');
engagementStyles.textContent = `
    .engagement-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0 0 20px 20px;
        overflow: hidden;
    }

    .engagement-bar {
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        transition: width 0.5s ease;
    }

    .pwa-update-notification {
        background: linear-gradient(135deg, #1b5a8d 0%, #2c5282 100%);
        color: white;
        border-radius: 12px;
        margin-bottom: 15px;
        padding: 15px;
        animation: slideInDown 0.3s ease;
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .notification-content i:first-child {
        font-size: 1.2rem;
    }

    .notification-content div {
        flex: 1;
    }

    .notification-content strong {
        display: block;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .notification-content p {
        margin: 0;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .notification-content button {
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .notification-content button:hover {
        opacity: 1;
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(engagementStyles);
</script>
@endpush
@endsection
