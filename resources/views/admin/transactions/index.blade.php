@extends('admin.layouts.app')

@section('title', 'Gestion des transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Suivi et validation des opérations financières')

@section('content')
    <!-- Stats des transactions -->
    <div class="transactions-stats">
        <div class="stat-box total">
            <div class="stat-icon">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number">{{ number_format($total_count ?? 0, 0, ',', ' ') }}</span>
                <span class="stat-label">Total transactions</span>
            </div>
        </div>
        <div class="stat-box pending">
            <div class="stat-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number">{{ number_format($pending_count ?? 0, 0, ',', ' ') }}</span>
                <span class="stat-label">En attente</span>
            </div>
        </div>
        <div class="stat-box completed">
            <div class="stat-icon">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number">{{ number_format($completed_count ?? 0, 0, ',', ' ') }}</span>
                <span class="stat-label">Validées</span>
            </div>
        </div>
        <div class="stat-box amount">
            <div class="stat-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="stat-details">
                <span class="stat-number">{{ number_format($total_amount ?? 0, 0, ',', ' ') }} XOF</span>
                <span class="stat-label">Volume total</span>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="transactions-toolbar">
        <form method="GET" class="toolbar-form" id="filterForm">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Rechercher par référence..." value="{{ $search ?? '' }}">
                @if($search)
                    <button type="button" class="clear-btn" onclick="clearSearch()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="credit" {{ ($type ?? '') == 'credit' ? 'selected' : '' }}>Dépôt</option>
                <option value="debit" {{ ($type ?? '') == 'debit' ? 'selected' : '' }}>Retrait</option>
                <option value="transfer" {{ ($type ?? '') == 'transfer' ? 'selected' : '' }}>Transfert</option>
                <option value="payment" {{ ($type ?? '') == 'payment' ? 'selected' : '' }}>Paiement</option>
            </select>

            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>Complétée</option>
                <option value="failed" {{ ($status ?? '') == 'failed' ? 'selected' : '' }}>Échouée</option>
                <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>

            <button type="submit" class="btn-filter">
                <i class="fa-solid fa-filter"></i> Filtrer
            </button>
        </form>

        <div class="toolbar-actions">
            <a href="{{ route('admin.transactions.export', request()->query()) }}" class="btn-export">
                <i class="fa-solid fa-download"></i> Exporter CSV
            </a>
        </div>
    </div>

    <!-- Table des transactions -->
    <div class="transactions-card">
        <div class="table-responsive">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th class="th-ref">Référence</th>
                        <th class="th-user">Client</th>
                        <th class="th-type">Type</th>
                        <th class="th-amount">Montant</th>
                        <th class="th-status">Statut</th>
                        <th class="th-date">Date</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr class="transaction-row" data-id="{{ $transaction->id }}">
                            <td class="td-ref">
                                <div class="ref-cell">
                                    <span class="ref-code">{{ $transaction->reference ?? $transaction->transaction_id }}</span>
                                    <span class="ref-wallet">{{ $transaction->wallet?->wallet_number ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="td-user">
                                @if($transaction->wallet?->user)
                                    <div class="user-mini">
                                        @if($transaction->wallet->user->photo)
                                            <img src="{{ asset('storage/' . $transaction->wallet->user->photo) }}" class="mini-avatar-img" alt="">
                                        @else
                                            <div class="mini-avatar">{{ substr($transaction->wallet->user->name, 0, 2) }}</div>
                                        @endif
                                        <div class="user-info">
                                            <span class="mini-name">{{ $transaction->wallet->user->name }}</span>
                                            <span class="mini-email">{{ $transaction->wallet->user->email }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Système</span>
                                @endif
                            </td>
                            <td class="td-type">
                                <span class="type-badge type-{{ $transaction->type }}">
                                    <i class="fa-solid {{ $transaction->type_icon_attribute ?? 'fa-circle' }}"></i>
                                    {{ $transaction->type_label_attribute ?? ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td class="td-amount">
                                <span class="amount-value {{ in_array($transaction->type, ['debit', 'withdrawal', 'payment']) ? 'negative' : 'positive' }}">
                                    {{ in_array($transaction->type, ['credit', 'deposit']) ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} XOF
                                </span>
                                @if($transaction->fee > 0)
                                    <span class="amount-fees">Frais: {{ number_format($transaction->fee, 0, ',', ' ') }} XOF</span>
                                @endif
                            </td>
                            <td class="td-status">
                                <span class="status-pill status-{{ $transaction->status }}">
                                    <i class="fa-solid {{ $transaction->status_icon_attribute ?? 'fa-circle' }}"></i>
                                    {{ $transaction->status_label_attribute ?? ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="td-date">
                                <div class="date-cell">
                                    <span class="date-main">{{ $transaction->created_at->format('d/m/Y') }}</span>
                                    <span class="date-time">{{ $transaction->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="td-action">
                                <div class="action-cell">
                                    <button class="btn-view" onclick="showDetails({{ $transaction->id }})" title="Voir détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    @if($transaction->status === 'pending' && in_array($transaction->type, ['debit', 'withdrawal']))
                                        <button type="button" class="btn-validate" onclick="openValidateModal({{ $transaction->id }})" title="Valider la transaction">
                                            <i class="fa-solid fa-check"></i>
                                        </button>

                                        <button type="button" class="btn-reject" onclick="openRejectModal({{ $transaction->id }})" title="Rejeter et rembourser">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @elseif($transaction->status === 'completed')
                                        <span class="status-done" title="Validée le {{ $transaction->completed_at?->format('d/m/Y H:i') }}">
                                            <i class="fa-solid fa-check-double"></i>
                                        </span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="status-failed" title="Rejetée">
                                            <i class="fa-solid fa-ban"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Détails cachés de la transaction -->
                        <tr class="details-row" id="details-{{ $transaction->id }}" style="display: none;">
                            <td colspan="7">
                                <div class="transaction-details-panel">
                                    <div class="details-grid">
                                        <div class="detail-item">
                                            <span class="detail-label">ID Transaction</span>
                                            <span class="detail-value">{{ $transaction->transaction_id }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Méthode de paiement</span>
                                            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Description</span>
                                            <span class="detail-value">{{ $transaction->description ?? 'N/A' }}</span>
                                        </div>
                                        @if($transaction->payment_method === 'mobile_money' && isset($transaction->metadata['phone_number']))
                                            <div class="detail-item">
                                                <span class="detail-label">Téléphone</span>
                                                <span class="detail-value">{{ $transaction->metadata['phone_number'] }}</span>
                                            </div>
                                        @elseif(isset($transaction->metadata['bank_name']))
                                            <div class="detail-item">
                                                <span class="detail-label">Banque</span>
                                                <span class="detail-value">{{ $transaction->metadata['bank_name'] }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Compte</span>
                                                <span class="detail-value">{{ $transaction->metadata['account_number'] }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Bénéficiaire</span>
                                                <span class="detail-value">{{ $transaction->metadata['account_name'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($transaction->metadata['note']))
                                            <div class="detail-item full-width">
                                                <span class="detail-label">Note du client</span>
                                                <span class="detail-value">{{ $transaction->metadata['note'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-content">
                                    <div class="empty-icon">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <h4>Aucune transaction trouvée</h4>
                                    <p>Essayez de modifier vos filtres de recherche</p>
                                    @if($search || $type || $status)
                                        <button class="btn-reset" onclick="resetFilters()">
                                            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="pagination-wrapper">
                {{ $transactions->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Modal de Validation -->
    <div class="modal-overlay" id="validateModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fa-solid fa-check-circle"></i> Valider le retrait</h3>
                <button class="modal-close" onclick="closeModal('validateModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-info-box success">
                    <i class="fa-solid fa-info-circle"></i>
                    <p>La transaction sera marquée comme complétée. Le client sera notifié par email.</p>
                </div>
                <form id="validateForm" method="POST" action="">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Notes administrateur (optionnel)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Commentaires internes..."></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('validateModal')">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check"></i> Confirmer la validation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Rejet -->
    <div class="modal-overlay" id="rejectModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fa-solid fa-times-circle"></i> Rejeter le retrait</h3>
                <button class="modal-close" onclick="closeModal('rejectModal')">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-info-box danger">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p><strong>Attention :</strong> Le montant sera recrédité sur le compte du client. Une justification est obligatoire.</p>
                </div>
                <form id="rejectForm" method="POST" action="">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Motif du rejet <span class="required">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Expliquez la raison du rejet au client..." required minlength="10"></textarea>
                        <small class="form-help">Minimum 10 caractères. Ce message sera envoyé au client par email.</small>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Annuler</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times"></i> Confirmer le rejet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Stats */
    .transactions-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-box {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .stat-box::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
    }

    .stat-box.total::before { background: #3b82f6; }
    .stat-box.pending::before { background: #f59e0b; }
    .stat-box.completed::before { background: #10b981; }
    .stat-box.amount::before { background: #8b5cf6; }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
    }

    .stat-box.total .stat-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-box.pending .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-box.completed .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-box.amount .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .stat-details {
        display: flex;
        flex-direction: column;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Toolbar */
    .transactions-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .toolbar-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 280px;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-box input {
        width: 100%;
        padding: 12px 40px 12px 42px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .clear-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: #f1f5f9;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
    }

    .filter-select {
        padding: 12px 36px 12px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 12px center;
        appearance: none;
        cursor: pointer;
        min-width: 150px;
    }

    .btn-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
    }

    .toolbar-actions {
        display: flex;
        gap: 12px;
    }

    .btn-export {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: #fff;
        color: #64748b;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-export:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    /* Table */
    .transactions-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .transactions-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .transactions-table th {
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        color: #64748b;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .transactions-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .transaction-row:hover {
        background: #f8fafc;
    }

    .details-row td {
        padding: 0;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Cells */
    .ref-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ref-code {
        font-family: monospace;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    .ref-wallet {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .user-mini {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mini-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .mini-avatar-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .mini-name {
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .mini-email {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    /* Type badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .type-badge.type-credit, .type-badge.type-deposit {
        background: #d1fae5;
        color: #059669;
    }

    .type-badge.type-debit, .type-badge.type-withdrawal {
        background: #fee2e2;
        color: #dc2626;
    }

    .type-badge.type-transfer {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .type-badge.type-payment {
        background: #f3e8ff;
        color: #7c3aed;
    }

    /* Amount */
    .td-amount {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .amount-value {
        font-family: monospace;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .amount-value.positive {
        color: #059669;
    }

    .amount-value.negative {
        color: #dc2626;
    }

    .amount-fees {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* Status pill */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-pill.status-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-pill.status-completed {
        background: #d1fae5;
        color: #059669;
    }

    .status-pill.status-failed {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-pill.status-cancelled {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Date */
    .date-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .date-main {
        color: #374151;
        font-weight: 500;
    }

    .date-time {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    /* Actions */
    .action-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-view, .btn-validate, .btn-reject {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .btn-view {
        background: #eff6ff;
        color: #3b82f6;
    }

    .btn-view:hover {
        background: #3b82f6;
        color: #fff;
    }

    .btn-validate {
        background: #f0fdf4;
        color: #22c55e;
    }

    .btn-validate:hover {
        background: #22c55e;
        color: #fff;
    }

    .btn-reject {
        background: #fef2f2;
        color: #ef4444;
    }

    .btn-reject:hover {
        background: #ef4444;
        color: #fff;
    }

    .status-done {
        color: #10b981;
        font-size: 1.1rem;
    }

    .status-failed {
        color: #ef4444;
        font-size: 1.1rem;
    }

    /* Details Panel */
    .transaction-details-panel {
        padding: 20px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-weight: 500;
        color: #1e293b;
    }

    /* Empty state */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 2rem;
    }

    .empty-content h4 {
        color: #1e293b;
        font-weight: 600;
        margin: 0;
    }

    .empty-content p {
        color: #64748b;
        margin: 0;
    }

    .btn-reset {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        margin-top: 8px;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
    }

    /* Modals */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-container {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-info-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .modal-info-box.success {
        background: #f0fdf4;
        color: #166534;
    }

    .modal-info-box.danger {
        background: #fef2f2;
        color: #991b1b;
    }

    .modal-info-box i {
        font-size: 1.25rem;
        margin-top: 2px;
    }

    .modal-info-box p {
        margin: 0;
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
        font-size: 0.9rem;
    }

    .required {
        color: #ef4444;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
        resize: vertical;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-help {
        display: block;
        margin-top: 6px;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        font-size: 0.95rem;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .transactions-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .transactions-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar-form {
            flex-direction: column;
        }

        .search-box {
            min-width: 100%;
        }

        .transactions-stats {
            grid-template-columns: 1fr;
        }

        .modal-actions {
            flex-direction: column;
        }

        .modal-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function clearSearch() {
        document.querySelector('input[name="search"]').value = '';
        document.getElementById('filterForm').submit();
    }

    function resetFilters() {
        window.location.href = '{{ route("admin.transactions.index") }}';
    }

    function showDetails(id) {
        const detailsRow = document.getElementById('details-' + id);
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
        } else {
            detailsRow.style.display = 'none';
        }
    }

    function openValidateModal(id) {
        const form = document.getElementById('validateForm');
        form.action = '{{ route("admin.transactions.validate", ":id") }}'.replace(':id', id);
        document.getElementById('validateModal').style.display = 'flex';
    }

    function openRejectModal(id) {
        const form = document.getElementById('rejectForm');
        form.action = '{{ route("admin.transactions.reject", ":id") }}'.replace(':id', id);
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Fermer modaux sur clic extérieur
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    // Auto-submit recherche avec debounce
    let searchTimeout;
    document.querySelector('input[name="search"]')?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        if(e.target.value.length > 2 || e.target.value.length === 0) {
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 800);
        }
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }
    });
</script>
@endpush
