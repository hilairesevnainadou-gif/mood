@extends('layouts.client')

@section('title', 'Mon Profil')

@section('styles')
<style>
.profile-page { padding: 1.5rem 0; max-width: 800px; margin: 0 auto; padding-bottom: 120px; }
.profile-header-card {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-700, #113a61) 100%);
    border-radius: 20px; padding: 2rem; color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden;
    box-shadow: 0 10px 25px rgba(27, 90, 141, 0.3);
}
.profile-photo-section { display: flex; align-items: center; gap: 1.5rem; position: relative; z-index: 1; }
.profile-photo-wrapper {
    position: relative; width: 110px; height: 110px; border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.4); overflow: hidden; background: white;
    cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    flex-shrink: 0;
}
.profile-photo-wrapper:hover { transform: scale(1.05); border-color: white; }
.profile-photo { width: 100%; height: 100%; object-fit: cover; display: block; }
.profile-photo-overlay {
    position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white; padding: 1.5rem 0.5rem 0.5rem; text-align: center; font-size: 0.75rem; opacity: 0;
    transition: opacity 0.3s; pointer-events: none;
}
.profile-photo-wrapper:hover .profile-photo-overlay { opacity: 1; }
.profile-info h1 { font-size: 1.75rem; margin: 0 0 0.25rem 0; font-family: 'Rajdhani', sans-serif; font-weight: 700; }
.profile-info p { margin: 0.25rem 0; opacity: 0.9; font-size: 0.95rem; }

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 1px solid #059669; color: #065f46; padding: 1rem 1.25rem; border-radius: 12px;
    margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 500;
}

