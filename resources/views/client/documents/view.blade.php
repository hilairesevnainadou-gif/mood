@extends('layouts.client')

@section('title', 'Détail du Document')

@section('content')

<script>
// Fonctions globales comme dans le index
window.toggleDocMenu = function() {
    document.getElementById('pwaDocMenu').classList.toggle('show');
};

window.closeDocMenu = function() {
    document.getElementById('pwaDocMenu').classList.remove('show');
};

window.confirmDeleteDoc = function() {
    window.pwaDeleteDocId = {{ $document->id }};
    document.getElementById('pwaDeleteSheet').classList.add('show');
    closeDocMenu();
};

window.closeDeleteDoc = function() {
    document.getElementById('pwaDeleteSheet').classList.remove('show');
    window.pwaDeleteDocId = null;
};

window.executeDeleteDoc = function() {
    const btn = document.getElementById('pwaBtnConfirmDelete');
    const spinner = btn.querySelector('.spinner-border');
    const text = btn.querySelector('.pwa-btn-text');

    btn.disabled = true;
    if (text) text.textContent = 'Suppression...';
    if (spinner) spinner.classList.remove('d-none');

    fetch('/client/documents/{{ $document->id }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeDeleteDoc();
            if (window.toast) window.toast.success('Succès', data.message);
            setTimeout(() => window.location.href = '{{ route('client.documents.index') }}', 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(e => {
        if (window.toast) window.toast.error('Erreur', e.message);
        btn.disabled = false;
        if (text) text.textContent = 'Supprimer';
        if (spinner) spinner.classList.add('d-none');
    });
};

window.openLightbox = function() {
    const modal = new bootstrap.Modal(document.getElementById('pwaImageModal'));
    modal.show();
};

// Fermer menu si clic extérieur
document.addEventListener('click', function(e) {
    const menu = document.getElementById('pwaDocMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.pwa-menu-trigger')) {
        menu.classList.remove('show');
    }
});

// Escape pour fermer
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteDoc();
        closeDocMenu();
    }
});
</script>

