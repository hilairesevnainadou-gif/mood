@extends('layouts.client')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
{{-- MODAL DE CONFIRMATION UNIVERSEL --}}
<div id="confirmModal" class="pwa-modal" style="display: none;">
    <div class="pwa-modal-overlay" onclick="closeModal()"></div>
    <div class="pwa-modal-content">
        <div class="pwa-modal-header">
            <i id="modalIcon" class="fas fa-question-circle"></i>
            <h3 id="modalTitle">Confirmation</h3>
        </div>
        <div class="pwa-modal-body">
            <p id="modalMessage">Êtes-vous sûr ?</p>
        </div>
        <div class="pwa-modal-footer">
            <button type="button" class="pwa-btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Annuler
            </button>
            <button type="button" id="modalConfirmBtn" class="pwa-btn-confirm">
                <i class="fas fa-check"></i> Confirmer
            </button>
        </div>
    </div>
</div>

{{-- FORMS CACHÉS POUR LES ACTIONS --}}
<form id="closeTicketForm" action="{{ route('client.support.close', $ticket->id) }}" method="POST" style="display: none;">
    @csrf
</form>
<form id="reopenTicketForm" action="{{ route('client.support.reopen', $ticket->id) }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    // Fonctions du Modal
    function openModal(config) {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        const icon = document.getElementById('modalIcon');
        const confirmBtn = document.getElementById('modalConfirmBtn');

        title.textContent = config.title || 'Confirmation';
        message.textContent = config.message || 'Êtes-vous sûr ?';
        icon.className = config.icon || 'fas fa-question-circle';

        // Style du bouton confirm
        confirmBtn.className = 'pwa-btn-confirm ' + (config.btnClass || '');
        confirmBtn.onclick = function() {
            config.onConfirm();
            closeModal();
        };

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('confirmModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Fonctions spécifiques
    function confirmCloseTicket() {
        openModal({
            title: 'Fermer le ticket',
            message: 'Êtes-vous sûr de vouloir fermer ce ticket ? Cette action est irréversible.',
            icon: 'fas fa-times-circle text-danger',
            btnClass: 'btn-danger',
            onConfirm: function() {
                document.getElementById('closeTicketForm').submit();
            }
        });
    }

    function confirmReopenTicket() {
        openModal({
            title: 'Rouvrir le ticket',
            message: 'Souhaitez-vous rouvrir ce ticket pour ajouter de nouveaux messages ?',
            icon: 'fas fa-redo text-warning',
            btnClass: 'btn-warning',
            onConfirm: function() {
                document.getElementById('reopenTicketForm').submit();
            }
        });
    }

    // Menu toggle functions
    function toggleTicketMenu(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('ticketMenu');
        if (menu) menu.classList.toggle('show');
    }

    function focusReply() {
        const textarea = document.getElementById('replyTextarea');
        if (textarea) {
            textarea.focus();
            setTimeout(function() {
                textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }

    // Fermer menu quand on clique ailleurs
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('ticketMenu');
        const btn = document.querySelector('.pwa-header-action');
        if (menu && !menu.contains(e.target) && btn && !btn.contains(e.target)) {
            menu.classList.remove('show');
        }
    });

    // Fermer modal avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

<div class="pwa-ticket-detail">
    {{-- Header Mobile --}}
    <div class="pwa-page-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <a href="{{ route('client.support') }}" class="pwa-back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pwa-header-text">
                <h1>Ticket #{{ $ticket->ticket_number }}</h1>
                <p>{{ $ticket->category_label }}</p>
            </div>
            <button type="button" class="pwa-header-action" onclick="toggleTicketMenu(event)">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

        {{-- Menu actions rapides (dropdown) --}}
        <div class="pwa-ticket-menu" id="ticketMenu">
            @if ($ticket->canBeReplied())
                <button type="button" onclick="focusReply()" class="pwa-menu-item">
                    <i class="fas fa-reply"></i> Répondre
                </button>
            @endif
            @if ($ticket->isOpen() || $ticket->isInProgress())
                <button type="button" class="pwa-menu-item text-danger" onclick="confirmCloseTicket()">
                    <i class="fas fa-times"></i> Fermer
                </button>
            @endif
            @if ($ticket->isClosed() || $ticket->isResolved())
                <button type="button" class="pwa-menu-item text-warning" onclick="confirmReopenTicket()">
                    <i class="fas fa-redo"></i> Rouvrir
                </button>
            @endif
        </div>
    </div>

    {{-- Le reste du contenu reste identique... --}}
    {{-- Info Card --}}
    <div class="pwa-ticket-info-card">
        <div class="pwa-ticket-status-row">
            <div class="pwa-status-badge {{ $ticket->status }}">
                @if ($ticket->status == 'open')
                    <i class="fas fa-clock"></i> Ouvert
                @elseif($ticket->status == 'in_progress')
                    <i class="fas fa-sync-alt fa-spin"></i> En cours
                @elseif($ticket->status == 'resolved')
                    <i class="fas fa-check-circle"></i> Résolu
                @else
                    <i class="fas fa-times-circle"></i> Fermé
                @endif
            </div>
            <span class="pwa-priority-badge {{ $ticket->priority }}">
                {!! $ticket->priority_badge !!}
            </span>
        </div>

        <h2 class="pwa-ticket-subject">{{ $ticket->subject }}</h2>

        <div class="pwa-ticket-meta-grid">
            <div class="pwa-meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ $ticket->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="pwa-meta-item">
                <i class="fas fa-user"></i>
                <span>{{ $ticket->user->name }}</span>
            </div>
            @if ($ticket->assignee)
                <div class="pwa-meta-item">
                    <i class="fas fa-headset"></i>
                    <span>{{ $ticket->assignee->name }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Timeline --}}
    <div class="pwa-section">
        <h3 class="pwa-section-title">Progression</h3>
        <div class="pwa-timeline">
            <div class="pwa-timeline-item {{ in_array($ticket->status, ['open', 'in_progress', 'resolved', 'closed']) ? 'completed' : '' }}">
                <div class="pwa-timeline-dot"></div>
                <div class="pwa-timeline-content">
                    <span>Créé</span>
                    <small>{{ $ticket->created_at->format('d/m') }}</small>
                </div>
            </div>
            <div class="pwa-timeline-item {{ in_array($ticket->status, ['in_progress', 'resolved', 'closed']) ? 'completed' : '' }}">
                <div class="pwa-timeline-dot"></div>
                <div class="pwa-timeline-content">
                    <span>En cours</span>
                    <small>
                        @if ($ticket->messages->where('is_admin', true)->count() > 0)
                            {{ $ticket->messages->where('is_admin', true)->first()->created_at->format('d/m') }}
                        @else
                            --
                        @endif
                    </small>
                </div>
            </div>
            <div class="pwa-timeline-item {{ in_array($ticket->status, ['resolved', 'closed']) ? 'completed' : '' }}">
                <div class="pwa-timeline-dot"></div>
                <div class="pwa-timeline-content">
                    <span>Résolu</span>
                    <small>{{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m') : '--' }}</small>
                </div>
            </div>
            <div class="pwa-timeline-item {{ $ticket->status == 'closed' ? 'completed' : '' }}">
                <div class="pwa-timeline-dot"></div>
                <div class="pwa-timeline-content">
                    <span>Fermé</span>
                    <small>{{ $ticket->closed_at ? $ticket->closed_at->format('d/m') : '--' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="pwa-section">
        <h3 class="pwa-section-title">Description</h3>
        <div class="pwa-description-card">
            {{ $ticket->description }}
        </div>

        @if ($ticket->metadata && isset($ticket->metadata['attachments']))
            <div class="pwa-attachments-section">
                <h4>Pièces jointes</h4>
                <div class="pwa-attachments-list">
                    @foreach ($ticket->metadata['attachments'] as $attachment)
                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="pwa-attachment-chip">
                            <i class="fas fa-paperclip"></i>
                            <span class="pwa-attachment-name">{{ \Illuminate\Support\Str::limit($attachment['name'], 20) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Conversation --}}
    <div class="pwa-section">
        <h3 class="pwa-section-title">
            Conversation
            <span class="pwa-message-count">{{ $ticket->messages->count() }}</span>
        </h3>

        <div class="pwa-messages-wrapper" id="messagesWrapper">
            @forelse($ticket->messages->sortBy('created_at') as $message)
                <div class="pwa-message-row {{ $message->is_admin ? 'admin' : 'client' }}">
                    <div class="pwa-message-bubble">
                        <div class="pwa-message-header">
                            <span class="pwa-message-author">{{ $message->is_admin ? 'Support Client' : $message->user->name }}</span>
                            <span class="pwa-message-time">{{ $message->created_at->format('H:i') }}</span>
                        </div>

                        <div class="pwa-message-content">
                            {{ $message->message }}
                        </div>

                        @if (is_array($message->attachments) && count($message->attachments) > 0)
                            <div class="pwa-message-attachments">
                                @foreach ($message->attachments as $index => $attachment)
                                    @php
                                        $attachmentName = $attachment['name'] ?? 'Fichier_' . ($index + 1);
                                        $attachmentPath = $attachment['path'] ?? null;
                                    @endphp

                                    @if ($attachmentPath)
                                        <a href="{{ route('client.support.download-attachment', [
                                            'ticketId' => $ticket->id,
                                            'messageId' => $message->id,
                                            'attachmentIndex' => $index,
                                        ]) }}" class="pwa-attachment-link" target="_blank">
                                            <i class="fas fa-download"></i>
                                            {{ \Illuminate\Support\Str::limit($attachmentName, 20) }}
                                            <small>({{ isset($attachment['size']) ? number_format($attachment['size'] / 1024, 1) . ' KB' : 'N/A' }})</small>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="pwa-message-avatar">
                        {{ strtoupper(substr($message->is_admin ? 'S' : $message->user->name, 0, 1)) }}
                    </div>
                </div>
            @empty
                <div class="pwa-empty-conversation">
                    <i class="fas fa-comments"></i>
                    <p>Aucun message pour le moment</p>
                    @if ($ticket->canBeReplied())
                        <button type="button" onclick="focusReply()" class="pwa-btn-primary sm">
                            Envoyer un message
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    {{-- Espace pour la barre de saisie --}}
    <div style="height: 200px;"></div>
</div>

{{-- Formulaire de réponse --}}
@if ($ticket->canBeReplied())
    <div class="pwa-reply-bar" id="replyForm">
        <form action="{{ route('client.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" id="replyFormElement">
            @csrf
            <div class="pwa-reply-input-group">
                <button type="button" class="pwa-attachment-btn" onclick="document.getElementById('replyAttachments').click()" title="Ajouter des pièces jointes">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="replyAttachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="display: none;">

                <textarea name="message" id="replyTextarea" class="pwa-reply-textarea" placeholder="Écrivez votre message..." rows="1" required></textarea>

                <button type="submit" class="pwa-send-btn" title="Envoyer">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <div id="attachmentPreview" class="pwa-attachment-preview-container"></div>
        </form>
    </div>

    <script>
        // Gestion upload fichiers
        (function() {
            const textarea = document.getElementById('replyTextarea');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                });
            }

            const messagesWrapper = document.getElementById('messagesWrapper');
            if (messagesWrapper && messagesWrapper.lastElementChild) {
                messagesWrapper.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }

            const fileInput = document.getElementById('replyAttachments');
            const previewContainer = document.getElementById('attachmentPreview');
            let filesArray = [];

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const newFiles = Array.from(e.target.files);
                    filesArray = [...filesArray, ...newFiles];
                    updateFileInput();
                    renderPreview();
                });
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            function getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const icons = {
                    'pdf': 'fa-file-pdf',
                    'doc': 'fa-file-word',
                    'docx': 'fa-file-word',
                    'jpg': 'fa-file-image',
                    'jpeg': 'fa-file-image',
                    'png': 'fa-file-image'
                };
                return icons[ext] || 'fa-file';
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                filesArray.forEach(file => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;
            }

            function renderPreview() {
                if (!previewContainer) return;
                previewContainer.innerHTML = '';

                filesArray.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'pwa-preview-item';
                    item.innerHTML = `
                        <i class="fas ${getFileIcon(file.name)}"></i>
                        <span class="pwa-preview-name" title="${file.name}">${file.name}</span>
                        <span class="pwa-preview-size">(${formatFileSize(file.size)})</span>
                        <button type="button" class="pwa-preview-remove" onclick="window.removeAttachment(${index})" title="Supprimer">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(item);
                });
            }

            window.removeAttachment = function(index) {
                filesArray.splice(index, 1);
                updateFileInput();
                renderPreview();
            };
        })();
    </script>
@endif

<style>
    /* MODAL STYLES */
    .pwa-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .pwa-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease;
    }

    .pwa-modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 360px;
        position: relative;
        z-index: 1;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        overflow: hidden;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(50px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .pwa-modal-header {
        background: linear-gradient(135deg, var(--secondary-50, #f8fafc) 0%, white 100%);
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid var(--secondary-100, #f1f5f9);
    }

    .pwa-modal-header i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .pwa-modal-header i.text-danger { color: #dc2626; }
    .pwa-modal-header i.text-warning { color: #d97706; }

    .pwa-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-800, #1e293b);
    }

    .pwa-modal-body {
        padding: 1.25rem;
        text-align: center;
    }

    .pwa-modal-body p {
        margin: 0;
        color: var(--secondary-600, #475569);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .pwa-modal-footer {
        padding: 1rem 1.25rem 1.25rem;
        display: flex;
        gap: 0.75rem;
    }

    .pwa-btn-secondary, .pwa-btn-confirm {
        flex: 1;
        padding: 0.875rem 1rem;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .pwa-btn-secondary {
        background: var(--secondary-100, #f1f5f9);
        color: var(--secondary-700, #334155);
    }

    .pwa-btn-secondary:hover {
        background: var(--secondary-200, #e2e8f0);
    }

    .pwa-btn-confirm {
        background: linear-gradient(135deg, var(--primary-500, #3b82f6) 0%, var(--primary-600, #2563eb) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .pwa-btn-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
    }

    .pwa-btn-confirm.btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .pwa-btn-confirm.btn-danger:hover {
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
    }

    .pwa-btn-confirm.btn-warning {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
    }

    .pwa-btn-confirm.btn-warning:hover {
        box-shadow: 0 6px 16px rgba(217, 119, 6, 0.4);
    }

    /* Reste des styles identiques... */
    .pwa-ticket-detail {
        padding: 0;
        max-width: 100%;
        background: var(--secondary-50, #f8fafc);
        min-height: 100vh;
        padding-bottom: 20px;
    }

    /* Header */
    .pwa-page-header {
        background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%);
        padding: 1rem 1.25rem;
        padding-top: calc(1rem + env(safe-area-inset-top, 0px));
        margin: -1rem -1rem 1rem -1rem;
        position: relative;
        overflow: visible;
        z-index: 100;
    }

    .pwa-header-bg {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
        background-size: 20px 20px;
        pointer-events: none;
    }

    .pwa-header-content {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: white;
    }

    .pwa-back-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .pwa-back-btn:hover { background: rgba(255, 255, 255, 0.3); }

    .pwa-header-text {
        flex: 1;
        min-width: 0;
    }

    .pwa-header-text h1 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.125rem 0;
        font-family: 'Rajdhani', sans-serif;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pwa-header-text p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.8rem;
    }

    .pwa-header-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.2s;
    }

    .pwa-header-action:hover { background: rgba(255, 255, 255, 0.3); }

    /* Menu déroulant */
    .pwa-ticket-menu {
        display: none;
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 1rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        min-width: 200px;
        z-index: 1000;
        overflow: hidden;
    }

    .pwa-ticket-menu.show {
        display: block;
        animation: slideDown 0.2s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pwa-menu-item {
        display: flex;
        width: 100%;
        padding: 0.875rem 1rem;
        border: none;
        background: none;
        text-align: left;
        font-size: 0.9rem;
        color: var(--secondary-800, #334155);
        cursor: pointer;
        border-bottom: 1px solid var(--secondary-100, #f1f5f9);
        transition: background 0.2s;
        align-items: center;
    }

    .pwa-menu-item:hover { background: var(--secondary-50, #f8fafc); }
    .pwa-menu-item:last-child { border-bottom: none; }
    .pwa-menu-item.text-danger { color: #dc2626; }
    .pwa-menu-item.text-danger:hover { background: #fef2f2; }
    .pwa-menu-item.text-warning { color: #d97706; }
    .pwa-menu-item.text-warning:hover { background: #fffbeb; }

    .pwa-menu-item i {
        width: 24px;
        margin-right: 0.5rem;
        text-align: center;
    }

    /* Info Card, Timeline, Description... (identique) */
    .pwa-ticket-info-card {
        margin: 0 1rem 1rem;
        padding: 1rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .pwa-ticket-status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .pwa-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .pwa-status-badge.open { background: #dbeafe; color: #2563eb; }
    .pwa-status-badge.in_progress { background: #fef3c7; color: #d97706; }
    .pwa-status-badge.resolved { background: #d1fae5; color: #059669; }
    .pwa-status-badge.closed { background: #f3f4f6; color: #6b7280; }

    .pwa-ticket-subject {
        font-size: 1rem;
        font-weight: 600;
        color: var(--secondary-800, #1e293b);
        margin: 0 0 0.75rem 0;
        line-height: 1.4;
    }

    .pwa-ticket-meta-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .pwa-meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: var(--secondary-600, #475569);
    }

    .pwa-section {
        padding: 0 1rem;
        margin-bottom: 1.25rem;
    }

    .pwa-section-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--secondary-800, #1e293b);
        margin: 0 0 0.75rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pwa-message-count {
        background: var(--primary-100, #dbeafe);
        color: var(--primary-700, #1d4ed8);
        padding: 0.125rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        margin-left: auto;
        font-weight: 600;
    }

    /* Timeline */
    .pwa-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 0 0.25rem;
        margin-bottom: 0.5rem;
    }

    .pwa-timeline:before {
        content: '';
        position: absolute;
        top: 8px;
        left: 15px;
        right: 15px;
        height: 2px;
        background: var(--secondary-200, #e2e8f0);
        z-index: 0;
    }

    .pwa-timeline-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .pwa-timeline-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--secondary-200, #e2e8f0);
        border: 2px solid white;
        box-shadow: 0 0 0 1px var(--secondary-200, #e2e8f0);
    }

    .pwa-timeline-item.completed .pwa-timeline-dot {
        background: var(--primary-500, #3b82f6);
        box-shadow: 0 0 0 1px var(--primary-500, #3b82f6);
    }

    .pwa-timeline-content {
        text-align: center;
        font-size: 0.65rem;
    }

    .pwa-timeline-content span {
        display: block;
        font-weight: 600;
        color: var(--secondary-700, #334155);
    }

    /* Description & Messages */
    .pwa-description-card {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        font-size: 0.9rem;
        line-height: 1.5;
        color: var(--secondary-700, #334155);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        white-space: pre-wrap;
    }

    .pwa-messages-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 0.5rem 0;
    }

    .pwa-message-row {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        max-width: 85%;
    }

    .pwa-message-row.client {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .pwa-message-row.admin {
        align-self: flex-start;
    }

    .pwa-message-bubble {
        padding: 0.875rem;
        border-radius: 16px;
        position: relative;
        word-wrap: break-word;
        max-width: 100%;
    }

    .pwa-message-row.client .pwa-message-bubble {
        background: linear-gradient(135deg, var(--primary-500, #3b82f6) 0%, var(--primary-600, #2563eb) 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .pwa-message-row.admin .pwa-message-bubble {
        background: white;
        color: var(--secondary-800, #1e293b);
        border: 1px solid var(--secondary-200, #e2e8f0);
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .pwa-message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.375rem;
        gap: 0.75rem;
    }

    .pwa-message-author {
        font-weight: 600;
        font-size: 0.8rem;
    }

    .pwa-message-row.client .pwa-message-author {
        color: rgba(255, 255, 255, 0.9);
    }

    .pwa-message-time {
        font-size: 0.7rem;
        opacity: 0.7;
    }

    .pwa-message-content {
        font-size: 0.9rem;
        line-height: 1.4;
        white-space: pre-wrap;
    }

    .pwa-message-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.75rem;
        flex-shrink: 0;
        background: var(--secondary-200, #e2e8f0);
        color: var(--secondary-700, #334155);
    }

    .pwa-message-row.client .pwa-message-avatar {
        background: var(--primary-100, #dbeafe);
        color: var(--primary-700, #1d4ed8);
    }

    .pwa-message-attachments {
        margin-top: 0.625rem;
        padding-top: 0.625rem;
        border-top: 1px dashed rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .pwa-message-row.admin .pwa-message-attachments {
        border-top-color: var(--secondary-200, #e2e8f0);
    }

    .pwa-attachment-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        padding: 0.375rem 0.625rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        text-decoration: none;
        color: inherit;
    }

    .pwa-message-row.admin .pwa-attachment-link {
        background: var(--secondary-50, #f8fafc);
        color: var(--primary-600, #2563eb);
    }

    .pwa-empty-conversation {
        text-align: center;
        padding: 2rem;
        color: var(--secondary-500, #64748b);
        background: white;
        border-radius: 16px;
        border: 2px dashed var(--secondary-200, #e2e8f0);
    }

    /* Barre de réponse */
    .pwa-reply-bar {
        position: fixed;
        bottom: 70px;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid var(--secondary-200, #e2e8f0);
        padding: 0.75rem 1rem;
        padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    }

    .pwa-reply-input-group {
        display: flex;
        align-items: flex-end;
        gap: 0.5rem;
        background: var(--secondary-50, #f8fafc);
        padding: 0.375rem;
        border-radius: 24px;
        border: 1px solid var(--secondary-200, #e2e8f0);
    }

    .pwa-attachment-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: white;
        color: var(--secondary-600, #475569);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .pwa-reply-textarea {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.5rem;
        font-size: 0.95rem;
        resize: none;
        max-height: 100px;
        font-family: inherit;
        outline: none;
    }

    .pwa-send-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, var(--primary-500, #3b82f6) 0%, var(--primary-600, #2563eb) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(27, 90, 141, 0.3);
    }

    .pwa-attachment-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
        padding: 0 0.25rem;
    }

    .pwa-preview-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        border: 1px solid var(--secondary-200, #e2e8f0);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        color: var(--secondary-700, #334155);
        animation: slideIn 0.3s ease;
    }

    .pwa-preview-remove {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--secondary-100, #f1f5f9);
        border: none;
        color: var(--secondary-500, #64748b);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-left: 0.25rem;
    }

    .pwa-preview-remove:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .pwa-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        background: linear-gradient(135deg, var(--primary-500, #3b82f6) 0%, var(--primary-600, #2563eb) 100%);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .pwa-btn-primary.sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    @media (min-width: 992px) {
        .pwa-ticket-detail {
            max-width: 800px;
            margin: 0 auto;
        }

        .pwa-reply-bar {
            position: relative;
            bottom: auto;
            margin: 0 1rem 1rem;
            border-radius: 16px;
        }

        .pwa-message-row {
            max-width: 70%;
        }

        .pwa-modal-content {
            max-width: 400px;
        }
    }
</style>
@endsection
