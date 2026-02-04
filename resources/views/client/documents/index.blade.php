@extends('layouts.client')

@section('title', 'Mes Documents')

@section('content')
{{-- SCRIPT DÉFINI AU DÉBUT pour garantir que les fonctions existent avant les onclick --}}
<script>
// Fonctions globales définies immédiatement
window.toggleDocActions = function(docId) {
    const card = document.querySelector('.pwa-doc-card[data-id="' + docId + '"]');
    if (!card) return;

    // Fermer les autres cartes ouvertes
    document.querySelectorAll('.pwa-doc-card.expanded').forEach(function(c) {
        if (c !== card) c.classList.remove('expanded');
    });

    card.classList.toggle('expanded');
};

window.toggleMissing = function() {
    var alert = document.getElementById('pwaMissingAlert');
    if (alert) alert.classList.toggle('collapsed');
};

window.filterDocuments = function(filter) {
    // Mettre à jour l'UI des boutons
    document.querySelectorAll('.pwa-filter-chip').forEach(function(chip) {
        chip.classList.remove('active');
        if (chip.getAttribute('data-filter') === filter) {
            chip.classList.add('active');
        }
    });

    // Filtrer les cartes
    document.querySelectorAll('.pwa-doc-card').forEach(function(card) {
        var status = card.getAttribute('data-status');
        var expired = card.getAttribute('data-expired') === 'true';
        var show = false;

        switch(filter) {
            case 'all': show = true; break;
            case 'validated': show = (status === 'validated' && !expired); break;
            case 'pending': show = (status === 'pending'); break;
            case 'rejected': show = (status === 'rejected'); break;
            case 'expired': show = expired; break;
        }

        card.style.display = show ? 'flex' : 'none';
    });
};

// Variables pour suppression/renouvellement
window.pwaDeleteId = null;
window.pwaRenewId = null;

window.confirmDelete = function(id, name) {
    window.pwaDeleteId = id;
    var nameEl = document.getElementById('pwaDeleteName');
    if (nameEl) nameEl.textContent = name;
    var sheet = document.getElementById('pwaDeleteSheet');
    if (sheet) sheet.classList.add('show');
};

window.closeDelete = function() {
    var sheet = document.getElementById('pwaDeleteSheet');
    if (sheet) sheet.classList.remove('show');
    window.pwaDeleteId = null;
};

