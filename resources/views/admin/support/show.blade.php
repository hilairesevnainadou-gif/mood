@extends('admin.layouts.app')

@section('title', 'Ticket support')
@section('page-title', 'Ticket support')
@section('page-subtitle', $ticket->ticket_number)

@section('content')
    <div class="admin-card">
        <h3>{{ $ticket->subject }}</h3>
        <p class="text-muted">Client: {{ $ticket->user?->full_name ?? 'N/A' }}</p>
        <p>{{ $ticket->description }}</p>
    </div>

    <div class="admin-section">
        <h2>Messages</h2>
        <div class="admin-thread">
            @forelse ($ticket->messages as $message)
                <div class="admin-thread-item {{ $message->is_admin ? 'admin' : 'client' }}">
                    <div class="admin-thread-meta">
                        <strong>{{ $message->is_admin ? 'Admin' : ($ticket->user?->full_name ?? 'Client') }}</strong>
                        <span>{{ optional($message->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <p>{{ $message->message }}</p>
                </div>
            @empty
                <p class="text-muted">Aucun message.</p>
            @endforelse
        </div>
    </div>

    <div class="admin-section">
        <h2>Répondre</h2>
        <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}">
            @csrf
            <div class="mb-3">
                <textarea class="form-control" rows="4" name="message" placeholder="Votre réponse..." required></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Envoyer</button>
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary">Retour</a>
        </form>
    </div>
@endsection
