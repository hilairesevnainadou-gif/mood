@extends('layouts.client')

@section('title', 'Mon Portefeuille')

@push('styles')
<style>
/* Variables CSS */


/* Wallet Header */
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

/* Balance Card */
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

/* Sections */
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

/* Funding Items */
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

.status-submitted { background: var(--secondary-100); color: var(--secondary-700); }
.status-under_review { background: var(--info-100); color: var(--info-700); }
.status-validated { background: var(--primary-100); color: var(--primary-700); }
.status-approved { background: var(--success-100); color: var(--success-700); }
.status-paid { background: var(--warning-100); color: var(--warning-700); }
.status-documents_validated { background: var(--info-100); color: var(--info-700); }
.status-transfer_pending { background: var(--warning-100); color: var(--warning-700); }
.status-completed { background: var(--success-100); color: var(--success-700); }
.status-rejected { background: var(--error-100); color: var(--error-700); }
.status-funded { background: var(--success-100); color: var(--success-700); }
.status-credited { background: var(--success-100); color: var(--success-700); }

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

/* Transactions */
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

.transaction-icon.credit { background: var(--success-50); color: var(--success-500); }
.transaction-icon.debit { background: var(--error-50); color: var(--error-500); }
.transaction-icon.transfer { background: var(--warning-50); color: var(--warning-500); }
.transaction-icon.payment { background: var(--info-50); color: var(--info-500); }
.transaction-icon.fee { background: var(--secondary-50); color: var(--secondary-500); }
.transaction-icon.refund { background: var(--primary-50); color: var(--primary-500); }

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

/* Stats Grid */
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

/* Empty States */
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

/* Floating Actions */
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
    position: relative;
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

/* Balance Actions */
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

/* Slide Modals */
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

/* PIN Modal Specific Styles */
.pin-form-group {
    margin-bottom: 1.5rem;
}

.pin-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--secondary-700);
    font-size: 0.95rem;
}

.pin-input-wrapper {
    position: relative;
}

.pin-input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--secondary-500);
}

.pin-form-control {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 2px solid var(--secondary-200);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: all var(--transition-fast);
}

.pin-form-control:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
}

.pin-toggle-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--secondary-500);
    cursor: pointer;
    font-size: 1rem;
    padding: 0.25rem;
}

.pin-toggle-btn:hover {
    color: var(--primary-500);
}

.pin-help-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--secondary-500);
}

.pin-strength {
    margin-top: 0.75rem;
}

.strength-label {
    font-size: 0.85rem;
    color: var(--secondary-600);
    margin-bottom: 0.25rem;
}

.strength-bar {
    height: 4px;
    background: var(--secondary-200);
    border-radius: 2px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    width: 33%;
    transition: all var(--transition-fast);
    border-radius: 2px;
}

.strength-fill.weak { background: var(--error-500); }
.strength-fill.medium { background: var(--warning-500); }
.strength-fill.strong { background: var(--success-500); }

.security-tips {
    background: var(--warning-50);
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    border: 1px solid var(--warning-200);
}

.tips-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--warning-700);
    font-weight: 600;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}

.tips-list {
    margin: 0;
    padding-left: 1.25rem;
    color: var(--secondary-600);
    font-size: 0.85rem;
}

.tips-list li {
    margin-bottom: 0.25rem;
}

