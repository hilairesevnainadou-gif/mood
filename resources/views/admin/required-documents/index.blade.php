@extends('admin.layouts.app')
@section('title', 'Documents Requis')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--admin-text);">Documents Requis</h1>
            <p class="mb-0" style="color: var(--admin-text-muted);">Gérez les documents obligatoires par type de membre</p>
        </div>
        <a href="{{ route('admin.required-documents.create') }}" class="btn btn-primary d-flex align-items-center gap-2"
           style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 10px; padding: 10px 20px; font-weight: 500; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); transition: all 0.2s ease;">
            <i class="fas fa-plus"></i>
            <span>Ajouter un document</span>
        </a>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--admin-success); border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--admin-danger); border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($documents as $memberType => $docs)
    @php
        $isEnterprise = $memberType === 'entreprise';
        $headerGradient = $isEnterprise
            ? 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)'
            : 'linear-gradient(135deg, var(--admin-accent) 0%, var(--admin-accent-hover) 100%)';
        $headerIcon = $isEnterprise ? 'building' : 'user';
    @endphp

    <div class="admin-card mb-4" style="padding: 0; overflow: hidden; border-radius: 16px;">
        {{-- Card Header --}}
        <div class="card-header text-white d-flex justify-content-between align-items-center"
             style="background: {{ $headerGradient }}; padding: 20px 24px; border: none;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <i class="fas fa-{{ $headerIcon }}" style="font-size: 1.1rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $memberTypes[$memberType] ?? ucfirst($memberType) }}</h5>
                    <span style="opacity: 0.7; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">{{ $memberType }}</span>
                </div>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #fff; font-size: 0.75rem; padding: 6px 12px; border-radius: 20px; margin-left: 8px;">
                    {{ $docs->count() }}
                </span>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="font-size: 0.9rem;">
                <thead style="background: var(--admin-bg);">
                    <tr style="color: var(--admin-text-muted); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th width="60" class="text-center py-3">Ordre</th>
                        <th class="py-3">Nom / Description</th>
                        <th width="100" class="py-3">Catégorie</th>
                        <th width="80" class="text-center py-3">Requis</th>
                        <th width="100" class="text-center py-3">Expiration</th>
                        <th class="py-3">Formats</th>
                        <th width="80" class="text-center py-3">Taille</th>
                        <th width="100" class="text-center py-3">Statut</th>
                        <th width="120" class="text-center py-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="background: #fff;">
                    @foreach($docs as $doc)
                    <tr style="{{ !$doc->is_active ? 'background: var(--admin-bg); opacity: 0.7;' : '' }}; transition: all 0.2s ease;">
                        {{-- Ordre --}}
                        <td class="text-center">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: var(--admin-bg); color: var(--admin-text); border-radius: 8px; font-weight: 600; font-size: 0.85rem; border: 1px solid var(--admin-border);">
                                {{ $doc->order }}
                            </span>
                        </td>

                        {{-- Nom et Description --}}
                        <td>
                            <div style="font-weight: 600; color: var(--admin-text); {{ !$doc->is_active ? 'text-decoration: line-through;' : '' }}; margin-bottom: 2px;">
                                {{ $doc->name }}
                            </div>
                            @if($doc->description)
                                <small style="color: var(--admin-text-muted); display: block; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $doc->description }}">
                                    {{ $doc->description }}
                                </small>
                            @endif
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            @php
                                $categoryColors = [
                                    'verification' => ['bg' => '#0ea5e9', 'light' => 'rgba(14, 165, 233, 0.1)'],
                                    'financial' => ['bg' => 'var(--admin-success)', 'light' => 'rgba(16, 185, 129, 0.1)'],
                                    'project' => ['bg' => 'var(--admin-warning)', 'light' => 'rgba(245, 158, 11, 0.1)'],
                                    'business' => ['bg' => 'var(--admin-accent)', 'light' => 'rgba(59, 130, 246, 0.1)'],
                                    'personal' => ['bg' => '#64748b', 'light' => 'rgba(100, 116, 139, 0.1)'],
                                    'other' => ['bg' => '#334155', 'light' => 'rgba(51, 65, 85, 0.1)']
                                ];
                                $catStyle = $categoryColors[$doc->category] ?? $categoryColors['other'];
                            @endphp
                            <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: {{ $catStyle['light'] }}; color: {{ $catStyle['bg'] }}; border: 1px solid {{ $catStyle['bg'] }}20;">
                                {{ $categories[$doc->category] ?? ucfirst($doc->category) }}
                            </span>
                        </td>

                        {{-- Requis --}}
                        <td class="text-center">
                            @if($doc->is_required)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 10px; background: rgba(239, 68, 68, 0.1); color: var(--admin-danger); border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-check" style="font-size: 0.7rem;"></i> Oui
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; padding: 6px 10px; background: var(--admin-bg); color: var(--admin-text-muted); border-radius: 6px; font-size: 0.75rem; font-weight: 500;">
                                    Non
                                </span>
                            @endif
                        </td>

                        {{-- Expiration --}}
                        <td class="text-center">
                            @if($doc->has_expiry_date)
                                @if($doc->validity_days)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 10px; background: rgba(245, 158, 11, 0.1); color: var(--admin-warning); border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-clock" style="font-size: 0.7rem;"></i> {{ $doc->validity_days }}j
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; padding: 6px; background: rgba(245, 158, 11, 0.1); color: var(--admin-warning); border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-infinity"></i>
                                    </span>
                                @endif
                            @else
                                <span style="color: var(--admin-text-light);">—</span>
                            @endif
                        </td>

                        {{-- Formats --}}
                        <td>
                            @if(!empty($doc->allowed_formats) && is_array($doc->allowed_formats))
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($doc->allowed_formats as $format)
                                        <span style="display: inline-block; padding: 4px 8px; background: var(--admin-bg); color: var(--admin-text-muted); border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; border: 1px solid var(--admin-border);">
                                            {{ strtoupper($format) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color: var(--admin-text-muted); font-size: 0.8rem;">Aucun</span>
                            @endif
                        </td>

                        {{-- Taille max --}}
                        <td class="text-center">
                            <span style="display: inline-block; padding: 6px 10px; background: var(--admin-bg); color: var(--admin-text); border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid var(--admin-border);">
                                {{ $doc->max_size_mb }} Mo
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td class="text-center">
                            <form action="{{ route('admin.required-documents.toggle-status', $doc) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                        style="border: none; padding: 8px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px; {{ $doc->is_active ? 'background: rgba(16, 185, 129, 0.1); color: var(--admin-success);' : 'background: var(--admin-bg); color: var(--admin-text-muted);' }}">
                                    <i class="fas fa-{{ $doc->is_active ? 'check' : 'times' }}" style="font-size: 0.7rem;"></i>
                                    {{ $doc->is_active ? 'Actif' : 'Inactif' }}
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.required-documents.edit', $doc) }}"
                                   style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--admin-border); background: #fff; color: var(--admin-accent); text-decoration: none; transition: all 0.2s ease;"
                                   onmouseover="this.style.background='var(--admin-accent)'; this.style.color='#fff'; this.style.borderColor='var(--admin-accent)';"
                                   onmouseout="this.style.background='#fff'; this.style.color='var(--admin-accent)'; this.style.borderColor='var(--admin-border)';"
                                   title="Modifier">
                                    <i class="fas fa-edit" style="font-size: 0.85rem;"></i>
                                </a>
                                <button type="button"
                                        onclick="openDeleteModal('{{ $doc->id }}', '{{ addslashes($doc->name) }}', '{{ $memberTypes[$doc->member_type] ?? $doc->member_type }}')"
                                        style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--admin-border); background: #fff; color: var(--admin-danger); cursor: pointer; transition: all 0.2s ease;"
                                        onmouseover="this.style.background='var(--admin-danger)'; this.style.color='#fff'; this.style.borderColor='var(--admin-danger)';"
                                        onmouseout="this.style.background='#fff'; this.style.color='var(--admin-danger)'; this.style.borderColor='var(--admin-border)';"
                                        title="Supprimer">
                                    <i class="fas fa-trash" style="font-size: 0.85rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="admin-card text-center py-5" style="border-radius: 16px;">
        <div class="mb-4" style="width: 80px; height: 80px; background: var(--admin-bg); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
            <i class="fas fa-folder-open" style="font-size: 2rem; color: var(--admin-text-light);"></i>
        </div>
        <h4 style="color: var(--admin-text); font-weight: 600; margin-bottom: 0.5rem;">Aucun document configuré</h4>
        <p style="color: var(--admin-text-muted); margin-bottom: 1.5rem;">Commencez par créer vos premiers documents requis pour les membres</p>
        <a href="{{ route('admin.required-documents.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2"
           style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 10px; padding: 12px 24px; font-weight: 500; color: #fff; text-decoration: none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
            <i class="fas fa-plus"></i>
            Créer le premier document
        </a>
    </div>
    @endforelse
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden;">
            {{-- Header --}}
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--admin-danger), #dc2626); padding: 20px 24px;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fas fa-trash-alt" style="font-size: 2rem; color: var(--admin-danger);"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--admin-text);">Êtes-vous sûr ?</h5>
                    <p class="mb-0" style="color: var(--admin-text-muted);">
                        Vous êtes sur le point de supprimer le document :
                    </p>
                </div>

                {{-- Infos du document --}}
                <div class="p-3 mb-3" style="background: var(--admin-bg); border-radius: 12px; border-left: 4px solid var(--admin-danger);">
                    <div class="fw-bold mb-1" id="modalDocName" style="color: var(--admin-text); font-size: 1.05rem;"></div>
                    <div class="small" style="color: var(--admin-text-muted);">
                        <i class="fas fa-user-tag me-1"></i>
                        <span id="modalDocMemberType"></span>
                    </div>
                </div>

                <div class="alert alert-warning" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: var(--admin-warning); border-radius: 10px; font-size: 0.9rem;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible. Le document sera définitivement supprimé de la base de données.
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="border-radius: 10px; padding: 10px 20px; font-weight: 500;">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            style="background: linear-gradient(135deg, var(--admin-danger), #dc2626); border: none; border-radius: 10px; padding: 10px 20px; font-weight: 500; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);">
                        <i class="fas fa-trash me-2"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animations spécifiques à cette page */
    .admin-card {
        animation: fadeIn 0.4s ease-out;
    }

    tr:hover {
        background-color: rgba(59, 130, 246, 0.02) !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal animations */
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }

    /* Responsive table */
    @media (max-width: 1200px) {
        .table-responsive {
            border-radius: 0 0 16px 16px;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .table-responsive {
            font-size: 0.85rem;
        }

        td, th {
            padding: 12px 8px !important;
        }
    }
</style>

@push('scripts')
<script>
    // Modal de suppression
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const modalDocName = document.getElementById('modalDocName');
    const modalDocMemberType = document.getElementById('modalDocMemberType');

    function openDeleteModal(id, name, memberType) {
        // Mettre à jour les infos dans le modal
        modalDocName.textContent = name;
        modalDocMemberType.textContent = memberType;

        // Mettre à jour l'action du formulaire
        const baseUrl = '{{ route("admin.required-documents.destroy", ":id") }}';
        deleteForm.action = baseUrl.replace(':id', id);

        // Ouvrir le modal
        deleteModal.show();
    }

    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.admin-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
@endpush
@endsection
