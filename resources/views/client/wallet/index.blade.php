@extends('layouts.client')

@section('title', 'Mon Portefeuille')

@push('styles')
<style>
/* Styles spécifiques au portefeuille */
.wallet-header {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    color: white;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.wallet-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
}

.wallet-info {
    position: relative;
    z-index: 1;
}

.wallet-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.wallet-title h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    flex: 1;
}

.wallet-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    font-weight: 500;
}

.wallet-status .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse 2s infinite;
}

.wallet-number {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
}

/* Balance Card - Redesign */
.balance-card {
    background: white;
    border-radius: var(--border-radius-lg);
    padding: 2.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--secondary-200);
    position: relative;
    overflow: hidden;
    text-align: center;
}

.balance-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

.balance-label {
    font-size: 1rem;
    color: var(--secondary-600);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.balance-amount {
    margin-bottom: 1.5rem;
}

.balance-amount h2 {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--secondary-900);
    margin: 0;
    line-height: 1;
    letter-spacing: -0.5px;
}

.balance-currency {
    font-size: 1.5rem;
    color: var(--secondary-700);
    font-weight: 600;
}

.balance-subtitle {
    color: var(--secondary-500);
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.balance-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid var(--secondary-200);
    font-size: 0.85rem;
    color: var(--secondary-600);
}

.balance-refresh-btn {
    background: none;
    border: none;
    color: var(--primary-500);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
}

.balance-refresh-btn:hover {
    background: var(--primary-50);
    color: var(--primary-600);
}

.balance-refresh-btn i {
    transition: transform var(--transition-fast);
}

.balance-refresh-btn:hover i {
    transform: rotate(180deg);
}

/* Sections améliorées */
.section {
    margin-bottom: 2.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--secondary-100);
}

.section-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--secondary-800);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-badge {
    background: var(--primary-100);
    color: var(--primary-700);
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius);
    font-size: 0.75rem;
    font-weight: 600;
}

.section-link {
    color: var(--primary-500);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all var(--transition-fast);
}

.section-link:hover {
    color: var(--primary-600);
    transform: translateX(2px);
}

/* Funding Items - Design amélioré */
.funding-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.funding-item {
    background: white;
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    transition: all var(--transition-fast);
    cursor: pointer;
    border: 1px solid var(--secondary-200);
    box-shadow: var(--shadow-sm);
}

.funding-item:hover {
    border-color: var(--primary-300);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.funding-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--border-radius);
    background: linear-gradient(135deg, var(--warning-500) 0%, var(--warning-700) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.funding-content {
    flex: 1;
    min-width: 0;
}

.funding-title {
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.funding-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    color: var(--secondary-600);
}

.funding-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.funding-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
}

.status-pending {
    background: var(--warning-100);
    color: var(--warning-700);
}

.status-approved {
    background: var(--primary-100);
    color: var(--primary-700);
}

.status-funded {
    background: var(--success-100);
    color: var(--success-700);
}

.funding-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    font-size: 0.9rem;
}

.funding-amount {
    font-weight: 700;
    color: var(--primary-600);
    font-size: 1.1rem;
}

.funding-action {
    flex-shrink: 0;
}

.funding-btn {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.funding-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Transactions - Design amélioré */
.transactions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.transaction-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: white;
    border-radius: var(--border-radius);
    border: 1px solid var(--secondary-200);
    transition: all var(--transition-fast);
    cursor: pointer;
}

.transaction-item:hover {
    border-color: var(--primary-300);
    transform: translateX(4px);
    box-shadow: var(--shadow-sm);
}

.transaction-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--border-radius);
    background: var(--primary-50);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-500);
    font-size: 1.25rem;
    flex-shrink: 0;
}

.transaction-details {
    flex: 1;
    min-width: 0;
}

.transaction-title {
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.transaction-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: var(--secondary-600);
}

.transaction-amount {
    font-weight: 700;
    font-size: 1.15rem;
    text-align: right;
    min-width: 120px;
}

.amount-positive {
    color: var(--success-600);
}

.amount-negative {
    color: var(--error-600);
}

