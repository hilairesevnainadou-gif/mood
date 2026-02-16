@extends('layouts.client')

@section('title', 'Nouveau ticket - Support')

@section('content')
<div class="content-wrapper">
    {{-- Header avec retour --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('client.support.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour
        </a>
        <h1 class="app-title mb-0">Nouveau ticket</h1>
        <div style="width: 40px;"></div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('client.support.submit') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
        @csrf

        {{-- Section Informations --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    <h5 class="mb-0">Informations</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="subject" class="form-label fw-bold">
                        Sujet <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control @error('subject') is-invalid @enderror"
                           id="subject"
                           name="subject"
                           placeholder="Décrivez brièvement votre problème"
                           value="{{ old('subject') }}"
                           required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="category" class="form-label fw-bold">
                            Catégorie <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('category') is-invalid @enderror"
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="priority" class="form-label fw-bold">
                            Priorité <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('priority') is-invalid @enderror"
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">
                        Description <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="5"
                              placeholder="Expliquez votre problème en détail..." required>{{ old('description') }}</textarea>
                    <div class="form-text">Soyez précis pour une réponse rapide</div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Pièces jointes --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-paperclip text-secondary"></i>
                        <h5 class="mb-0">Pièces jointes</h5>
                    </div>
                    <span class="badge bg-light text-secondary">Optionnel</span>
                </div>
            </div>
            <div class="card-body">
                <div class="upload-zone border border-dashed rounded p-4 text-center bg-light" id="dropZone" style="cursor: pointer;">
                    <input type="file"
                           class="d-none"
                           id="attachments"
                           name="attachments[]"
                           multiple
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <div class="upload-content">
                        <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                        </div>
                        <p class="fw-bold mb-1">Appuyez pour ajouter</p>
                        <p class="text-muted mb-1">ou glissez-déposez</p>
                        <p class="small text-muted">JPG, PNG, PDF, DOC • Max 2MB</p>
                    </div>
                </div>

                <div id="attachmentsPreview" class="mt-3"></div>

                @error('attachments')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Info card --}}
        <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
                <strong>Délai de réponse</strong>
                <p class="mb-0 small">Notre équipe répond sous 24-48h. Pour les urgences, choisissez la priorité "Urgent".</p>
            </div>
        </div>

        {{-- Boutons Submit --}}
        <div class="d-flex gap-3">
            <a href="{{ route('client.support.index') }}" class="btn btn-secondary flex-fill">Annuler</a>
            <button type="submit" class="btn btn-primary flex-fill" id="submitBtn">
                <i class="fas fa-paper-plane me-2"></i> Envoyer
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachmentsInput = document.getElementById('attachments');
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const form = document.getElementById('ticketForm');
    const submitBtn = document.getElementById('submitBtn');
    const dropZone = document.getElementById('dropZone');

    // Upload click
    dropZone.addEventListener('click', () => attachmentsInput.click());

    // Upload change
    attachmentsInput.addEventListener('change', updatePreview);

    // Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary', 'bg-primary-light');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary', 'bg-primary-light');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary', 'bg-primary-light');
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
                item.className = 'd-flex align-items-center gap-3 p-2 bg-light rounded mb-2';
                item.innerHTML = `
                    <div class="bg-white rounded p-2 text-primary">
                        <i class="${getFileIcon(file.type)}"></i>
                    </div>
                    <div class="flex-fill" style="min-width: 0;">
                        <div class="text-truncate fw-bold small">${file.name}</div>
                        <div class="small text-muted">${formatBytes(file.size)}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
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
        if (mime.includes('image')) return 'fas fa-file-image fa-lg';
        if (mime.includes('pdf')) return 'fas fa-file-pdf fa-lg';
        if (mime.includes('word')) return 'fas fa-file-word fa-lg';
        return 'fas fa-file fa-lg';
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
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Envoi...';
    });
});
</script>
@endpush
@endsection
