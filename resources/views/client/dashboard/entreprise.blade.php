@extends('layouts.client')

@section('title', 'Tableau de bord - Entreprise BHDM')

@section('content')
<div class="pwa-dashboard enterprise-dashboard" id="enterprise-dashboard">
    <!-- Carte Mission BHDM -->
    <section class="bhdm-mission-card" aria-label="Mission BHDM">
        <div class="mission-header">
            <div class="bhdm-logo" aria-hidden="true">
                <i class="fas fa-hands-helping"></i>
            </div>
            <div class="mission-title">
                <h1>BHDM - Bureau Humanitaire pour le Développement Mondial</h1>
                <p class="mission-tagline">Créer • Préserver • Soutenir • Accompagner • Promouvoir</p>
            </div>
        </div>
        <blockquote class="mission-quote">
            <i class="fas fa-quote-left" aria-hidden="true"></i>
            <p>Luttons ensemble contre les inégalités et construisons une prospérité partagée</p>
            <i class="fas fa-quote-right" aria-hidden="true"></i>
        </blockquote>
    </section>

    <!-- Statut Entreprise -->
    <section class="enterprise-status-card" aria-label="Informations entreprise">
        <div class="status-header">
            <div class="enterprise-avatar">
                @if($user->company_logo ?? false)
                    <img src="{{ Storage::url($user->company_logo) }}"
                         alt="Logo {{ $user->company_name }}"
                         loading="lazy"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-fallback" style="display: none;">
                        <i class="fas fa-building" aria-hidden="true"></i>
                    </div>
                @else
                    <div class="avatar-fallback">
                        <i class="fas fa-building" aria-hidden="true"></i>
                    </div>
                @endif
                <div class="verification-badge" title="Entreprise vérifiée BHDM">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </div>
            </div>

            <div class="enterprise-info">
                <h2>{{ $user->company_name ?? 'Mon Entreprise' }}</h2>
                <div class="enterprise-meta">
                    <span class="meta-item">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        {{ $user->expected_jobs ?? 0 }} {{ ($user->expected_jobs ?? 0) == 1 ? 'employé' : 'employés' }}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-industry" aria-hidden="true"></i>
                        {{ $entrepriseStats['sector'] ?? 'Secteur non spécifié' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="status-stats">
            <div class="stat-item">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $entrepriseStats['total_projects'] ?? 0 }}</h3>
                    <p>Projets BHDM</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $entrepriseStats['projects_by_status']['approved'] ?? 0 }}</h3>
                    <p>Projets approuvés</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" aria-hidden="true">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="stat-content">
                    @php
                        $totalProjects = $entrepriseStats['total_projects'] ?? 0;
                        $approvedProjects = $entrepriseStats['projects_by_status']['approved'] ?? 0;
                        $successRate = $totalProjects > 0 ? round(($approvedProjects / $totalProjects) * 100, 1) : 0;
                    @endphp
                    <h3>{{ $successRate }}%</h3>
                    <p>Taux succès</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portefeuille Entreprise -->
    <section class="enterprise-wallet-card" aria-label="Portefeuille">
        <div class="wallet-header">
            <div class="wallet-title">
                <h3>
                    <i class="fas fa-piggy-bank" aria-hidden="true"></i>
                    Votre trésorerie BHDM
                </h3>
                <p class="wallet-subtitle">Fonds disponibles pour le développement</p>
            </div>
            <a href="{{ route('client.wallet.index') }}" class="wallet-link" aria-label="Accéder au portefeuille">
                <i class="fas fa-external-link-alt" aria-hidden="true"></i>
            </a>
        </div>

        <div class="wallet-content">
            <div class="balance-display">
                <div class="balance-amount">
                    <span class="currency">FCFA</span>
                    <h2>{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
                </div>
                @php
                    // CORRECTION: Vérifier si wallet_change est un tableau et extraire la valeur si nécessaire
                    $walletChangeRaw = $generalStats['wallet_change'] ?? 0;
                    
                    // Si c'est un tableau, prendre la première valeur numérique trouvée ou 0
                    if (is_array($walletChangeRaw)) {
                        $walletChange = 0;
                        // Essayer de trouver une valeur numérique dans le tableau
                        foreach ($walletChangeRaw as $key => $value) {
                            if (is_numeric($value)) {
                                $walletChange = (float) $value;
                                break;
                            }
                        }
                    } else {
                        $walletChange = is_numeric($walletChangeRaw) ? (float) $walletChangeRaw : 0;
                    }
                @endphp
                <div class="balance-trend {{ $walletChange >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $walletChange >= 0 ? 'up' : 'down' }}" aria-hidden="true"></i>
                    <span>{{ abs($walletChange) }}% ce mois</span>
                </div>
            </div>

            <div class="wallet-actions">
                <a href="{{ route('client.wallet.index') }}?action=deposit" class="wallet-action deposit">
                    <div class="action-icon" aria-hidden="true">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <span>Déposer</span>
                </a>
                <a href="{{ route('client.wallet.index') }}?action=withdraw" class="wallet-action withdraw">
                    <div class="action-icon" aria-hidden="true">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <span>Retirer</span>
                </a>
                <a href="{{ route('client.wallet.transactions') }}" class="wallet-action history">
                    <div class="action-icon" aria-hidden="true">
                        <i class="fas fa-history"></i>
                    </div>
                    <span>Historique</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Types de Financement -->
    <section class="funding-types" aria-label="Solutions de financement">
        <div class="section-header">
            <h3>
                <i class="fas fa-hand-holding-usd" aria-hidden="true"></i>
                Nos solutions de financement
            </h3>
        </div>

        <div class="funding-cards">
            <article class="funding-card grant">
                <div class="funding-header">
                    <div class="funding-icon" aria-hidden="true">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h4>Subventions d'exploitation</h4>
                </div>
                <div class="funding-body">
                    <p>Aide financière non remboursable pour les charges courantes</p>
                    <ul class="funding-features">
                        <li><i class="fas fa-check" aria-hidden="true"></i> Non remboursable</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Charges courantes</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Aide immédiate</li>
                    </ul>
                </div>
                <div class="funding-footer">
                    <a href="{{ route('client.requests.create') }}?type=grant" class="apply-btn">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        Demander
                    </a>
                </div>
            </article>

            <article class="funding-card microcredit">
                <div class="funding-header">
                    <div class="funding-icon" aria-hidden="true">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4>Microcrédits solidaires</h4>
                </div>
                <div class="funding-body">
                    <p>Petits prêts avec accompagnement pour projets personnels ou professionnels</p>
                    <ul class="funding-features">
                        <li><i class="fas fa-check" aria-hidden="true"></i> Taux réduits</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Accompagnement inclus</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Accessible à tous</li>
                    </ul>
                </div>
                <div class="funding-footer">
                    <a href="{{ route('client.requests.create') }}?type=microcredit" class="apply-btn">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        Demander
                    </a>
                </div>
            </article>
        </div>
    </section>

    <!-- Projets Actifs -->
    <section class="active-projects" aria-label="Projets en cours">
        <div class="section-header">
            <h3>
                <i class="fas fa-tasks" aria-hidden="true"></i>
                Vos projets actifs
            </h3>
            <a href="{{ route('client.requests.index') }}" class="see-all">
                Tout voir
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </a>
        </div>

        @if(isset($requests) && $requests->count() > 0)
            <div class="projects-list">
                @foreach($requests->take(3) as $project)
                    <article class="project-card" onclick="navigateTo('{{ route('client.requests.show', $project->id) }}')" role="button" tabindex="0">
                        <div class="project-header">
                            <div class="project-badge status-{{ $project->status }}">
                                {{ ucfirst($project->status) }}
                            </div>
                            <div class="project-amount">
                                {{ number_format($project->amount_requested ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>

                        <div class="project-body">
                            <h4>{{ Str::limit($project->title, 40) }}</h4>
                            <p class="project-description">{{ Str::limit($project->description ?? 'Aucune description', 60) }}</p>

                            <div class="project-meta">
                                <span class="meta-item">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    {{ $project->created_at->format('d/m/Y') }}
                                </span>
                                @if(($project->expected_jobs ?? 0) > 0)
                                    <span class="meta-item">
                                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                                        {{ $project->expected_jobs }} emplois
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="project-footer">
                            <div class="project-type">
                                @switch($project->category ?? 'default')
                                    @case('agriculture')
                                        <i class="fas fa-seedling" aria-hidden="true"></i> Agriculture
                                        @break
                                    @case('technology')
                                        <i class="fas fa-laptop-code" aria-hidden="true"></i> Technologie
                                        @break
                                    @case('commerce')
                                        <i class="fas fa-store" aria-hidden="true"></i> Commerce
                                        @break
                                    @default
                                        <i class="fas fa-briefcase" aria-hidden="true"></i> {{ ucfirst($project->category ?? 'Général') }}
                                @endswitch
                            </div>
                            <div class="project-actions">
                                <button class="action-btn" onclick="event.stopPropagation(); navigateTo('{{ route('client.requests.show', $project->id) }}')" aria-label="Voir le projet">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-projects">
                <div class="empty-icon" aria-hidden="true">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h4>Commencez votre premier projet</h4>
                <p>Créez votre premier projet de développement avec l'accompagnement BHDM</p>
                <a href="{{ route('client.requests.create') }}" class="start-project-btn">
                    <i class="fas fa-rocket" aria-hidden="true"></i>
                    Lancer un projet
                </a>
            </div>
        @endif
    </section>

    <!-- Accompagnement BHDM -->
    <section class="accompaniment-section" aria-label="Processus d'accompagnement">
        <div class="section-header">
            <h3>
                <i class="fas fa-hands-helping" aria-hidden="true"></i>
                Votre accompagnement BHDM
            </h3>
        </div>

        <div class="accompaniment-steps">
            <div class="step-card">
                <div class="step-number" aria-hidden="true">1</div>
                <div class="step-content">
                    <h4>Diagnostic personnalisé</h4>
                    <p>Analyse approfondie de vos besoins et potentiel</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number" aria-hidden="true">2</div>
                <div class="step-content">
                    <h4>Financement adapté</h4>
                    <p>Solution financière sur mesure pour votre projet</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number" aria-hidden="true">3</div>
                <div class="step-content">
                    <h4>Suivi & Formation</h4>
                    <p>Accompagnement continu et développement de compétences</p>
                </div>
            </div>
        </div>

        <div class="accompaniment-cta">
            <a href="{{ route('client.support.create') }}" class="cta-btn">
                <i class="fas fa-comments" aria-hidden="true"></i>
                Demander un accompagnement
            </a>
        </div>
    </section>

    <!-- Récompenses et Fidélité -->
    <section class="rewards-section" aria-label="Programme de fidélité">
        <div class="section-header">
            <h3>
                <i class="fas fa-award" aria-hidden="true"></i>
                Votre fidélité récompensée
            </h3>
        </div>

        <div class="rewards-progress">
            @php
                $currentPoints = $entrepriseStats['loyalty_points'] ?? 0;
                $nextLevelPoints = 1000;
                $progressPercent = min(($currentPoints / $nextLevelPoints) * 100, 100);
                $pointsToNext = max($nextLevelPoints - $currentPoints, 0);
            @endphp

            <div class="progress-info">
                <div class="current-level">
                    <span class="level-badge">Niveau {{ $entrepriseStats['loyalty_level'] ?? 1 }}</span>
                </div>
                <div class="progress-stats">
                    <span class="points">{{ $currentPoints }} points</span>
                    <span class="next-level">Prochain niveau: {{ $pointsToNext }} points</span>
                </div>
            </div>

            <div class="progress-bar-container" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: {{ $progressPercent }}%"></div>
            </div>

            <div class="rewards-benefits">
                <div class="benefit-item">
                    <i class="fas fa-percentage" aria-hidden="true"></i>
                    <span>Taux préférentiel</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-rocket" aria-hidden="true"></i>
                    <span>Traitement prioritaire</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-gift" aria-hidden="true"></i>
                    <span>Bonus fidélité</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Bouton Flottant d'Action -->
    <div class="pwa-floating-action-container">
        <button class="pwa-floating-action-button" id="mainFab" aria-label="Menu rapide" aria-expanded="false" aria-controls="fab-menu">
            <i class="fas fa-plus" aria-hidden="true"></i>
            <span class="fab-label">Actions rapides</span>
        </button>

        <nav class="fab-sub-buttons" id="fab-menu" aria-hidden="true" aria-label="Actions rapides">
            <a href="{{ route('client.requests.create') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon request-icon" aria-hidden="true">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <span class="fab-sub-label">Nouveau projet</span>
                </div>
            </a>
            <a href="{{ route('client.trainings') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon training-icon" aria-hidden="true">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="fab-sub-label">Formations</span>
                </div>
            </a>
            <a href="{{ route('client.documents.upload') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon document-icon" aria-hidden="true">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <span class="fab-sub-label">Documents</span>
                </div>
            </a>
            <a href="{{ route('client.support.create') }}" class="fab-sub-btn">
                <div class="fab-sub-content">
                    <div class="fab-sub-icon support-icon" aria-hidden="true">
                        <i class="fas fa-headset"></i>
                    </div>
                    <span class="fab-sub-label">Support</span>
                </div>
            </a>
        </nav>
    </div>

    <div class="fab-overlay" id="fabOverlay" aria-hidden="true"></div>
    <div class="pwa-bottom-spacer"></div>
</div>

<style>
/* ============================================
   VARIABLES CSS & CONFIGURATION
   ============================================ */
:root {
    --primary-color: #1b5a8d;
    --primary-dark: #0f3460;
    --primary-light: #2c5282;
    --success-color: #10b981;
    --success-light: #34d399;
    --warning-color: #f59e0b;
    --warning-light: #fbbf24;
    --danger-color: #ef4444;
    --purple-color: #8b5cf6;
    --purple-light: #a78bfa;
    --gray-color: #64748b;
    --gray-light: #94a3b8;
    --bg-color: #f8fafc;
    --card-bg: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================
   LAYOUT DE BASE
   ============================================ */
.enterprise-dashboard {
    padding: 16px;
    padding-bottom: 120px;
    min-height: 100vh;
    background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
    position: relative;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ============================================
   CARTE MISSION BHDM
   ============================================ */
.bhdm-mission-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    color: white;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.bhdm-mission-card::before,
.bhdm-mission-card::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    pointer-events: none;
}

.bhdm-mission-card::before {
    top: -30%;
    right: -20%;
    width: 250px;
    height: 250px;
}

.bhdm-mission-card::after {
    bottom: -40%;
    left: -30%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
}

.mission-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.bhdm-logo {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
}

.mission-title h1 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.01em;
}

.mission-tagline {
    margin: 6px 0 0 0;
    font-size: 0.85rem;
    opacity: 0.9;
    font-weight: 500;
    letter-spacing: 0.02em;
}

.mission-quote {
    position: relative;
    z-index: 1;
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-lg);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    margin: 0;
}

.mission-quote p {
    margin: 0;
    font-size: 1rem;
    font-style: italic;
    line-height: 1.5;
    font-weight: 500;
}

.mission-quote i {
    opacity: 0.6;
    font-size: 0.9rem;
}

.mission-quote i:first-child {
    margin-right: 12px;
    vertical-align: super;
}

.mission-quote i:last-child {
    margin-left: 12px;
    vertical-align: sub;
}

/* ============================================
   STATUT ENTREPRISE
   ============================================ */
.enterprise-status-card {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.status-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 24px;
}

.enterprise-avatar {
    position: relative;
    width: 72px;
    height: 72px;
    flex-shrink: 0;
}

.enterprise-avatar img {
    width: 100%;
    height: 100%;
    border-radius: var(--radius-lg);
    object-fit: cover;
    box-shadow: var(--shadow-md);
}

.avatar-fallback {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: var(--shadow-md);
}

.verification-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 28px;
    height: 28px;
    background: var(--success-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    border: 3px solid white;
    box-shadow: var(--shadow-sm);
}

.enterprise-info {
    flex: 1;
    min-width: 0;
}

.enterprise-info h2 {
    margin: 0 0 10px 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.3;
    word-wrap: break-word;
}

.enterprise-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.meta-item {
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.meta-item i {
    color: var(--primary-color);
    width: 16px;
    text-align: center;
}

.status-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.stat-item {
    text-align: center;
    padding: 12px 8px;
    border-radius: var(--radius-md);
    transition: background var(--transition-fast);
}

.stat-item:hover {
    background: var(--bg-color);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 1.25rem;
    box-shadow: var(--shadow-md);
}

.stat-content h3 {
    margin: 0 0 4px 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.stat-content p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
}

/* ============================================
   IMPACT DASHBOARD
   ============================================ */
.impact-dashboard {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.impact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.impact-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.impact-header h3 i {
    color: var(--warning-color);
}

.score-badge {
    padding: 6px 14px;
    background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    box-shadow: var(--shadow-sm);
}

.impact-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}

.metric-card {
    text-align: center;
    padding: 20px 16px;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all var(--transition-normal);
    border: 2px solid transparent;
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.metric-card:active {
    transform: scale(0.98);
}

.metric-card[data-metric="economic"] {
    background: rgba(27, 90, 141, 0.08);
    border-color: rgba(27, 90, 141, 0.15);
}

.metric-card[data-metric="economic"]:hover {
    background: rgba(27, 90, 141, 0.12);
}

.metric-card[data-metric="social"] {
    background: rgba(16, 185, 129, 0.08);
    border-color: rgba(16, 185, 129, 0.15);
}

.metric-card[data-metric="social"]:hover {
    background: rgba(16, 185, 129, 0.12);
}

.metric-card[data-metric="development"] {
    background: rgba(139, 92, 246, 0.08);
    border-color: rgba(139, 92, 246, 0.15);
}

.metric-card[data-metric="development"]:hover {
    background: rgba(139, 92, 246, 0.12);
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 1.5rem;
    color: white;
    box-shadow: var(--shadow-md);
}

.metric-card[data-metric="economic"] .metric-icon {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
}

.metric-card[data-metric="social"] .metric-icon {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
}

.metric-card[data-metric="development"] .metric-icon {
    background: linear-gradient(135deg, var(--purple-color) 0%, var(--purple-light) 100%);
}

.metric-content h4 {
    margin: 0 0 6px 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
}

.metric-content p {
    margin: 0 0 12px 0;
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.metric-progress {
    height: 6px;
    background: rgba(0, 0, 0, 0.08);
    border-radius: 3px;
    overflow: hidden;
}

.metric-card[data-metric="economic"] .progress-bar {
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
}

.metric-card[data-metric="social"] .progress-bar {
    background: linear-gradient(90deg, var(--success-color), var(--success-light));
}

.metric-card[data-metric="development"] .progress-bar {
    background: linear-gradient(90deg, var(--purple-color), var(--purple-light));
}

.impact-actions {
    text-align: center;
}

.impact-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: 0 4px 14px rgba(27, 90, 141, 0.3);
}

.impact-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(27, 90, 141, 0.4);
}

.impact-btn:active {
    transform: scale(0.98);
}

/* ============================================
   PORTEFEUILLE ENTREPRISE
   ============================================ */
.enterprise-wallet-card {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.wallet-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.wallet-title h3 {
    margin: 0 0 6px 0;
    font-size: 1.1rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.wallet-title h3 i {
    color: var(--primary-color);
}

.wallet-subtitle {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.wallet-link {
    color: var(--primary-color);
    font-size: 1.2rem;
    text-decoration: none;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all var(--transition-fast);
}

.wallet-link:hover {
    background: rgba(27, 90, 141, 0.1);
}

.wallet-content {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: var(--radius-lg);
    padding: 24px;
    border: 1px solid var(--border-color);
}

.balance-display {
    text-align: center;
    margin-bottom: 24px;
}

.balance-amount {
    margin-bottom: 12px;
}

.currency {
    font-size: 1rem;
    color: var(--text-secondary);
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    letter-spacing: 0.05em;
}

.balance-amount h2 {
    margin: 0;
    font-size: 2.75rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    letter-spacing: -0.02em;
}

.balance-trend {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.balance-trend.positive {
    background: rgba(16, 185, 129, 0.15);
    color: var(--success-color);
}

.balance-trend.negative {
    background: rgba(239, 68, 68, 0.15);
    color: var(--danger-color);
}

.wallet-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.wallet-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 16px 12px;
    border-radius: var(--radius-md);
    text-decoration: none;
    transition: all var(--transition-fast);
    font-weight: 600;
}

.wallet-action:hover {
    transform: translateY(-2px);
}

.wallet-action:active {
    transform: scale(0.95);
}

.wallet-action.deposit {
    background: rgba(16, 185, 129, 0.12);
    color: var(--success-color);
}

.wallet-action.deposit:hover {
    background: rgba(16, 185, 129, 0.2);
}

.wallet-action.withdraw {
    background: rgba(239, 68, 68, 0.12);
    color: var(--danger-color);
}

.wallet-action.withdraw:hover {
    background: rgba(239, 68, 68, 0.2);
}

.wallet-action.history {
    background: rgba(100, 116, 139, 0.12);
    color: var(--gray-color);
}

.wallet-action.history:hover {
    background: rgba(100, 116, 139, 0.2);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: var(--shadow-md);
}

.wallet-action.deposit .action-icon {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
}

.wallet-action.withdraw .action-icon {
    background: linear-gradient(135deg, var(--danger-color) 0%, #f87171 100%);
}

.wallet-action.history .action-icon {
    background: linear-gradient(135deg, var(--gray-color) 0%, var(--gray-light) 100%);
}

.wallet-action span {
    font-size: 0.85rem;
}

/* ============================================
   TYPES DE FINANCEMENT
   ============================================ */
.funding-types {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
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

.funding-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

.funding-card {
    border-radius: var(--radius-lg);
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: all var(--transition-normal);
    border: 2px solid transparent;
}

.funding-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.funding-card.grant {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.03) 100%);
    border-color: rgba(16, 185, 129, 0.2);
}

.funding-card.microcredit {
    background: linear-gradient(135deg, rgba(27, 90, 141, 0.08) 0%, rgba(27, 90, 141, 0.03) 100%);
    border-color: rgba(27, 90, 141, 0.2);
}

.funding-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.funding-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
    box-shadow: var(--shadow-md);
    flex-shrink: 0;
}

.funding-card.grant .funding-icon {
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
}

.funding-card.microcredit .funding-icon {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
}

.funding-header h4 {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
    font-weight: 700;
    line-height: 1.3;
}

.funding-body p {
    margin: 0 0 16px 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.funding-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.funding-features li {
    font-size: 0.875rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.funding-features i {
    color: var(--success-color);
    font-size: 0.9rem;
}

.funding-footer {
    margin-top: 20px;
}

.apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.3);
}

.apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(27, 90, 141, 0.4);
}

.apply-btn:active {
    transform: scale(0.98);
}

/* ============================================
   PROJETS ACTIFS
   ============================================ */
.active-projects {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.see-all {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: gap var(--transition-fast);
}

.see-all:hover {
    gap: 10px;
}

.projects-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.project-card {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 20px;
    cursor: pointer;
    transition: all var(--transition-normal);
    background: white;
}

.project-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.project-card:active {
    transform: scale(0.99);
}

.project-card:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    flex-wrap: wrap;
    gap: 8px;
}

.project-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.project-badge.status-pending {
    background: rgba(245, 158, 11, 0.15);
    color: #d97706;
}

.project-badge.status-processing {
    background: rgba(59, 130, 246, 0.15);
    color: #1d4ed8;
}

.project-badge.status-approved {
    background: rgba(16, 185, 129, 0.15);
    color: #065f46;
}

.project-badge.status-rejected {
    background: rgba(239, 68, 68, 0.15);
    color: #b91c1c;
}

.project-badge.status-funded {
    background: rgba(139, 92, 246, 0.15);
    color: #7c3aed;
}

.project-amount {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--primary-color);
}

.project-body h4 {
    margin: 0 0 8px 0;
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 600;
    line-height: 1.4;
}

.project-description {
    margin: 0 0 12px 0;
    font-size: 0.85rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.project-meta .meta-item {
    font-size: 0.8rem;
}

.project-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

.project-type {
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.project-type i {
    color: var(--primary-color);
}

.project-actions .action-btn {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    border: 2px solid var(--border-color);
    background: white;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    font-size: 1rem;
}

.project-actions .action-btn:hover {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
}

.project-actions .action-btn:active {
    transform: scale(0.95);
}

.empty-projects {
    text-align: center;
    padding: 48px 24px;
}

.empty-icon {
    width: 88px;
    height: 88px;
    background: rgba(27, 90, 141, 0.1);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 2.5rem;
    color: var(--primary-color);
}

.empty-projects h4 {
    margin: 0 0 12px 0;
    font-size: 1.25rem;
    color: var(--text-primary);
    font-weight: 600;
}

.empty-projects p {
    color: var(--text-secondary);
    margin-bottom: 24px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.start-project-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: 0 4px 14px rgba(27, 90, 141, 0.3);
}

.start-project-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(27, 90, 141, 0.4);
}

.start-project-btn:active {
    transform: scale(0.98);
}

/* ============================================
   ACCOMPAGNEMENT
   ============================================ */
.accompaniment-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.accompaniment-steps {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 24px;
}

.step-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--bg-color);
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--primary-color);
    transition: all var(--transition-fast);
}

.step-card:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.step-number {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: var(--shadow-md);
}

.step-content h4 {
    margin: 0 0 6px 0;
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 600;
}

.step-content p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.accompaniment-cta {
    text-align: center;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%);
    color: white;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-normal);
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}

.cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.cta-btn:active {
    transform: scale(0.98);
}

/* ============================================
   RÉCOMPENSES
   ============================================ */
.rewards-section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.rewards-progress {
    padding: 24px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.level-badge {
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%);
    color: white;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    box-shadow: var(--shadow-sm);
}

.progress-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}

.points {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
}

.next-level {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.progress-bar-container {
    height: 10px;
    background: #e2e8f0;
    border-radius: 5px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.rewards-progress .progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--warning-color), var(--warning-light));
    border-radius: 5px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
}

.rewards-benefits {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 16px;
}

.benefit-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-align: center;
    padding: 16px;
    background: white;
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.benefit-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.benefit-item i {
    font-size: 1.75rem;
    color: var(--primary-color);
}

.benefit-item span {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 600;
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
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 10px 30px rgba(27, 90, 141, 0.4);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    z-index: 1002;
}

.pwa-floating-action-button:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 15px 40px rgba(27, 90, 141, 0.5);
}

.pwa-floating-action-button.active {
    transform: rotate(45deg);
    background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%);
}

.fab-label {
    position: absolute;
    right: 75px;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 10px 18px;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
    pointer-events: none;
    font-weight: 600;
    box-shadow: var(--shadow-lg);
}

.pwa-floating-action-button:hover .fab-label {
    opacity: 1;
    visibility: visible;
    right: 80px;
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
    box-shadow: var(--shadow-xl);
    transition: all var(--transition-fast);
    overflow: hidden;
    opacity: 0;
    transform: translateX(50px);
    border: 2px solid transparent;
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
    transform: translateX(-8px);
    border-color: var(--primary-color);
}