/* Stats Grid - Design amélioré */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius-lg);
    padding: 1.75rem;
    border: 1px solid var(--secondary-200);
    transition: all var(--transition-fast);
    text-align: center;
}

.stat-card:hover {
    border-color: var(--primary-300);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin: 0 auto 1.25rem;
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

.stat-card:nth-child(2) .stat-icon {
    background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%);
}

.stat-card:nth-child(3) .stat-icon {
    background: linear-gradient(135deg, var(--warning-500) 0%, var(--warning-700) 100%);
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--secondary-900);
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--secondary-600);
    font-weight: 500;
}

/* Empty States améliorés */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--secondary-600);
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: var(--secondary-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--secondary-400);
    margin: 0 auto 1.5rem;
}

.empty-state h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.5rem;
}

.empty-state p {
    margin-bottom: 1.5rem;
    color: var(--secondary-600);
}

/* BOUTONS FLOTTANTS EN HAUT À DROITE - POSITIONNEMENT CORRECT */
.floating-actions-top {
    position: fixed;
    top: 120px;
    right: 2rem;
    z-index: 990;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius-lg);
    padding: 1rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--secondary-200);
}

.floating-action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.floating-action-label {
    background: white;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--secondary-800);
    box-shadow: var(--shadow-md);
    white-space: nowrap;
    opacity: 0;
    transform: translateX(10px);
    transition: all var(--transition-fast);
    pointer-events: none;
    position: absolute;
    right: 70px;
}

.floating-action-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: none;
    color: white;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-xl);
    cursor: pointer;
    transition: all var(--transition-base);
    position: relative;
    outline: none;
}

.floating-action-item:hover .floating-action-label {
    opacity: 1;
    transform: translateX(0);
}

/* Styles spécifiques pour chaque bouton */
.floating-action-btn.deposit {
    background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%);
}

.floating-action-btn.withdraw {
    background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%);
}

.floating-action-btn.pin {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

.floating-action-btn:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-2xl);
}

.floating-action-btn:active {
    transform: scale(0.95);
}

/* Boutons d'action dans la balance card */
.balance-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.main-action-btn {
    background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius-lg);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-md);
    flex: 1;
    min-width: 200px;
    justify-content: center;
}

.main-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.main-action-btn i {
    font-size: 1.2rem;
}

.main-action-btn.withdraw {
    background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%);
}

.main-action-btn.pin {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .wallet-header {
        padding: 1.5rem;
    }

    .wallet-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .balance-card {
        padding: 2rem 1.5rem;
    }

    .balance-amount h2 {
        font-size: 2.75rem;
    }

    .funding-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .funding-info {
        flex-direction: column;
        gap: 0.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .floating-actions-top {
        position: fixed;
        top: auto;
        bottom: 5rem;
        right: 1rem;
        flex-direction: row;
        padding: 0.75rem;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 50px;
    }

    .floating-action-btn {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .floating-action-label {
        display: none;
    }

    .balance-actions {
        flex-direction: column;
        gap: 0.5rem;
    }

    .main-action-btn {
        width: 100%;
        min-width: unset;
    }
}

@media (max-width: 480px) {
    .balance-amount h2 {
        font-size: 2.25rem;
    }

    .transaction-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .transaction-amount {
        text-align: left;
        width: 100%;
    }

    .floating-actions-top {
        bottom: 4.5rem;
        right: 0.5rem;
        padding: 0.5rem;
        gap: 0.25rem;
    }

    .floating-action-btn {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
}

/* Animation pour le rafraîchissement */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}

/* Styles pour les slide modals */
.slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1100;
    display: none;
    opacity: 0;
    transition: opacity var(--transition-base);
}

.slide-modal.show {
    display: block;
    opacity: 1;
}

.slide-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    max-width: 400px;
    height: 100%;
    background: white;
    transform: translateX(100%);
    transition: transform var(--transition-base);
    display: flex;
    flex-direction: column;
}

.slide-modal.show .slide-content {
    transform: translateX(0);
}