<div class="pwa-doc-viewport">
    {{-- Header cohérent avec le index --}}
    <div class="pwa-docs-header" style="margin: -1rem -1rem 1rem -1rem; border-radius: 0 0 20px 20px;">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content" style="position: relative; z-index: 2;">
            <a href="{{ route('client.documents.index') }}" class="pwa-back-btn" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; font-size: 1.25rem;">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="pwa-header-text" style="flex: 1;">
                <span style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.1em;">{{ $documentTypeName }}</span>
                <h1 style="font-size: 1.25rem; margin: 0; font-family: 'Rajdhani', sans-serif; font-weight: 700;">{{ Str::limit($document->name, 25) }}</h1>
            </div>

            <button class="pwa-menu-trigger" onclick="toggleDocMenu()" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); border: none; color: white; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-ellipsis-v"></i>
            </button>

            {{-- Menu contextuel (même style que le index) --}}
            <div class="pwa-context-menu" id="pwaDocMenu" style="position: absolute; top: 60px; right: 1rem; background: white; border-radius: 14px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); min-width: 220px; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s; z-index: 1001; overflow: hidden; border: 1px solid var(--secondary-200);">
                @if($fileExists)
                <a href="{{ route('client.documents.download', $document->id) }}" class="pwa-menu-item" style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem 1.25rem; text-decoration: none; color: var(--secondary-700); border-bottom: 1px solid var(--secondary-100);">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-50); color: var(--primary-600); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div style="font-weight: 500;">Télécharger</div>
                </a>
                @endif

                @if($document->status === 'pending' || $document->status === 'rejected')
                <button class="pwa-menu-item" onclick="confirmDeleteDoc()" style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem 1.25rem; width: 100%; background: none; border: none; color: var(--error-600); cursor: pointer;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--error-50); color: var(--error-600); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div style="font-weight: 500;">Supprimer</div>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Contenu scrollable --}}
    <div class="pwa-doc-content" style="padding: 0 1rem 2rem 1rem; max-width: 800px; margin: 0 auto;">

        {{-- Card Preview (même style que pwa-doc-card du index) --}}
        <div class="pwa-doc-card" style="background: white; border-radius: 14px; border: 1px solid var(--secondary-200); overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); margin-bottom: 1rem;">
            @if($fileExists)
                @if($document->isImage())
                    <div onclick="openLightbox()" style="position: relative; height: 350px; cursor: pointer; overflow: hidden;">
                        <img src="{{ $document->file_url }}" alt="{{ $document->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; bottom: 1rem; right: 1rem; width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: var(--secondary-800);">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                @elseif($document->isPdf())
                    <div style="padding: 3rem 1.5rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                        <div style="width: 100px; height: 100px; border-radius: 24px; background: linear-gradient(135deg, #fee2e2, white); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 3rem; border: 2px solid #fecaca;">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3 style="margin: 0; font-family: 'Rajdhani', sans-serif; color: var(--secondary-800); font-size: 1.1rem;">{{ $document->original_filename }}</h3>
                        <span style="color: var(--secondary-500); font-size: 0.875rem;">{{ $formattedSize }} • PDF</span>
                        <a href="{{ $document->file_url }}" target="_blank" class="pwa-action-btn" style="margin-top: 0.5rem; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--primary-500); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 12px rgba(27, 90, 141, 0.3);">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Ouvrir le PDF</span>
                        </a>
                    </div>
                @else
                    <div style="padding: 3rem 1.5rem; text-align: center;">
                        <div style="width: 100px; height: 100px; border-radius: 24px; background: linear-gradient(135deg, var(--primary-50), white); color: var(--primary-600); display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 1rem; border: 2px solid var(--primary-100);">
                            <i class="{{ $document->file_icon }}"></i>
                        </div>
                        <h3 style="margin: 0 0 1rem 0; color: var(--secondary-800);">{{ $document->original_filename }}</h3>
                        <a href="{{ route('client.documents.download', $document->id) }}" class="pwa-action-btn" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--primary-500); color: white; border-radius: 10px; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-download"></i>
                            <span>Télécharger</span>
                        </a>
                    </div>
                @endif
            @else
                <div style="padding: 3rem 1.5rem; text-align: center; color: var(--secondary-500);">
                    <div style="width: 80px; height: 80px; background: var(--warning-50); color: var(--warning-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 style="color: var(--secondary-800); margin: 0 0 0.5rem 0;">Fichier non trouvé</h3>
                    <p style="margin: 0;">Le document n'est plus disponible sur le serveur</p>
                </div>
            @endif
        </div>

        {{-- Status Card --}}
        <div class="pwa-doc-card" style="background: white; border-radius: 14px; border: 1px solid var(--secondary-200); overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.04); padding: 1.25rem; margin-bottom: 1rem;">
            @switch($document->status)
                @case('validated')
                    @if($document->is_expired)
                        <div class="pwa-status-badge expired" style="width: 100%; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; margin-bottom: 1rem;">
                            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
                            <div>
                                <div style="font-weight: 700;">Document expiré</div>
                                <div style="font-size: 0.85rem; opacity: 0.9;">Le {{ $document->expiry_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @else
                        <div class="pwa-status-badge success" style="width: 100%; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; margin-bottom: 1rem; background: var(--success-50); color: var(--success-700); border-radius: 10px; border: 1px solid var(--success-200);">
                            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
                            <div>
                                <div style="font-weight: 700;">Document validé</div>
                                <div style="font-size: 0.85rem; opacity: 0.9;">Depuis le {{ $document->validated_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @endif
                    @break
                @case('pending')
                    <div class="pwa-status-badge warning" style="width: 100%; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; margin-bottom: 1rem; background: var(--warning-50); color: var(--warning-700); border-radius: 10px; border: 1px solid var(--warning-200);">
                        <i class="fas fa-clock" style="font-size: 1.25rem;"></i>
                        <div>
                            <div style="font-weight: 700;">En attente de validation</div>
                            <div style="font-size: 0.85rem; opacity: 0.9;">Délai moyen : 24-48h</div>
                        </div>
                    </div>
                    @break
                @case('rejected')
                    <div class="pwa-status-badge danger" style="width: 100%; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; margin-bottom: 1rem; background: var(--error-50); color: var(--error-700); border-radius: 10px; border: 1px solid var(--error-200);">
                        <i class="fas fa-times-circle" style="font-size: 1.25rem;"></i>
                        <div>
                            <div style="font-weight: 700;">Document rejeté</div>
                            <div style="font-size: 0.85rem; opacity: 0.9;">Action requise de votre part</div>
                        </div>
                    </div>
                    @break
            @endswitch

            {{-- Grille d'infos (même style que le index) --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                <div style="padding: 0.875rem; background: var(--secondary-50); border-radius: 10px; border: 1px solid var(--secondary-100);">
                    <div style="font-size: 0.75rem; color: var(--secondary-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; font-weight: 600;">Nom</div>
                    <div style="font-weight: 700; color: var(--secondary-800); font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($document->original_filename, 18) }}</div>
                </div>

                <div style="padding: 0.875rem; background: var(--secondary-50); border-radius: 10px; border: 1px solid var(--secondary-100);">
                    <div style="font-size: 0.75rem; color: var(--secondary-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; font-weight: 600;">Taille</div>
                    <div style="font-weight: 700; color: var(--secondary-800); font-size: 0.9rem;">{{ $formattedSize }}</div>
                </div>

                <div style="padding: 0.875rem; background: var(--secondary-50); border-radius: 10px; border: 1px solid var(--secondary-100);">
                    <div style="font-size: 0.75rem; color: var(--secondary-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; font-weight: 600;">Upload</div>
                    <div style="font-weight: 700; color: var(--secondary-800); font-size: 0.9rem;">{{ $document->uploaded_at->format('d/m/Y') }}</div>
                </div>

                <div style="padding: 0.875rem; background: var(--secondary-50); border-radius: 10px; border: 1px solid var(--secondary-100);">
                    <div style="font-size: 0.75rem; color: var(--secondary-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; font-weight: 600;">Expiration</div>
                    <div style="font-weight: 700; color: {{ $document->is_expired ? 'var(--error-600)' : 'var(--secondary-800)' }}; font-size: 0.9rem;">
                        @if($document->expiry_date)
                            {{ $document->expiry_date->format('d/m/Y') }}
                        @else
                            Aucune
                        @endif
                    </div>
                </div>
            </div>

            @if($document->description)
            <div style="margin-top: 1rem; padding: 1rem; background: linear-gradient(135deg, var(--secondary-50), white); border-radius: 10px; border: 1px solid var(--secondary-200);">
                <div style="font-size: 0.75rem; color: var(--secondary-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; font-weight: 600;">
                    <i class="fas fa-align-left me-1"></i> Description
                </div>
                <div style="color: var(--secondary-700); font-size: 0.95rem; line-height: 1.5;">{{ $document->description }}</div>
            </div>
            @endif

            @if($document->status === 'rejected' && $document->rejection_reason)
            <div style="margin-top: 1rem; padding: 1rem; background: linear-gradient(135deg, var(--error-50), white); border-radius: 10px; border: 1px solid var(--error-200); border-left: 4px solid var(--error-500);">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--error-700); font-weight: 700; margin-bottom: 0.5rem; font-family: 'Rajdhani', sans-serif; font-size: 1.1rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Raison du rejet</span>
                </div>
                <p style="margin: 0 0 0.75rem 0; color: var(--error-800); line-height: 1.5;">{{ $document->rejection_reason }}</p>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--error-600); padding-top: 0.75rem; border-top: 1px dashed rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-lightbulb"></i>
                    <span>Veuillez corriger et réuploader</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions Grid (comme dans le index) --}}
        <div style="display: grid; grid-template-columns: repeat({{ ($fileExists ? 2 : 0) + (($document->status === 'pending' || $document->status === 'rejected') ? 1 : 0) }}, 1fr); gap: 0.625rem;">
            @if($fileExists)
            <a href="{{ route('client.documents.view.page', $document->id) }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: white; border: 1px solid var(--secondary-200); border-radius: 14px; text-decoration: none; color: var(--info-600); box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                <i class="fas fa-eye" style="font-size: 1.5rem;"></i>
                <span style="font-size: 0.875rem; font-weight: 600;">Voir</span>
            </a>

            <a href="{{ route('client.documents.download', $document->id) }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: white; border: 1px solid var(--secondary-200); border-radius: 14px; text-decoration: none; color: var(--primary-600); box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                <i class="fas fa-download" style="font-size: 1.5rem;"></i>
                <span style="font-size: 0.875rem; font-weight: 600;">Télécharger</span>
            </a>
            @endif

            @if($document->status === 'pending' || $document->status === 'rejected')
            <button onclick="confirmDeleteDoc()" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: white; border: 1px solid var(--error-200); border-radius: 14px; color: var(--error-600); cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                <i class="fas fa-trash-alt" style="font-size: 1.5rem;"></i>
                <span style="font-size: 0.875rem; font-weight: 600;">Supprimer</span>
            </button>
            @endif
        </div>
    </div>
</div>

{{-- Modal Image --}}
@if($document->isImage() && $fileExists)
<div class="modal fade" id="pwaImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: rgba(0,0,0,0.95);">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" style="font-family: 'Rajdhani', sans-serif;">{{ $document->name }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('client.documents.download', $document->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center p-0">
                <img src="{{ $document->file_url }}" class="img-fluid" style="max-height: 90vh;" alt="{{ $document->name }}">
            </div>
        </div>
    </div>
</div>
@endif

{{-- Bottom Sheet Delete (identique au index) --}}
@if($document->status === 'pending' || $document->status === 'rejected')
<div class="pwa-bottom-sheet" id="pwaDeleteSheet">
    <div class="pwa-sheet-overlay" onclick="closeDeleteDoc()"></div>
    <div class="pwa-sheet-content">
        <div class="pwa-sheet-header">
            <div class="pwa-sheet-drag"></div>
            <h3>Supprimer le document</h3>
        </div>
        <div class="pwa-sheet-body">
            <p>Supprimer <strong>{{ $document->name }}</strong> ?</p>
            <p class="text-danger small">Cette action est irréversible.</p>
        </div>
        <div class="pwa-sheet-footer">
            <button class="pwa-btn-cancel" onclick="closeDeleteDoc()">Annuler</button>
            <button class="pwa-btn-confirm-delete" id="pwaBtnConfirmDelete" onclick="executeDeleteDoc()">
                <span class="pwa-btn-text">Supprimer</span>
                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </div>
    </div>
</div>
@endif

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
.pwa-missing-alert { background: linear-gradient(135deg, #fff9db 0%, #fff3bf 100%); border: 1px solid #ffd43b; border-radius: 14px; margin-bottom: 1.25rem; overflow: hidden; }
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
.pwa-filters-wrap { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; }
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
.pwa-docs-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
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
