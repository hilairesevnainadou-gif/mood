@extends('layouts.client')

@section('title', 'Téléverser un document')

@section('content')

<script>
window.UploadManager = {
    fileSelected: false,

    removeFile: function() {
        document.getElementById('document').value = '';
        document.getElementById('uploadPlaceholder').style.display = 'block';
        document.getElementById('filePreview').style.display = 'none';
        this.fileSelected = false;
        this.updateSubmitButton();
    },

    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },

    getFileIcon: function(mimeType) {
        if (mimeType.startsWith('image/')) return 'fas fa-file-image';
        if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
        if (mimeType.includes('word')) return 'fas fa-file-word';
        if (mimeType.includes('excel') || mimeType.includes('sheet')) return 'fas fa-file-excel';
        if (mimeType.includes('powerpoint')) return 'fas fa-file-powerpoint';
        return 'fas fa-file';
    },

    updateFilePreview: function(file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = this.formatFileSize(file.size);
        document.getElementById('fileIcon').innerHTML = '<i class="' + this.getFileIcon(file.type) + '"></i>';
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('filePreview').style.display = 'flex';
        this.fileSelected = true;
        this.updateSubmitButton();
    },

    updateSubmitButton: function() {
        const btn = document.getElementById('submitBtn');
        if (this.fileSelected) {
            btn.style.opacity = '1';
            btn.disabled = false;
        } else {
            btn.style.opacity = '0.6';
            btn.disabled = true;
        }
    },

  submitForm: function(e) {
        e.preventDefault();

        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitBtn');
        const progressDiv = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressPercent = document.getElementById('progressPercent');

        // Reset erreurs
        document.querySelectorAll('.pwa-error-msg').forEach(el => el.remove());

        // UI Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';
        progressDiv.style.display = 'block';

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressFill.style.width = percentComplete + '%';
                progressPercent.textContent = percentComplete + '%';
            }
        });

        xhr.addEventListener('load', function() {
            let response;

            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                // Erreur HTML (non JSON)
                console.error('Response not JSON:', xhr.responseText.substring(0, 500));
                UploadManager.handleError('Erreur serveur. Veuillez réessayer.');
                return;
            }

            if (xhr.status === 200 && response.success) {
                // Succès
                progressFill.style.width = '100%';
                progressPercent.textContent = '100%';

                if (window.toast) {
                    window.toast.success('Succès', response.message);
                }

                setTimeout(() => {
                    window.location.href = response.redirect || '{{ route('client.documents.index') }}';
                }, 800);

            } else {
                // Erreur validation ou autre
                UploadManager.handleError(response.message || 'Erreur lors de l\'upload');

                // Afficher l'erreur sous le champ file si c'est une erreur de fichier
                if (response.message && response.message.toLowerCase().includes('fichier')) {
                    const uploadZone = document.getElementById('uploadZone');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'pwa-error-msg';
                    errorDiv.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; font-size: 0.875rem; color: var(--error-600);';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i>' + response.message;
                    uploadZone.parentNode.appendChild(errorDiv);
                }
            }
        });

        xhr.addEventListener('error', function() {
            UploadManager.handleError('Erreur réseau. Vérifiez votre connexion.');
        });

        xhr.addEventListener('abort', function() {
            UploadManager.handleError('Upload annulé.');
        });

        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
    },

   handleError: function(message) {
        const submitBtn = document.getElementById('submitBtn');
        const progressDiv = document.getElementById('uploadProgress');

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Réessayer';
        progressDiv.style.display = 'none';
        document.getElementById('progressFill').style.width = '0%';
        document.getElementById('progressPercent').textContent = '0%';

        if (window.toast) {
            window.toast.error('Erreur', message);
        } else {
            alert(message);
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('document');
    const uploadZone = document.getElementById('uploadZone');
    const uploadForm = document.getElementById('uploadForm');

    // Disable submit initially
    UploadManager.updateSubmitButton();

    // Drag & Drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
        uploadZone.style.borderColor = 'var(--primary-500)';
        uploadZone.style.background = 'rgba(59, 130, 246, 0.05)';
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
        uploadZone.style.borderColor = '';
        uploadZone.style.background = '';
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        uploadZone.style.borderColor = '';
        uploadZone.style.background = '';

        if (e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            // Vérifier la taille max côté client
            const maxSize = {{ $requiredDoc->max_size_mb ?? 5 }} * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Fichier trop volumineux. Max: {{ $requiredDoc->max_size_mb ?? 5 }} Mo');
                return;
            }
            fileInput.files = e.dataTransfer.files;
            UploadManager.updateFilePreview(file);
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            UploadManager.updateFilePreview(this.files[0]);
        }
    });

    // Soumission AJAX
    uploadForm.addEventListener('submit', UploadManager.submitForm);
});
</script>

