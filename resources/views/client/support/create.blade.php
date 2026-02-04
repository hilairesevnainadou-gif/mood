@extends('layouts.client')

@section('title', 'Nouveau ticket - Support')

@section('content')
<div class="pwa-create-container">
    {{-- Header avec retour --}}
    <div class="pwa-page-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <a href="{{ route('client.support') }}" class="pwa-back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pwa-header-text">
                <h1>Nouveau ticket</h1>
                <p>Support technique</p>
            </div>
            <div class="pwa-header-icon">
                <i class="fas fa-headset"></i>
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('client.support.submit') }}" method="POST" enctype="multipart/form-data" id="ticketForm" class="pwa-form">
        @csrf

        {{-- Section Informations --}}
        <div class="pwa-card pwa-form-card">
            <div class="pwa-card-header">
                <i class="fas fa-info-circle"></i>
                <h2>Informations</h2>
            </div>

            <div class="pwa-form-group">
                <label for="subject" class="pwa-label required">Sujet</label>
                <input type="text"
                       class="pwa-input @error('subject') is-invalid @enderror"
                       id="subject"
                       name="subject"
                       placeholder="Décrivez brièvement votre problème"
                       value="{{ old('subject') }}"
                       required>
                @error('subject')
                    <div class="pwa-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="pwa-form-row">
                <div class="pwa-form-group flex-1">
                    <label for="category" class="pwa-label required">Catégorie</label>
                    <select class="pwa-select @error('category') is-invalid @enderror"
                            id="category"
                            name="category"
                            required>
                        <option value="">Choisir...</option>
                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>Général</option>
                        <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Facturation</option>
                        <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>Compte</option>
                        <option value="training" {{ old('category') == 'training' ? 'selected' : '' }}>Formation</option>
                        <option value="funding" {{ old('category') == 'funding' ? 'selected' : '' }}>Financement</option>
                        <option value="document" {{ old('category') == 'document' ? 'selected' : '' }}>Document</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('category')
                        <div class="pwa-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pwa-form-group flex-1">
                    <label for="priority" class="pwa-label required">Priorité</label>
                    <select class="pwa-select @error('priority') is-invalid @enderror"
                            id="priority"
                            name="priority"
                            required>
                        <option value="">Choisir...</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <div class="pwa-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="pwa-form-group">
                <label for="description" class="pwa-label required">Description</label>
                <textarea class="pwa-textarea @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          rows="5"
                          placeholder="Expliquez votre problème en détail..." required>{{ old('description') }}</textarea>
                <div class="pwa-help-text">Soyez précis pour une réponse rapide</div>
                @error('description')
                    <div class="pwa-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Section Pièces jointes --}}
        <div class="pwa-card pwa-form-card">
            <div class="pwa-card-header secondary">
                <i class="fas fa-paperclip"></i>
                <h2>Pièces jointes</h2>
                <span class="pwa-badge-light">Optionnel</span>
            </div>

            <div class="pwa-upload-zone" id="dropZone">
                <input type="file"
                       class="pwa-file-input"
                       id="attachments"
                       name="attachments[]"
                       multiple
                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                <div class="pwa-upload-content">
                    <div class="pwa-upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p class="pwa-upload-title">Appuyez pour ajouter</p>
                    <p class="pwa-upload-subtitle">ou glissez-déposez</p>
                    <p class="pwa-upload-hint">JPG, PNG, PDF, DOC • Max 2MB</p>
                </div>
            </div>

            <div id="attachmentsPreview" class="pwa-attachments-list"></div>

            @error('attachments')
                <div class="pwa-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Info card --}}
        <div class="pwa-info-card">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Délai de réponse</strong>
                <p>Notre équipe répond sous 24-48h. Pour les urgences, choisissez la priorité "Urgent".</p>
            </div>
        </div>

        {{-- Boutons Submit --}}
        <div class="pwa-submit-bar">
            <a href="{{ route('client.support') }}" class="pwa-btn-secondary">Annuler</a>
            <button type="submit" class="pwa-btn-primary" id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                <span>Envoyer</span>
            </button>
        </div>
    </form>
</div>

<style>
.pwa-create-container {
    padding: 0 0 2rem 0;
    max-width: 100%;
    /* Espace pour la barre fixe et le bottom nav sur mobile */
    padding-bottom: calc(140px + env(safe-area-inset-bottom, 0px));
}

.pwa-page-header {
    background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin: -1rem -1rem 1rem -1rem;
    position: relative;
    overflow: hidden;
}

.pwa-header-bg {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
    background-size: 20px 20px;
}

.pwa-header-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}

.pwa-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    backdrop-filter: blur(10px);
    transition: all 0.2s;
    flex-shrink: 0;
}