.slide-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--secondary-200);
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.slide-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.slide-close {
    background: rgba(255,255,255,0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.slide-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.slide-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
}

/* Animation pulse pour le statut */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

@section('content')
<div class="wallet-container">
    <!-- BOUTONS FLOTTANTS EN HAUT À DROITE - POSITIONNÉS HAUT -->
    <div class="floating-actions-top">
        <!-- Bouton de dépôt (TOUJOURS visible) -->
        <div class="floating-action-item">
            <span class="floating-action-label">Déposer des fonds</span>
            <button class="floating-action-btn deposit" onclick="showDepositModal()">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <!-- Bouton de retrait (seulement si solde >= 1000) -->
        @if($wallet->balance >= 1000)
        <div class="floating-action-item">
            <span class="floating-action-label">Retirer des fonds</span>
            <button class="floating-action-btn withdraw" onclick="showWithdrawModal()">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        @endif

        <!-- Bouton de changement du PIN (TOUJOURS visible) -->
        <div class="floating-action-item">
            <span class="floating-action-label">Changer le PIN</span>
            <button class="floating-action-btn pin" onclick="showPinModal()">
                <i class="fas fa-key"></i>
            </button>
        </div>
    </div>

    <!-- Wallet Header -->
    <div class="wallet-header">
        <div class="wallet-info">
            <div class="wallet-title">
                <h1>
                    <i class="fas fa-wallet"></i>
                    Mon Portefeuille
                </h1>
                <div class="wallet-status" id="walletOnlineStatus">
                    <span class="status-dot"></span>
                    <span>En ligne</span>
                </div>
            </div>

            <div class="wallet-number">
                <i class="fas fa-id-card"></i>
                <span>Portefeuille {{ $wallet->wallet_number ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-label">
            <i class="fas fa-wallet"></i>
            Solde disponible
        </div>

        <div class="balance-amount">
            <h2 id="walletBalance">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
            <div class="balance-currency">Francs CFA</div>
        </div>

        <div class="balance-subtitle">
            <i class="fas fa-check-circle" style="color: var(--success-500);"></i>
            Solde à jour
        </div>

        <!-- Boutons d'action dans la balance card -->
        <div class="balance-actions">
            <button class="main-action-btn" onclick="showDepositModal()">
                <i class="fas fa-plus-circle"></i>
                Déposer des fonds
            </button>

            @if($wallet->balance >= 1000)
            <button class="main-action-btn withdraw" onclick="showWithdrawModal()">
                <i class="fas fa-minus-circle"></i>
                Retirer des fonds
            </button>
            @endif

            <!-- BOUTON CHANGER LE PIN -->
            <button class="main-action-btn pin" onclick="showPinModal()">
                <i class="fas fa-key"></i>
                Changer le PIN
            </button>
        </div>

        <div class="balance-footer">
            <span>Dernière mise à jour: <span id="balanceUpdateTime">{{ now()->format('H:i') }}</span></span>
            <button class="balance-refresh-btn" onclick="refreshBalance()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
        </div>
    </div>

    <!-- Financements en attente -->
    @if(isset($pendingFundings) && $pendingFundings->count() > 0)
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-clock"></i> Financements en attente</h3>
            <span class="section-badge">{{ $pendingFundings->count() }}</span>
        </div>

        <div class="funding-list">
            @foreach($pendingFundings as $funding)
            <div class="funding-item" onclick="showFundingDetails('{{ $funding->id }}')">
                <div class="funding-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="funding-content">
                    <div class="funding-title">{{ Str::limit($funding->title, 40) }}</div>
                    <div class="funding-meta">
                        <span class="funding-date">
                            <i class="far fa-calendar"></i>
                            {{ $funding->created_at->format('d/m/Y') }}
                        </span>
                        <span class="funding-status status-{{ $funding->status }}">
                            {{ ucfirst($funding->status) }}
                        </span>
                    </div>
                    <div class="funding-info">
                        <span class="funding-amount">
                            Demandé: {{ number_format($funding->amount_requested, 0, ',', ' ') }} F
                        </span>
                        @if($funding->amount_approved)
                        <span style="color: var(--success-600);">
                            Approuvé: {{ number_format($funding->amount_approved, 0, ',', ' ') }} F
                        </span>
                        @endif
                    </div>
                </div>
                @if($funding->status === 'funded')
                <div class="funding-action">
                    <button class="funding-btn" onclick="creditFunding(event, '{{ $funding->id }}')">
                        <i class="fas fa-check-circle"></i>
                        Créditer
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistiques du mois -->
    @if(isset($monthlyStats))
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-chart-bar"></i> Statistiques du mois</h3>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-value">{{ number_format($monthlyStats['deposits'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Dépôts effectués</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-value">{{ number_format($monthlyStats['withdrawals'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Retraits effectués</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="stat-value">{{ number_format($monthlyStats['pending_fundings'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Financements en attente</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transactions récentes -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-exchange-alt"></i> Dernières transactions</h3>
            @if(isset($transactions) && $transactions->count() > 0)
            <a href="{{ route('client.wallet.transactions') }}" class="section-link">
                Voir tout <i class="fas fa-arrow-right"></i>
            </a>
            @endif
        </div>

        @if(isset($transactions) && $transactions->count() > 0)
        <div class="transactions-list">
            @foreach($transactions->take(5) as $transaction)
            <div class="transaction-item" onclick="showTransactionDetails('{{ $transaction->id }}')">
                <div class="transaction-icon">
                    @if($transaction->type === 'deposit')
                        <i class="fas fa-arrow-down"></i>
                    @elseif($transaction->type === 'withdrawal')
                        <i class="fas fa-arrow-up"></i>
                    @elseif($transaction->type === 'funding')
                        <i class="fas fa-hand-holding-usd"></i>
                    @else
                        <i class="fas fa-exchange-alt"></i>
                    @endif
                </div>

                <div class="transaction-details">
                    <div class="transaction-title">{{ $transaction->description }}</div>
                    <div class="transaction-meta">
                        <span class="transaction-date">
                            <i class="far fa-clock"></i>
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </span>
                        <span class="transaction-status">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>

                <div class="transaction-amount">
                    <span class="{{ in_array($transaction->type, ['deposit', 'funding']) ? 'amount-positive' : 'amount-negative' }}">
                        {{ in_array($transaction->type, ['deposit', 'funding']) ? '+' : '-' }}
                        {{ number_format($transaction->amount, 0, ',', ' ') }} F
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <h4>Aucune transaction</h4>
            <p>Vous n'avez pas encore effectué de transaction</p>
        </div>
        @endif
    </div>
</div>

<!-- Inclusion des modals existants -->
@include('client.wallet.modals.deposit')
@include('client.wallet.modals.withdraw')
@include('client.wallet.modals.pin')

@endsection

@push('scripts')
<script>
// DÉFINIR LES FONCTIONS GLOBALES EN PREMIER
window.showPinModal = function() {
    if (!navigator.onLine) {
        if (window.toast) {
            window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
        }
        return;
    }

    const modal = document.getElementById('pinSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Réinitialiser les formulaires
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => form.reset());

        // Mettre le focus sur le premier champ
        const firstInput = modal.querySelector('input');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    } else {
        console.error('Modal PIN non trouvé');
        if (window.toast) {
            window.toast.error('Erreur', 'Impossible d\'ouvrir la gestion du PIN');
        }
    }
};

window.showDepositModal = function() {
    if (!navigator.onLine) {
        if (window.toast) {
            window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
        }
        return;
    }

    const modal = document.getElementById('depositSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

window.showWithdrawModal = function() {
    if (!navigator.onLine) {
        if (window.toast) {
            window.toast.error('Mode hors ligne', 'Cette fonctionnalité nécessite une connexion Internet');
        }
        return;
    }

    // Récupérer le solde actuel
    const balanceElement = document.getElementById('walletBalance');
    let currentBalance = 0;
    if (balanceElement) {
        const balanceText = balanceElement.textContent.replace(/\s/g, '');
        currentBalance = parseInt(balanceText) || 0;
    }

    if (currentBalance < 1000) {
        if (window.toast) {
            window.toast.error('Solde insuffisant', 'Minimum 1 000 FCFA requis pour un retrait');
        }
        return;
    }

    // Vérifier le PIN d'abord
    const modal = document.getElementById('verifyPinSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        const pinInput = document.getElementById('quickPinInput');
        if (pinInput) {
            pinInput.value = '';
            pinInput.focus();
        }
    }
};

// Variables globales pour le portefeuille
let walletBalance = {{ $wallet->balance ?? 0 }};
let lastRefreshTime = '{{ now()->format("Y-m-d H:i:s") }}';
let refreshInterval;
let autoRefreshEnabled = true;
let pinVerified = false;
let pinVerificationExpiry = 0;
let pendingWithdrawAction = null;

// Fonction pour ouvrir le modal de retrait (après vérification PIN)
window.openWithdrawModal = function() {
    const modal = document.getElementById('withdrawSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

// Fonction pour fermer un modal
window.closeSlide = function(slideId) {
    const modal = document.getElementById(slideId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
};

// Fonction pour vérifier le PIN pour retrait
window.verifyPinForWithdrawal = function() {
    const modal = document.getElementById('verifyPinSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        const pinInput = document.getElementById('quickPinInput');
        if (pinInput) {
            pinInput.value = '';
            pinInput.focus();
        }
    }
};

// INITIALISATION
document.addEventListener('DOMContentLoaded', function() {
    initWallet();
    initSlideModals();
    initQuickPinVerification();
    startAutoRefresh();
    checkPinVerification();
    updateFloatingButtons();
    setupFloatingButtonsBehavior();
});

// RESTE DU CODE EXISTANT...
function setupFloatingButtonsBehavior() {
    const floatingActions = document.querySelector('.floating-actions-top');
    if (!floatingActions) return;

    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (window.innerWidth > 768) {
            if (scrollTop > 100) {
                floatingActions.style.top = '80px';
                floatingActions.style.transition = 'top 0.3s ease';
            } else {
                floatingActions.style.top = '120px';
                floatingActions.style.transition = 'top 0.3s ease';
            }

            if (scrollTop > lastScrollTop && scrollTop > 200) {
                floatingActions.style.opacity = '0.6';
                floatingActions.style.transform = 'translateY(-10px)';
            } else {
                floatingActions.style.opacity = '1';
                floatingActions.style.transform = 'translateY(0)';
            }
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    });
}

function updateFloatingButtons() {
    const withdrawItem = document.querySelector('.floating-action-item:nth-child(2)');
    const withdrawMainBtn = document.querySelector('.main-action-btn.withdraw');

    if (withdrawItem) {
        if (walletBalance >= 1000) {
            withdrawItem.style.display = 'flex';
        } else {
            withdrawItem.style.display = 'none';
        }
    }

    if (withdrawMainBtn) {
        if (walletBalance >= 1000) {
            withdrawMainBtn.style.display = 'flex';
        } else {
            withdrawMainBtn.style.display = 'none';
        }
    }
}

function initWallet() {
    updateWalletOnlineStatus();

    window.addEventListener('online', updateWalletOnlineStatus);
    window.addEventListener('offline', updateWalletOnlineStatus);

    initWalletActions();
}

function initSlideModals() {
    document.querySelectorAll('.slide-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            const modalId = this.closest('.slide-modal').id;
            closeSlide(modalId);
        });
    });

    document.querySelectorAll('.slide-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSlide(this.id);
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.slide-modal.show').forEach(modal => {
                closeSlide(modal.id);
            });
        }
    });
}

function initQuickPinVerification() {
    const quickPinInput = document.getElementById('quickPinInput');
    if (quickPinInput) {
        quickPinInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
            if (this.value.length === 6) {
                setTimeout(() => {
                    document.getElementById('quickVerifyPinForm').dispatchEvent(new Event('submit'));
                }, 300);
            }
        });

        const parent = quickPinInput.parentElement;
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'toggle-pin-visibility';
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
        toggleButton.style.position = 'absolute';
        toggleButton.style.right = '15px';
        toggleButton.style.top = '50%';
        toggleButton.style.transform = 'translateY(-50%)';
        toggleButton.style.background = 'none';
        toggleButton.style.border = 'none';
        toggleButton.style.color = 'var(--secondary-500)';
        toggleButton.style.cursor = 'pointer';
        toggleButton.style.fontSize = '1rem';

        toggleButton.addEventListener('click', function() {
            const isPassword = quickPinInput.type === 'password';
            quickPinInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });

        parent.style.position = 'relative';
        parent.appendChild(toggleButton);
    }

    const quickVerifyForm = document.getElementById('quickVerifyPinForm');
    if (quickVerifyForm) {
        quickVerifyForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const pin = formData.get('pin');

            if (pin.length !== 6) {
                if (window.toast) {
                    window.toast.error('Le PIN doit contenir exactement 6 chiffres');
                }
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route("client.wallet.verify-pin") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    pinVerified = true;
                    pinVerificationExpiry = Date.now() + (30 * 60 * 1000);

                    if (window.toast) {
                        window.toast.success('PIN vérifié', 'Vérification réussie');
                    }

                    closeSlide('verifyPinSlide');

                    if (data.auth_token) {
                        localStorage.setItem('wallet_pin_token', data.auth_token);
                        localStorage.setItem('wallet_pin_expiry', pinVerificationExpiry);
                    }

                    // Ouvrir le modal de retrait après vérification
                    setTimeout(() => {
                        openWithdrawModal();
                    }, 500);

                    pendingWithdrawAction = null;
                } else {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    if (window.toast) {
                        window.toast.error('PIN incorrect', 'Veuillez réessayer');
                    }
                    quickPinInput.value = '';
                    quickPinInput.focus();
                }
            } catch (error) {
                console.error('Error:', error);
                button.innerHTML = originalText;
                button.disabled = false;
                if (window.toast) {
                    window.toast.error('Erreur de connexion');
                }
            }
        });
    }
}

