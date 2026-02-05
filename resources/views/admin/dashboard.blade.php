@extends('admin.layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d’ensemble des indicateurs clés')

@section('content')
    <div class="admin-grid">
        <div class="admin-card">
            <h3>Utilisateurs</h3>
            <p class="admin-metric">{{ $stats['users'] }}</p>
            <span>{{ $stats['active_users'] }} actifs</span>
        </div>
        <div class="admin-card">
            <h3>Transactions</h3>
            <p class="admin-metric">{{ $stats['transactions'] }}</p>
            <span>Total enregistrées</span>
        </div>
        <div class="admin-card">
            <h3>Demandes de financement</h3>
            <p class="admin-metric">{{ $stats['funding_requests'] }}</p>
            <span>Soumises</span>
        </div>
        <div class="admin-card">
            <h3>Documents en attente</h3>
            <p class="admin-metric">{{ $stats['documents_pending'] }}</p>
            <span>À valider</span>
        </div>
        <div class="admin-card">
            <h3>Formations</h3>
            <p class="admin-metric">{{ $stats['trainings'] }}</p>
            <span>Catalogue</span>
        </div>
        <div class="admin-card">
            <h3>Tickets Support</h3>
            <p class="admin-metric">{{ $stats['support_tickets'] }}</p>
            <span>Ouverts</span>
        </div>
    </div>

    <div class="admin-section">
        <h2>Derniers utilisateurs</h2>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Inscription</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentUsers as $user)
                        <tr>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>{{ optional($user->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Aucun utilisateur récent.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-section">
        <h2>Dernières transactions</h2>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->reference ?? $transaction->transaction_id }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>{{ $transaction->formatted_amount ?? number_format($transaction->amount, 0, ',', ' ') . ' XOF' }}</td>
                            <td>{{ $transaction->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Aucune transaction récente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-section">
        <h2>Tickets support récents</h2>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Sujet</th>
                        <th>Client</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->user?->full_name ?? 'N/A' }}</td>
                            <td>{{ $ticket->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Aucun ticket récent.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
