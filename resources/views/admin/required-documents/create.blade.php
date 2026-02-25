@extends('admin.layouts.app')

@section('title', 'Ajouter un Document Requis')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--admin-text);">Ajouter un Document Requis</h1>
            <p class="mb-0" style="color: var(--admin-text-muted);">Définissez un nouveau document obligatoire pour les membres</p>
        </div>
        <a href="{{ route('admin.required-documents.index') }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-2"
           style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
            <i class="fas fa-arrow-left"></i>
            <span>Retour</span>
        </a>
    </div>

    {{-- Messages d'erreur globaux --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--admin-danger); border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.required-documents.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Colonne principale --}}
            <div class="col-lg-8">
                <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">
                            <i class="fas fa-file-alt me-2" style="color: var(--admin-accent);"></i>
                            Informations générales
                        </h5>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Type de membre --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Type de membre <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <select name="member_type"
                                        class="form-select @error('member_type') is-invalid @enderror"
                                        required
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($memberTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('member_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('member_type')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Type de document --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Type de document <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <select name="document_type"
                                        id="docTypeSelect"
                                        class="form-select @error('document_type') is-invalid @enderror"
                                        required
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($documentTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                    <option value="custom" {{ old('document_type') == 'custom' ? 'selected' : '' }}>-- Personnalisé --</option>
                                </select>

                                {{-- Input personnalisé --}}
                                <div id="customDocTypeWrapper" class="mt-2 {{ old('document_type') == 'custom' ? '' : 'd-none' }}">
                                    <input type="text"
                                           name="document_type_custom"
                                           id="customDocType"
                                           class="form-control @error('document_type_custom') is-invalid @enderror"
                                           placeholder="Nom du type personnalisé"
                                           value="{{ old('document_type_custom') }}"
                                           style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px;">
                                </div>

                                @error('document_type')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nom du document --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Nom du document <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="Ex: Carte nationale d'identité"
                                       style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                @error('name')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Description
                                </label>
                                <textarea name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Description détaillée du document (optionnel)..."
                                          style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem; resize: vertical;">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Catégorie --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Catégorie <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <select name="category"
                                        class="form-select @error('category') is-invalid @enderror"
                                        required
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', 'verification') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Ordre d'affichage --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Ordre d'affichage <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius: 10px 0 0 10px; border-color: var(--admin-border); background: var(--admin-bg);">
                                        <i class="fas fa-sort-numeric-down" style="color: var(--admin-text-muted);"></i>
                                    </span>
                                    <input type="number"
                                           name="order"
                                           class="form-control @error('order') is-invalid @enderror"
                                           value="{{ old('order', 0) }}"
                                           min="0"
                                           required
                                           style="border-radius: 0 10px 10px 0; border-color: var(--admin-border); padding: 12px 16px;">
                                </div>
                                @error('order')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Fichiers --}}
                <div class="admin-card mt-4" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">
                            <i class="fas fa-file-upload me-2" style="color: var(--admin-accent);"></i>
                            Configuration des fichiers
                        </h5>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Taille max --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Taille maximale <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="max_size_mb"
                                           class="form-control @error('max_size_mb') is-invalid @enderror"
                                           value="{{ old('max_size_mb', 5) }}"
                                           min="1"
                                           max="50"
                                           required
                                           style="border-radius: 10px 0 0 10px; border-color: var(--admin-border); padding: 12px 16px;">
                                    <span class="input-group-text" style="border-radius: 0 10px 10px 0; border-color: var(--admin-border); background: var(--admin-bg); font-weight: 600; color: var(--admin-text-muted);">
                                        Mo
                                    </span>
                                </div>
                                @error('max_size_mb')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Formats acceptés --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-3" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Formats de fichier acceptés
                                </label>
                                <div class="row g-2">
                                    @foreach($fileFormats as $format => $label)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="form-check custom-checkbox p-0">
                                            <input type="checkbox"
                                                   class="form-check-input d-none"
                                                   name="allowed_formats[]"
                                                   value="{{ $format }}"
                                                   id="format_{{ $format }}"
                                                   {{ in_array($format, old('allowed_formats', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 text-center py-2 px-3"
                                                   for="format_{{ $format }}"
                                                   style="border: 2px solid var(--admin-border); border-radius: 8px; cursor: pointer; transition: all 0.2s ease; font-size: 0.85rem; font-weight: 500; color: var(--admin-text-muted);">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('allowed_formats')
                                    <div class="invalid-feedback d-block mt-2" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="col-lg-4">
                {{-- Options --}}
                <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">
                            <i class="fas fa-cog me-2" style="color: var(--admin-accent);"></i>
                            Options
                        </h5>
                    </div>

                    <div class="p-4">
                        <div class="d-flex flex-column gap-4">
                            {{-- Document obligatoire --}}
                            <div class="d-flex align-items-center justify-content-between p-3"
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div>
                                    <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.9rem;">
                                        <i class="fas fa-asterisk me-2" style="color: var(--admin-danger); font-size: 0.8rem;"></i>
                                        Obligatoire
                                    </div>
                                    <small style="color: var(--admin-text-muted);">Le document doit être fourni</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_required"
                                           id="is_required"
                                           value="1"
                                           {{ old('is_required', true) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>

                            {{-- Date d'expiration --}}
                            <div class="d-flex align-items-center justify-content-between p-3"
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div>
                                    <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.9rem;">
                                        <i class="fas fa-calendar-alt me-2" style="color: var(--admin-warning); font-size: 0.8rem;"></i>
                                        Expiration
                                    </div>
                                    <small style="color: var(--admin-text-muted);">Le document a une date de validité</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="has_expiry_date"
                                           id="has_expiry_date"
                                           value="1"
                                           {{ old('has_expiry_date') ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>

                            {{-- Validité conditionnelle --}}
                            <div id="validitySection" class="{{ old('has_expiry_date') ? '' : 'd-none' }}">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Durée de validité (jours)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius: 10px 0 0 10px; border-color: var(--admin-border); background: var(--admin-bg);">
                                        <i class="fas fa-clock" style="color: var(--admin-text-muted);"></i>
                                    </span>
                                    <input type="number"
                                           name="validity_days"
                                           class="form-control"
                                           value="{{ old('validity_days') }}"
                                           min="1"
                                           placeholder="Illimité"
                                           style="border-radius: 0 10px 10px 0; border-color: var(--admin-border); padding: 12px 16px;">
                                </div>
                                <small style="color: var(--admin-text-muted); font-size: 0.8rem;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Laissez vide pour une validité illimitée
                                </small>
                            </div>

                            {{-- Document actif --}}
                            <div class="d-flex align-items-center justify-content-between p-3"
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div>
                                    <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.9rem;">
                                        <i class="fas fa-power-off me-2" style="color: var(--admin-success); font-size: 0.8rem;"></i>
                                        Actif
                                    </div>
                                    <small style="color: var(--admin-text-muted);">Visible pour les membres</small>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_active"
                                           id="is_active"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-column gap-3 mt-4">
                    <button type="submit"
                            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
                            style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 12px; padding: 14px 24px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); transition: all 0.2s ease;">
                        <i class="fas fa-save"></i>
                        Enregistrer le document
                    </button>

                    <a href="{{ route('admin.required-documents.index') }}"
                       class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2"
                       style="border-radius: 12px; padding: 14px 24px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Checkbox custom styling */
    .custom-checkbox input:checked + label {
        background: rgba(59, 130, 246, 0.1) !important;
        border-color: var(--admin-accent) !important;
        color: var(--admin-accent) !important;
    }

    .custom-checkbox label:hover {
        border-color: var(--admin-accent) !important;
        color: var(--admin-text) !important;
    }

    /* Form switch styling */
    .form-check-input:checked {
        background-color: var(--admin-accent);
        border-color: var(--admin-accent);
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }

    /* Invalid feedback styling */
    .is-invalid {
        border-color: var(--admin-danger) !important;
    }

    .is-invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.25) !important;
    }

    /* Input group styling */
    .input-group-text {
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group .form-control:focus {
        border-left: 1px solid var(--admin-accent);
    }

    /* Animation */
    .admin-card {
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Hover effects */
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35) !important;
    }

    .btn-outline-secondary:hover {
        background: var(--admin-bg);
        color: var(--admin-text);
    }
</style>
@endpush

@push('scripts')
<script>
    // Gestion du type de document personnalisé
    const docTypeSelect = document.getElementById('docTypeSelect');
    const customWrapper = document.getElementById('customDocTypeWrapper');
    const customInput = document.getElementById('customDocType');

    docTypeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customWrapper.classList.remove('d-none');
            customInput.required = true;
            customInput.name = 'document_type';
            this.name = 'document_type_select';
            setTimeout(() => customInput.focus(), 100);
        } else {
            customWrapper.classList.add('d-none');
            customInput.required = false;
            customInput.name = 'document_type_custom';
            this.name = 'document_type';
        }
    });

    // Gestion de la date d'expiration
    const expiryToggle = document.getElementById('has_expiry_date');
    const validitySection = document.getElementById('validitySection');

    expiryToggle.addEventListener('change', function() {
        if (this.checked) {
            validitySection.classList.remove('d-none');
            validitySection.querySelector('input').focus();
        } else {
            validitySection.classList.add('d-none');
            validitySection.querySelector('input').value = '';
        }
    });

    // Animation des cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.admin-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
@endpush
@endsection
