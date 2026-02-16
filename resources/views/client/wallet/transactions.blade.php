@extends('layouts.client')

@section('title', 'Historique des Transactions - Mon Portefeuille')

@section('content')
<div class="wallet-transactions-container">
    {{-- Header --}}
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('client.wallet.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="header-text">
                <h1>Historique des Transactions</h1>
                <p>Toutes vos opérations financières</p>
            </div>
        </div>
    </div>

    {{-- Résumé --}}
    <div class="stats-summary">
        <div class="stat-card income">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Entrées</span>
                <strong class="stat-value">{{ number_format($stats['total_in'] ?? 0, 0, ',', ' ') }} FCFA</strong>
            </div>
        </div>
        <div class="stat-card expense">
            <div class="stat-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Sorties</span>
                <strong class="stat-value">{{ number_format($stats['total_out'] ?? 0, 0, ',', ' ') }} FCFA</strong>
            </div>
        </div>
        <div class="stat-card count">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Transactions</span>
                <strong class="stat-value">{{ $stats['count'] ?? 0 }}</strong>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="filters-section">
        <form method="GET" action="{{ route('client.wallet.transactions') }}" class="filters-form" id="filterForm">
            <div class="filter-group">
                <label>Type</label>
                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Dépôt</option>
                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Retrait</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfert</option>
                    <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Paiement</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Statut</label>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Du</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}" onchange="this.form.submit()">
            </div>

            <div class="filter-group">
                <label>Au</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}" onchange="this.form.submit()">
            </div>

            @if(request()->hasAny(['type', 'status', 'date_from', 'date_to']))
                <a href="{{ route('client.wallet.transactions') }}" class="reset-btn">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            @endif
        </form>
    </div>

    {{-- Liste des transactions --}}
    <div class="transactions-list">
        @forelse($transactions as $transaction)
            <div class="transaction-item {{ $transaction->type }} {{ $transaction->status }}" onclick="toggleDetails({{ $transaction->id }})">
                <div class="transaction-icon">
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
                        @default
                            <i class="fas fa-circle"></i>
                    @endswitch
                </div>

                <div class="transaction-content">
                    <div class="transaction-main">
                        <div class="transaction-info">
                            <div class="description-wrapper">
                                <h4 class="transaction-description {{ strlen($transaction->description ?? '') > 40 ? 'truncated' : '' }}"
                                    title="{{ $transaction->description }}">
                                    {{ Str::limit($transaction->description ?? 'Transaction sans description', 40, '...') }}
                                </h4>
                                @if(strlen($transaction->description ?? '') > 40)
                                    <span class="expand-hint">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                @endif
                            </div>

                            <span class="transaction-date">
                                <i class="far fa-clock"></i>
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div class="transaction-amount {{ in_array($transaction->type, ['credit', 'deposit']) ? 'positive' : 'negative' }}">
                            {{ in_array($transaction->type, ['credit', 'deposit']) ? '+' : '-' }}
                            {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                        </div>
                    </div>

                    <div class="transaction-meta">
                        <span class="transaction-type">{{ $transaction->getTypeLabel() }}</span>
                        <span class="transaction-status status-{{ $transaction->status }}">
                            {{ $transaction->getStatusLabel() }}
                        </span>
                        <span class="transaction-ref">Ref: {{ $transaction->reference ?? $transaction->transaction_id }}</span>
                    </div>

                    {{-- Détails expandables --}}
                    <div class="transaction-details" id="details-{{ $transaction->id }}" style="display: none;" onclick="event.stopPropagation()">
                        <div class="details-content">
                            <div class="detail-row">
                                <span>Description complète</span>
                                <p>{{ $transaction->description ?? 'Aucune description' }}</p>
                            </div>

                            @if($transaction->metadata)
                                @if(isset($transaction->metadata['phone_number']))
                                    <div class="detail-row">
                                        <span>Téléphone</span>
                                        <p>{{ $transaction->metadata['phone_number'] }}</p>
                                    </div>
                                @endif
                                @if(isset($transaction->metadata['phone']))
                                    <div class="detail-row">
                                        <span>Téléphone</span>
                                        <p>{{ $transaction->metadata['phone'] }}</p>
                                    </div>
                                @endif
                                @if(isset($transaction->metadata['bank_name']))
                                    <div class="detail-row">
                                        <span>Banque</span>
                                        <p>{{ $transaction->metadata['bank_name'] }}</p>
                                    </div>
                                @endif
                                @if(isset($transaction->metadata['account_name']))
                                    <div class="detail-row">
                                        <span>Bénéficiaire</span>
                                        <p>{{ $transaction->metadata['account_name'] }}</p>
                                    </div>
                                @endif
                                @if(isset($transaction->metadata['note']))
                                    <div class="detail-row">
                                        <span>Note</span>
                                        <p>{{ $transaction->metadata['note'] }}</p>
                                    </div>
                                @endif
                                @if(isset($transaction->metadata['rejection_reason']))
                                    <div class="detail-row alert">
                                        <span>Motif du rejet</span>
                                        <p>{{ $transaction->metadata['rejection_reason'] }}</p>
                                    </div>
                                @endif
                            @endif

                            @if($transaction->payment_method)
                                <div class="detail-row">
                                    <span>Méthode de paiement</span>
                                    <p>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</p>
                                </div>
                            @endif

                            <div class="detail-row">
                                <span>ID Transaction</span>
                                <p class="mono">{{ $transaction->transaction_id }}</p>
                            </div>

                            @if($transaction->completed_at)
                                <div class="detail-row">
                                    <span>Date de complétion</span>
                                    <p>{{ $transaction->completed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif

                            @if($transaction->fee > 0)
                                <div class="detail-row">
                                    <span>Frais</span>
                                    <p>{{ number_format($transaction->fee, 0, ',', ' ') }} FCFA</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Indicateur d'expansion --}}
                <div class="expand-indicator">
                    <i class="fas fa-chevron-down" id="indicator-{{ $transaction->id }}"></i>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3>Aucune transaction</h3>
                <p>Vous n'avez pas encore effectué de transactions.</p>
                <a href="{{ route('client.wallet.index') }}" class="btn-primary">
                    <i class="fas fa-wallet"></i> Accéder au portefeuille
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
        <div class="pagination-container">
            {{ $transactions->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Container */
.wallet-transactions-container {
    min-height: 100vh;
    background: #f8fafc;
    padding-bottom: 2rem;
}

/* Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1.5rem;
    padding-top: calc(1.5rem + env(safe-area-inset-top, 0px));
    color: white;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.back-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
    flex-shrink: 0;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.header-text h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.header-text p {
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Stats Summary */
.stats-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem;
    max-width: 1200px;
    margin: -1.5rem auto 0;
    position: relative;
    z-index: 10;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.stat-card.income .stat-icon {
    background: #dcfce7;
    color: #16a34a;
}

.stat-card.expense .stat-icon {
    background: #fee2e2;
    color: #dc2626;
}

.stat-card.count .stat-icon {
    background: #e0e7ff;
    color: #4f46e5;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-top: 0.25rem;
    word-break: break-word;
}

/* Filters */
.filters-section {
    padding: 1rem;
    max-width: 1200px;
    margin: 1rem auto 0;
}

.filters-form {
    background: white;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-group {
    flex: 1;
    min-width: 140px;
}

.filter-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}

.filter-select,
.filter-input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem;
    background: white;
    color: #1e293b;
    transition: all 0.2s;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #667eea;
}

.reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: #fee2e2;
    color: #dc2626;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    margin-bottom: 0.25rem;
}

.reset-btn:hover {
    background: #fecaca;
}

/* Transactions List */
.transactions-list {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.transaction-item {
    background: white;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
    cursor: pointer;
    position: relative;
}

.transaction-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.transaction-item.expanded {
    box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.15);
}

.transaction-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.transaction-item.credit .transaction-icon,
.transaction-item.deposit .transaction-icon {
    background: #dcfce7;
    color: #16a34a;
}

.transaction-item.debit .transaction-icon,
.transaction-item.withdrawal .transaction-icon {
    background: #fee2e2;
    color: #dc2626;
}

.transaction-item.transfer .transaction-icon {
    background: #fef3c7;
    color: #d97706;
}

.transaction-item.payment .transaction-icon {
    background: #e0e7ff;
    color: #4f46e5;
}

.transaction-content {
    flex: 1;
    min-width: 0;
}

.transaction-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.transaction-info {
    flex: 1;
    min-width: 0;
}

/* Description avec gestion du texte long */
.description-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.transaction-description {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    line-height: 1.4;
}

.transaction-description.truncated {
    cursor: help;
}

.expand-hint {
    color: #94a3b8;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.transaction-date {
    font-size: 0.75rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.transaction-amount {
    font-size: 1rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
}

.transaction-amount.positive {
    color: #16a34a;
}

.transaction-amount.negative {
    color: #dc2626;
}

.transaction-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 0.5rem;
}

.transaction-type {
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    background: #f1f5f9;
    color: #64748b;
    border-radius: 20px;
    font-weight: 500;
}

.transaction-status {
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    border-radius: 20px;
    font-weight: 500;
}

.status-completed {
    background: #dcfce7;
    color: #16a34a;
}

.status-pending {
    background: #fef3c7;
    color: #d97706;
}

.status-processing {
    background: #dbeafe;
    color: #2563eb;
}

.status-failed {
    background: #fee2e2;
    color: #dc2626;
}

.status-cancelled {
    background: #f3f4f6;
    color: #6b7280;
}

.transaction-ref {
    font-size: 0.75rem;
    color: #94a3b8;
    font-family: monospace;
}

/* Expandable Details */
.transaction-details {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px dashed #e2e8f0;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.details-content {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.detail-row {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-row.alert {
    background: #fee2e2;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 4px solid #dc2626;
}

.detail-row.alert span {
    color: #dc2626;
    font-weight: 600;
}

.detail-row.alert p {
    color: #7f1d1d;
}

.detail-row span {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 500;
}

.detail-row p {
    margin: 0;
    font-size: 0.875rem;
    color: #1e293b;
    line-height: 1.5;
    word-break: break-word;
}

.detail-row p.mono {
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    color: #64748b;
    background: #f1f5f9;
    padding: 0.5rem;
    border-radius: 6px;
}

/* Expand Indicator */
.expand-indicator {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: all 0.3s;
    flex-shrink: 0;
    margin-top: 0.5rem;
}

.transaction-item.expanded .expand-indicator {
    transform: rotate(180deg);
    color: #667eea;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #94a3b8;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.empty-state p {
    color: #64748b;
    margin: 0 0 1.5rem 0;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Pagination */
.pagination-container {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-summary {
        grid-template-columns: 1fr;
        margin-top: -2rem;
    }

    .filters-form {
        flex-direction: column;
    }

    .filter-group {
        width: 100%;
    }

    .transaction-main {
        flex-direction: column;
        gap: 0.5rem;
    }

    .transaction-amount {
        align-self: flex-start;
    }

    .description-wrapper {
        flex-wrap: wrap;
    }

    .stat-value {
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleDetails(transactionId) {
    const details = document.getElementById('details-' + transactionId);
    const indicator = document.getElementById('indicator-' + transactionId);
    const item = details.closest('.transaction-item');

    if (details.style.display === 'none' || details.style.display === '') {
        // Fermer tous les autres détails ouverts
        document.querySelectorAll('.transaction-details').forEach(d => {
            if (d.id !== 'details-' + transactionId) {
                d.style.display = 'none';
            }
        });
        document.querySelectorAll('.transaction-item.expanded').forEach(i => {
            if (!i.contains(details)) {
                i.classList.remove('expanded');
            }
        });
        document.querySelectorAll('.expand-indicator i').forEach(ind => {
            if (ind.id !== 'indicator-' + transactionId) {
                ind.classList.remove('fa-chevron-up');
                ind.classList.add('fa-chevron-down');
            }
        });

        // Ouvrir celui-ci
        details.style.display = 'block';
        indicator.classList.remove('fa-chevron-down');
        indicator.classList.add('fa-chevron-up');
        item.classList.add('expanded');
    } else {
        // Fermer
        details.style.display = 'none';
        indicator.classList.remove('fa-chevron-up');
        indicator.classList.add('fa-chevron-down');
        item.classList.remove('expanded');
    }
}

// Empêcher la fermeture quand on clique dans les détails
document.querySelectorAll('.transaction-details').forEach(detail => {
    detail.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
@endpush
