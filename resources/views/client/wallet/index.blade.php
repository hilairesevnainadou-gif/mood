@extends('layouts.client')

@section('title', 'Mon Portefeuille')

@push('styles')
<style>
/* Variables CSS */


/* Wallet Header */
.wallet-header {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
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
    margin-bottom: 0.75rem;
}

.wallet-title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.wallet-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-dot {
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
}

/* Balance Card */
.balance-card {
    background: white;
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--secondary-200);
    text-align: center;
    position: relative;
}

.balance-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-500) 0%, var(--primary-700) 100%);
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
}

.balance-label {
    font-size: 0.9rem;
    color: var(--secondary-600);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.balance-amount h2 {
    font-size: 3rem;
    font-weight: 800;
    color: var(--secondary-900);
    margin: 0;
    line-height: 1;
}

.balance-currency {
    font-size: 1.25rem;
    color: var(--secondary-600);
    font-weight: 600;
    margin-top: 0.5rem;
}

.balance-subtitle {
    color: var(--secondary-500);
    font-size: 0.9rem;
    margin: 1rem 0;
}

/* Action Buttons */
.balance-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.main-action-btn {
    padding: 0.75rem 1.25rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    color: white;
    flex: 1;
    min-width: 140px;
    justify-content: center;
}

.main-action-btn.deposit {
    background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%);
}

.main-action-btn.withdraw {
    background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%);
}

.main-action-btn.pin {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

.main-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Floating Actions */
.floating-actions-top {
    position: fixed;
    top: 100px;
    right: 1rem;
    z-index: 990;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.floating-action-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    color: white;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    box-shadow: var(--shadow-xl);
}

.floating-action-btn.deposit { background: linear-gradient(135deg, var(--success-500) 0%, var(--success-700) 100%); }
.floating-action-btn.withdraw { background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%); }
.floating-action-btn.pin { background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%); }

.floating-action-btn:hover {
    transform: scale(1.1);
}

/* Sections */
.section {
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--secondary-100);
}

.section-header h3 {
    font-size: 1.1rem;
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
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Funding Items */
.funding-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.funding-item {
    background: white;
    border-radius: var(--border-radius);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid var(--secondary-200);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.funding-item:hover {
    border-color: var(--primary-300);
    transform: translateX(4px);
    box-shadow: var(--shadow-sm);
}

.funding-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--border-radius);
    background: linear-gradient(135deg, var(--warning-500) 0%, var(--warning-700) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.funding-content {
    flex: 1;
    min-width: 0;
}

.funding-title {
    font-weight: 600;
    color: var(--secondary-800);
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.funding-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.8rem;
    color: var(--secondary-600);
}

.funding-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
}

/* Status Colors */
.status-submitted { background: var(--secondary-100); color: var(--secondary-700); }
.status-under_review { background: var(--info-100); color: var(--info-500); }
.status-validated { background: var(--primary-100); color: var(--primary-700); }
.status-approved { background: var(--success-100); color: var(--success-700); }
.status-paid { background: var(--warning-100); color: var(--warning-700); }
.status-documents_validated { background: var(--info-100); color: var(--info-500); }
.status-transfer_pending { background: var(--warning-100); color: var(--warning-700); }
.status-completed { background: var(--success-100); color: var(--success-700); }
.status-rejected { background: var(--error-100); color: var(--error-700); }
.status-funded { background: var(--success-100); color: var(--success-700); }
.status-credited { background: var(--success-100); color: var(--success-700); }

.funding-info {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.funding-amount {
    font-weight: 700;
    color: var(--primary-600);
}

.funding-btn {
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    border: 1px solid var(--secondary-200);
    text-align: center;
    transition: all var(--transition-fast);
}

.stat-card:hover {
    border-color: var(--primary-300);
    box-shadow: var(--shadow-sm);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-700) 100%);
}

.stat-card:nth-child(2) .stat-icon {
    background: linear-gradient(135deg, var(--error-500) 0%, var(--error-700) 100%);
}

.stat-card:nth-child(3) .stat-icon {
    background: linear-gradient(135deg, var(--warning-500) 0%, var(--warning-700) 100%);
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--secondary-900);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.85rem;
    color: var(--secondary-600);
}

/* Transactions */
.transactions-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.transaction-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border-radius: var(--border-radius);
    border: 1px solid var(--secondary-200);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.transaction-item:hover {
    border-color: var(--primary-300);
    transform: translateX(4px);
}

.transaction-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.transaction-icon.credit { background: var(--success-50); color: var(--success-600); }
.transaction-icon.debit { background: var(--error-50); color: var(--error-600); }
.transaction-icon.transfer { background: var(--warning-50); color: var(--warning-600); }
.transaction-icon.payment { background: var(--info-50); color: var(--info-500); }

.transaction-details {
    flex: 1;
    min-width: 0;
}