.tips-list li:last-child {
    margin-bottom: 0;
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

    .slide-content {
        max-width: 100%;
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

/* Animations */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

@section('content')
<div class="wallet-container">
    <!-- Floating Actions -->
    <div class="floating-actions-top">
        <div class="floating-action-item">
            <span class="floating-action-label">Déposer des fonds</span>
            <button class="floating-action-btn deposit" onclick="showDepositModal()">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        @if($wallet->balance >= 1000)
        <div class="floating-action-item">
            <span class="floating-action-label">Retirer des fonds</span>
            <button class="floating-action-btn withdraw" onclick="showWithdrawModal()">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        @endif

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

        <!-- Action Buttons -->
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

    <!-- Pending Fundings -->
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
                    <div class="funding-title">{{ Str::limit($funding->title ?? $funding->request_number, 40) }}</div>
                    <div class="funding-meta">
                        <span class="funding-date">
                            <i class="far fa-calendar"></i>
                            {{ $funding->created_at->format('d/m/Y') }}
                        </span>
                        <span class="funding-status status-{{ $funding->status }}">
                            {{ $funding->status_label ?? ucfirst(str_replace('_', ' ', $funding->status)) }}
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
                @if(in_array($funding->status, ['completed', 'credited']) && !$funding->credited_at)
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

    <!-- Monthly Stats -->
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
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-value">{{ number_format($monthlyStats['payments'] ?? 0, 0, ',', ' ') }}</div>
                <div class="stat-label">Paiements de frais</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Transactions -->
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
                <div class="transaction-icon {{ $transaction->type }}">
                    @switch($transaction->type)
                        @case('credit')
                            <i class="fas fa-arrow-down"></i>
                            @break
                        @case('debit')
                            <i class="fas fa-arrow-up"></i>
                            @break
                        @case('transfer')
                            <i class="fas fa-exchange-alt"></i>
                            @break
                        @case('payment')
                            <i class="fas fa-credit-card"></i>
                            @break
                        @case('fee')
                            <i class="fas fa-percentage"></i>
                            @break
                        @case('refund')
                            <i class="fas fa-undo"></i>
                            @break
                        @default
                            <i class="fas fa-circle"></i>
                    @endswitch
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
                    @php
                        $isPositive = in_array($transaction->type, ['credit', 'refund']);
                        $isNegative = in_array($transaction->type, ['debit', 'payment', 'fee']);
                    @endphp
                    <span class="{{ $isPositive ? 'amount-positive' : ($isNegative ? 'amount-negative' : '') }}">
                        @if($isPositive)
                            +
                        @elseif($isNegative)
                            -
                        @endif
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

<!-- PIN Modal -->
<div class="slide-modal" id="pinSlide">
    <div class="slide-content">
        <div class="slide-header">
            <h3><i class="fas fa-key"></i> Changer le code PIN</h3>
            <button class="slide-close" onclick="closeSlide('pinSlide')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="slide-body">
            <form id="pinForm">
                @csrf

                <div class="pin-form-group">
                    <label class="pin-form-label">
                        <i class="fas fa-lock"></i>
                        PIN actuel
                    </label>
                    <div class="pin-input-wrapper">
                        <i class="fas fa-lock pin-input-icon"></i>
                        <input type="password"
                               class="pin-form-control"
                               name="current_pin"
                               placeholder="6 chiffres"
                               maxlength="6"
                               pattern="[0-9]{6}">
                        <button type="button" class="pin-toggle-btn" onclick="togglePinVisibility(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="pin-help-text">
                        Laissez vide si c'est votre première configuration
                    </small>
                </div>

                <div class="pin-form-group">
                    <label class="pin-form-label">
                        <i class="fas fa-key"></i>
                        Nouveau PIN
                    </label>
                    <div class="pin-input-wrapper">
                        <i class="fas fa-key pin-input-icon"></i>
                        <input type="password"
                               class="pin-form-control"
                               name="new_pin"
                               placeholder="6 chiffres"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required
                               id="newPinInput">
                        <button type="button" class="pin-toggle-btn" onclick="togglePinVisibility(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="pin-strength">
                        <div class="strength-label">Force du PIN : <span id="pinStrengthText">Faible</span></div>
                        <div class="strength-bar">
                            <div class="strength-fill weak" id="pinStrengthBar"></div>
                        </div>
                    </div>
                </div>

                <div class="pin-form-group">
                    <label class="pin-form-label">
                        <i class="fas fa-check-circle"></i>
                        Confirmer le PIN
                    </label>
                    <div class="pin-input-wrapper">
                        <i class="fas fa-check-circle pin-input-icon"></i>
                        <input type="password"
                               class="pin-form-control"
                               name="new_pin_confirmation"
                               placeholder="6 chiffres"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required>
                        <button type="button" class="pin-toggle-btn" onclick="togglePinVisibility(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="security-tips">
                    <div class="tips-header">
                        <i class="fas fa-shield-alt"></i>
                        <span>Conseils de sécurité</span>
                    </div>
                    <ul class="tips-list">
                        <li>Utilisez 6 chiffres différents</li>
                        <li>Évitez les séquences simples (123456, 000000)</li>
                        <li>Ne partagez jamais votre PIN</li>
                        <li>Changez votre PIN régulièrement</li>
                    </ul>
                </div>

                <button type="submit" class="main-action-btn" style="width: 100%;">
                    <i class="fas fa-save"></i>
                    Enregistrer le nouveau PIN
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Include other modals -->
@include('client.wallet.modals.deposit')
@include('client.wallet.modals.withdraw')

@endsection

@push('scripts')
<script>
// Global variables
let walletBalance = {{ $wallet->balance ?? 0 }};
let lastRefreshTime = '{{ now()->format("Y-m-d H:i:s") }}';
let refreshInterval;
let autoRefreshEnabled = true;

// Modal functions
window.showPinModal = function() {
    if (!navigator.onLine) {
        showToast('Cette fonctionnalité nécessite une connexion Internet', 'error');
        return;
    }

    const modal = document.getElementById('pinSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Reset form
        const form = modal.querySelector('form');
        if (form) form.reset();
        
        // Reset strength indicator
        const strengthBar = document.getElementById('pinStrengthBar');
        const strengthText = document.getElementById('pinStrengthText');
        if (strengthBar) {
            strengthBar.className = 'strength-fill weak';
            strengthBar.style.width = '33%';
        }
        if (strengthText) strengthText.textContent = 'Faible';
        
        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input[name="current_pin"]');
            if (firstInput) firstInput.focus();
        }, 300);
    }
};

