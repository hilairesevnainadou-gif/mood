{{-- resources/views/admin/documents/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Documents de ' . ($user->full_name ?? $user->name))
@section('page-title', 'Documents Utilisateur')
@section('page-subtitle', $user->full_name ?? $user->name)

@push('styles')
<style>
    .user-profile-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .user-profile-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .user-avatar-xl {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 600;
    }

    .user-profile-details h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .user-profile-details p {
        color: var(--admin-text-muted);
        margin: 0;
        font-size: 0.9375rem;
    }

    .user-profile-stats {
        display: flex;
        gap: 2rem;
    }

    .profile-stat {
        text-align: center;
    }

    .profile-stat-value {
        font-size: 2rem;
        font-weight: 700;
    }

    .profile-stat-value.pending { color: #d97706; }
    .profile-stat-value.validated { color: #059669; }
    .profile-stat-value.rejected { color: #dc2626; }

    .profile-stat-label {
        font-size: 0.875rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
    }

    .documents-detailed-list {
        display: grid;
        gap: 1rem;
    }

    .document-detailed-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.5rem;
        align-items: center;
        transition: all 0.3s ease;
    }

    .document-detailed-card.validated {
        border-left: 4px solid #059669;
    }

    .document-detailed-card.rejected {
        border-left: 4px solid #dc2626;
    }

    .document-detailed-card.pending {
        border-left: 4px solid #d97706;
    }

    .document-preview {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        background: var(--admin-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid var(--admin-border);
    }

    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .document-preview-icon {
        font-size: 3rem;
        color: var(--admin-text-muted);
    }

    .document-details h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0 0 0.75rem 0;
    }

    .document-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--admin-text-muted);
    }

    .meta-item i {
        color: var(--admin-accent);
        width: 16px;
    }

    .document-status-section {
        text-align: right;
    }

    .status-timeline {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: var(--admin-text-muted);
    }

    .timeline-item.active {
        color: var(--admin-text);
        font-weight: 500;
    }

    .timeline-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--admin-border);
    }

    .timeline-item.active .timeline-dot {
        background: var(--admin-accent);
    }

    .document-actions-vertical {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-action-large {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-size: 0.9375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-action-large.validate {
        background: var(--admin-success);
        color: white;
    }

    .btn-action-large.validate:hover:not(:disabled) {
        background: #059669;
    }

    .btn-action-large.reject {
        background: var(--admin-danger);
        color: white;
    }

    .btn-action-large.reject:hover:not(:disabled) {
        background: #dc2626;
    }

    .btn-action-large.secondary {
        background: var(--admin-bg);
        color: var(--admin-text);
        border: 1px solid var(--admin-border);
    }

    .btn-action-large.secondary:hover:not(:disabled) {
        background: var(--admin-border);
    }

    .btn-action-large:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-action-large.validated-state {
        background: #d1fae5;
        color: #065f46;
        cursor: default;
    }

    .btn-action-large.rejected-state {
        background: #fee2e2;
        color: #991b1b;
        cursor: default;
    }

    .rejection-reason {
        background: #fee2e2;
        border-left: 4px solid var(--admin-danger);
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-top: 1rem;
    }

    .validation-info {
        background: #d1fae5;
        border-left: 4px solid var(--admin-success);
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-top: 1rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--admin-text-muted);
        text-decoration: none;
        margin-bottom: 1rem;
        font-size: 0.9375rem;
    }

    .back-link:hover {
        color: var(--admin-accent);
    }

    .global-actions {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .progress-bar {
        flex: 1;
        min-width: 200px;
        height: 8px;
        background: var(--admin-bg);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #059669, #10b981);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 0.875rem;
        color: var(--admin-text-muted);
        margin-left: 1rem;
    }

    @media (max-width: 768px) {
        .document-detailed-card {
            grid-template-columns: 1fr;
        }

        .document-preview {
            width: 100%;
            height: 200px;
        }

        .document-status-section {
            text-align: left;
        }

        .document-actions-vertical {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@section('content')
    <a href="{{ route('admin.documents.index') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Retour à la liste
    </a>

    {{-- User Header --}}
    <div class="user-profile-header">
        <div class="user-profile-info">
            <div class="user-avatar-xl">
                {{ strtoupper(substr($user->first_name ?? 'N', 0, 1) . substr($user->last_name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-profile-details">
                <h1>{{ $user->full_name ?? $user->name }}</h1>
                <p>
                    <i class="fa-solid fa-envelope"></i> {{ $user->email }}<br>
                    <i class="fa-solid fa-phone"></i> {{ $user->phone ?? 'Non renseigné' }}<br>
                    <i class="fa-solid fa-calendar"></i> Inscrit le {{ $user->created_at?->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>
        </div>

        <div class="user-profile-stats">
            <div class="profile-stat">
                <div class="profile-stat-value pending">{{ $documents->where('status', 'pending')->count() }}</div>
                <div class="profile-stat-label">En attente</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value validated">{{ $documents->where('status', 'validated')->count() }}</div>
                <div class="profile-stat-label">Validés</div>
            </div>
            <div class="profile-stat">
                <div class="profile-stat-value rejected">{{ $documents->where('status', 'rejected')->count() }}</div>
                <div class="profile-stat-label">Rejetés</div>
            </div>
        </div>
    </div>

    @php
        $pendingDocs = $documents->where('status', 'pending');
        $hasPending = $pendingDocs->count() > 0;
        $totalDocs = $documents->count();
        $validatedDocs = $documents->where('status', 'validated')->count();
        $progressPercent = $totalDocs > 0 ? round(($validatedDocs / $totalDocs) * 100) : 0;
    @endphp

    {{-- Actions globales et progression --}}
    <div class="global-actions">
        <div style="display: flex; align-items: center; flex: 1;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $progressPercent }}%"></div>
            </div>
            <span class="progress-text">{{ $validatedDocs }}/{{ $totalDocs }} validés ({{ $progressPercent }}%)</span>
        </div>

        {{-- Bouton "Tout valider" : visible uniquement si documents en attente --}}
        @if($hasPending)
            <form method="POST" action="{{ route('admin.documents.validate-user', $user->id) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-action-large validate">
                    <i class="fa-solid fa-check-double"></i> Valider les {{ $pendingDocs->count() }} document(s) en attente
                </button>
            </form>
        @else
            <button type="button" class="btn-action-large validated-state" disabled>
                <i class="fa-solid fa-check-circle"></i> Tous les documents sont validés
            </button>
        @endif
    </div>

    {{-- Documents List --}}
    <div class="documents-detailed-list">
        @forelse($documents as $document)
            @php
                $canValidate = in_array($document->status, ['pending', 'rejected']);
                $canReject = $document->status === 'pending';
                $canReset = in_array($document->status, ['validated', 'rejected']);
                $isValidated = $document->status === 'validated';
                $isRejected = $document->status === 'rejected';
                $isPending = $document->status === 'pending';
            @endphp

            <div class="document-detailed-card {{ $document->status }}" id="doc-{{ $document->id }}">
                <div class="document-preview">
                    @if($document->isImage() && $document->file_url)
                        <img src="{{ $document->file_url }}" alt="{{ $document->name }}">
                    @else
                        <i class="fa-solid {{ $document->file_icon ?? 'fa-file' }} document-preview-icon"
                           style="{{ $document->isPdf() ? 'color: #dc2626;' : ($document->isWordDocument() ? 'color: #2563eb;' : '') }}"></i>
                    @endif
                </div>

                <div class="document-details">
                    <h3>
                        {{ $document->name }}
                        @if($isValidated)
                            <i class="fa-solid fa-check-circle" style="color: #059669; margin-left: 0.5rem;"></i>
                        @elseif($isRejected)
                            <i class="fa-solid fa-times-circle" style="color: #dc2626; margin-left: 0.5rem;"></i>
                        @endif
                    </h3>

                    <div class="document-meta-grid">
                        <div class="meta-item">
                            <i class="fa-solid fa-tag"></i>
                            <span>{{ $document->type_label }} ({{ $document->category }})</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-weight-hanging"></i>
                            <span>{{ $document->formatted_size }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-calendar"></i>
                            <span>Uploadé le {{ $document->created_at?->format('d/m/Y à H:i') ?? 'Date inconnue' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-file"></i>
                            <span>{{ $document->original_filename }}</span>
                        </div>
                    </div>

                    @if($isRejected && $document->rejection_reason)
                        <div class="rejection-reason">
                            <strong><i class="fa-solid fa-times-circle"></i> Motif du rejet :</strong>
                            {{ $document->rejection_reason }}
                            <br><small>Par {{ $document->validator?->name ?? 'Admin' }} le {{ $document->validated_at?->format('d/m/Y') ?? 'N/A' }}</small>
                        </div>
                    @endif

                    @if($isValidated)
                        <div class="validation-info">
                            <strong><i class="fa-solid fa-check-circle"></i> Validé</strong>
                            Par {{ $document->validator?->name ?? 'Admin' }} le {{ $document->validated_at?->format('d/m/Y à H:i') ?? 'N/A' }}
                        </div>
                    @endif
                </div>

                <div class="document-status-section">
                    <div class="status-timeline">
                        <div class="timeline-item {{ $isPending ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <span>En attente</span>
                        </div>
                        <div class="timeline-item {{ $isValidated ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <span>Validé</span>
                        </div>
                        <div class="timeline-item {{ $isRejected ? 'active' : '' }}">
                            <div class="timeline-dot"></div>
                            <span>Rejeté</span>
                        </div>
                    </div>

                    <div class="document-actions-vertical">
                        {{-- Bouton Valider : visible si pending ou rejected --}}
                        @if($canValidate)
                            <form method="POST" action="{{ route('admin.documents.validate', $document->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action-large validate">
                                    <i class="fa-solid fa-check"></i> Valider
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn-action-large validated-state" disabled>
                                <i class="fa-solid fa-check"></i> Déjà validé
                            </button>
                        @endif

                        {{-- Bouton Rejeter : visible uniquement si pending --}}
                        @if($canReject)
                            <button type="button" class="btn-action-large reject" onclick="openRejectModal({{ $document->id }})">
                                <i class="fa-solid fa-times"></i> Rejeter
                            </button>
                        @elseif($isRejected)
                            <button type="button" class="btn-action-large rejected-state" disabled>
                                <i class="fa-solid fa-times"></i> Déjà rejeté
                            </button>
                        @else
                            <button type="button" class="btn-action-large secondary" disabled title="Impossible de rejeter un document validé">
                                <i class="fa-solid fa-ban"></i> Rejet impossible
                            </button>
                        @endif

                        <a href="{{ route('admin.documents.download', $document->id) }}" class="btn-action-large secondary">
                            <i class="fa-solid fa-download"></i> Télécharger
                        </a>

                        {{-- Bouton Remettre en attente : visible si validated ou rejected --}}
                        @if($canReset)
                            <form method="POST" action="{{ route('admin.documents.pending', $document->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-action-large secondary" onclick="return confirm('Remettre ce document en attente ?')">
                                    <i class="fa-solid fa-undo"></i> Remettre en attente
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="text-align: center; padding: 4rem; background: white; border-radius: 16px;">
                <i class="fa-solid fa-folder-open" style="font-size: 4rem; color: var(--admin-border); margin-bottom: 1rem;"></i>
                <h3>Aucun document</h3>
                <p>Cet utilisateur n'a pas encore uploadé de documents.</p>
            </div>
        @endforelse
    </div>

    {{-- Reject Modal --}}
    <div class="modal-overlay" id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; border-radius: 16px; width: 90%; max-width: 500px; padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-times-circle" style="color: var(--admin-danger);"></i> Rejeter le document</h3>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Motif du rejet <span style="color: var(--admin-danger);">*</span></label>
                    <textarea name="reason" required minlength="10" style="width: 100%; padding: 0.75rem; border: 1px solid var(--admin-border); border-radius: 8px; min-height: 100px;" placeholder="Expliquez pourquoi ce document est rejeté (minimum 10 caractères)..."></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" class="btn-action-large secondary" onclick="closeRejectModal()">Annuler</button>
                    <button type="submit" class="btn-action-large reject">Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRejectModal(docId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `{{ url('admin/documents') }}/${docId}/reject`;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRejectModal();
    });
</script>
@endpush
