@extends('admin.layouts.app')

@section('title', 'Détails transaction #' . ($transaction->reference ?? $transaction->id))
@section('page-title', 'Détails de la transaction')
@section('page-subtitle', 'Réf: ' . ($transaction->reference ?? $transaction->transaction_id))

@section('content')
    <!-- Header avec actions -->
    <div class="transaction-header">
        <div class="transaction-identity">
            <div class="transaction-icon-large {{ $transaction->type }}">
                <i class="fa-solid {{ $transaction->type_icon }}"></i>
            </div>
            <div class="transaction-info">
                <h1>Transaction #{{ $transaction->reference ?? $transaction->transaction_id }}</h1>
                <div class="transaction-meta">
                    <span class="type-badge-large {{ $transaction->type }}">
                        {{ $transaction->type_label }}
                    </span>
                    <span class="separator">•</span>
                    <span class="date">{{ $transaction->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="transaction-actions">
            @if($transaction->isPending())
                <form method="POST" action="{{ route('admin.transactions.validate', $transaction->id) }}" class="inline-form" onsubmit="return confirm('Valider cette transaction ?')">
                    @csrf
                    <button type="submit" class="btn-validate-lg">
                        <i class="fa-solid fa-check"></i> Valider
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.transactions.reject', $transaction->id) }}" class="inline-form" onsubmit="return confirm('Rejeter cette transaction ?')">
                    @csrf
                    <button type="submit" class="btn-reject-lg">
                        <i class="fa-solid fa-xmark"></i> Rejeter
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.transactions.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Statut de la transaction -->
    <div class="status-banner status-{{ $transaction->status }}">
        <div class="status-content">
            <i class="fa-solid {{ $transaction->status_icon }}"></i>
            <div>
                <span class="status-title">Statut: {{ $transaction->status_label }}</span>
                @if($transaction->completed_at)
                    <span class="status-date">Traitée le {{ $transaction->completed_at->format('d/m/Y à H:i') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="transaction-content-grid">
        <!-- Colonne gauche: Détails financiers -->
        <div class="transaction-column">
            <!-- Montants -->
            <div class="detail-card">
                <div class="card-header-detail">
                    <h3><i class="fa-solid fa-money-bill-wave"></i> Détails financiers</h3>
                </div>
                <div class="card-body-detail">
                    <div class="amount-display">
                        <span class="amount-label">Montant</span>
                        <span class="amount-value {{ $transaction->amount > 0 ? 'positive' : 'negative' }}">
                            {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->formatted_amount }}
                        </span>
                    </div>

                    <div class="amount-breakdown">
                        <div class="breakdown-row">
                            <span>Montant brut</span>
                            <span>{{ number_format($transaction->amount, 0, ',', ' ') }} XOF</span>
                        </div>
                        @if($transaction->fee > 0)
                            <div class="breakdown-row">
                                <span>Frais de transaction</span>
                                <span>{{ $transaction->formatted_fee }}</span>
                            </div>
                        @endif
                        <div class="breakdown-row total">
                            <span>Total</span>
                            <span>{{ $transaction->formatted_total }}</span>
                        </div>
                    </div>

                    <div class="payment-method">
                        <span class="method-label">Méthode de paiement</span>
                        <span class="method-value">
                            <i class="fa-solid fa-credit-card"></i>
                            {{ $transaction->payment_method ?? 'Non spécifiée' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($transaction->description)
                <div class="detail-card">
                    <div class="card-header-detail">
                        <h3><i class="fa-solid fa-align-left"></i> Description</h3>
                    </div>
                    <div class="card-body-detail">
                        <p class="description-text">{{ $transaction->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Métadonnées -->
            @if($transaction->metadata)
                <div class="detail-card">
                    <div class="card-header-detail">
                        <h3><i class="fa-solid fa-code"></i> Métadonnées</h3>
                    </div>
                    <div class="card-body-detail">
                        <pre class="metadata-json">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <!-- Colonne droite: Informations liées -->
        <div class="transaction-column">
            <!-- Utilisateur -->
            <div class="detail-card">
                <div class="card-header-detail">
                    <h3><i class="fa-solid fa-user"></i> Utilisateur</h3>
                </div>
                <div class="card-body-detail">
                    @if($transaction->wallet?->user)
                        <div class="user-card">
                            <div class="user-avatar-lg">
                                {{ $transaction->wallet->user->initials }}
                            </div>
                            <div class="user-info-detail">
                                <span class="user-name-lg">{{ $transaction->wallet->user->full_name }}</span>
                                <span class="user-email">{{ $transaction->wallet->user->email }}</span>
                                <span class="user-phone">{{ $transaction->wallet->user->phone ?? 'Pas de téléphone' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $transaction->wallet->user->id) }}" class="btn-view-user">
                            <i class="fa-solid fa-external-link-alt"></i> Voir le profil
                        </a>
                    @else
                        <div class="empty-info">
                            <i class="fa-solid fa-user-slash"></i>
                            <span>Utilisateur non trouvé</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Wallet -->
            <div class="detail-card">
                <div class="card-header-detail">
                    <h3><i class="fa-solid fa-wallet"></i> Wallet</h3>
                </div>
                <div class="card-body-detail">
                    @if($transaction->wallet)
                        <div class="wallet-info">
                            <div class="wallet-row">
                                <span class="wallet-label">ID Wallet</span>
                                <span class="wallet-value">#{{ $transaction->wallet->id }}</span>
                            </div>
                            <div class="wallet-row">
                                <span class="wallet-label">Adresse</span>
                                <span class="wallet-value mono">{{ Str::limit($transaction->wallet->wallet_address, 20) }}</span>
                            </div>
                            <div class="wallet-row">
                                <span class="wallet-label">Solde actuel</span>
                                <span class="wallet-value">{{ number_format($transaction->wallet->balance, 0, ',', ' ') }} XOF</span>
                            </div>
                        </div>
                    @else
                        <div class="empty-info">
                            <i class="fa-solid fa-wallet-slash"></i>
                            <span>Wallet non trouvé</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historique des statuts -->
            <div class="detail-card">
                <div class="card-header-detail">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Historique</h3>
                </div>
                <div class="card-body-detail">
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title">Transaction créée</span>
                                <span class="timeline-time">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        @if($transaction->isCompleted())
                            <div class="timeline-item active">
                                <div class="timeline-dot success"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Transaction validée</span>
                                    <span class="timeline-time">{{ $transaction->completed_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        @elseif($transaction->isFailed() || $transaction->isCancelled())
                            <div class="timeline-item active">
                                <div class="timeline-dot danger"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">Transaction {{ $transaction->status_label }}</span>
                                    <span class="timeline-time">{{ $transaction->completed_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item pending">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">En attente de validation</span>
                                    <span class="timeline-time">--</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Header */
    .transaction-header {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .transaction-identity {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .transaction-icon-large {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #fff;
    }

    .transaction-icon-large.deposit { background: linear-gradient(135deg, #10b981, #059669); }
    .transaction-icon-large.withdrawal { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .transaction-icon-large.transfer { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .transaction-icon-large.payment { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .transaction-info h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px 0;
    }

    .transaction-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-size: 0.9rem;
    }

    .type-badge-large {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .type-badge-large.deposit { background: #d1fae5; color: #059669; }
    .type-badge-large.withdrawal { background: #fee2e2; color: #dc2626; }
    .type-badge-large.transfer { background: #dbeafe; color: #1d4ed8; }
    .type-badge-large.payment { background: #f3e8ff; color: #7c3aed; }

    .transaction-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-validate-lg, .btn-reject-lg, .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .btn-validate-lg {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-validate-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }

    .btn-reject-lg {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-reject-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    .btn-back {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-back:hover {
        background: #e2e8f0;
        color: #374151;
    }

    /* Status banner */
    .status-banner {
        padding: 20px 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .status-banner.status-pending {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        color: #92400e;
    }

    .status-banner.status-completed {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    .status-banner.status-failed,
    .status-banner.status-cancelled {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }

    .status-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .status-content > i {
        font-size: 1.5rem;
    }

    .status-title {
        display: block;
        font-weight: 600;
        font-size: 1rem;
    }

    .status-date {
        display: block;
        font-size: 0.875rem;
        opacity: 0.8;
        margin-top: 4px;
    }

    /* Layout */
    .transaction-content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .transaction-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Cards */
    .detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .card-header-detail {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-detail h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .card-header-detail i {
        color: #3b82f6;
    }

    .card-body-detail {
        padding: 24px;
    }

    /* Amount display */
    .amount-display {
        text-align: center;
        padding-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 24px;
    }

    .amount-label {
        display: block;
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .amount-value {
        display: block;
        font-size: 2.5rem;
        font-weight: 700;
        font-family: monospace;
    }

    .amount-value.positive {
        color: #059669;
    }

    .amount-value.negative {
        color: #dc2626;
    }

    /* Breakdown */
    .amount-breakdown {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
    }

    .breakdown-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        color: #64748b;
    }

    .breakdown-row.total {
        padding-top: 12px;
        border-top: 2px solid #f1f5f9;
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
    }

    /* Payment method */
    .payment-method {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .method-label {
        font-size: 0.875rem;
        color: #64748b;
    }

    .method-value {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        color: #1e293b;
        font-weight: 500;
    }

    .method-value i {
        color: #94a3b8;
    }

    /* Description */
    .description-text {
        color: #374151;
        line-height: 1.6;
        margin: 0;
    }

    /* Metadata */
    .metadata-json {
        background: #1e293b;
        color: #e2e8f0;
        padding: 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        overflow-x: auto;
        margin: 0;
    }

    /* User card */
    .user-card {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .user-avatar-lg {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .user-info-detail {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-name-lg {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
    }

    .user-email {
        font-size: 0.9rem;
        color: #64748b;
    }

    .user-phone {
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .btn-view-user {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-view-user:hover {
        background: #3b82f6;
        color: #fff;
    }

    /* Wallet info */
    .wallet-info {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .wallet-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .wallet-label {
        font-size: 0.9rem;
        color: #64748b;
    }

    .wallet-value {
        font-weight: 500;
        color: #1e293b;
    }

    .wallet-value.mono {
        font-family: monospace;
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    /* Empty info */
    .empty-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 40px;
        color: #94a3b8;
    }

    .empty-info i {
        font-size: 2.5rem;
    }

    /* Timeline */
    .timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .timeline-item {
        display: flex;
        gap: 16px;
        position: relative;
        padding-bottom: 24px;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item.active:not(:last-child)::before {
        background: #3b82f6;
    }

    .timeline-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #e2e8f0;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e2e8f0;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .timeline-item.active .timeline-dot {
        background: #3b82f6;
        box-shadow: 0 0 0 2px #3b82f6;
    }

    .timeline-dot.success {
        background: #10b981;
        box-shadow: 0 0 0 2px #10b981;
    }

    .timeline-dot.danger {
        background: #ef4444;
        box-shadow: 0 0 0 2px #ef4444;
    }

    .timeline-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .timeline-title {
        font-weight: 600;
        color: #1e293b;
    }

    .timeline-time {
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .transaction-content-grid {
            grid-template-columns: 1fr;
        }

        .transaction-header {
            flex-direction: column;
            text-align: center;
        }

        .transaction-identity {
            flex-direction: column;
        }

        .transaction-actions {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 640px) {
        .amount-value {
            font-size: 1.75rem;
        }

        .btn-validate-lg, .btn-reject-lg, .btn-back {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