window.executeDelete = function() {
    if (!window.pwaDeleteId) return;

    var btn = document.getElementById('pwaBtnDelete');
    var spinner = btn ? btn.querySelector('.spinner-border') : null;
    var text = btn ? btn.querySelector('.pwa-btn-text') : null;

    if (btn) btn.disabled = true;
    if (text) text.textContent = 'Suppression...';
    if (spinner) spinner.classList.remove('d-none');

    fetch('/client/documents/' + window.pwaDeleteId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            window.closeDelete();
            if (window.toast) window.toast.success('Succès', data.message);
            setTimeout(function() { window.location.reload(); }, 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(function(error) {
        if (window.toast) window.toast.error('Erreur', error.message || 'Erreur lors de la suppression');
        if (btn) btn.disabled = false;
        if (text) text.textContent = 'Supprimer';
        if (spinner) spinner.classList.add('d-none');
    });
};

window.confirmRenew = function(id, name) {
    window.pwaRenewId = id;
    var nameEl = document.getElementById('pwaRenewName');
    if (nameEl) nameEl.textContent = name;
    var sheet = document.getElementById('pwaRenewSheet');
    if (sheet) sheet.classList.add('show');
};

window.closeRenew = function() {
    var sheet = document.getElementById('pwaRenewSheet');
    if (sheet) sheet.classList.remove('show');
    window.pwaRenewId = null;
};

window.executeRenew = function() {
    if (!window.pwaRenewId) return;

    var btn = document.getElementById('pwaBtnRenew');
    var spinner = btn ? btn.querySelector('.spinner-border') : null;
    var text = btn ? btn.querySelector('.pwa-btn-text') : null;

    if (btn) btn.disabled = true;
    if (text) text.textContent = 'Renouvellement...';
    if (spinner) spinner.classList.remove('d-none');

    fetch('/client/documents/' + window.pwaRenewId + '/renew', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            window.closeRenew();
            if (window.toast) window.toast.success('Succès', data.message);
            setTimeout(function() { window.location.reload(); }, 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(function(error) {
        if (window.toast) window.toast.error('Erreur', error.message || 'Erreur lors du renouvellement');
        if (btn) btn.disabled = false;
        if (text) text.textContent = 'Renouveler';
        if (spinner) spinner.classList.add('d-none');
    });
};

window.showReason = function(name, reason) {
    var nameEl = document.getElementById('pwaRejectDocName');
    var reasonEl = document.getElementById('pwaRejectReason');
    if (nameEl) nameEl.textContent = name;
    if (reasonEl) reasonEl.textContent = reason || 'Aucune raison spécifiée';

    var modalEl = document.getElementById('pwaRejectModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

// Gestion du bouton refresh
document.addEventListener('DOMContentLoaded', function() {
    var refreshBtn = document.getElementById('pwaRefreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.classList.add('spinning');
            setTimeout(function() { window.location.reload(); }, 500);
        });
    }

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeDelete();
            window.closeRenew();
        }
    });
});
</script>

<div class="pwa-docs-container">
    {{-- Header Mobile --}}
    <div class="pwa-docs-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <div class="pwa-completion-ring">
                <svg width="70" height="70" viewBox="0 0 70 70">
                    <circle cx="35" cy="35" r="31" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="5"/>
                    <circle cx="35" cy="35" r="31" fill="none" stroke="white" stroke-width="5"
                            stroke-dasharray="195"
                            stroke-dashoffset="{{ 195 * (1 - $completionPercentage/100) }}"
                            stroke-linecap="round" transform="rotate(-90 35 35)"/>
                </svg>
                <span class="pwa-percentage">{{ $completionPercentage }}%</span>
            </div>
            <div class="pwa-header-text">
                <h1>Mes Documents</h1>
                <p>{{ $completedRequired }}/{{ $totalRequired }} requis</p>
                @if($completionPercentage < 100)
                    <span class="pwa-badge-incomplete">
                        <i class="fas fa-exclamation-circle"></i> Profil incomplet
                    </span>
                @else
                    <span class="pwa-badge-complete">
                        <i class="fas fa-check-circle"></i> Profil complet
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="pwa-stats-scroll">
        <div class="pwa-stats-track">
            <div class="pwa-stat-pill total">
                <div class="pwa-stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $documents->count() }}</span>
                    <span class="pwa-stat-label">Total</span>
                </div>
            </div>
            <div class="pwa-stat-pill validated">
                <div class="pwa-stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $documents->where('status', 'validated')->count() }}</span>
                    <span class="pwa-stat-label">Validés</span>
                </div>
            </div>
            <div class="pwa-stat-pill pending">
                <div class="pwa-stat-icon"><i class="fas fa-clock"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ $documents->where('status', 'pending')->count() }}</span>
                    <span class="pwa-stat-label">En attente</span>
                </div>
            </div>
            @if($completionPercentage < 100)
            <div class="pwa-stat-pill missing">
                <div class="pwa-stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="pwa-stat-info">
                    <span class="pwa-stat-num">{{ count($missingDocuments) }}</span>
                    <span class="pwa-stat-label">Manquants</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Affichage conditionnel : Si documents manquants, montrer la section manquante --}}
    @if(count($missingDocuments) > 0)
    {{-- Alert Missing --}}
    <div class="pwa-missing-alert" id="pwaMissingAlert">
        <div class="pwa-missing-header" onclick="toggleMissing()">
            <div class="pwa-missing-title">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <span>{{ count($missingDocuments) }} document(s) manquant(s)</span>
            </div>
            <i class="fas fa-chevron-down pwa-missing-toggle" id="pwaMissingToggle"></i>
        </div>
        <div class="pwa-missing-content" id="pwaMissingContent">
            @foreach($missingDocuments as $doc)
            <div class="pwa-missing-item">
                <div class="pwa-missing-info">
                    <span class="pwa-missing-name">{{ $doc->name }}</span>
                    @if($doc->allowed_formats)
                    <small>{{ implode(', ', array_map('strtoupper', $doc->allowed_formats)) }} • Max {{ $doc->max_size_mb }} Mo</small>
                    @endif
                </div>
                <a href="{{ route('client.documents.upload.form', ['type' => $doc->document_type]) }}" class="pwa-missing-add">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filtres --}}
    <div class="pwa-filters-wrap">
        <div class="pwa-filters-scroll">
            <button class="pwa-filter-chip active" data-filter="all" onclick="filterDocuments('all')">
                <span>Tous</span>
                <span class="pwa-filter-count">{{ $documents->count() }}</span>
            </button>
            <button class="pwa-filter-chip" data-filter="validated" onclick="filterDocuments('validated')">
                <i class="fas fa-check-circle text-success me-1"></i> Validés
            </button>
            <button class="pwa-filter-chip" data-filter="pending" onclick="filterDocuments('pending')">
                <i class="fas fa-clock text-warning me-1"></i> En attente
            </button>
            <button class="pwa-filter-chip" data-filter="rejected" onclick="filterDocuments('rejected')">
                <i class="fas fa-times-circle text-danger me-1"></i> Rejetés
            </button>
            <button class="pwa-filter-chip" data-filter="expired" onclick="filterDocuments('expired')">
                <i class="fas fa-calendar-times text-danger me-1"></i> Expirés
            </button>
        </div>
        <button class="pwa-refresh-btn" id="pwaRefreshBtn">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    {{-- Liste Documents --}}
    <div class="pwa-docs-list" id="pwaDocsList">
        @forelse($documents as $document)
        <div class="pwa-doc-card"
             data-status="{{ $document->status }}"
             data-expired="{{ $document->is_expired ? 'true' : 'false' }}"
             data-id="{{ $document->id }}">

            <div class="pwa-card-main" onclick="toggleDocActions({{ $document->id }})">
                <div class="pwa-doc-icon {{ $document->isImage() ? 'img' : ($document->isPdf() ? 'pdf' : 'file') }}">
                    <i class="{{ $document->file_icon }}"></i>
                </div>

                <div class="pwa-doc-details">
                    <h3>{{ $document->name }}</h3>
                    <p class="pwa-doc-filename">{{ Str::limit($document->original_filename, 25) }}</p>

                    <div class="pwa-doc-meta">
                        <span class="pwa-doc-type">{{ $document->type_label }}</span>
                        <span class="pwa-doc-size">{{ $document->formatted_size }}</span>
                    </div>

                    <div class="pwa-doc-status-row">
                        @if($document->status === 'validated')
                            @if($document->is_expired)
                                <span class="pwa-status-badge expired">
                                    <i class="fas fa-exclamation-triangle"></i> Expiré
                                </span>
                            @else
                                <span class="pwa-status-badge success">
                                    <i class="fas fa-check-circle"></i> Validé
                                </span>
                            @endif
                        @elseif($document->status === 'pending')
                            <span class="pwa-status-badge warning">
                                <i class="fas fa-clock"></i> En attente
                            </span>
                        @elseif($document->status === 'rejected')
                            <span class="pwa-status-badge danger">
                                <i class="fas fa-times-circle"></i> Rejeté
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pwa-doc-chevron">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pwa-card-actions" id="pwaActions{{ $document->id }}">
                <div class="pwa-actions-grid">
                    @if($document->file_url)
                    <a href="{{ route('client.documents.view.page', $document->id) }}" class="pwa-action-btn view" onclick="event.stopPropagation();">
                        <i class="fas fa-eye"></i><span>Voir</span>
                    </a>
                    <a href="{{ route('client.documents.download', $document->id) }}" class="pwa-action-btn download" onclick="event.stopPropagation();">
                        <i class="fas fa-download"></i><span>Télécharger</span>
                    </a>
                    @endif

                    @if($document->status === 'pending' || $document->status === 'rejected')
                    <button class="pwa-action-btn delete" onclick="event.stopPropagation(); confirmDelete({{ $document->id }}, '{{ addslashes($document->name) }}');">
                        <i class="fas fa-trash"></i><span>Supprimer</span>
                    </button>
                    @endif

                    @if($document->is_expired && $document->status === 'validated')
                    <button class="pwa-action-btn renew" onclick="event.stopPropagation(); confirmRenew({{ $document->id }}, '{{ addslashes($document->name) }}');">
                        <i class="fas fa-redo"></i><span>Renouveler</span>
                    </button>
                    @endif

                    @if($document->status === 'rejected' && $document->rejection_reason)
                    <button class="pwa-action-btn reason" onclick="event.stopPropagation(); showReason('{{ addslashes($document->name) }}', '{{ addslashes($document->rejection_reason) }}');">
                        <i class="fas fa-info-circle"></i><span>Raison</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="pwa-empty-state">
            <div class="pwa-empty-icon"><i class="fas fa-folder-open"></i></div>
            <h3>Aucun document</h3>
            <p>Commencez par ajouter vos documents</p>
            @if(count($requiredDocuments) > 0)
                @php
                    $firstDoc = is_array($requiredDocuments) ? $requiredDocuments[0] : $requiredDocuments->first();
                @endphp
                <a href="{{ route('client.documents.upload.form', ['type' => $firstDoc->document_type]) }}" class="pwa-btn-primary">
                    <i class="fas fa-plus me-2"></i> Ajouter un document
                </a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- FAB --}}
    @if(count($missingDocuments) > 0)
        @php
            $firstMissing = is_array($missingDocuments) ? $missingDocuments[0] : $missingDocuments->first();
        @endphp
        <a href="{{ route('client.documents.upload.form', ['type' => $firstMissing->document_type]) }}" class="pwa-fab">
            <i class="fas fa-plus"></i>
        </a>
    @endif