<div class="pwa-upload-viewport">
    {{-- Header cohérent --}}
    <div class="pwa-docs-header" style="margin: -1rem -1rem 1rem -1rem;">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content" style="position: relative; z-index: 2;">
            <button onclick="window.history.back()" class="pwa-back-btn" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); border: none; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-arrow-left"></i>
            </button>

            <div class="pwa-header-text" style="flex: 1;">
                <span style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.1em;">Nouveau document</span>
                <h1 style="font-size: 1.25rem; margin: 0; font-family: 'Rajdhani', sans-serif; font-weight: 700;">{{ Str::limit($requiredDoc->name ?? 'Upload', 25) }}</h1>
            </div>
        </div>
    </div>

    <div class="pwa-upload-content" style="padding: 0 1rem 2rem 1rem; max-width: 800px; margin: 0 auto;">

        @if($requiredDoc)
            @if($existingDocument && $existingDocument->status === 'pending')
            <div class="pwa-doc-card" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fcd34d; border-radius: 14px; padding: 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; color: var(--warning-600); font-size: 1.5rem; flex-shrink: 0;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: var(--secondary-800); margin-bottom: 0.25rem;">Document en attente</div>
                    <div style="font-size: 0.9rem; color: var(--secondary-600);">Vous avez déjà un document en attente de validation.</div>
                    <a href="{{ route('client.documents.index') }}" style="color: var(--primary-600); font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 0.5rem;">
                        Voir mes documents <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </div>
            @endif

            {{-- Card Informations --}}
            <div class="pwa-doc-card" style="background: white; border-radius: 14px; border: 1px solid var(--secondary-200); box-shadow: 0 2px 6px rgba(0,0,0,0.04); margin-bottom: 1rem; overflow: hidden;">
                <div style="padding: 1rem; background: var(--secondary-50); border-bottom: 1px solid var(--secondary-200); display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-info-circle" style="color: var(--primary-500); font-size: 1.25rem;"></i>
                    <span style="font-weight: 700; color: var(--secondary-700);">Informations requises</span>
                </div>

                <div style="padding: 1rem;">
                    @if($requiredDoc->description)
                    <p style="color: var(--secondary-600); font-size: 0.95rem; line-height: 1.5; margin: 0 0 1rem 0;">{{ $requiredDoc->description }}</p>
                    @endif

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                        <div style="text-align: center; padding: 0.75rem; background: var(--secondary-50); border-radius: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: var(--primary-500); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--secondary-500); margin-bottom: 0.25rem;">Formats</div>
                            <div style="font-weight: 700; font-size: 0.8rem; color: var(--secondary-800); word-break: break-word;">
                                @if($requiredDoc->allowed_formats && count($requiredDoc->allowed_formats) > 0)
                                    {{ implode(', ', array_map('strtoupper', $requiredDoc->allowed_formats)) }}
                                @else
                                    Tous
                                @endif
                            </div>
                        </div>

                        <div style="text-align: center; padding: 0.75rem; background: var(--secondary-50); border-radius: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: var(--primary-500); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <i class="fas fa-weight"></i>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--secondary-500); margin-bottom: 0.25rem;">Taille max</div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: var(--secondary-800);">{{ $requiredDoc->max_size_mb }} Mo</div>
                        </div>

                        <div style="text-align: center; padding: 0.75rem; background: var(--secondary-50); border-radius: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; color: var(--primary-500); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--secondary-500); margin-bottom: 0.25rem;">Validité</div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: var(--secondary-800);">
                                @if($requiredDoc->has_expiry_date)
                                    {{ $requiredDoc->validity_days ? $requiredDoc->validity_days . ' j' : 'Oui' }}
                                @else
                                    Permanent
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulaire Upload --}}
            <form action="{{ route('client.documents.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="pwa-doc-card" style="background: white; border-radius: 14px; border: 1px solid var(--secondary-200); box-shadow: 0 2px 6px rgba(0,0,0,0.04); overflow: hidden;">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="name" value="{{ $requiredDoc->name }}">

                <div style="padding: 1.25rem;">
                    {{-- Upload Zone --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 700; color: var(--secondary-700); margin-bottom: 0.75rem;">
                            <i class="fas fa-cloud-upload-alt" style="color: var(--primary-500); margin-right: 0.5rem;"></i>
                            Fichier <span style="color: var(--error-500);">*</span>
                        </label>

                        <div id="uploadZone" style="position: relative; border: 2px dashed var(--secondary-300); border-radius: 16px; background: var(--secondary-50); transition: all 0.2s; overflow: hidden;">
                            <input type="file" name="document" id="document" required
                                   accept="{{ $requiredDoc->allowed_formats ? '.' . implode(',.', $requiredDoc->allowed_formats) : '' }}"
                                   style="position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;">

                            <div id="uploadPlaceholder" style="padding: 2.5rem 1.5rem; text-align: center;">
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); color: var(--primary-500); font-size: 1.5rem;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div style="font-weight: 700; color: var(--secondary-700); margin-bottom: 0.5rem;">Toucher pour sélectionner</div>
                                <div style="font-size: 0.875rem; color: var(--secondary-500); margin-bottom: 1rem;">ou glissez-déposez</div>
                                <div style="display: inline-block; font-size: 0.75rem; color: var(--secondary-500); background: white; padding: 0.375rem 0.875rem; border-radius: 50px; border: 1px solid var(--secondary-200);">
                                    @if($requiredDoc->allowed_formats && count($requiredDoc->allowed_formats) > 0)
                                        {{ implode(', ', array_map('strtoupper', array_slice($requiredDoc->allowed_formats, 0, 3))) }}
                                        @if(count($requiredDoc->allowed_formats) > 3) ... @endif
                                    @else
                                        Tous formats
                                    @endif
                                     • Max {{ $requiredDoc->max_size_mb }} Mo
                                </div>
                            </div>

                            <div id="filePreview" style="display: none; padding: 1.25rem; align-items: center; gap: 1rem; background: white;">
                                <div id="fileIcon" style="width: 56px; height: 56px; border-radius: 12px; background: var(--primary-50); display: flex; align-items: center; justify-content: center; color: var(--primary-500); font-size: 1.5rem; flex-shrink: 0;">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div id="fileName" style="font-weight: 700; color: var(--secondary-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.25rem;">Fichier</div>
                                    <div id="fileSize" style="font-size: 0.875rem; color: var(--secondary-500);">0 KB</div>
                                </div>
                                <button type="button" onclick="UploadManager.removeFile()" style="width: 40px; height: 40px; border-radius: 50%; border: none; background: var(--secondary-100); color: var(--secondary-600); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        @error('document')
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; font-size: 0.875rem; color: var(--error-600);">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: 1.5rem;">
                        <label for="description" style="display: block; font-size: 0.875rem; font-weight: 700; color: var(--secondary-700); margin-bottom: 0.75rem;">
                            <i class="fas fa-align-left" style="color: var(--primary-500); margin-right: 0.5rem;"></i>
                            Description <span style="font-weight: 400; color: var(--secondary-500);">(optionnel)</span>
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  style="width: 100%; padding: 0.875rem; border: 1px solid var(--secondary-300); border-radius: 12px; font-size: 0.95rem; color: var(--secondary-700); resize: vertical; font-family: inherit;"
                                  placeholder="Ajoutez une description...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Date d'expiration --}}
                    @if($requiredDoc->has_expiry_date)
                    <div style="margin-bottom: 1.5rem;">
                        <label for="expiry_date" style="display: block; font-size: 0.875rem; font-weight: 700; color: var(--secondary-700); margin-bottom: 0.75rem;">
                            <i class="fas fa-calendar-alt" style="color: var(--primary-500); margin-right: 0.5rem;"></i>
                            Expire le <span style="color: var(--error-500);">*</span>
                        </label>
                        <input type="date" name="expiry_date" id="expiry_date" required min="{{ date('Y-m-d') }}"
                               value="{{ old('expiry_date') }}"
                               style="width: 100%; padding: 0.875rem; border: 1px solid var(--secondary-300); border-radius: 12px; font-size: 0.95rem; color: var(--secondary-700);">
                        @error('expiry_date')
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; font-size: 0.875rem; color: var(--error-600);">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    @endif

                    {{-- Progress Bar (cachée initialement) --}}
                    <div id="uploadProgress" style="display: none; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; color: var(--secondary-700);">
                            <span><i class="fas fa-circle-notch fa-spin me-2"></i>Téléversement...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                        <div style="height: 8px; background: var(--secondary-200); border-radius: 4px; overflow: hidden;">
                            <div id="progressFill" style="height: 100%; width: 0%; background: linear-gradient(90deg, var(--primary-500), var(--primary-700)); transition: width 0.1s linear;"></div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <button type="button" onclick="window.history.back()" class="btn-cancel-no-style" style="padding: 1rem; border-radius: 12px; border: none; background: var(--secondary-100); color: var(--secondary-700); font-weight: 700; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i class="fas fa-arrow-left"></i>
                            Annuler
                        </button>
                        <button type="submit" id="submitBtn" style="padding: 1rem; border-radius: 12px; border: none; background: linear-gradient(135deg, var(--primary-500), var(--primary-700)); color: white; font-weight: 700; font-size: 0.95rem; cursor: pointer; box-shadow: 0 4px 12px rgba(27, 90, 141, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.5rem; opacity: 0.6;" disabled>
                            <i class="fas fa-upload"></i>
                            Envoyer
                        </button>
                    </div>
                </div>
            </form>

        @else
            {{-- Error State --}}
            <div class="pwa-doc-card" style="background: white; border-radius: 14px; border: 1px solid var(--secondary-200); box-shadow: 0 2px 6px rgba(0,0,0,0.04); padding: 3rem 1.5rem; text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--warning-50); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--warning-500); font-size: 2rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 style="font-family: 'Rajdhani', sans-serif; color: var(--secondary-800); margin: 0 0 0.5rem 0;">Type introuvable</h3>
                <p style="color: var(--secondary-500); margin-bottom: 1.5rem; line-height: 1.5;">Ce type de document n'est pas disponible.</p>
                <a href="{{ route('client.documents.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; background: var(--primary-500); color: white; border-radius: 12px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        @endif
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