.pwa-back-btn:active {
    transform: scale(0.95);
    background: rgba(255,255,255,0.3);
}

.pwa-header-text {
    flex: 1;
}

.pwa-header-text h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    font-family: 'Rajdhani', sans-serif;
}

.pwa-header-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.pwa-header-icon {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* Form */
.pwa-form {
    padding: 0 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.pwa-card {
    background: white;
    border-radius: 14px;
    border: 1px solid var(--secondary-200, #e5e7eb);
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    overflow: hidden;
}

.pwa-form-card {
    padding: 1.25rem;
}

.pwa-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.pwa-card-header i {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.pwa-card-header.secondary i {
    background: linear-gradient(135deg, var(--secondary-500, #6b7280) 0%, var(--secondary-600, #4b5563) 100%);
}

.pwa-card-header h2 {
    flex: 1;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--secondary-800, #1f2937);
    margin: 0;
}

.pwa-badge-light {
    background: var(--secondary-100, #f3f4f6);
    color: var(--secondary-600, #6b7280);
    padding: 0.25rem 0.625rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Form Elements */
.pwa-form-group {
    margin-bottom: 1.25rem;
}

.pwa-form-group:last-child {
    margin-bottom: 0;
}

.pwa-form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.flex-1 {
    flex: 1;
    margin-bottom: 0;
}

.pwa-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--secondary-700, #374151);
    margin-bottom: 0.5rem;
}

.pwa-label.required:after {
    content: " *";
    color: var(--error-500, #ef4444);
}

.pwa-input,
.pwa-textarea,
.pwa-select {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1.5px solid var(--secondary-200, #e5e7eb);
    border-radius: 12px;
    font-size: 0.95rem;
    color: var(--secondary-800, #1f2937);
    background: white;
    transition: all 0.2s;
    -webkit-appearance: none;
    appearance: none;
}

.pwa-input:focus,
.pwa-textarea:focus,
.pwa-select:focus {
    outline: none;
    border-color: var(--primary-500, #1b5a8d);
    box-shadow: 0 0 0 4px rgba(27, 90, 141, 0.1);
}

.pwa-input.is-invalid,
.pwa-textarea.is-invalid,
.pwa-select.is-invalid {
    border-color: var(--error-500, #ef4444);
}

.pwa-textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
}

.pwa-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

.pwa-help-text {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: var(--secondary-500, #6b7280);
}

.pwa-error {
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: var(--error-600, #dc2626);
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* Upload Zone */
.pwa-upload-zone {
    position: relative;
    border: 2px dashed var(--secondary-300, #d1d5db);
    border-radius: 12px;
    padding: 2rem 1rem;
    text-align: center;
    transition: all 0.2s;
    background: var(--secondary-50, #f8fafc);
    cursor: pointer;
}

.pwa-upload-zone:active,
.pwa-upload-zone.dragover {
    border-color: var(--primary-500, #1b5a8d);
    background: rgba(27, 90, 141, 0.05);
}

.pwa-file-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}

.pwa-upload-content {
    pointer-events: none;
}

.pwa-upload-icon {
    width: 56px;
    height: 56px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: var(--primary-500, #1b5a8d);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.pwa-upload-title {
    font-weight: 600;
    color: var(--secondary-800, #1f2937);
    margin: 0 0 0.25rem 0;
    font-size: 0.95rem;
}

.pwa-upload-subtitle {
    font-size: 0.875rem;
    color: var(--secondary-600, #6b7280);
    margin: 0 0 0.5rem 0;
}

.pwa-upload-hint {
    font-size: 0.75rem;
    color: var(--secondary-500, #6b7280);
    margin: 0;
}

/* Attachments List */
.pwa-attachments-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
}

.pwa-attachment-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem;
    background: var(--secondary-50, #f8fafc);
    border-radius: 10px;
    border: 1px solid var(--secondary-200, #e5e7eb);
}

.pwa-attachment-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-500, #1b5a8d);
    font-size: 1.125rem;
}

.pwa-attachment-info {
    flex: 1;
    min-width: 0;
}

.pwa-attachment-name {
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--secondary-800, #1f2937);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.pwa-attachment-size {
    font-size: 0.75rem;
    color: var(--secondary-500, #6b7280);
}

.pwa-attachment-remove {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: none;
    color: var(--error-500, #ef4444);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    flex-shrink: 0;
}

.pwa-attachment-remove:active {
    background: var(--error-50, #fef2f2);
    transform: scale(0.95);
}

/* Info Card */
.pwa-info-card {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 1px solid #93c5fd;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    color: #1e40af;
}

.pwa-info-card i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.pwa-info-card strong {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.pwa-info-card p {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.5;
    opacity: 0.9;
}

/* Remplacer la section .pwa-submit-bar et les media queries existantes par ceci */

.pwa-submit-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 1rem 1.25rem;
    padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px));
    border-top: 1px solid var(--secondary-200, #e5e7eb);
    display: flex;
    gap: 0.75rem;
    z-index: 1000;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
}

/* CORRECTION : Remonter les boutons au-dessus du bottom nav */
@media (max-width: 991px) {
    .pwa-submit-bar {
        bottom: 70px; /* Augmenté de 56px à 70px pour remonter */
        bottom: calc(70px + env(safe-area-inset-bottom, 0px));
        padding-bottom: 0.75rem;
        padding-top: 0.75rem;
    }

    /* Ajuster l'espace en bas du contenu pour éviter la troncature */
    .pwa-create-container {
        padding-bottom: calc(200px + env(safe-area-inset-bottom, 0px));
    }

    /* Réduire légèrement la taille des boutons sur mobile pour plus d'espace */
    .pwa-btn-primary,
    .pwa-btn-secondary {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
}

/* Alternative : Si vous préférez que les boutons ne soient pas collés au bottom nav */
@media (max-width: 991px) {
    /* Option 2 : Position flottante avec marge */
    .pwa-submit-bar {
        position: fixed;
        bottom: 80px; /* Position plus haute */
        left: 1rem;
        right: 1rem;
        border-radius: 12px;
        border: 1px solid var(--secondary-200);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(8px);
    }

    /* Ajustement spécifique pour très petits écrans */
    @media (max-height: 700px) {
        .pwa-submit-bar {
            bottom: 65px;
        }
    }
}

.pwa-btn-primary,
.pwa-btn-secondary {
    flex: 1;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.pwa-btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.3);
}

.pwa-btn-primary:active {
    transform: scale(0.98);
    box-shadow: 0 2px 8px rgba(27, 90, 141, 0.2);
}

.pwa-btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.pwa-btn-secondary {
    background: var(--secondary-100, #f3f4f6);
    color: var(--secondary-700, #374151);
    border: 1px solid var(--secondary-200, #e5e7eb);
}

.pwa-btn-secondary:active {
    background: var(--secondary-200, #e5e7eb);
}

/* Desktop : pas de bottom nav, barre en bas normale */
@media (min-width: 992px) {
    .pwa-create-container {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 2rem; /* Reset sur desktop */
    }

    .pwa-submit-bar {
        position: relative; /* Plus fixe sur desktop */
        margin-top: 2rem;
        border-top: none;
        box-shadow: none;
        background: transparent;
        padding: 0;
    }
}

@media (max-width: 380px) {
    .pwa-form-row {
        flex-direction: column;
        gap: 0;
    }

    .pwa-form-row .pwa-form-group {
        margin-bottom: 1.25rem;
    }
}
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachmentsInput = document.getElementById('attachments');
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const form = document.getElementById('ticketForm');
    const submitBtn = document.getElementById('submitBtn');
    const dropZone = document.getElementById('dropZone');

    // Upload
    attachmentsInput.addEventListener('change', updatePreview);

    // Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        attachmentsInput.files = e.dataTransfer.files;
        updatePreview();
    });

    function updatePreview() {
        attachmentsPreview.innerHTML = '';

        if (attachmentsInput.files.length > 0) {
            Array.from(attachmentsInput.files).forEach((file, index) => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`"${file.name}" dépasse 2MB`);
                    return;
                }

                const item = document.createElement('div');
                item.className = 'pwa-attachment-item';
                item.innerHTML = `
                    <div class="pwa-attachment-icon">
                        <i class="${getFileIcon(file.type)}"></i>
                    </div>
                    <div class="pwa-attachment-info">
                        <span class="pwa-attachment-name">${file.name}</span>
                        <span class="pwa-attachment-size">${formatBytes(file.size)}</span>
                    </div>
                    <button type="button" class="pwa-attachment-remove" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                attachmentsPreview.appendChild(item);
            });
        }
    }

    window.removeFile = function(index) {
        const dt = new DataTransfer();
        Array.from(attachmentsInput.files).forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        attachmentsInput.files = dt.files;
        updatePreview();
    };

    function getFileIcon(mime) {
        if (mime.includes('image')) return 'fas fa-file-image';
        if (mime.includes('pdf')) return 'fas fa-file-pdf';
        if (mime.includes('word')) return 'fas fa-file-word';
        return 'fas fa-file';
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + ['Bytes','KB','MB'][i];
    }

    // Soumission
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Envoi...';
    });
});
</script>
@endpush
@endsection