</div>

{{-- Bottom Sheet Delete --}}
<div class="pwa-bottom-sheet" id="pwaDeleteSheet">
    <div class="pwa-sheet-overlay" onclick="closeDelete()"></div>
    <div class="pwa-sheet-content">
        <div class="pwa-sheet-header">
            <div class="pwa-sheet-drag"></div>
            <h3>Supprimer le document</h3>
        </div>
        <div class="pwa-sheet-body">
            <p>Supprimer <strong id="pwaDeleteName"></strong> ?</p>
            <p class="text-danger small">Action irréversible.</p>
        </div>
        <div class="pwa-sheet-footer">
            <button class="pwa-btn-cancel" onclick="closeDelete()">Annuler</button>
            <button class="pwa-btn-confirm-delete" id="pwaBtnDelete" onclick="executeDelete()">
                <span class="pwa-btn-text">Supprimer</span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
    </div>
</div>

{{-- Bottom Sheet Renew --}}
<div class="pwa-bottom-sheet" id="pwaRenewSheet">
    <div class="pwa-sheet-overlay" onclick="closeRenew()"></div>
    <div class="pwa-sheet-content">
        <div class="pwa-sheet-header">
            <div class="pwa-sheet-drag"></div>
            <h3>Renouveler</h3>
        </div>
        <div class="pwa-sheet-body">
            <p>Renouveler <strong id="pwaRenewName"></strong> ?</p>
            <div class="alert alert-warning small">
                <i class="fas fa-exclamation-triangle me-1"></i> Le document sera en attente de validation.
            </div>
        </div>
        <div class="pwa-sheet-footer">
            <button class="pwa-btn-cancel" onclick="closeRenew()">Annuler</button>
            <button class="pwa-btn-confirm-renew" id="pwaBtnRenew" onclick="executeRenew()">
                <span class="pwa-btn-text">Renouveler</span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
    </div>
