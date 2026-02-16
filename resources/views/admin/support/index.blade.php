@extends('admin.layouts.app')

@section('title', 'Support')
@section('page-title', 'Support')
@section('page-subtitle', 'Gestion des tickets clients')

@push('styles')
<style>
    /* Styles spécifiques au tableau de tickets */
    .tickets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .tickets-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stat-card {
        background: #fff;
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: var(--admin-shadow-sm);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-icon.open { background: rgba(59, 130, 246, 0.1); color: var(--admin-accent); }
    .stat-icon.pending { background: rgba(245, 158, 11, 0.1); color: var(--admin-warning); }
    .stat-icon.closed { background: rgba(16, 185, 129, 0.1); color: var(--admin-success); }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--admin-text);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Tableau amélioré */
    .tickets-table-container {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--admin-border);
        box-shadow: var(--admin-shadow);
        overflow: hidden;
    }

    .tickets-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .tickets-table thead th {
        background: var(--admin-bg);
        color: var(--admin-text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px;
        border-bottom: 2px solid var(--admin-border);
        white-space: nowrap;
    }

    .tickets-table tbody tr {
        transition: all 0.2s ease;
    }

    .tickets-table tbody tr:hover {
        background: var(--admin-card-hover);
        transform: scale(1.002);
        box-shadow: var(--admin-shadow-sm);
        z-index: 1;
        position: relative;
    }

    .tickets-table td {
        padding: 20px 16px;
        border-bottom: 1px solid var(--admin-border);
        vertical-align: middle;
    }

    .tickets-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Cellule Ticket */
    .ticket-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ticket-number {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--admin-accent);
        background: rgba(59, 130, 246, 0.1);
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        width: fit-content;
    }

    .ticket-date {
        font-size: 0.8rem;
        color: var(--admin-text-muted);
    }

    /* Cellule Sujet */
    .subject-cell {
        max-width: 300px;
    }

    .subject-text {
        font-weight: 500;
        color: var(--admin-text);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }

    /* Cellule Client */
    .client-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .client-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .client-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .client-name {
        font-weight: 500;
        color: var(--admin-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .client-email {
        font-size: 0.8rem;
        color: var(--admin-text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badges */
    .badge-priority, .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: 1px solid transparent;
    }

    /* Priorités */
    .badge-priority.low {
        background: rgba(16, 185, 129, 0.1);
        color: var(--admin-success);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .badge-priority.medium {
        background: rgba(245, 158, 11, 0.1);
        color: var(--admin-warning);
        border-color: rgba(245, 158, 11, 0.2);
    }

    .badge-priority.high {
        background: rgba(239, 68, 68, 0.1);
        color: var(--admin-danger);
        border-color: rgba(239, 68, 68, 0.2);
    }

    /* Statuts */
    .badge-status.open {
        background: rgba(59, 130, 246, 0.1);
        color: var(--admin-accent);
        border-color: rgba(59, 130, 246, 0.2);
    }

    .badge-status.pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--admin-warning);
        border-color: rgba(245, 158, 11, 0.2);
    }

    .badge-status.closed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--admin-success);
        border-color: rgba(16, 185, 129, 0.2);
    }

    /* Bouton action */
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid var(--admin-accent);
        color: var(--admin-accent);
        background: transparent;
        text-decoration: none;
    }

    .btn-view:hover {
        background: var(--admin-accent);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    /* Empty state */
    .tickets-empty {
        text-align: center;
        padding: 64px 24px;
        color: var(--admin-text-muted);
    }

    .tickets-empty i {
        font-size: 4rem;
        margin-bottom: 24px;
        opacity: 0.3;
        color: var(--admin-accent);
    }

    .tickets-empty h4 {
        color: var(--admin-text);
        margin-bottom: 8px;
    }

    /* Pagination */
    .pagination-container {
        padding: 20px;
        border-top: 1px solid var(--admin-border);
        display: flex;
        justify-content: center;
    }

    .pagination {
        margin: 0;
        gap: 4px;
    }

    .pagination .page-link {
        border: 1px solid var(--admin-border);
        color: var(--admin-text);
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background: var(--admin-accent);
        color: #fff;
        border-color: var(--admin-accent);
    }

    .pagination .page-item.active .page-link {
        background: var(--admin-accent);
        border-color: var(--admin-accent);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .tickets-table-container {
            overflow-x: auto;
        }

        .tickets-table {
            min-width: 800px;
        }

        .subject-cell {
            max-width: 200px;
        }
    }

    @media (max-width: 768px) {
        .tickets-header {
            flex-direction: column;
            align-items: stretch;
        }

        .tickets-stats {
            justify-content: center;
        }

        .stat-card {
            flex: 1;
            min-width: 120px;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
    <div class="tickets-header">
        <div>
            <h4 class="mb-1">Vue d'ensemble</h4>
            <p class="text-muted mb-0">Gérez et suivez tous les tickets clients</p>
        </div>

        <div class="tickets-stats">
            <div class="stat-card">
                <div class="stat-icon open">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $tickets->where('status', 'open')->count() ?? 0 }}</span>
                    <span class="stat-label">Ouverts</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $tickets->where('status', 'pending')->count() ?? 0 }}</span>
                    <span class="stat-label">En attente</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon closed">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $tickets->where('status', 'closed')->count() ?? 0 }}</span>
                    <span class="stat-label">Résolus</span>
                </div>
            </div>
        </div>
    </div>

    <div class="tickets-table-container">
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Sujet</th>
                    <th>Client</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>
                            <div class="ticket-cell">
                                <span class="ticket-number">#{{ $ticket->ticket_number }}</span>
                                <span class="ticket-date">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ $ticket->created_at?->format('d/m/Y') ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="subject-cell">
                            <div class="subject-text" title="{{ $ticket->subject }}">
                                {{ $ticket->subject }}
                            </div>
                        </td>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">
                                    {{ substr($ticket->user?->full_name ?? 'N/A', 0, 1) }}
                                </div>
                                <div class="client-info">
                                    <span class="client-name">{{ $ticket->user?->full_name ?? 'N/A' }}</span>
                                    <span class="client-email">{{ $ticket->user?->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $priorityClass = match($ticket->priority ?? 'medium') {
                                    'low' => 'low',
                                    'high' => 'high',
                                    default => 'medium'
                                };
                                $priorityLabel = match($ticket->priority ?? 'medium') {
                                    'low' => 'Basse',
                                    'high' => 'Haute',
                                    default => 'Moyenne'
                                };
                            @endphp
                            <span class="badge-priority {{ $priorityClass }}">
                                <i class="fa-solid fa-flag"></i>
                                {{ $priorityLabel }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($ticket->status ?? 'open') {
                                    'pending' => 'pending',
                                    'closed' => 'closed',
                                    default => 'open'
                                };
                                $statusLabel = match($ticket->status ?? 'open') {
                                    'pending' => 'En attente',
                                    'closed' => 'Résolu',
                                    default => 'Ouvert'
                                };
                            @endphp
                            <span class="badge-status {{ $statusClass }}">
                                <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn-view">
                                <i class="fa-solid fa-eye"></i>
                                Consulter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="tickets-empty">
                                <i class="fa-solid fa-inbox"></i>
                                <h4>Aucun ticket en cours</h4>
                                <p>Les tickets clients apparaîtront ici</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($tickets->hasPages())
            <div class="pagination-container">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