function checkPinVerification() {
    const pinExpiry = localStorage.getItem('wallet_pin_expiry');
    if (pinExpiry && Date.now() < parseInt(pinExpiry)) {
        pinVerified = true;
        pinVerificationExpiry = parseInt(pinExpiry);
    }
}

function updateWalletOnlineStatus() {
    const statusElement = document.getElementById('walletOnlineStatus');
    if (statusElement) {
        const isOnline = navigator.onLine;
        const dot = statusElement.querySelector('.status-dot');
        const text = statusElement.querySelector('span:last-child');

        if (isOnline) {
            dot.style.background = '#22c55e';
            dot.style.animation = 'pulse 2s infinite';
            text.textContent = 'En ligne';

            if (autoRefreshEnabled) {
                refreshBalance();
            }
        } else {
            dot.style.background = '#ef4444';
            dot.style.animation = 'none';
            text.textContent = 'Hors ligne';

            clearInterval(refreshInterval);

            if (window.toast) {
                window.toast.warning('Mode hors ligne',
                    'Certaines fonctionnalités peuvent être limitées');
            }
        }
    }
}

function initWalletActions() {
    const actionButtons = document.querySelectorAll('.funding-btn, .main-action-btn, .floating-action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });

        button.addEventListener('mouseenter', function() {
            if (this.classList.contains('floating-action-btn')) {
                this.style.transform = 'scale(1.1)';
            } else {
                this.style.transform = 'translateY(-2px)';
            }
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
}

async function refreshBalance() {
    if (!navigator.onLine) {
        return;
    }

    const refreshBtn = document.querySelector('.balance-refresh-btn');
    const amountElement = document.getElementById('walletBalance');
    const timeElement = document.getElementById('balanceUpdateTime');

    if (refreshBtn) {
        const icon = refreshBtn.querySelector('i');
        icon.classList.add('spin');
        refreshBtn.disabled = true;
        refreshBtn.style.opacity = '0.7';
    }

    try {
        const response = await fetch('{{ route("client.wallet.get-info") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            cache: 'no-cache'
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.success) {
            const oldBalance = walletBalance;
            walletBalance = data.wallet.balance;

            if (amountElement) {
                amountElement.textContent = new Intl.NumberFormat('fr-FR').format(walletBalance);
            }

            const now = new Date();
            lastRefreshTime = now.toISOString();

            if (timeElement) {
                timeElement.textContent = now.getHours().toString().padStart(2, '0') + ':' +
                                        now.getMinutes().toString().padStart(2, '0');
            }

            updateFloatingButtons();

            if (window.toast && Math.abs(walletBalance - oldBalance) >= 1000) {
                const difference = walletBalance - oldBalance;
                if (difference > 0) {
                    window.toast.success('Nouvelle transaction',
                        `+${new Intl.NumberFormat('fr-FR').format(difference)} F`);
                } else if (difference < 0) {
                    window.toast.info('Transaction débitée',
                        `${new Intl.NumberFormat('fr-FR').format(difference)} F`);
                }
            }
        }
    } catch (error) {
        console.error('Error refreshing balance:', error);
        if (window.toast) {
            window.toast.error('Erreur', 'Impossible de rafraîchir le solde');
        }
    } finally {
        if (refreshBtn) {
            const icon = refreshBtn.querySelector('i');
            icon.classList.remove('spin');
            refreshBtn.disabled = false;
            refreshBtn.style.opacity = '1';
        }
    }
}

function startAutoRefresh() {
    if (!autoRefreshEnabled) return;

    if (navigator.onLine) {
        refreshBalance();
    }

    clearInterval(refreshInterval);
    refreshInterval = setInterval(() => {
        if (navigator.onLine) {
            refreshBalance();
        }
    }, 3600000);
}

async function showFundingDetails(fundingId) {
    if (window.toast) {
        window.toast.info('Chargement', 'Chargement des détails...');
    }
}

async function creditFunding(event, fundingId) {
    event.stopPropagation();

    if (!confirm('Voulez-vous créditer ce financement sur votre portefeuille ?')) {
        return;
    }

    if (!navigator.onLine) {
        if (window.toast) {
            window.toast.error('Mode hors ligne', 'Cette opération nécessite une connexion Internet');
        }
        return;
    }

    const button = event.target.closest('button');
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    button.disabled = true;

    try {
        const response = await fetch(`/api/wallet/funding/${fundingId}/credit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            if (window.toast) {
                window.toast.success('Financement crédité', 'Le montant a été crédité sur votre portefeuille');
            }
            await refreshBalance();
            setTimeout(() => location.reload(), 1500);
        } else {
            button.innerHTML = originalText;
            button.disabled = false;
            if (window.toast) {
                window.toast.error('Erreur', data.message || 'Impossible de créditer le financement');
            }
        }
    } catch (error) {
        console.error('Error crediting funding:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        if (window.toast) {
            window.toast.error('Erreur', 'Une erreur est survenue');
        }
    }
}

function showTransactionDetails(transactionId) {
    if (window.toast) {
        window.toast.info('Transaction', 'Détails de transaction bientôt disponibles');
    }
}

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        startAutoRefresh();
    }
});

window.addEventListener('focus', function() {
    if (autoRefreshEnabled && navigator.onLine) {
        refreshBalance();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.slide-modal.show').forEach(modal => {
            closeSlide(modal.id);
        });
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshBalance();
    }

    if (e.key === 'd' && !e.ctrlKey && !e.metaKey) {
        e.preventDefault();
        showDepositModal();
    }

    if (e.key === 'w' && !e.ctrlKey && !e.metaKey && walletBalance >= 1000) {
        e.preventDefault();
        showWithdrawModal();
    }

    if (e.key === 'p' && !e.ctrlKey && !e.metaKey) {
        e.preventDefault();
        showPinModal();
    }
});

let inactivityTimer;
function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        if (pinVerified) {
            pinVerified = false;
            localStorage.removeItem('wallet_pin_token');
            localStorage.removeItem('wallet_pin_expiry');
            if (window.toast) {
                window.toast.info('Sécurité', 'Votre session PIN a expiré');
            }
        }
    }, 30 * 60 * 1000);
}

['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetInactivityTimer);
});

resetInactivityTimer();

window.addEventListener('resize', function() {
    updateFloatingButtons();
});

function highlightFloatingButton(type) {
    const button = document.querySelector(`.floating-action-btn.${type}`);
    if (button) {
        button.style.transform = 'scale(1.2)';
        button.style.boxShadow = '0 0 30px rgba(0,0,0,0.3)';

        setTimeout(() => {
            button.style.transform = '';
            button.style.boxShadow = '';
        }, 300);
    }
}
</script>
@endpush

