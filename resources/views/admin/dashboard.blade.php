@extends('admin.layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de l\'activité')

@section('content')
    <!-- Header avec statut temps réel -->
    <div class="dashboard-header">
        <div class="header-info">
            <div class="live-indicator">
                <span class="pulse"></span>
                <span class="live-text">Système opérationnel</span>
            </div>
            <p class="header-date">{{ now()->isoFormat('dddd D MMMM YYYY à HH:mm') }}</p>
        </div>
        <div class="header-actions">
            <button class="btn-refresh" onclick="refreshDashboard()" title="Actualiser les données">
                <i class="fa-solid fa-rotate"></i>
                <span>Actualiser</span>
            </button>
            <a href="{{ route('admin.reports.generate') }}" class="btn-export">
                <i class="fa-solid fa-file-export"></i>
                <span>Rapport</span>
            </a>
        </div>
    </div>

    <!-- Alertes critiques -->
    @if($alerts['urgent_tickets'] > 0 || $alerts['funding_ready_for_transfer'] > 0 || $alerts['documents_expiring_soon'] > 0)
        <div class="alerts-bar">
            @if($alerts['urgent_tickets'] > 0)
                <a href="{{ route('admin.support.index') }}" class="alert-item urgent">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ $alerts['urgent_tickets'] }} ticket{{ $alerts['urgent_tickets'] > 1 ? 's' : '' }} urgent{{ $alerts['urgent_tickets'] > 1 ? 's' : '' }}</span>
                </a>
            @endif
            @if($alerts['funding_ready_for_transfer'] > 0)
                <a href="{{ route('admin.funding.pending-transfers') }}" class="alert-item warning">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>{{ $alerts['funding_ready_for_transfer'] }} transfert{{ $alerts['funding_ready_for_transfer'] > 1 ? 's' : '' }} à exécuter</span>
                </a>
            @endif
            @if($alerts['documents_expiring_soon'] > 0)
                <a href="{{ route('admin.documents.index') }}" class="alert-item info">
                    <i class="fa-solid fa-file-circle-exclamation"></i>
                    <span>{{ $alerts['documents_expiring_soon'] }} document{{ $alerts['documents_expiring_soon'] > 1 ? 's' : '' }} expirent bientôt</span>
                </a>
            @endif
        </div>
    @endif

    <!-- Grille de statistiques principales -->
    <div class="stats-grid">
        <!-- Utilisateurs -->
        <div class="stat-card blue" data-aos="fade-up" data-aos-delay="0">
            <div class="stat-bg-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Utilisateurs</span>
                    @php
                        $userTrend = $stats['users']['new_last_month'] > 0
                            ? round((($stats['users']['new_this_month'] - $stats['users']['new_last_month']) / $stats['users']['new_last_month']) * 100, 1)
                            : 100;
                    @endphp
                    <span class="stat-trend {{ $userTrend >= 0 ? 'up' : 'down' }}">
                        <i class="fa-solid fa-arrow-{{ $userTrend >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($userTrend) }}%
                    </span>
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['users']['total'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail success">{{ $stats['users']['active'] }} actifs</span>
                    <span class="separator">•</span>
                    <span class="stat-detail">{{ $stats['users']['new_this_month'] }} ce mois</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-users"></i></div>
        </div>

        <!-- Transactions -->
        <div class="stat-card emerald" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-bg-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Transactions</span>
                    <span class="stat-trend up">
                        <i class="fa-solid fa-arrow-up"></i>
                        {{ $stats['transactions']['completed_this_month'] }}
                    </span>
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['transactions']['total'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail warning">{{ $stats['transactions']['pending'] }} en attente</span>
                    <span class="separator">•</span>
                    <span class="stat-detail">{{ number_format($stats['transactions']['total_amount_this_month'], 0, ',', ' ') }} FCFA ce mois</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-money-bill-wave"></i></div>
        </div>

        <!-- Demandes de financement -->
        <div class="stat-card amber" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-bg-icon"><i class="fa-solid fa-file-signature"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Demandes</span>
                    @if($stats['funding_requests']['pending_transfer'] > 0)
                        <span class="stat-trend up">
                            <i class="fa-solid fa-clock"></i>
                            {{ $stats['funding_requests']['pending_transfer'] }} transferts
                        </span>
                    @endif
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['funding_requests']['total'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail warning">{{ $stats['funding_requests']['pending'] }} en validation</span>
                    <span class="separator">•</span>
                    <span class="stat-detail info">{{ $stats['funding_requests']['needs_payment'] }} paiements</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-file-signature"></i></div>
            @if($stats['funding_requests']['pending'] > 0)
                <div class="stat-alert-badge"></div>
            @endif
        </div>

        <!-- Documents -->
        <div class="stat-card rose {{ $stats['documents']['pending_validation'] > 0 ? 'alert' : '' }}" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-bg-icon"><i class="fa-solid fa-folder-open"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Documents</span>
                    @if($stats['documents']['pending_validation'] > 0)
                        <span class="stat-trend down">
                            <i class="fa-solid fa-exclamation"></i>
                            Action requise
                        </span>
                    @endif
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['documents']['pending_validation'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail danger">En attente de validation</span>
                    <span class="separator">•</span>
                    <span class="stat-detail">{{ $stats['documents']['validated_this_month'] }} validés ce mois</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-folder-open"></i></div>
            @if($stats['documents']['pending_validation'] > 0)
                <div class="stat-alert-badge"></div>
            @endif
        </div>

        <!-- Formations -->
        <div class="stat-card violet" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-bg-icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Formations</span>
                    <span class="stat-trend up">
                        <i class="fa-solid fa-user-plus"></i>
                        {{ $stats['trainings']['enrollments_this_month'] }}
                    </span>
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['trainings']['total'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail success">{{ $stats['trainings']['active'] }} actives</span>
                    <span class="separator">•</span>
                    <span class="stat-detail">{{ $stats['trainings']['enrollments_this_month'] }} inscriptions ce mois</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-graduation-cap"></i></div>
        </div>

        <!-- Support -->
        <div class="stat-card cyan {{ $stats['support_tickets']['open'] > 0 ? 'alert' : '' }}" data-aos="fade-up" data-aos-delay="500">
            <div class="stat-bg-icon"><i class="fa-solid fa-headset"></i></div>
            <div class="stat-main">
                <div class="stat-header">
                    <span class="stat-label">Support</span>
                    @if($stats['support_tickets']['unassigned'] > 0)
                        <span class="stat-trend down">
                            <i class="fa-solid fa-user-xmark"></i>
                            {{ $stats['support_tickets']['unassigned'] }} non assignés
                        </span>
                    @endif
                </div>
                <div class="stat-value-wrapper">
                    <span class="stat-value" data-target="{{ $stats['support_tickets']['open'] }}">0</span>
                </div>
                <div class="stat-footer">
                    <span class="stat-detail warning">{{ $stats['support_tickets']['in_progress'] }} en cours</span>
                    <span class="separator">•</span>
                    <span class="stat-detail">{{ $stats['support_tickets']['resolved_this_month'] }} résolus ce mois</span>
                </div>
            </div>
            <div class="stat-icon-wrap"><i class="fa-solid fa-headset"></i></div>
            @if($alerts['urgent_tickets'] > 0)
                <div class="stat-alert-badge"></div>
            @endif
        </div>
    </div>

    <!-- Section principale : Graphique + Actions rapides -->
    <div class="dashboard-main-grid">
        <!-- Graphique d'activité -->
        <div class="dashboard-card chart-container" data-aos="fade-right">
            <div class="card-header">
                <div class="header-left">
                    <h3><i class="fa-solid fa-chart-line"></i> Activité des 6 derniers mois</h3>
                    <div class="chart-legend">
                        <span class="legend-item"><span class="dot primary"></span> Demandes de financement</span>
                        <span class="legend-item"><span class="dot secondary"></span> Nouveaux utilisateurs</span>
                    </div>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Actions rapides contextuelles -->
        <div class="dashboard-card quick-actions" data-aos="fade-left">
            <div class="card-header">
                <h3><i class="fa-solid fa-bolt"></i> Actions prioritaires</h3>
            </div>
            <div class="actions-grid">
                @if($stats['funding_requests']['pending'] > 0)
                    <a href="{{ route('admin.funding.pending-validation') }}" class="action-card priority-high">
                        <div class="action-visual amber">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <span class="action-badge">{{ $stats['funding_requests']['pending'] }}</span>
                        </div>
                        <div class="action-content">
                            <span class="action-title">Valider demandes</span>
                            <span class="action-desc">{{ $stats['funding_requests']['pending'] }} en attente de validation</span>
                        </div>
                        <i class="fa-solid fa-arrow-right action-arrow"></i>
                    </a>
                @endif

                @if($stats['documents']['pending_validation'] > 0)
                    <a href="{{ route('admin.documents.index') }}" class="action-card priority-high">
                        <div class="action-visual rose">
                            <i class="fa-solid fa-file-circle-check"></i>
                            <span class="action-badge alert">{{ $stats['documents']['pending_validation'] }}</span>
                        </div>
                        <div class="action-content">
                            <span class="action-title">Vérifier documents</span>
                            <span class="action-desc">KYC en attente de validation</span>
                        </div>
                        <i class="fa-solid fa-arrow-right action-arrow"></i>
                    </a>
                @endif

                @if($alerts['funding_ready_for_transfer'] > 0)
                    <a href="{{ route('admin.funding.pending-transfers') }}" class="action-card priority-high">
                        <div class="action-visual emerald">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span class="action-badge">{{ $alerts['funding_ready_for_transfer'] }}</span>
                        </div>
                        <div class="action-content">
                            <span class="action-title">Exécuter transferts</span>
                            <span class="action-desc">Prêts à être crédités</span>
                        </div>
                        <i class="fa-solid fa-arrow-right action-arrow"></i>
                    </a>
                @endif

                @if($stats['support_tickets']['open'] > 0)
                    <a href="{{ route('admin.support.index') }}" class="action-card">
                        <div class="action-visual cyan">
                            <i class="fa-solid fa-reply"></i>
                            @if($alerts['urgent_tickets'] > 0)
                                <span class="action-badge alert">{{ $alerts['urgent_tickets'] }}</span>
                            @endif
                        </div>
                        <div class="action-content">
                            <span class="action-title">Répondre tickets</span>
                            <span class="action-desc">{{ $stats['support_tickets']['open'] }} ouverts</span>
                        </div>
                        <i class="fa-solid fa-arrow-right action-arrow"></i>
                    </a>
                @endif

                <a href="{{ route('admin.users.index') }}" class="action-card">
                    <div class="action-visual blue">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div class="action-content">
                        <span class="action-title">Gérer utilisateurs</span>
                        <span class="action-desc">{{ $stats['users']['total'] }} inscrits</span>
                    </div>
                    <i class="fa-solid fa-arrow-right action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tableaux de données récents -->
    <div class="tables-grid">
        <!-- Derniers utilisateurs -->
        <div class="dashboard-card table-card" data-aos="fade-up">
            <div class="card-header">
                <div class="header-title">
                    <div class="title-icon blue"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h3>Derniers inscrits</h3>
                        <span class="subtitle">{{ $stats['users']['new_this_month'] }} ce mois</span>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn-view-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-body">
                @forelse($recentUsers as $user)
                    <div class="table-row user-row" onclick="window.location='{{ route('admin.users.show', $user['id']) }}'">
                        <div class="user-info">
                            <div class="avatar {{ $user['is_verified'] ? 'verified' : 'unverified' }}">
                                <span class="initials">{{ substr($user['full_name'], 0, 1) }}</span>
                                @if($user['is_verified'])
                                    <span class="verified-badge"><i class="fa-solid fa-check"></i></span>
                                @endif
                            </div>
                            <div class="user-details">
                                <span class="name">{{ $user['full_name'] }}</span>
                                <span class="meta">{{ $user['member_type_label'] }} • {{ $user['email'] }}</span>
                            </div>
                        </div>
                        <div class="user-meta">
                            <span class="time">{{ $user['created_at']->diffForHumans() }}</span>
                            @if($user['has_wallet'])
                                <span class="wallet-badge"><i class="fa-solid fa-wallet"></i> Wallet</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state-compact">
                        <i class="fa-solid fa-user-slash"></i>
                        <p>Aucun utilisateur récent</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Transactions récentes -->
        <div class="dashboard-card table-card" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header">
                <div class="header-title">
                    <div class="title-icon emerald"><i class="fa-solid fa-money-bill-transfer"></i></div>
                    <div>
                        <h3>Transactions récentes</h3>
                        <span class="subtitle">Dernières 24h</span>
                    </div>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="btn-view-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-body">
                @forelse($recentTransactions as $transaction)
                    <div class="table-row transaction-row" onclick="window.location='{{ route('admin.transactions.show', $transaction['id']) }}'">
                        <div class="transaction-info">
                            <div class="transaction-icon {{ $transaction['type'] }}">
                                <i class="fa-solid {{
                                    $transaction['type'] === 'deposit' ? 'fa-arrow-down' :
                                    ($transaction['type'] === 'withdrawal' ? 'fa-arrow-up' : 'fa-exchange-alt')
                                }}"></i>
                            </div>
                            <div class="transaction-details">
                                <span class="amount {{ $transaction['amount'] >= 0 ? 'positive' : 'negative' }}">
                                    {{ $transaction['amount_formatted'] }}
                                </span>
                                <span class="meta">{{ $transaction['user_name'] }}</span>
                            </div>
                        </div>
                        <div class="transaction-status">
                            <span class="status-badge-sm status-{{ $transaction['status'] }}">
                                {{ ucfirst($transaction['status']) }}
                            </span>
                            <span class="time">{{ $transaction['created_at']->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-compact">
                        <i class="fa-solid fa-receipt"></i>
                        <p>Aucune transaction récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tickets support -->
        <div class="dashboard-card table-card" data-aos="fade-up" data-aos-delay="200">
            <div class="card-header">
                <div class="header-title">
                    <div class="title-icon violet"><i class="fa-solid fa-headset"></i></div>
                    <div>
                        <h3>Tickets à traiter</h3>
                        <span class="subtitle">{{ $stats['support_tickets']['open'] + $stats['support_tickets']['in_progress'] }} actifs</span>
                    </div>
                </div>
                <a href="{{ route('admin.support.index') }}" class="btn-view-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="table-body">
                @forelse($recentTickets as $ticket)
                    <div class="table-row ticket-row {{ $ticket['can_be_replied'] ? 'actionable' : '' }}"
                         onclick="window.location='{{ route('admin.support.show', $ticket['id']) }}'">
                        <div class="ticket-info">
                            <div class="priority-indicator {{ $ticket['priority'] }}"></div>
                            <div class="ticket-details">
                                <span class="subject">{{ Str::limit($ticket['subject'], 35) }}</span>
                                <span class="meta">
                                    {{ $ticket['category_label'] }} • Par {{ $ticket['user_name'] }}
                                    @if($ticket['assigned_to_name'])
                                        • Assigné à {{ $ticket['assigned_to_name'] }}
                                    @else
                                        • <span class="unassigned">Non assigné</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="ticket-status">
                            <span class="priority-badge-sm {{ $ticket['priority'] }}">
                                {{ $ticket['priority_badge'] }}
                            </span>
                            <span class="time">{{ $ticket['created_at']->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-compact">
                        <i class="fa-solid fa-check-circle"></i>
                        <p>Aucun ticket en attente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Répartition détaillée -->
    <div class="dashboard-sections">
        <div class="dashboard-card details-card" data-aos="fade-up">
            <div class="card-header">
                <h3><i class="fa-solid fa-chart-pie"></i> Répartition détaillée</h3>
            </div>
            <div class="details-grid">
                <div class="detail-block">
                    <h4>Utilisateurs</h4>
                    <div class="detail-items">
                        <div class="detail-item">
                            <span class="label">Particuliers</span>
                            <span class="value">{{ $stats['users']['particuliers'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Entreprises</span>
                            <span class="value">{{ $stats['users']['entreprises'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Vérifiés</span>
                            <span class="value success">{{ $stats['users']['verified'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-block">
                    <h4>Documents</h4>
                    <div class="detail-items">
                        <div class="detail-item">
                            <span class="label">Profil</span>
                            <span class="value warning">{{ $stats['documents']['profile_pending'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Financements</span>
                            <span class="value warning">{{ $stats['documents']['funding_pending'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Rejetés</span>
                            <span class="value danger">{{ $stats['documents']['rejected'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-block">
                    <h4>Financements</h4>
                    <div class="detail-items">
                        @foreach($stats['funding_requests']['by_status'] as $status => $count)
                            <div class="detail-item">
                                <span class="label">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                <span class="value">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    :root {
        --primary-blue: #3b82f6;
        --primary-emerald: #10b981;
        --primary-amber: #f59e0b;
        --primary-rose: #f43f5e;
        --primary-violet: #8b5cf6;
        --primary-cyan: #06b6d4;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
    }

    /* Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .header-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary-emerald);
    }

    .pulse {
        width: 8px;
        height: 8px;
        background: var(--primary-emerald);
        border-radius: 50%;
        position: relative;
        animation: pulse 2s infinite;
    }

    .pulse::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: var(--primary-emerald);
        opacity: 0.4;
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    @keyframes pulse-ring {
        0% { transform: scale(1); opacity: 0.4; }
        100% { transform: scale(3); opacity: 0; }
    }

    .header-date {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
        text-transform: capitalize;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-refresh, .btn-export {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--gray-700);
        text-decoration: none;
    }

    .btn-refresh:hover {
        background: var(--gray-50);
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .btn-refresh.spinning i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .btn-export {
        background: var(--gray-900);
        color: white;
        border-color: var(--gray-900);
    }

    .btn-export:hover {
        background: var(--gray-800);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Alertes */
    .alerts-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .alert-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .alert-item.urgent {
        background: #fee2e2;
        color: #dc2626;
    }

    .alert-item.warning {
        background: #fef3c7;
        color: #d97706;
    }

    .alert-item.info {
        background: #cffafe;
        color: #0891b2;
    }

    .alert-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
        border: 1px solid var(--gray-100);
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }

    .stat-card.alert {
        border-left: 4px solid currentColor;
    }

    .stat-bg-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.03;
        pointer-events: none;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-bg-icon {
        transform: scale(1.1) rotate(5deg);
        opacity: 0.05;
    }

    .stat-main {
        flex: 1;
        z-index: 1;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 20px;
    }

    .stat-trend.up {
        background: #d1fae5;
        color: #059669;
    }

    .stat-trend.down {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-value-wrapper {
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1;
        font-feature-settings: "tnum";
        font-variant-numeric: tabular-nums;
    }

    .stat-footer {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .stat-detail.success { color: #059669; font-weight: 600; }
    .stat-detail.warning { color: #d97706; font-weight: 600; }
    .stat-detail.danger { color: #dc2626; font-weight: 600; }
    .stat-detail.info { color: #0891b2; font-weight: 600; }

    .separator {
        color: var(--gray-300);
    }

    .stat-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        z-index: 1;
    }

    .stat-alert-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        animation: blink 2s infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    /* Couleurs */
    .stat-card.blue { --card-color: var(--primary-blue); }
    .stat-card.emerald { --card-color: var(--primary-emerald); }
    .stat-card.amber { --card-color: var(--primary-amber); }
    .stat-card.rose { --card-color: var(--primary-rose); }
    .stat-card.violet { --card-color: var(--primary-violet); }
    .stat-card.cyan { --card-color: var(--primary-cyan); }

    .stat-card.blue .stat-icon-wrap { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-card.emerald .stat-icon-wrap { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card.amber .stat-icon-wrap { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card.rose .stat-icon-wrap { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .stat-card.violet .stat-icon-wrap { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-card.cyan .stat-icon-wrap { background: linear-gradient(135deg, #06b6d4, #0891b2); }

    /* Main Grid */
    .dashboard-main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    @media (max-width: 1200px) {
        .dashboard-main-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Dashboard Card */
    .dashboard-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
        border: 1px solid var(--gray-100);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-100);
    }

    .header-left {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .header-left h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-left h3 i {
        color: var(--primary-blue);
    }

    .chart-legend {
        display: flex;
        gap: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.primary { background: var(--primary-blue); }
    .dot.secondary { background: var(--primary-violet); }

    .chart-body {
        padding: 24px;
        height: 350px;
    }

    /* Quick Actions */
    .quick-actions .card-header {
        border-bottom: none;
        padding-bottom: 0;
    }

    .quick-actions .card-header h3 i {
        color: var(--primary-amber);
    }

    .actions-grid {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .action-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        background: var(--gray-50);
        border: 1px solid transparent;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .action-card:hover {
        background: white;
        border-color: var(--gray-200);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .action-card.priority-high {
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border-color: #fed7aa;
    }

    .action-card.priority-high:hover {
        background: linear-gradient(135deg, #ffedd5, #fed7aa);
    }

    .action-visual {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        position: relative;
        flex-shrink: 0;
    }

    .action-visual.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .action-visual.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .action-visual.rose { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .action-visual.emerald { background: linear-gradient(135deg, #10b981, #059669); }
    .action-visual.cyan { background: linear-gradient(135deg, #06b6d4, #0891b2); }

    .action-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: var(--primary-rose);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        min-width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        padding: 0 6px;
    }

    .action-badge.alert {
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .action-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .action-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.95rem;
    }

    .action-desc {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .action-arrow {
        color: var(--gray-400);
        transition: all 0.2s ease;
    }

    .action-card:hover .action-arrow {
        color: var(--gray-600);
        transform: translateX(4px);
    }

    /* Tables Grid */
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 24px;
        margin-bottom: 28px;
    }

    @media (max-width: 840px) {
        .tables-grid {
            grid-template-columns: 1fr;
        }
    }

    .table-card .card-header {
        padding: 20px;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .title-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
    }

    .title-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .title-icon.emerald { background: linear-gradient(135deg, #10b981, #059669); }
    .title-icon.violet { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .header-title h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .subtitle {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .btn-view-all {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary-blue);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view-all:hover {
        gap: 10px;
        color: #2563eb;
    }

    /* Table Body */
    .table-body {
        padding: 8px;
    }

    .table-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-radius: 10px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .table-row:hover {
        background: var(--gray-50);
    }

    .table-row.actionable {
        border-left: 3px solid var(--primary-blue);
    }

    /* User Row */
    .user-row .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-violet));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .avatar.verified {
        background: linear-gradient(135deg, var(--primary-emerald), #059669);
    }

    .verified-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 16px;
        height: 16px;
        background: var(--primary-emerald);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        border: 2px solid white;
    }

    .user-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .user-details .name {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.95rem;
    }

    .user-details .meta {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .user-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .time {
        font-size: 0.875rem;
        color: var(--gray-400);
    }

    .wallet-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        background: #dbeafe;
        color: #2563eb;
    }

    /* Transaction Row */
    .transaction-row .transaction-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .transaction-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }

    .transaction-icon.deposit { background: linear-gradient(135deg, #10b981, #059669); }
    .transaction-icon.withdrawal { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .transaction-icon.transfer { background: linear-gradient(135deg, #3b82f6, #2563eb); }

    .transaction-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .transaction-details .amount {
        font-weight: 700;
        font-family: 'SF Mono', monospace;
        font-size: 0.95rem;
    }

    .transaction-details .amount.positive { color: #059669; }
    .transaction-details .amount.negative { color: #dc2626; }

    .transaction-details .meta {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .transaction-status {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .status-badge-sm {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: capitalize;
    }

    .status-badge-sm.status-completed,
    .status-badge-sm.status-success { background: #d1fae5; color: #059669; }
    .status-badge-sm.status-pending { background: #fef3c7; color: #d97706; }
    .status-badge-sm.status-failed,
    .status-badge-sm.status-cancelled { background: #fee2e2; color: #dc2626; }

    /* Ticket Row */
    .ticket-row .ticket-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .priority-indicator {
        width: 4px;
        height: 40px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .priority-indicator.urgent,
    .priority-indicator.high { background: #dc2626; }
    .priority-indicator.medium { background: #d97706; }
    .priority-indicator.low { background: #059669; }

    .ticket-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .ticket-details .subject {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ticket-details .meta {
        font-size: 0.875rem;
        color: var(--gray-500);
    }

    .ticket-details .meta .unassigned {
        color: #dc2626;
        font-weight: 600;
    }

    .ticket-status {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .priority-badge-sm {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: capitalize;
    }

    .priority-badge-sm.urgent,
    .priority-badge-sm.high { background: #fee2e2; color: #dc2626; }
    .priority-badge-sm.medium { background: #fef3c7; color: #d97706; }
    .priority-badge-sm.low { background: #d1fae5; color: #059669; }

    /* Empty State */
    .empty-state-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        color: var(--gray-400);
        gap: 8px;
    }

    .empty-state-compact i {
        font-size: 2rem;
        opacity: 0.5;
    }

    .empty-state-compact p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* Details Section */
    .dashboard-sections {
        margin-bottom: 28px;
    }

    .details-card .card-header h3 i {
        color: var(--primary-violet);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        padding: 24px;
    }

    .detail-block h4 {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 16px 0;
    }

    .detail-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--gray-100);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-item .label {
        font-size: 0.95rem;
        color: var(--gray-600);
    }

    .detail-item .value {
        font-weight: 700;
        color: var(--gray-900);
        font-size: 1rem;
    }

    .detail-item .value.success { color: #059669; }
    .detail-item .value.warning { color: #d97706; }
    .detail-item .value.danger { color: #dc2626; }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-value {
            font-size: 2rem;
        }

        .alerts-bar {
            flex-direction: column;
        }

        .alert-item {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<script>
    // Initialisation AOS
    AOS.init({
        duration: 600,
        once: true,
        offset: 50
    });

    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-value');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('fr-FR');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('fr-FR');
                }
            };

            if (target > 0) {
                updateCounter();
            } else {
                counter.textContent = '0';
            }
        });
    }

    // Graphique avec données réelles du contrôleur
    function initChart() {
        const ctx = document.getElementById('activityChart').getContext('2d');

        // Données depuis le contrôleur
        const fundingData = @json($chartData['funding_by_month']->pluck('count'));
        const userData = @json($chartData['users_by_month']->pluck('count'));
        const labels = @json($chartData['funding_by_month']->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y')));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Demandes de financement',
                    data: fundingData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Nouveaux utilisateurs',
                    data: userData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, family: 'Inter' },
                        bodyFont: { size: 13, family: 'Inter' }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 12 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 12 } }
                    }
                }
            }
        });
    }

    // Rafraîchissement
    function refreshDashboard() {
        const btn = document.querySelector('.btn-refresh');
        btn.classList.add('spinning');

        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        animateCounters();
        initChart();
    });
</script>
@endpush
