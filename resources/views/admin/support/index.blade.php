@extends('admin.layouts.app')

@section('title', 'Support')
@section('page-title', 'Support')
@section('page-subtitle', 'Gestion des tickets clients')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Sujet</th>
                    <th>Client</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->user?->full_name ?? 'N/A' }}</td>
                        <td>{!! $ticket->priority_badge !!}</td>
                        <td>{!! $ticket->status_badge !!}</td>
                        <td>
                            <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                Consulter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Aucun ticket en cours.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tickets->links() }}
@endsection