.fab-sub-content {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
}

.fab-sub-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
    box-shadow: var(--shadow-md);
}

.request-icon { background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); }
.training-icon { background: linear-gradient(135deg, var(--success-color) 0%, var(--success-light) 100%); }
.document-icon { background: linear-gradient(135deg, var(--purple-color) 0%, var(--purple-light) 100%); }
.support-icon { background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-light) 100%); }

.fab-sub-label {
    font-size: 1rem;
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
    background: rgba(0, 0, 0, 0.6);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
    backdrop-filter: blur(4px);
}

.fab-overlay.active {
    opacity: 1;
    visibility: visible;
}

.pwa-bottom-spacer {
    height: 120px;
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
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
    animation: fadeInUp 0.6s ease-out backwards;
}

.bhdm-mission-card { animation-delay: 0.1s; }
.enterprise-status-card { animation-delay: 0.2s; }
.impact-dashboard { animation-delay: 0.3s; }
.enterprise-wallet-card { animation-delay: 0.4s; }
.funding-types { animation-delay: 0.5s; }
.active-projects { animation-delay: 0.6s; }
.accompaniment-section { animation-delay: 0.7s; }
.rewards-section { animation-delay: 0.8s; }

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.verification-badge {
    animation: pulse 2s infinite;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.pwa-floating-action-button {
    animation: slideInRight 0.5s ease-out 1s backwards;
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */
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
        padding: 20px;
        margin-bottom: 16px;
    }

    .mission-title h1 {
        font-size: 1rem;
    }

    .impact-metrics {
        grid-template-columns: 1fr;
    }

    .status-stats {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .wallet-actions {
        grid-template-columns: 1fr;
    }

    .rewards-benefits {
        grid-template-columns: 1fr;
    }

    .pwa-floating-action-container {
        bottom: 80px;
        right: 16px;
    }

    .pwa-floating-action-button {
        width: 56px;
        height: 56px;
        font-size: 1.25rem;
    }

    .fab-label {
        display: none;
    }

    .fab-sub-buttons {
        min-width: 180px;
    }

    .fab-sub-content {
        padding: 14px 16px;
    }
}

@media (min-width: 768px) {
    .funding-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .impact-metrics {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Mode PWA Standalone */
@media (display-mode: standalone) {
    .enterprise-dashboard {
        padding-top: calc(20px + env(safe-area-inset-top));
        padding-bottom: calc(140px + env(safe-area-inset-bottom));
    }

    .pwa-floating-action-container {
        bottom: calc(env(safe-area-inset-bottom) + 90px);
    }

    .bhdm-mission-card {
        margin-top: env(safe-area-inset-top);
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
        --border-color: #334155;
    }

    .enterprise-dashboard {
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    }

    .bhdm-mission-card::before,
    .bhdm-mission-card::after {
        background: rgba(255, 255, 255, 0.05);
    }

    .wallet-content,
    .rewards-progress,
    .step-card {
        background: #334155;
        border-color: #475569;
    }

    .metric-card[data-metric="economic"] {
        background: rgba(96, 165, 250, 0.1);
        border-color: rgba(96, 165, 250, 0.2);
    }

    .metric-card[data-metric="social"] {
        background: rgba(52, 211, 153, 0.1);
        border-color: rgba(52, 211, 153, 0.2);
    }

    .metric-card[data-metric="development"] {
        background: rgba(167, 139, 250, 0.1);
        border-color: rgba(167, 139, 250, 0.2);
    }

    .project-card {
        background: var(--card-bg);
        border-color: #475569;
    }

    .project-card:hover {
        border-color: #60a5fa;
    }

    .pwa-floating-action-button {
        border-color: var(--card-bg);
    }

    .fab-sub-btn {
        background: var(--card-bg);
        border-color: #475569;
    }

    .fab-sub-label {
        color: #e5e7eb;
    }

    .empty-icon {
        background: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
    }

    .benefit-item {
        background: #334155;
    }

    .benefit-item i {
        color: #60a5fa;
    }
}

/* ============================================
   ACCESSIBILITÉ
   ============================================ */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }

    .verification-badge {
        animation: none;
    }
}

/* Focus visible pour navigation clavier */
.project-card:focus-visible,
.metric-card:focus-visible,
.wallet-action:focus-visible,
.apply-btn:focus-visible,
.impact-btn:focus-visible,
.start-project-btn:focus-visible,
.cta-btn:focus-visible,
.fab-sub-btn:focus-visible {
    outline: 3px solid var(--primary-color);
    outline-offset: 2px;
}

/* Support tactile amélioré */
@media (hover: none) and (pointer: coarse) {
    .metric-card:hover,
    .project-card:hover,
    .funding-card:hover,
    .step-card:hover,
    .benefit-item:hover {
        transform: none;
    }

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
        transform: scale(0.98);
    }
}