window.showDepositModal = function() {
    if (!navigator.onLine) {
        showToast('Cette fonctionnalité nécessite une connexion Internet', 'error');
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
        showToast('Cette fonctionnalité nécessite une connexion Internet', 'error');
        return;
    }

    if (walletBalance < 1000) {
        showToast('Minimum 1 000 FCFA requis pour un retrait', 'error');
        return;
    }

    const modal = document.getElementById('withdrawSlide');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

window.closeSlide = function(slideId) {
    const modal = document.getElementById(slideId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
};

// PIN visibility toggle
window.togglePinVisibility = function(btn) {
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('i');
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
};

// Toast function
function showToast(message, type = 'info') {
    if (window.toast) {
        switch(type) {
            case 'success': window.toast.success(message); break;
            case 'error': window.toast.error(message); break;
            case 'warning': window.toast.warning(message); break;
            default: window.toast.info(message);
        }
    } else {
        alert(message);
    }
}

// PIN strength checker
function updatePinStrength(pin) {
    const bar = document.getElementById('pinStrengthBar');
    const text = document.getElementById('pinStrengthText');
    
    if (!bar || !text) return;
    
    let strength = 'weak';
    let width = '33%';
    let label = 'Faible';
    
    if (pin.length === 6) {
        const hasRepeating = /^(\d)\1{5}$/.test(pin);
        const isSequential = /012345|123456|234567|345678|456789|987654|876543|765432|654321|543210/.test(pin);
        const isCommon = ['000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999', '123456', '654321'].includes(pin);
        
        if (hasRepeating || isSequential || isCommon) {
            strength = 'weak';
            width = '33%';
            label = 'Faible';
        } else if (/^(?=.*(\d)(?!\1))(?=.*(\d)(?!\1)(?!\2))(?=.*(\d)(?!\1)(?!\2)(?!\3)).{6}$/.test(pin)) {
            strength = 'strong';
            width = '100%';
            label = 'Fort';
        } else {
            strength = 'medium';
            width = '66%';
            label = 'Moyen';
        }
    }
    
    bar.className = 'strength-fill ' + strength;
    bar.style.width = width;
    text.textContent = label;
}

function isSimplePin(pin) {
    const patterns = ['000000', '111111', '222222', '333333', '444444', '555555', '666666', '777777', '888888', '999999', '123456', '654321', '121212', '112233', '111222'];
    if (/^(\d)\1{5}$/.test(pin)) return true;
    if (/012345|123456|234567|345678|456789/.test(pin)) return true;
    if (/987654|876543|765432|654321|543210/.test(pin)) return true;
    return patterns.includes(pin);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initWallet();
    initPinModal();
    startAutoRefresh();
    updateFloatingButtons();
});

function initWallet() {
    updateWalletOnlineStatus();
    window.addEventListener('online', updateWalletOnlineStatus);
    window.addEventListener('offline', updateWalletOnlineStatus);
    
    // Close modals on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.slide-modal.show').forEach(modal => {
                closeSlide(modal.id);
            });
        }
    });
    
    // Close on backdrop click
    document.querySelectorAll('.slide-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeSlide(this.id);
        });
    });
}

