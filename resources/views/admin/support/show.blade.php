@extends('admin.layouts.app')

@section('title', 'Ticket support')
@section('page-title', 'Ticket support')
@section('page-subtitle', $ticket->ticket_number)

@push('styles')
<style>
    /* Styles spécifiques au support ticket */
    .ticket-header {
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        border-left: 4px solid var(--admin-accent);
    }

    .ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--admin-border);
    }

    .ticket-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        color: var(--admin-text-muted);
    }

    .ticket-meta-item i {
        color: var(--admin-accent);
    }

    .ticket-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .ticket-status.open {
        background: rgba(59, 130, 246, 0.1);
        color: var(--admin-accent);
    }

    .ticket-status.pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--admin-warning);
    }

    .ticket-status.closed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--admin-success);
    }

    /* Thread de messages */
    .admin-thread {
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-height: 600px;
        overflow-y: auto;
        padding: 8px;
    }

    .admin-thread-item {
        display: flex;
        flex-direction: column;
        max-width: 80%;
        animation: slideIn 0.3s ease-out;
    }

    .admin-thread-item.admin {
        align-self: flex-end;
    }

    .admin-thread-item.client {
        align-self: flex-start;
    }

    .thread-bubble {
        padding: 16px 20px;
        border-radius: 20px;
        position: relative;
        box-shadow: var(--admin-shadow-sm);
    }

    .admin-thread-item.admin .thread-bubble {
        background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .admin-thread-item.client .thread-bubble {
        background: #fff;
        border: 1px solid var(--admin-border);
        border-bottom-left-radius: 4px;
    }

    .thread-bubble p {
        margin: 0;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .admin-thread-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 0.8rem;
    }

    .admin-thread-item.admin .admin-thread-meta {
        justify-content: flex-end;
        color: var(--admin-text-muted);
    }

    .admin-thread-item.client .admin-thread-meta {
        color: var(--admin-text-muted);
    }

    .thread-author {
        font-weight: 600;
        color: var(--admin-text);
    }

    .admin-thread-item.admin .thread-author {
        color: var(--admin-accent);
    }

    .thread-time {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .thread-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .admin-thread-item.admin .thread-avatar {
        background: var(--admin-accent);
        color: #fff;
        order: 2;
    }

    .admin-thread-item.client .thread-avatar {
        background: var(--admin-bg);
        color: var(--admin-text-muted);
        border: 2px solid var(--admin-border);
    }

    /* Formulaire de réponse */
    .reply-section {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--admin-border);
        box-shadow: var(--admin-shadow-sm);
    }

    .reply-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .reply-textarea {
        border: 2px solid var(--admin-border);
        border-radius: 12px;
        padding: 16px;
        font-family: inherit;
        font-size: 0.95rem;
        resize: vertical;
        transition: all 0.2s ease;
        background: var(--admin-bg);
    }

    .reply-textarea:focus {
        outline: none;
        border-color: var(--admin-accent);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .reply-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .reply-hint {
        font-size: 0.8rem;
        color: var(--admin-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Empty state */
    .thread-empty {
        text-align: center;
        padding: 48px 24px;
        color: var(--admin-text-muted);
    }

    .thread-empty i {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-thread-item {
            max-width: 90%;
        }

        .ticket-meta {
            flex-direction: column;
            gap: 8px;
        }

        .reply-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .reply-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
    <div class="admin-card ticket-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h3 class="mb-2">{{ $ticket->subject }}</h3>
                <div class="ticket-meta">
                    <div class="ticket-meta-item">
                        <i class="fa-solid fa-user"></i>
                        <span>{{ $ticket->user?->full_name ?? 'Client inconnu' }}</span>
                    </div>
                    <div class="ticket-meta-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $ticket->user?->email ?? 'N/A' }}</span>
                    </div>
                    <div class="ticket-meta-item">
                        <i class="fa-solid fa-calendar"></i>
                        <span>Créé le {{ $ticket->created_at?->format('d/m/Y à H:i') ?? 'Date inconnue' }}</span>
                    </div>
                </div>
            </div>
            <span class="ticket-status {{ $ticket->status ?? 'open' }}">
                <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                {{ ucfirst($ticket->status ?? 'Ouvert') }}
            </span>
        </div>

        <div class="mt-4 p-3 bg-light rounded-3">
            <p class="mb-0 text-secondary">{{ $ticket->description }}</p>
        </div>
    </div>

    <div class="admin-section mb-4">
        <h2 class="mb-4 d-flex align-items-center gap-2">
            <i class="fa-solid fa-comments text-primary"></i>
            Messages
            <span class="badge bg-secondary rounded-pill">{{ $ticket->messages?->count() ?? 0 }}</span>
        </h2>

        <div class="admin-thread" id="messageThread">
            @forelse ($ticket->messages as $message)
                <div class="admin-thread-item {{ $message->is_admin ? 'admin' : 'client' }}">
                    <div class="admin-thread-meta">
                        <div class="thread-avatar">
                            @if($message->is_admin)
                                <i class="fa-solid fa-shield-halved"></i>
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </div>
                        <span class="thread-author">
                            {{ $message->is_admin ? 'Support technique' : ($ticket->user?->full_name ?? 'Client') }}
                        </span>
                        <span class="thread-time">
                            <i class="fa-regular fa-clock"></i>
                            {{ optional($message->created_at)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="thread-bubble">
                        <p>{{ $message->message }}</p>
                    </div>
                </div>
            @empty
                <div class="thread-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Aucun message pour le moment.<br>Commencez la conversation ci-dessous.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="reply-section">
        <h2 class="mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-reply text-primary"></i>
            Répondre
        </h2>

        <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" class="reply-form">
            @csrf
            <textarea
                class="reply-textarea"
                rows="4"
                name="message"
                placeholder="Écrivez votre réponse ici..."
                required
                autofocus
            ></textarea>

            <div class="reply-actions">
                <span class="reply-hint">
                    <i class="fa-solid fa-circle-info"></i>
                    Cette réponse sera envoyée par email au client
                </span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Retour
                    </a>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Envoyer la réponse
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Scroll automatique vers le dernier message
    document.addEventListener('DOMContentLoaded', function() {
        const thread = document.getElementById('messageThread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    });
</script>
@endpush