/* ============================================
   UTILITAIRES
   ============================================ */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
</style>

@push('scripts')
<script>
(function() {
    'use strict';

    // ==========================================
    // CONFIGURATION
    // ==========================================
    const CONFIG = {
        animationDelay: 100,
        refreshInterval: 60000,
        vibrateDuration: 30
    };

    // ==========================================
    // UTILITAIRES
    // ==========================================
    const utils = {
        formatNumber: (num) => new Intl.NumberFormat('fr-FR').format(num),

        vibrate: (pattern = CONFIG.vibrateDuration) => {
            if ('vibrate' in navigator) navigator.vibrate(pattern);
        },

        debounce: (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: (func, limit) => {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    };

    // ==========================================
    // GESTION DU FAB (Floating Action Button)
    // ==========================================
    class FabController {
        constructor() {
            this.mainFab = document.getElementById('mainFab');
            this.fabMenu = document.getElementById('fab-menu');
            this.fabOverlay = document.getElementById('fabOverlay');
            this.isOpen = false;

            if (this.mainFab && this.fabMenu && this.fabOverlay) {
                this.init();
            }
        }

        init() {
            this.mainFab.addEventListener('click', (e) => this.toggle(e));
            this.fabOverlay.addEventListener('click', () => this.close());

            // Fermer avec Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) this.close();
            });

            // Fermer au scroll
            let lastScroll = 0;
            window.addEventListener('scroll', utils.throttle(() => {
                const currentScroll = window.pageYOffset;
                if (this.isOpen && Math.abs(currentScroll - lastScroll) > 5) {
                    this.close();
                }
                lastScroll = currentScroll;
            }, 100));

            // Fermer au clic sur un sous-bouton
            this.fabMenu.querySelectorAll('.fab-sub-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    setTimeout(() => this.close(), 150);
                });
            });
        }

        toggle(e) {
            e.stopPropagation();
            this.isOpen ? this.close() : this.open();
        }

        open() {
            this.isOpen = true;
            this.mainFab.classList.add('active');
            this.mainFab.setAttribute('aria-expanded', 'true');
            this.fabMenu.classList.add('show');
            this.fabMenu.setAttribute('aria-hidden', 'false');
            this.fabOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            utils.vibrate();
        }

        close() {
            this.isOpen = false;
            this.mainFab.classList.remove('active');
            this.mainFab.setAttribute('aria-expanded', 'false');
            this.fabMenu.classList.remove('show');
            this.fabMenu.setAttribute('aria-hidden', 'true');
            this.fabOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // ==========================================
    // GESTION DES DONNÉES EN TEMPS RÉEL
    // ==========================================
    class DataManager {
        constructor() {
            this.cache = new Map();
            this.init();
        }

        init() {
            this.setupVisibilityHandler();
            this.startPeriodicRefresh();
        }

        setupVisibilityHandler() {
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.refreshData();
                }
            });
        }

        startPeriodicRefresh() {
            setInterval(() => this.refreshData(), CONFIG.refreshInterval);
        }

        async refreshData() {
            try {
                // Simulation de récupération de données
                // Remplacez par votre vraie API
                const mockData = this.generateMockData();
                this.updateUI(mockData);
            } catch (error) {
                console.warn('Mode hors ligne - Utilisation du cache');
                this.loadFromCache();
            }
        }

        generateMockData() {
            // Simulation de variations de données
            return {
                totalApproved: Math.floor(Math.random() * 1000000),
                jobsCreated: Math.floor(Math.random() * 50),
                trainingCompleted: Math.floor(Math.random() * 10),
                loyaltyPoints: Math.floor(Math.random() * 1000)
            };
        }

        updateUI(data) {
            // Mise à jour des métriques économiques
            const economicMetric = document.querySelector('[data-metric="economic"] h4');
            if (economicMetric) {
                economicMetric.textContent = utils.formatNumber(data.totalApproved) + ' FCFA';
            }

            // Mise à jour des emplois
            const socialMetric = document.querySelector('[data-metric="social"] h4');
            if (socialMetric) {
                socialMetric.textContent = data.jobsCreated;
            }

            // Mise à jour des formations
            const trainingMetric = document.querySelector('[data-metric="development"] h4');
            if (trainingMetric) {
                trainingMetric.textContent = data.trainingCompleted;
            }

            // Notification de mise à jour
            this.showUpdateNotification();
        }

        showUpdateNotification() {
            const existing = document.querySelector('.update-notification');
            if (existing) return;

            const notification = document.createElement('div');
            notification.className = 'update-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-sync-alt fa-spin"></i>
                    <span>Données actualisées</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        loadFromCache() {
            // Implémentez le chargement depuis localStorage ou Cache API
            console.log('Chargement depuis le cache...');
        }
    }

    // ==========================================
    // GESTION DE L'ENGAGEMENT UTILISATEUR
    // ==========================================
    class EngagementTracker {
        constructor() {
            this.storageKey = 'bhdm_enterprise_engagement';
            this.init();
        }

        init() {
            this.trackVisit();
            this.calculateEngagementScore();
        }

        trackVisit() {
            const visits = this.getVisits();
            visits.push({
                timestamp: Date.now(),
                path: window.location.pathname
            });

            // Garder seulement les 30 derniers jours
            const thirtyDaysAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
            const recentVisits = visits.filter(v => v.timestamp > thirtyDaysAgo);

            localStorage.setItem(this.storageKey, JSON.stringify(recentVisits));
        }

        getVisits() {
            try {
                return JSON.parse(localStorage.getItem(this.storageKey)) || [];
            } catch {
                return [];
            }
        }

        calculateEngagementScore() {
            const visits = this.getVisits();
            const uniqueDays = new Set(visits.map(v =>
                new Date(v.timestamp).toDateString()
            )).size;

            let score = 0;
            if (uniqueDays >= 20) score = 100;
            else if (uniqueDays >= 10) score = 75;
            else if (uniqueDays >= 5) score = 50;
            else score = 25;

            this.displayEngagement(score);
        }

        displayEngagement(score) {
            // Créer un indicateur d'engagement si non existant
            let indicator = document.querySelector('.engagement-indicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.className = 'engagement-indicator';
                indicator.innerHTML = `
                    <div class="engagement-bar" style="width: 0%"></div>
                    <span class="engagement-text">Engagement: ${score}%</span>
                `;

                const missionCard = document.querySelector('.bhdm-mission-card');
                if (missionCard) {
                    missionCard.appendChild(indicator);
                }
            }

            // Animer la barre
            setTimeout(() => {
                const bar = indicator.querySelector('.engagement-bar');
                if (bar) bar.style.width = score + '%';
            }, 500);
        }
    }

    // ==========================================
    // NAVIGATION
    // ==========================================
    window.navigateTo = function(url) {
        if (url) window.location.href = url;
    };

    // ==========================================
    // INITIALISATION
    // ==========================================
    function init() {
        new FabController();
        new DataManager();
        new EngagementTracker();

        // Détection PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            document.body.classList.add('pwa-standalone');
        }

        // Gestion des erreurs d'images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const fallback = this.nextElementSibling;
                if (fallback && fallback.classList.contains('avatar-fallback')) {
                    fallback.style.display = 'flex';
                }
            });
        });
    }

    // Démarrer quand le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Support pour Turbo/Turbolinks
    document.addEventListener('turbo:load', init);
})();
</script>
@endpush
@endsection