.profile-card {
    background: white; border-radius: 16px; border: 1px solid var(--secondary-200, #e5e7eb);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; overflow: hidden;
}
.profile-card-header {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--secondary-100, #f3f4f6);
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(to right, #fafafa, #ffffff);
}
.profile-card-header h2 { font-size: 1.15rem; margin: 0; color: var(--secondary-800, #1f2937); display: flex; align-items: center; gap: 0.5rem; font-weight: 600; }
.profile-card-body { padding: 1.5rem; }

.form-group { margin-bottom: 1.5rem; }
.form-group:last-child { margin-bottom: 0; }
.form-label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--secondary-700, #374151); margin-bottom: 0.5rem; }
.form-input {
    width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--secondary-200, #e5e7eb);
    border-radius: 10px; font-size: 0.95rem; transition: all 0.2s; background: white; color: var(--secondary-800);
}
.form-input:focus { outline: none; border-color: var(--primary-500, #1b5a8d); box-shadow: 0 0 0 3px rgba(27, 90, 141, 0.1); }
.form-input:disabled { background: #f3f4f6; color: #6b7280; cursor: not-allowed; }
.error-message { color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.25rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.password-section { margin-top: 0.5rem; }
.password-toggle-btn {
    background: white; border: 2px dashed var(--secondary-300, #d1d5db);
    padding: 1rem 1.25rem; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: space-between;
    width: 100%; font-weight: 600; color: var(--secondary-700, #374151); transition: all 0.3s;
}
.password-toggle-btn:hover { border-color: var(--primary-500, #1b5a8d); color: var(--primary-600, #1b5a8d); background: var(--primary-50, #eff6ff); }
.password-toggle-btn.active { border-style: solid; border-color: var(--primary-500, #1b5a8d); background: var(--primary-50, #eff6ff); color: var(--primary-700, #1d4ed8); }
.password-toggle-btn i.fa-chevron-down { transition: transform 0.3s ease; }
.password-toggle-btn.active i.fa-chevron-down { transform: rotate(180deg); }

.password-form-container {
    max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s;
    opacity: 0; margin-top: 0;
}
.password-form-container.open {
    max-height: 500px; opacity: 1; margin-top: 1.5rem; padding-top: 1.5rem;
    border-top: 2px dashed var(--secondary-200, #e5e7eb);
}

.photo-preview-section {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac;
    border-radius: 16px; padding: 1.5rem; margin: 1.5rem 0; text-align: center; display: none; position: relative;
}
.photo-preview-section.show { display: block; animation: slideIn 0.3s ease; }
.preview-label { font-size: 0.875rem; color: #166534; margin-bottom: 0.75rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.preview-image-container { position: relative; display: inline-block; }
.preview-image { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
.remove-photo-btn {
    position: absolute; top: -5px; right: -5px; background: #dc2626; color: white;
    border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.2s; font-size: 0.875rem;
}
.remove-photo-btn:hover { transform: scale(1.1); background: #b91c1c; }
.file-info { margin-top: 0.75rem; font-size: 0.875rem; color: #166534; background: rgba(255,255,255,0.8); padding: 0.5rem 1rem; border-radius: 20px; display: inline-block; font-weight: 500; }

.btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white; border: none; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem;
    cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25); min-width: 200px;
}
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(27, 90, 141, 0.35); }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
.btn-secondary {
    background: white; color: var(--secondary-700, #374151); border: 2px solid var(--secondary-300, #d1d5db);
    padding: 1rem 2rem; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer;
    transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; min-width: 140px;
}
.btn-secondary:hover { background: var(--secondary-50, #f9fafb); border-color: var(--secondary-400, #9ca3af); color: var(--secondary-800); }
.form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--secondary-200, #e5e7eb); }

.text-primary { color: var(--primary-500, #1b5a8d); }
.text-warning { color: #f59e0b; }
.text-success { color: #10b981; }

@keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 640px) {
    .form-row { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column-reverse; }
    .btn-primary, .btn-secondary { width: 100%; }
    .profile-photo-section { flex-direction: column; text-align: center; }
    .profile-info h1 { font-size: 1.5rem; }
}
</style>
@endsection

@section('content')
<div class="profile-page">
    {{-- Messages de succès --}}
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header avec photo --}}
    <div class="profile-header-card">
        <div class="profile-photo-section">
            <div class="profile-photo-wrapper" onclick="document.getElementById('photoInput').click()">
                <img id="currentPhoto"
                             src="{{ $user->profile_photo ? asset('storage/profiles/' . $user->profile_photo) . '?v=' . time() : asset('images/default-avatar.png') }}"
                     alt="Photo de profil"
                     class="profile-photo">
                <div class="profile-photo-overlay">
                    <i class="fas fa-camera"></i><br>Changer
                </div>
            </div>

            <div class="profile-info">
                <h1>{{ Auth::user()->name }}</h1>
                <p><i class="fas fa-envelope"></i> {{ Auth::user()->email }}</p>
                <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.5rem;">
                    <i class="fas fa-calendar-alt"></i> Membre depuis {{ Auth::user()->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Preview nouvelle photo (caché par défaut) --}}
    <div id="newPhotoPreview" class="photo-preview-section">
        <div class="preview-label"><i class="fas fa-eye"></i> Nouvelle photo sélectionnée</div>
        <div class="preview-image-container">
            <img id="photoPreview" src="" alt="Preview" class="preview-image">
            <button type="button" class="remove-photo-btn" onclick="removePhotoSelection()" title="Annuler la sélection">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="file-info">
            <span id="fileName"></span> (<span id="fileSize"></span>)
        </div>
    </div>

    {{-- Formulaire principal --}}
    <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('PUT')

        {{-- Input file caché --}}
        <input type="file"
               id="photoInput"
               name="photo"
               accept="image/jpeg,image/png,image/jpg,image/webp"
               style="display: none;">

        {{-- Carte Informations --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h2><i class="fas fa-user-circle text-primary"></i> Informations personnelles</h2>
            </div>
            <div class="profile-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom complet *</label>
                        <input type="text"
                               name="name"
                               class="form-input"
                               value="{{ old('name', Auth::user()->name) }}"
                               required>
                        @error('name')
                            <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse email *</label>
                        <input type="email"
                               name="email"
                               class="form-input"
                               value="{{ old('email', Auth::user()->email) }}"
                               required>
                        @error('email')
                            <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel"
                               name="phone"
                               class="form-input"
                               value="{{ old('phone', Auth::user()->phone) }}"
                               placeholder="Ex: 06 12 34 56 78">
                        @error('phone')
                            <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID Membre</label>
                        <input type="text"
                               class="form-input"
                               value="{{ Auth::user()->member_id ?? 'N/A' }}"
                               disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text"
                           name="address"
                           class="form-input"
                           value="{{ old('address', Auth::user()->address) }}"
                           placeholder="Votre adresse complète">
                    @error('address')
                        <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Carte Sécurité (Mot de passe) --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h2><i class="fas fa-lock text-warning"></i> Sécurité</h2>
            </div>
            <div class="profile-card-body">
                <div class="password-section">
                    <button type="button"
                            id="passwordToggleBtn"
                            class="password-toggle-btn {{ $errors->has('current_password') || $errors->has('new_password') ? 'active' : '' }}"
                            onclick="window.togglePasswordSection()">
                        <span style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-key"></i> Changer mon mot de passe
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    {{-- Formulaire mot de passe (caché par défaut) --}}
                    <div id="passwordFormContainer" class="password-form-container {{ $errors->has('current_password') || $errors->has('new_password') ? 'open' : '' }}">

                        @error('current_password')
                            <div class="error-message" style="margin-bottom: 1rem;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror

                        <div class="form-group">
                            <label class="form-label">Mot de passe actuel *</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-input"
                                   placeholder="Entrez votre mot de passe actuel">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nouveau mot de passe *</label>
                                <input type="password"
                                       name="new_password"
                                       class="form-input"
                                       placeholder="Minimum 8 caractères"
                                       minlength="8">
                                @error('new_password')
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Confirmer le mot de passe *</label>
                                <input type="password"
                                       name="new_password_confirmation"
                                       class="form-input"
                                       placeholder="Répétez le mot de passe">
                            </div>
                        </div>

                        <p style="font-size: 0.8rem; color: var(--secondary-500); margin-top: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-info-circle"></i>
                            Laissez ces champs vides si vous ne souhaitez pas changer votre mot de passe.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <a href="{{ route('client.dashboard') }}" class="btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" class="btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

{{-- SCRIPT CRITIQUE : Doit être chargé immédiatement pour les onclick --}}
<script>
// Fonctions globales définies immédiatement (pas dans DOMContentLoaded)
window.handlePhotoUpload = function(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB

    // Validation
    if (!file.type.match('image.*')) {
        alert('Veuillez sélectionner une image valide (JPG, PNG, WebP)');
        input.value = '';
        return;
    }
    if (file.size > maxSize) {
        alert('La taille maximale est de 2 Mo');
        input.value = '';
        return;
    }

    // Affichage preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImg = document.getElementById('photoPreview');
        const previewContainer = document.getElementById('newPhotoPreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        if (previewImg) previewImg.src = e.target.result;
        if (previewContainer) {
            previewContainer.style.display = 'block';
            setTimeout(() => previewContainer.classList.add('show'), 10);
        }
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = (file.size/1024).toFixed(1) + ' Ko';
    };
    reader.readAsDataURL(file);
};

window.removePhotoSelection = function() {
    const input = document.getElementById('photoInput');
    const previewContainer = document.getElementById('newPhotoPreview');
    if (input) input.value = '';
    if (previewContainer) {
        previewContainer.classList.remove('show');
        setTimeout(() => previewContainer.style.display = 'none', 300);
    }
};

window.togglePasswordSection = function() {
    const container = document.getElementById('passwordFormContainer');
    const btn = document.getElementById('passwordToggleBtn');

    if (!container || !btn) return;

    if (container.classList.contains('open')) {
        container.classList.remove('open');
        btn.classList.remove('active');
    } else {
        container.classList.add('open');
        btn.classList.add('active');
        // Focus après animation
        setTimeout(() => {
            const input = container.querySelector('input[name="current_password"]');
            if (input) input.focus();
        }, 400);
    }
};
</script>
@endsection

@push('scripts')
<script>
// Validation et améliorations une fois le DOM chargé
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const currentPass = form.querySelector('input[name="current_password"]').value;
            const newPass = form.querySelector('input[name="new_password"]').value;
            const confirmPass = form.querySelector('input[name="new_password_confirmation"]').value;

            // Validation côté client
            if (newPass && !currentPass) {
                e.preventDefault();
                alert('Veuillez saisir votre mot de passe actuel pour le changer.');
                window.togglePasswordSection();
                form.querySelector('input[name="current_password"]').focus();
                return false;
            }

            if (newPass && newPass !== confirmPass) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                form.querySelector('input[name="new_password_confirmation"]').focus();
                return false;
            }

            if (newPass && newPass.length < 8) {
                e.preventDefault();
                alert('Le nouveau mot de passe doit contenir au moins 8 caractères.');
                return false;
            }

            // Animation de chargement
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Enregistrement...';
            }
        });
    }

    // Si erreurs de validation mot de passe, garder ouvert
    @if($errors->has('current_password') || $errors->has('new_password'))
        setTimeout(() => {
            const input = document.querySelector('input[name="current_password"]');
            if (input) input.focus();
        }, 500);
    @endif
});
</script>
@endpush