</div>

{{-- Modal Rejection --}}
<div class="modal fade" id="pwaRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Document rejeté</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 id="pwaRejectDocName" class="mb-3 fw-bold"></h6>
                <div class="alert alert-danger" id="pwaRejectReason"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Compris</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* [CSS identique au précédent avec préfixe pwa-] */
.pwa-docs-container { padding: 0 0 2rem 0; max-width: 100%; }
.pwa-docs-header { background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%); padding: 1.25rem; padding-top: calc(1.25rem + env(safe-area-inset-top, 0px)); margin: -1rem -1rem 1rem -1rem; position: relative; overflow: hidden; }
.pwa-header-bg { position: absolute; inset: 0; opacity: 0.1; background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px; }
.pwa-header-content { position: relative; display: flex; align-items: center; gap: 1rem; color: white; }
.pwa-completion-ring { position: relative; width: 70px; height: 70px; flex-shrink: 0; }
.pwa-percentage { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.1rem; font-weight: 700; }
.pwa-header-text h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0; font-family: 'Rajdhani', sans-serif; }
.pwa-header-text p { font-size: 0.85rem; opacity: 0.9; margin: 0 0 0.5rem 0; }
.pwa-badge-incomplete, .pwa-badge-complete { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(10px); }
.pwa-badge-incomplete { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); }
.pwa-badge-complete { background: rgba(34, 197, 94, 0.3); border: 1px solid rgba(34, 197, 94, 0.5); }
.pwa-stats-scroll { margin: 0 -1rem 1.25rem -1rem; padding: 0 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
.pwa-stats-scroll::-webkit-scrollbar { display: none; }
.pwa-stats-track { display: flex; gap: 0.625rem; width: max-content; }
.pwa-stat-pill { display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1rem; background: white; border-radius: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); border: 1px solid var(--secondary-200, #e5e7eb); min-width: 130px; }
.pwa-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; }
.pwa-stat-pill.validated .pwa-stat-icon { background: var(--success-50, #f0fdf4); color: var(--success-600, #16a34a); }
.pwa-stat-pill.pending .pwa-stat-icon { background: var(--warning-50, #fffbeb); color: var(--warning-600, #d97706); }
.pwa-stat-pill.missing .pwa-stat-icon { background: var(--error-50, #fef2f2); color: var(--error-600, #dc2626); }
.pwa-stat-pill.total .pwa-stat-icon { background: var(--primary-50, #e8f4fd); color: var(--primary-600, #164a77); }
.pwa-stat-num { font-size: 1.25rem; font-weight: 700; color: var(--secondary-800, #1f2937); line-height: 1; }
.pwa-stat-label { font-size: 0.75rem; color: var(--secondary-500, #6b7280); }
.pwa-missing-alert { background: linear-gradient(135deg, #fff9db 0%, #fff3bf 100%); border: 1px solid #ffd43b; border-radius: 14px; margin: 0 1rem 1.25rem 1rem; overflow: hidden; }
.pwa-missing-header { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; cursor: pointer; user-select: none; }
.pwa-missing-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--secondary-800, #1f2937); }
.pwa-missing-toggle { transition: transform 0.3s; color: var(--secondary-500, #6b7280); }
.pwa-missing-alert.collapsed .pwa-missing-toggle { transform: rotate(-180deg); }
.pwa-missing-content { max-height: 300px; overflow: hidden; transition: max-height 0.3s ease; }
.pwa-missing-alert.collapsed .pwa-missing-content { max-height: 0; }
.pwa-missing-item { display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.875rem; margin: 0 0.75rem 0.5rem 0.75rem; background: white; border-radius: 10px; border: 1px solid #ffe066; }
.pwa-missing-item:last-child { margin-bottom: 0.75rem; }
.pwa-missing-info { display: flex; flex-direction: column; min-width: 0; }
.pwa-missing-name { font-weight: 600; font-size: 0.9rem; color: var(--secondary-800, #1f2937); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pwa-missing-info small { color: var(--secondary-500, #6b7280); font-size: 0.75rem; }
.pwa-missing-add { width: 36px; height: 36px; border-radius: 9px; background: var(--primary-500, #1b5a8d); color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; text-decoration: none; }
.pwa-missing-add:active { transform: scale(0.95); background: var(--primary-600, #164a77); }
.pwa-filters-wrap { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; padding: 0 1rem; }
.pwa-filters-scroll { flex: 1; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; display: flex; gap: 0.5rem; }
.pwa-filters-scroll::-webkit-scrollbar { display: none; }
.pwa-filter-chip { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; background: white; border: 1px solid var(--secondary-200, #e5e7eb); border-radius: 50px; font-size: 0.875rem; font-weight: 500; color: var(--secondary-600, #6b7280); white-space: nowrap; transition: all 0.2s; cursor: pointer; -webkit-tap-highlight-color: transparent; }
.pwa-filter-chip:active { transform: scale(0.95); }
.pwa-filter-chip.active { background: var(--primary-500, #1b5a8d); color: white; border-color: var(--primary-500, #1b5a8d); box-shadow: 0 4px 10px rgba(27, 90, 141, 0.25); }
.pwa-filter-count { background: var(--secondary-100, #f3f4f6); color: var(--secondary-700, #374151); padding: 0.125rem 0.5rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.pwa-filter-chip.active .pwa-filter-count { background: rgba(255,255,255,0.3); color: white; }
.pwa-refresh-btn { width: 40px; height: 40px; border-radius: 10px; background: white; border: 1px solid var(--secondary-200, #e5e7eb); color: var(--secondary-600, #6b7280); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.pwa-refresh-btn:active { background: var(--secondary-100, #f3f4f6); color: var(--primary-500, #1b5a8d); }
.pwa-refresh-btn.spinning i { animation: pwa-spin 0.5s linear; }
@keyframes pwa-spin { to { transform: rotate(360deg); } }
.pwa-docs-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; padding: 0 1rem; }
.pwa-doc-card { background: white; border-radius: 14px; border: 1px solid var(--secondary-200, #e5e7eb); overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.2s; }
.pwa-doc-card:active { transform: scale(0.98); }
.pwa-card-main { display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem; cursor: pointer; }
.pwa-doc-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.pwa-doc-icon.img { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #2563eb; }
.pwa-doc-icon.pdf { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; }
.pwa-doc-icon.file { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #4b5563; }
.pwa-doc-details { flex: 1; min-width: 0; }
.pwa-doc-details h3 { font-size: 0.95rem; font-weight: 600; color: var(--secondary-800, #1f2937); margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pwa-doc-filename { font-size: 0.8rem; color: var(--secondary-500, #6b7280); margin: 0 0 0.375rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pwa-doc-meta { display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.375rem; }
.pwa-doc-type { font-size: 0.7rem; padding: 0.2rem 0.5rem; background: var(--secondary-100, #f3f4f6); color: var(--secondary-600, #6b7280); border-radius: 6px; font-weight: 500; }
.pwa-doc-size { font-size: 0.7rem; color: var(--secondary-400, #9ca3af); }
.pwa-doc-status-row { display: flex; align-items: center; gap: 0.375rem; flex-wrap: wrap; }
.pwa-status-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
.pwa-status-badge.success { background: var(--success-50, #f0fdf4); color: var(--success-700, #15803d); }
.pwa-status-badge.warning { background: var(--warning-50, #fffbeb); color: var(--warning-700, #b45309); }
.pwa-status-badge.danger { background: var(--error-50, #fef2f2); color: var(--error-700, #b91c1c); }
.pwa-status-badge.expired { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
.pwa-doc-chevron { color: var(--secondary-400, #9ca3af); transition: transform 0.3s; font-size: 0.875rem; }
.pwa-doc-card.expanded .pwa-doc-chevron { transform: rotate(90deg); }
.pwa-card-actions { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background: var(--secondary-50, #f8fafc); border-top: 1px solid var(--secondary-200, #e5e7eb); }
.pwa-doc-card.expanded .pwa-card-actions { max-height: 150px; }
.pwa-actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; padding: 0.625rem; }
.pwa-action-btn { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; padding: 0.625rem 0.25rem; background: white; border: 1px solid var(--secondary-200, #e5e7eb); border-radius: 10px; font-size: 0.7rem; color: var(--secondary-700, #374151); transition: all 0.2s; cursor: pointer; text-decoration: none; }
.pwa-action-btn:active { transform: scale(0.95); background: var(--secondary-50, #f8fafc); }
.pwa-action-btn i { font-size: 1.125rem; }
.pwa-action-btn.view { color: var(--info-600, #0369a1); border-color: var(--info-200, #bfdbfe); }
.pwa-action-btn.download { color: var(--primary-600, #164a77); border-color: var(--primary-200, #d1e9fb); }
.pwa-action-btn.delete { color: var(--error-600, #dc2626); border-color: var(--error-200, #fecaca); }
.pwa-action-btn.renew { color: var(--warning-600, #d97706); border-color: var(--warning-200, #fde68a); }
.pwa-action-btn.reason { color: var(--secondary-600, #6b7280); border-color: var(--secondary-300, #d1d5db); }
.pwa-empty-state { text-align: center; padding: 2.5rem 1rem; background: white; border-radius: 14px; border: 2px dashed var(--secondary-300, #d1d5db); }
.pwa-empty-icon { width: 64px; height: 64px; margin: 0 auto 1rem; background: var(--secondary-100, #f3f4f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--secondary-400, #9ca3af); }
.pwa-empty-state h3 { color: var(--secondary-800, #1f2937); font-size: 1rem; margin-bottom: 0.25rem; }
.pwa-empty-state p { color: var(--secondary-500, #6b7280); font-size: 0.875rem; margin-bottom: 1.25rem; }
.pwa-btn-primary { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.25rem; background: var(--primary-500, #1b5a8d); color: white; border-radius: 10px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; width: 100%; max-width: 260px; }
.pwa-btn-primary:active { background: var(--primary-600, #164a77); }
.pwa-fab { position: fixed; bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px) + 60px); right: 1.25rem; width: 52px; height: 52px; background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; box-shadow: 0 4px 12px rgba(27, 90, 141, 0.35); z-index: 999; text-decoration: none; }
.pwa-fab:active { transform: scale(0.95); box-shadow: 0 2px 8px rgba(27, 90, 141, 0.25); }
.pwa-bottom-sheet { position: fixed; inset: 0; z-index: 9999; visibility: hidden; opacity: 0; transition: opacity 0.3s, visibility 0.3s; }
.pwa-bottom-sheet.show { visibility: visible; opacity: 1; }
.pwa-sheet-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
.pwa-sheet-content { position: absolute; bottom: 0; left: 0; right: 0; background: white; border-radius: 20px 20px 0 0; padding: 1rem 1.25rem; padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px)); transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.pwa-bottom-sheet.show .pwa-sheet-content { transform: translateY(0); }
.pwa-sheet-header { text-align: center; margin-bottom: 1.25rem; }
.pwa-sheet-drag { width: 36px; height: 4px; background: var(--secondary-300, #d1d5db); border-radius: 2px; margin: 0 auto 0.75rem auto; }
.pwa-sheet-header h3 { font-size: 1.1rem; font-weight: 700; color: var(--secondary-800, #1f2937); margin: 0; }
.pwa-sheet-body { margin-bottom: 1.25rem; }
.pwa-sheet-body p { color: var(--secondary-600, #6b7280); font-size: 0.95rem; margin-bottom: 0.5rem; }
.pwa-sheet-footer { display: flex; gap: 0.75rem; }
.pwa-sheet-footer button { flex: 1; padding: 0.875rem; border-radius: 12px; font-weight: 600; font-size: 0.95rem; border: none; cursor: pointer; transition: transform 0.2s; display: flex; align-items: center; justify-content: center; }
.pwa-sheet-footer button:active { transform: scale(0.98); }
.pwa-btn-cancel { background: var(--secondary-100, #f3f4f6); color: var(--secondary-700, #374151); }
.pwa-btn-confirm-delete { background: var(--error-500, #ef4444); color: white; }
.pwa-btn-confirm-renew { background: var(--warning-500, #f59e0b); color: white; }
</style>
@endpush