.transaction-title {
    font-weight: 600;
    color: var(--secondary-800);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.transaction-meta {
    display: flex;
    gap: 0.75rem;
    font-size: 0.75rem;
    color: var(--secondary-500);
}

.transaction-amount {
    font-weight: 700;
    font-size: 1rem;
    text-align: right;
    min-width: 100px;
}

.amount-positive { color: var(--success-600); }
.amount-negative { color: var(--error-600); }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--secondary-500);
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: var(--secondary-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--secondary-400);
    margin: 0 auto 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .wallet-header { padding: 1rem; }
    .wallet-title h1 { font-size: 1.25rem; }
    .balance-amount h2 { font-size: 2.5rem; }

    .floating-actions-top {
        position: fixed;
        bottom: 5rem;
        top: auto;
        right: 0.5rem;
        flex-direction: row;
        gap: 0.5rem;
    }

    .floating-action-btn {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
    }

    .balance-actions {
        flex-direction: column;
    }

    .main-action-btn {
        width: 100%;
    }

    .funding-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .funding-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>
@endpush

@section('content')
<div class="wallet-container">
    <!-- Floating Actions -->
    <div class="floating-actions-top">
        <button class="floating-action-btn deposit" onclick="showDepositModal()" title="Déposer">
            <i class="fas fa-plus"></i>
        </button>

        @if(($wallet->balance ?? 0) >= 1000)
        <button class="floating-action-btn withdraw" onclick="showWithdrawModal()" title="Retirer">
            <i class="fas fa-minus"></i>
        </button>
        @endif

        <button class="floating-action-btn pin" onclick="showPinModal()" title="Changer PIN">
            <i class="fas fa-key"></i>
        </button>
    </div>

    <!-- Header -->
    <div class="wallet-header">
        <div class="wallet-info">
            <div class="wallet-title">
                <h1><i class="fas fa-wallet"></i> Mon Portefeuille</h1>
                <div class="wallet-status">
                    <span class="status-dot"></span>
                    <span id="walletStatusText">En ligne</span>
                </div>
            </div>
            <div class="wallet-number">
                <i class="fas fa-id-card"></i>
                <span>{{ $wallet->wallet_number ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-label">
            <i class="fas fa-wallet"></i> Solde disponible
        </div>

        <div class="balance-amount">
            <h2 id="walletBalance">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</h2>
            <div class="balance-currency">Francs CFA</div>
        </div>

        <div class="balance-subtitle">
            <i class="fas fa-check-circle" style="color: var(--success-500);"></i> Solde à jour
        </div>

        <div class="balance-actions">
            <button class="main-action-btn deposit" onclick="showDepositModal()">
                <i class="fas fa-plus-circle"></i> Déposer
            </button>

            @if(($wallet->balance ?? 0) >= 1000)
            <button class="main-action-btn withdraw" onclick="showWithdrawModal()">
                <i class="fas fa-minus-circle"></i> Retirer
            </button>
            @endif

            <button class="main-action-btn pin" onclick="showPinModal()">
                <i class="fas fa-key"></i> PIN
            </button>
        </div>
    </div>

    <!-- Pending Fundings -->
    @if(isset($pendingFundings) && $pendingFundings->count() > 0)
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-clock"></i> Financements en attente</h3>
            <span class="section-badge">{{ $pendingFundings->count() }}</span>
        </div>

        <div class="funding-list">
            @foreach($pendingFundings as $funding)
            <div class="funding-item" onclick="showFundingDetails({{ $funding->id }})">
                <div class="funding-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="funding-content">
                    <div class="funding-title">{{ Str::limit($funding->title ?? $funding->request_number, 35) }}</div>
                    <div class="funding-meta">
                        <span><i class="far fa-calendar"></i> {{ $funding->created_at->format('d/m/Y') }}</span>
                        <span class="funding-status status-{{ $funding->status }}">
                            {{ $funding->status_label ?? ucfirst(str_replace('_', ' ', $funding->status)) }}
                        </span>
                    </div>
                    <div class="funding-info">
                        <span class="funding-amount">{{ number_format($funding->amount_requested, 0, ',', ' ') }} F</span>
                        @if($funding->amount_approved)
                        <span style="color: var(--success-600);">{{ number_format($funding->amount_approved, 0, ',', ' ') }} F</span>
                        @endif
                    </div>
                </div>
                @if(in_array($funding->status, ['completed', 'transfer_completed']) && !$funding->credited_at)
                <button class="funding-btn" onclick="event.stopPropagation(); creditFunding({{ $funding->id }})">
                    <i class="fas fa-check-circle"></i> Créditer
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Monthly Stats -->
    @if(isset($monthlyStats))
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-chart-bar"></i> Ce mois</h3>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="stat-value">{{ number_format($monthlyStats['deposits'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Dépôts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="stat-value">{{ number_format($monthlyStats['withdrawals'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Retraits</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                <div class="stat-value">{{ number_format($monthlyStats['payments'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Paiements</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Transactions -->
    <div class="section">
        <div class="section-header">
            <h3><i class="fas fa-exchange-alt"></i> Transactions</h3>
            @if(isset($transactions) && $transactions->count() > 0)
            <a href="{{ route('client.wallet.transactions') }}" style="color: var(--primary-500); font-size: 0.85rem;">
                Voir tout <i class="fas fa-arrow-right"></i>
            </a>
            @endif
        </div>

        @if(isset($transactions) && $transactions->count() > 0)
        <div class="transactions-list">
            @foreach($transactions->take(5) as $transaction)
            <div class="transaction-item" onclick="showTransactionDetails({{ $transaction->id }})">
                @php
                    $iconClass = $transaction->type;
                    $iconMap = [
                        'credit' => 'fa-arrow-down',
                        'debit' => 'fa-arrow-up',
                        'transfer' => 'fa-exchange-alt',
                        'payment' => 'fa-credit-card',
                        'fee' => 'fa-percentage',
                        'refund' => 'fa-undo'
                    ];
                    $isPositive = in_array($transaction->type, ['credit', 'refund']);
                    $isNegative = in_array($transaction->type, ['debit', 'payment', 'fee']);
                @endphp
                <div class="transaction-icon {{ $iconClass }}">
                    <i class="fas {{ $iconMap[$transaction->type] ?? 'fa-circle' }}"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-title">{{ Str::limit($transaction->description, 25) }}</div>
                    <div class="transaction-meta">
                        <span>{{ $transaction->created_at->format('d/m/Y') }}</span>
                        <span>{{ ucfirst($transaction->status) }}</span>
                    </div>
                </div>
                <div class="transaction-amount {{ $isPositive ? 'amount-positive' : ($isNegative ? 'amount-negative' : '') }}">
                    {{ $isPositive ? '+' : ($isNegative ? '-' : '') }}
                    {{ number_format($transaction->amount, 0, ',', ' ') }} F
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-exchange-alt"></i></div>
            <h4>Aucune transaction</h4>
            <p>Commencez par faire un dépôt</p>
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
@include('client.wallet.modals.deposit')
@include('client.wallet.modals.withdraw')
@include('client.wallet.modals.pin')

@endsection

@push('scripts')
<script>
// Variable globale UNIQUE - pas de "let" ou "const" ici
window.walletBalance = {{ $wallet->balance ?? 0 }};

// Fonctions globales
window.showDepositModal = function() {
    if (!navigator.onLine) {
        showToast('Connexion requise', 'error');
        return;
    }
    if (typeof window.DepositModal !== 'undefined') {
        window.DepositModal.open();
    } else {
        console.error('DepositModal non chargé');
        showToast('Erreur de chargement du modal', 'error');
    }
};

window.showWithdrawModal = function() {
    if (!navigator.onLine) {
        showToast('Connexion requise', 'error');
        return;
    }
    if (window.walletBalance < 1000) {
        showToast('Solde insuffisant (min: 1000 F)', 'error');
        return;
    }
    const modal = document.getElementById('withdrawSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

window.showPinModal = function() {
    const modal = document.getElementById('pinSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

window.closeSlide = function(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
};

// Toast notification
window.showToast = function(message, type = 'success') {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle' };

    toast.innerHTML = `<i class="fas ${icons[type]}"></i><span>${message}</span>`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#f59e0b'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Fermeture modals
    document.querySelectorAll('.slide-close, .modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.slide-modal, .modal-overlay');
            if (modal) closeSlide(modal.id);
        });
    });

    // Fermer au clic extérieur
    document.querySelectorAll('.slide-modal, .modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeSlide(this.id);
        });
    });

    // Touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.slide-modal.show, .modal-overlay.active').forEach(m => {
                closeSlide(m.id);
            });
        }
    });

    // Refresh auto seulement si en ligne
    if (navigator.onLine) {
        setInterval(() => {
            refreshBalance();
        }, 60000);
    }
});

window.refreshBalance = async function() {
    try {
        const response = await fetch('{{ route("client.wallet.get-info") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();
        if (data.success) {
            window.walletBalance = data.wallet.balance;
            const balanceEl = document.getElementById('walletBalance');
            if (balanceEl) {
                balanceEl.textContent = new Intl.NumberFormat('fr-FR').format(window.walletBalance);
            }
            updateButtons();
        }
    } catch (e) {
        console.error('Refresh error:', e);
    }
};

window.updateButtons = function() {
    const withdrawBtns = document.querySelectorAll('.main-action-btn.withdraw, .floating-action-btn.withdraw');
    withdrawBtns.forEach(btn => {
        btn.style.display = window.walletBalance >= 1000 ? 'flex' : 'none';
    });
};

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
`;
document.head.appendChild(style);
</script>
@endpush