function initPinModal() {
    const newPinInput = document.getElementById('newPinInput');
    if (newPinInput) {
        newPinInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            updatePinStrength(this.value);
        });
    }
    
    // Restrict to numbers only
    const pinInputs = document.querySelectorAll('#pinSlide input[type="password"], #pinSlide input[type="text"]');
    pinInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    });
    
    // Form submission
    const pinForm = document.getElementById('pinForm');
    if (pinForm) {
        pinForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const newPin = formData.get('new_pin');
            const confirmPin = formData.get('new_pin_confirmation');
            
            if (newPin.length !== 6) {
                showToast('Le PIN doit contenir exactement 6 chiffres', 'warning');
                return;
            }
            
            if (newPin !== confirmPin) {
                showToast('Les PIN ne correspondent pas', 'error');
                return;
            }
            
            if (isSimplePin(newPin)) {
                showToast('Choisissez un PIN plus sécurisé', 'warning');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route("client.wallet.set-pin") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('PIN changé avec succès', 'success');
                    this.reset();
                    updatePinStrength('');
                    setTimeout(() => closeSlide('pinSlide'), 1500);
                } else {
                    showToast(data.message || 'Erreur lors du changement', 'error');
                }
            } catch (error) {
                showToast('Erreur de connexion', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
}

function updateWalletOnlineStatus() {
    const statusEl = document.getElementById('walletOnlineStatus');
    if (!statusEl) return;
    
    const isOnline = navigator.onLine;
    const dot = statusEl.querySelector('.status-dot');
    const text = statusEl.querySelector('span:last-child');
    
    if (isOnline) {
        dot.style.background = '#22c55e';
        dot.style.animation = 'pulse 2s infinite';
        text.textContent = 'En ligne';
        if (autoRefreshEnabled) refreshBalance();
    } else {
        dot.style.background = '#ef4444';
        dot.style.animation = 'none';
        text.textContent = 'Hors ligne';
        clearInterval(refreshInterval);
        showToast('Certaines fonctionnalités peuvent être limitées', 'warning');
    }
}

async function refreshBalance() {
    if (!navigator.onLine) return;
    
    const btn = document.querySelector('.balance-refresh-btn');
    const icon = btn?.querySelector('i');
    
    if (icon) icon.classList.add('spin');
    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.7';
    }
    
    try {
        const response = await fetch('{{ route("client.wallet.get-info") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            cache: 'no-cache'
        });
        
        if (!response.ok) throw new Error('Network error');
        
        const data = await response.json();
        
        if (data.success) {
            const oldBalance = walletBalance;
            walletBalance = data.wallet.balance;
            
            const balanceEl = document.getElementById('walletBalance');
            if (balanceEl) {
                balanceEl.textContent = new Intl.NumberFormat('fr-FR').format(walletBalance);
            }
            
            const timeEl = document.getElementById('balanceUpdateTime');
            if (timeEl) {
                const now = new Date();
                timeEl.textContent = now.getHours().toString().padStart(2, '0') + ':' + 
                                   now.getMinutes().toString().padStart(2, '0');
            }
            
            updateFloatingButtons();
            
            if (Math.abs(walletBalance - oldBalance) >= 1000) {
                const diff = walletBalance - oldBalance;
                if (diff > 0) {
                    showToast(`+${new Intl.NumberFormat('fr-FR').format(diff)} F`, 'success');
                }
            }
        }
    } catch (error) {
        console.error('Refresh error:', error);
    } finally {
        if (icon) icon.classList.remove('spin');
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    }
}

function updateFloatingButtons() {
    const withdrawItem = document.querySelector('.floating-action-item:nth-child(2)');
    const withdrawBtn = document.querySelector('.main-action-btn.withdraw');
    
    if (withdrawItem) {
        withdrawItem.style.display = walletBalance >= 1000 ? 'flex' : 'none';
    }
    if (withdrawBtn) {
        withdrawBtn.style.display = walletBalance >= 1000 ? 'flex' : 'none';
    }
}

function startAutoRefresh() {
    if (!autoRefreshEnabled) return;
    if (navigator.onLine) refreshBalance();
    clearInterval(refreshInterval);
    refreshInterval = setInterval(() => {
        if (navigator.onLine) refreshBalance();
    }, 3600000); // 1 hour
}

async function creditFunding(event, fundingId) {
    event.stopPropagation();
    
    if (!confirm('Voulez-vous créditer ce financement sur votre portefeuille ?')) return;
    if (!navigator.onLine) {
        showToast('Cette opération nécessite une connexion Internet', 'error');
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    btn.disabled = true;
    
    try {
        const response = await fetch(`/client/wallet/funding/${fundingId}/credit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Financement crédité avec succès', 'success');
            await refreshBalance();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Erreur lors du crédit', 'error');
        }
    } catch (error) {
        showToast('Erreur de connexion', 'error');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function showFundingDetails(fundingId) {
    showToast('Détails du financement - Fonctionnalité à venir', 'info');
}

function showTransactionDetails(transactionId) {
    showToast('Détails de la transaction - Fonctionnalité à venir', 'info');
}

// Visibility change handler
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        startAutoRefresh();
    }
});
</script>
@endpush