@extends('admin.layouts.app')

@section('title', 'Modifier le Document Requis')

@section('content')
<div class="container-fluid py-4">
    {{-- Header avec info document --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #fff;"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold" style="color: var(--admin-text);">Modifier le Document</h1>
                <p class="mb-0 d-flex align-items-center gap-2" style="color: var(--admin-text-muted);">
                    <span style="font-weight: 500; color: var(--admin-text);">{{ $requiredDocument->name }}</span>
                    <span style="color: var(--admin-border);">•</span>
                    <span class="badge" style="background: {{ $requiredDocument->is_active ? 'rgba(16, 185, 129, 0.1)' : 'rgba(100, 116, 139, 0.1)' }}; color: {{ $requiredDocument->is_active ? 'var(--admin-success)' : 'var(--admin-text-muted)' }}; border: 1px solid {{ $requiredDocument->is_active ? 'rgba(16, 185, 129, 0.2)' : 'rgba(100, 116, 139, 0.2)' }}; border-radius: 20px; padding: 4px 12px; font-size: 0.75rem;">
                        <i class="fas fa-{{ $requiredDocument->is_active ? 'check' : 'times' }} me-1" style="font-size: 0.6rem;"></i>
                        {{ $requiredDocument->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.required-documents.index') }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-2"
           style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
            <i class="fas fa-arrow-left"></i>
            <span>Retour à la liste</span>
        </a>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--admin-success); border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--admin-danger); border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--admin-danger); border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Veuillez corriger les erreurs suivantes</strong>
        </div>
    @endif

    <form action="{{ route('admin.required-documents.update', $requiredDocument) }}" method="POST" id="updateForm">
        @csrf
        @method('PUT')

        {{-- Champs cachés pour les types non modifiables --}}
        <input type="hidden" name="member_type" value="{{ $requiredDocument->member_type }}">
        <input type="hidden" name="document_type" value="{{ $requiredDocument->document_type }}">

        <div class="row g-4">
            {{-- Colonne principale --}}
            <div class="col-lg-8">
                {{-- Informations système (lecture seule) --}}
                <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px; background: var(--admin-bg); border: 1px solid var(--admin-border);">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text-muted);">
                            <i class="fas fa-lock me-2"></i>
                            Informations système
                        </h5>
                        <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: var(--admin-text-muted); border-radius: 20px; padding: 6px 12px; font-size: 0.75rem;">
                            <i class="fas fa-ban me-1"></i>Non modifiable
                        </span>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Type de membre (lecture seule) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text-muted); font-size: 0.9rem;">
                                    Type de membre
                                </label>
                                <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                     style="background: #fff; border: 1px solid var(--admin-border);">
                                    <div style="width: 40px; height: 40px; background: {{ $requiredDocument->member_type === 'entreprise' ? 'linear-gradient(135deg, #0f172a, #1e293b)' : 'linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover))' }}; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-{{ $requiredDocument->member_type === 'entreprise' ? 'building' : 'user' }}" style="color: #fff; font-size: 1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.95rem;">
                                            {{ $memberTypes[$requiredDocument->member_type] ?? ucfirst($requiredDocument->member_type) }}
                                        </div>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                            {{ $requiredDocument->member_type }}
                                        </small>
                                    </div>
                                    <i class="fas fa-lock ms-auto" style="color: var(--admin-text-light); font-size: 0.9rem;" title="Non modifiable"></i>
                                </div>
                            </div>

                            {{-- Type de document (lecture seule) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text-muted); font-size: 0.9rem;">
                                    Type de document
                                </label>
                                <div class="p-3 rounded-3 d-flex align-items-center gap-3"
                                     style="background: #fff; border: 1px solid var(--admin-border); font-family: monospace;">
                                    <div style="width: 40px; height: 40px; background: var(--admin-bg); border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--admin-border);">
                                        <i class="fas fa-code" style="color: var(--admin-text-muted); font-size: 1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.95rem;">
                                            {{ $requiredDocument->document_type }}
                                        </div>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">
                                            Identifiant technique
                                        </small>
                                    </div>
                                    <i class="fas fa-lock" style="color: var(--admin-text-light); font-size: 0.9rem;" title="Non modifiable"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informations modifiables --}}
                <div class="admin-card mt-4" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--admin-border) !important; background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover));">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="fas fa-edit me-2"></i>
                            Informations modifiables
                        </h5>
                        <span class="badge bg-white text-primary" style="font-size: 0.75rem; padding: 6px 12px; border-radius: 20px;">
                            ID: {{ $requiredDocument->id }}
                        </span>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Nom du document --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    Nom du document <span style="color: var(--admin-danger);">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $requiredDocument->name) }}"
                                       required
                                       placeholder="Nom affiché aux utilisateurs"
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
                                          placeholder="Description détaillée du document..."
                                          style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem; resize: vertical;">{{ old('description', $requiredDocument->description) }}</textarea>
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
                                        <option value="{{ $key }}" {{ old('category', $requiredDocument->category) == $key ? 'selected' : '' }}>
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
                                           value="{{ old('order', $requiredDocument->order) }}"
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

                {{-- Configuration des fichiers --}}
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
                                           value="{{ old('max_size_mb', $requiredDocument->max_size_mb) }}"
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
                                                   {{ in_array($format, old('allowed_formats', $requiredDocument->allowed_formats ?? [])) ? 'checked' : '' }}>
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

                {{-- Zone dangereuse --}}
                <div class="admin-card mt-4" style="padding: 0; overflow: hidden; border-radius: 16px; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <div class="p-4 border-bottom" style="border-color: rgba(239, 68, 68, 0.2) !important; background: rgba(239, 68, 68, 0.05);">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-danger);">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Zone dangereuse
                        </h5>
                    </div>

                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h6 class="mb-1 fw-semibold" style="color: var(--admin-text);">Supprimer ce document</h6>
                                <p class="mb-0" style="color: var(--admin-text-muted); font-size: 0.9rem;">
                                    Cette action est irréversible. Les documents déjà uploadés par les membres ne seront pas supprimés.
                                </p>
                            </div>
                            <button type="button"
                                    class="btn btn-outline-danger d-flex align-items-center gap-2"
                                    onclick="confirmDelete()"
                                    style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-width: 2px;">
                                <i class="fas fa-trash"></i>
                                Supprimer définitivement
                            </button>
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
                                           {{ old('is_required', $requiredDocument->is_required) ? 'checked' : '' }}
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
                                           {{ old('has_expiry_date', $requiredDocument->has_expiry_date) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>

                            {{-- Validité conditionnelle --}}
                            <div id="validitySection" class="{{ old('has_expiry_date', $requiredDocument->has_expiry_date) ? '' : 'd-none' }}">
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
                                           value="{{ old('validity_days', $requiredDocument->validity_days) }}"
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
                                           {{ old('is_active', $requiredDocument->is_active) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Métadonnées --}}
                <div class="admin-card mt-4" style="padding: 0; overflow: hidden; border-radius: 16px; background: var(--admin-bg);">
                    <div class="p-4">
                        <h6 class="mb-3 fw-bold" style="color: var(--admin-text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fas fa-info-circle me-2"></i>Informations système
                        </h6>
                        <div class="d-flex flex-column gap-2" style="font-size: 0.85rem; color: var(--admin-text-muted);">
                            <div class="d-flex justify-content-between">
                                <span>Créé le</span>
                                <span style="color: var(--admin-text); font-weight: 500;">{{ $requiredDocument->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Dernière modification</span>
                                <span style="color: var(--admin-text); font-weight: 500;">{{ $requiredDocument->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            @if($requiredDocument->deleted_at)
                                <div class="d-flex justify-content-between" style="color: var(--admin-danger);">
                                    <span><i class="fas fa-trash me-1"></i>Supprimé le</span>
                                    <span style="font-weight: 500;">{{ $requiredDocument->deleted_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex flex-column gap-3 mt-4">
                    <button type="submit"
                            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
                            style="background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-hover)); border: none; border-radius: 12px; padding: 14px 24px; font-weight: 600; font-size: 0.95rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25); transition: all 0.2s ease;">
                        <i class="fas fa-save"></i>
                        Enregistrer les modifications
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

    {{-- Formulaire de suppression caché --}}
    <form action="{{ route('admin.required-documents.destroy', $requiredDocument) }}"
          method="POST"
          id="deleteForm"
          class="d-none">
        @csrf
        @method('DELETE')
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

    .btn-outline-danger:hover {
        background: var(--admin-danger);
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    // Gestion de l'affichage de la section validité
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

    // Confirmation de suppression
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?\n\nCette action est irréversible.')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Validation du formulaire avant soumission
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        const formats = document.querySelectorAll('input[name="allowed_formats[]"]:checked');
        if (formats.length === 0) {
            if (!confirm('Aucun format de fichier sélectionné. Le document n\'acceptera aucun fichier.\n\nContinuer quand même ?')) {
                e.preventDefault();
            }
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
