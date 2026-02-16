@extends('admin.layouts.app')

@section('title', 'Documents par Utilisateur')
@section('page-title', 'Documents')
@section('page-subtitle', 'Validation par utilisateur')

@push('styles')
<style>
    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-icon.users { background: #dbeafe; color: #2563eb; }
    .stat-icon.pending { background: #fef3c7; color: #d97706; }
    .stat-icon.validated { background: #d1fae5; color: #059669; }
    .stat-icon.rejected { background: #fee2e2; color: #dc2626; }

    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-info span {
        font-size: 0.875rem;
        color: var(--admin-text-muted);
    }

    /* Filters */
    .filters-bar {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--admin-border);
        background: white;
        color: var(--admin-text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-btn.active {
        background: var(--admin-accent);
        color: white;
        border-color: var(--admin-accent);
    }

    /* User Cards */
    .users-grid {
        display: grid;
        gap: 1.5rem;
    }

    .user-documents-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--admin-shadow-sm);
        border: 1px solid var(--admin-border);
        overflow: hidden;
    }

    .user-header {
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--admin-bg) 0%, #f8fafc 100%);
        border-bottom: 1px solid var(--admin-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .user-info-main {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar-lg {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .user-details h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0 0 0.25rem 0;
    }

    .user-details p {
        font-size: 0.875rem;
        color: var(--admin-text-muted);
        margin: 0;
    }

    .user-stats {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .user-stat {
        text-align: center;
    }

    .user-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-text);
    }

    .user-stat-label {
        font-size: 0.75rem;
        color: var(--admin-text-muted);
        text-transform: uppercase;
    }

    .user-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Documents List */
    .documents-list {
        padding: 1rem;
    }

    .document-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: 10px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .document-item:hover {
        background: var(--admin-bg);
        border-color: var(--admin-border);
    }

    .document-item:not(:last-child) {
        margin-bottom: 0.5rem;
    }

    .doc-select {
        margin-right: 1rem;
    }

    .custom-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid var(--admin-border);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: white;
    }

    .custom-checkbox:hover {
        border-color: var(--admin-accent);
    }

    .custom-checkbox.checked {
        background: var(--admin-accent);
        border-color: var(--admin-accent);
        color: white;
    }

    .doc-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .doc-icon.pdf { background: #fee2e2; color: #dc2626; }
    .doc-icon.image { background: #d1fae5; color: #059669; }
    .doc-icon.word { background: #dbeafe; color: #2563eb; }
    .doc-icon.excel { background: #d1fae5; color: #059669; }
    .doc-icon.default { background: var(--admin-bg); color: var(--admin-text-muted); }

    .doc-info {
        flex: 1;
        min-width: 0;
    }

    .doc-name {
        font-weight: 600;
        font-size: 0.9375rem;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .doc-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: var(--admin-text-muted);
    }

    .doc-status {
        margin-right: 1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.validated {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .doc-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--admin-border);
        background: white;
        color: var(--admin-text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        background: var(--admin-bg);
        color: var(--admin-text);
    }

    .btn-icon.validate:hover {
        background: #d1fae5;
        color: #059669;
        border-color: #059669;
    }

    .btn-icon.reject:hover {
        background: #fee2e2;
        color: #dc2626;
        border-color: #dc2626;
    }

    .btn-icon.view:hover {
        background: #dbeafe;
        color: #2563eb;
        border-color: #2563eb;
    }

    .btn-icon:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .btn-icon.validated-state {
        background: #d1fae5;
        color: #059669;
        border-color: #059669;
        cursor: default;
    }

    .btn-icon.rejected-state {
        background: #fee2e2;
        color: #dc2626;
        border-color: #dc2626;
        cursor: default;
    }

    /* Bulk Actions Bar */
    .bulk-actions-bar {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--admin-shadow-xl);
        border: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 1000;
        transition: transform 0.3s ease;
    }

    .bulk-actions-bar.active {
        transform: translateX(-50%) translateY(0);
    }

    .bulk-info {
        font-size: 0.875rem;
        color: var(--admin-text);
        font-weight: 500;
    }

    .bulk-info span {
        color: var(--admin-accent);
        font-weight: 700;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: var(--admin-shadow-sm);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--admin-border);
        margin-bottom: 1rem;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: hidden;
        animation: modalSlide 0.3s ease;
    }

    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--admin-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--admin-text-muted);
        cursor: pointer;
        font-size: 1.25rem;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: var(--admin-bg);
        color: var(--admin-text);
    }

    .modal-body {
        padding: 1.25rem;
    }

    .modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--admin-border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--admin-text);
    }

    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--admin-border);
        border-radius: 8px;
        font-size: 0.875rem;
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    .form-group textarea:focus {
        outline: none;
        border-color: var(--admin-accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .user-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .document-item {
            flex-wrap: wrap;
        }

        .doc-actions {
            width: 100%;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                <span>Utilisateurs</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                <span>En attente</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon validated">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['validated'] ?? 0 }}</h3>
                <span>Validés</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected">
                <i class="fa-solid fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['rejected'] ?? 0 }}</h3>
                <span>Rejetés</span>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filters-bar">
        <div class="filter-group">
            <a href="{{ route('admin.documents.index') }}"
               class="filter-btn {{ !request('filter') ? 'active' : '' }}">
                Tous les utilisateurs
            </a>
            <a href="{{ route('admin.documents.index', ['filter' => 'pending']) }}"
               class="filter-btn {{ request('filter') === 'pending' ? 'active' : '' }}">
                <i class="fa-solid fa-clock"></i> En attente de validation
            </a>
            <a href="{{ route('admin.documents.index', ['filter' => 'complete']) }}"
               class="filter-btn {{ request('filter') === 'complete' ? 'active' : '' }}">
                <i class="fa-solid fa-check-double"></i> Profils complets
            </a>
        </div>
    </div>

    {{-- Users Grid --}}
    <div class="users-grid">
        @forelse ($users as $user)
            @php
                $pendingCount = $user->documents->where('status', 'pending')->count();
                $hasPending = $pendingCount > 0;
            @endphp

            <div class="user-documents-card" data-user-id="{{ $user->id }}">
                <div class="user-header">
                    <div class="user-info-main">
                        <div class="user-avatar-lg">
                            {{ strtoupper(substr($user->first_name ?? 'N', 0, 1) . substr($user->last_name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <h3>{{ $user->full_name ?? $user->name }}</h3>
                            <p>{{ $user->email }} • {{ $user->documents->count() }} document(s)</p>
                        </div>
                    </div>

                    <div class="user-stats">
                        <div class="user-stat">
                            <div class="user-stat-value" style="color: #d97706;">
                                {{ $pendingCount }}
                            </div>
                            <div class="user-stat-label">En attente</div>
                        </div>
                        <div class="user-stat">
                            <div class="user-stat-value" style="color: #059669;">
                                {{ $user->documents->where('status', 'validated')->count() }}
                            </div>
                            <div class="user-stat-label">Validés</div>
                        </div>
                        <div class="user-stat">
                            <div class="user-stat-value" style="color: #dc2626;">
                                {{ $user->documents->where('status', 'rejected')->count() }}
                            </div>
                            <div class="user-stat-label">Rejetés</div>
                        </div>
                    </div>

                    <div class="user-actions">
                        @if($hasPending)
                            <button type="button" class="btn btn-success" onclick="validateAllForUser({{ $user->id }})">
                                <i class="fa-solid fa-check-double"></i> Tout valider ({{ $pendingCount }})
                            </button>
                        @else
                            <button type="button" class="btn btn-success" disabled style="opacity: 0.5; cursor: not-allowed;">
                                <i class="fa-solid fa-check-double"></i> Tout validé ✓
                            </button>
                        @endif

                        <a href="{{ route('admin.documents.show', $user->id) }}" class="btn btn-secondary">
                            <i class="fa-solid fa-eye"></i> Voir détails
                        </a>
                    </div>
                </div>

                <div class="documents-list">
                    @foreach($user->documents as $document)
                        @php
                            $canValidate = in_array($document->status, ['pending', 'rejected']);
                            $canReject = $document->status === 'pending';
                            $isValidated = $document->status === 'validated';
                            $isRejected = $document->status === 'rejected';
                        @endphp

                        <div class="document-item" data-doc-id="{{ $document->id }}" data-status="{{ $document->status }}">
                            {{-- Checkbox : uniquement si en attente ou rejeté (peut être validé) --}}
                            <div class="doc-select">
                                @if($canValidate)
                                    <div class="custom-checkbox" onclick="toggleSelect(this, {{ $document->id }})" data-selectable="true">
                                        <i class="fa-solid fa-check" style="display: none;"></i>
                                    </div>
                                @else
                                    <div class="custom-checkbox checked" style="background: #d1fae5; border-color: #059669; color: #059669; cursor: default;">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="doc-icon {{ $document->isPdf() ? 'pdf' : ($document->isImage() ? 'image' : ($document->isWordDocument() ? 'word' : ($document->isExcelDocument() ? 'excel' : 'default'))) }}">
                                <i class="fa-solid {{ $document->file_icon ?? 'fa-file' }}"></i>
                            </div>

                            <div class="doc-info">
                                <div class="doc-name">{{ $document->name }}</div>
                                <div class="doc-meta">
                                    <span><i class="fa-solid fa-tag"></i> {{ $document->type_label }}</span>
                                    <span><i class="fa-solid fa-weight-hanging"></i> {{ $document->formatted_size }}</span>
                                    <span><i class="fa-solid fa-calendar"></i> {{ $document->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <div class="doc-status">
                                <span class="status-badge {{ $document->status }}">
                                    {{ $document->status_label }}
                                </span>
                            </div>

                            <div class="doc-actions">
                                {{-- Bouton Valider : visible si pending ou rejected --}}
                                @if($canValidate)
                                    <form method="POST" action="{{ route('admin.documents.validate', $document->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-icon validate" title="Valider ce document">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn-icon validated-state" title="Déjà validé" disabled>
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif

                                {{-- Bouton Rejeter : visible uniquement si pending --}}
                                @if($canReject)
                                    <button type="button" class="btn-icon reject" title="Rejeter ce document"
                                            onclick="openRejectModal({{ $document->id }}, '{{ addslashes($document->name) }}')">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                @elseif($isRejected)
                                    <button type="button" class="btn-icon rejected-state" title="Déjà rejeté" disabled>
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-icon" style="opacity: 0.3; cursor: not-allowed;" title="Validation impossible" disabled>
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                @endif

                                <a href="{{ route('admin.documents.download', $document->id) }}" class="btn-icon" title="Télécharger">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-solid fa-folder-open"></i>
                <h3>Aucun utilisateur avec des documents</h3>
                <p>Il n'y a pas de documents à afficher selon vos critères.</p>
            </div>
        @endforelse
    </div>

    {{ $users->links() }}

    {{-- Bulk Actions Bar --}}
    <div class="bulk-actions-bar" id="bulkActionsBar">
        <div class="bulk-info">
            <span id="selectedCount">0</span> document(s) sélectionné(s)
        </div>
        <form id="bulkValidateForm" method="POST" action="{{ route('admin.documents.bulk-validate') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="document_ids" id="selectedDocuments">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-check"></i> Valider la sélection
            </button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="clearSelection()">
            <i class="fa-solid fa-times"></i> Annuler
        </button>
    </div>

    {{-- Reject Modal --}}
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-times-circle" style="color: var(--admin-danger); margin-right: 0.5rem;"></i>
                    Rejeter le document
                </h3>
                <button type="button" class="modal-close" onclick="closeRejectModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Document : <span id="rejectDocName" style="color: var(--admin-text-muted); font-weight: 400;"></span></label>
                    </div>
                    <div class="form-group">
                        <label for="rejectReason">Motif du rejet <span style="color: var(--admin-danger);">*</span></label>
                        <textarea name="reason" id="rejectReason" required minlength="10" placeholder="Expliquez pourquoi ce document est rejeté (minimum 10 caractères)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-times"></i> Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Variables globales
    let selectedDocs = new Set();

    // Toggle sélection d'un document
    function toggleSelect(checkbox, docId) {
        // Ne rien faire si ce n'est pas sélectionnable
        if (checkbox.getAttribute('data-selectable') !== 'true') return;

        const icon = checkbox.querySelector('i');

        if (checkbox.classList.contains('checked')) {
            checkbox.classList.remove('checked');
            icon.style.display = 'none';
            selectedDocs.delete(docId);
        } else {
            checkbox.classList.add('checked');
            icon.style.display = 'block';
            selectedDocs.add(docId);
        }

        updateBulkActions();
    }

    // Mise à jour de la barre d'actions en masse
    function updateBulkActions() {
        const bar = document.getElementById('bulkActionsBar');
        const count = document.getElementById('selectedCount');
        const input = document.getElementById('selectedDocuments');

        count.textContent = selectedDocs.size;
        input.value = Array.from(selectedDocs).join(',');

        if (selectedDocs.size > 0) {
            bar.classList.add('active');
        } else {
            bar.classList.remove('active');
        }
    }

    // Vider la sélection
    function clearSelection() {
        selectedDocs.clear();
        document.querySelectorAll('.custom-checkbox[data-selectable="true"].checked').forEach(function(cb) {
            cb.classList.remove('checked');
            cb.querySelector('i').style.display = 'none';
        });
        updateBulkActions();
    }

    // Valider tous les documents d'un utilisateur
    function validateAllForUser(userId) {
        const pendingCountElement = document.querySelector('[data-user-id="' + userId + '"] .user-stat-value');
        const pendingCount = pendingCountElement ? pendingCountElement.textContent.trim() : '0';

        if (!confirm('Valider les ' + pendingCount + ' document(s) en attente de cet utilisateur ?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.documents.validate-user", ":userId") }}'.replace(':userId', userId);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';

        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }

    // Ouvrir le modal de rejet
    function openRejectModal(docId, docName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const nameSpan = document.getElementById('rejectDocName');

        form.action = '{{ url("admin/documents") }}/' + docId + '/reject';
        nameSpan.textContent = docName;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus sur le textarea après animation
        setTimeout(function() {
            document.getElementById('rejectReason').focus();
        }, 100);
    }

    // Fermer le modal de rejet
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('rejectReason').value = '';
    }

    // Fermer le modal en cliquant sur l'overlay
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });

    // Fermer avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
        }
    });

    // Confirmation pour la validation individuelle
    document.querySelectorAll('form[action*="validate"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Valider ce document ?')) {
                e.preventDefault();
            }
        });
    });

    // Confirmation pour la validation en masse
    document.getElementById('bulkValidateForm').addEventListener('submit', function(e) {
        if (!confirm('Valider ' + selectedDocs.size + ' document(s) sélectionné(s) ?')) {
            e.preventDefault();
        }
    });
</script>
@endpush
