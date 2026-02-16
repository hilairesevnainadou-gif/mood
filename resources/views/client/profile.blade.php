@extends('layouts.client')

@section('title', 'Mon Profil')

@section('content')
<div class="profile-page">
    {{-- Messages de succès --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Messages d'erreur --}}
    @if($errors->any() && !session('success'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header avec photo --}}
    <div class="profile-header-card">
        <div class="profile-photo-section">
            <div class="profile-photo-wrapper" onclick="document.getElementById('photoInput').click()">
                {{-- CORRECTION : Utilisation directe de l'accessor profile_photo_url --}}
                <img id="currentPhoto"
                     src="{{ $user->profile_photo_url }}?v={{ time() }}"
                     alt="Photo de profil"
                     class="profile-photo">
                <div class="profile-photo-overlay">
                    <i class="fas fa-camera"></i><br>Changer
                </div>
            </div>

            <div class="profile-info">
                <h1>{{ $user->full_name }}</h1>
                <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                <p class="member-since">
                    <i class="fas fa-calendar-alt"></i> Membre depuis {{ $user->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Preview nouvelle photo --}}
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

        <input type="file"
               id="photoInput"
               name="photo"
               accept="image/jpeg,image/png,image/jpg,image/webp"
               style="display: none;"
               onchange="handlePhotoUpload(this)">

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
                               class="form-input @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse email *</label>
                        <input type="email"
                               name="email"
                               class="form-input @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
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
                               class="form-input @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="Ex: 06 12 34 56 78">
                        @error('phone')
                            <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID Membre</label>
                        <input type="text" class="form-input" value="{{ $user->member_id ?? 'N/A' }}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text"
                           name="address"
                           class="form-input @error('address') is-invalid @enderror"
                           value="{{ old('address', $user->address) }}"
                           placeholder="Votre adresse complète">
                    @error('address')
                        <span class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Carte Sécurité --}}
        <div class="profile-card">
            <div class="profile-card-header">
                <h2><i class="fas fa-lock text-warning"></i> Sécurité</h2>
            </div>
            <div class="profile-card-body">
                <div class="password-section">
                    <button type="button"
                            id="passwordToggleBtn"
                            class="password-toggle-btn {{ $errors->has('current_password') || $errors->has('new_password') ? 'active' : '' }}"
                            onclick="togglePasswordSection()">
                        <span><i class="fas fa-key"></i> Changer mon mot de passe</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div id="passwordFormContainer" class="password-form-container {{ $errors->has('current_password') || $errors->has('new_password') ? 'open' : '' }}">
                        @error('current_password')
                            <div class="error-message mb-3"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror

                        <div class="form-group">
                            <label class="form-label">Mot de passe actuel *</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-input @error('current_password') is-invalid @enderror"
                                   placeholder="Entrez votre mot de passe actuel">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nouveau mot de passe *</label>
                                <input type="password"
                                       name="new_password"
                                       class="form-input @error('new_password') is-invalid @enderror"
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

                        <p class="password-hint">
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
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </form>
</div>

<style>
/* Styles spécifiques à la page profil - s'intègrent avec votre CSS existant */
.profile-page {
    max-width: 800px;
    margin: 0 auto;
    padding-bottom: 100px;
}

.profile-header-card {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-700, #113a61) 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(27, 90, 141, 0.3);
}

.profile-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
    pointer-events: none;
}

.profile-photo-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.profile-photo-wrapper {
    position: relative;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.4);
    overflow: hidden;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    flex-shrink: 0;
}

.profile-photo-wrapper:hover {
    transform: scale(1.05);
    border-color: white;
}

.profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.profile-photo-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white;
    padding: 1.5rem 0.5rem 0.5rem;
    text-align: center;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.profile-photo-wrapper:hover .profile-photo-overlay {
    opacity: 1;
}

.profile-info h1 {
    font-size: 1.75rem;
    margin: 0 0 0.25rem 0;
    font-family: 'Rajdhani', sans-serif;
    font-weight: 700;
}

.profile-info p {
    margin: 0.25rem 0;
    opacity: 0.9;
    font-size: 0.95rem;
}

.member-since {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-top: 0.5rem;
}

/* Preview photo */
.photo-preview-section {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #86efac;
    border-radius: 16px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    text-align: center;
    display: none;
    position: relative;
    animation: slideIn 0.3s ease;
}

.photo-preview-section.show {
    display: block;
}

.preview-label {
    font-size: 0.875rem;
    color: #166534;
    margin-bottom: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.preview-image-container {
    position: relative;
    display: inline-block;
}

.preview-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.remove-photo-btn {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc2626;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    transition: all 0.2s;
    font-size: 0.875rem;
}

.remove-photo-btn:hover {
    transform: scale(1.1);
    background: #b91c1c;
}

.file-info {
    margin-top: 0.75rem;
    font-size: 0.875rem;
    color: #166534;
    background: rgba(255,255,255,0.8);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    display: inline-block;
    font-weight: 500;
}

/* Cards */
.profile-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--secondary-200, #e5e7eb);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.profile-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--secondary-100, #f3f4f6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #fafafa, #ffffff);
}

.profile-card-header h2 {
    font-size: 1.15rem;
    margin: 0;
    color: var(--secondary-800, #1f2937);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.profile-card-body {
    padding: 1.5rem;
}

/* Formulaires */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--secondary-700, #374151);
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--secondary-200, #e5e7eb);
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s;
    background: white;
    color: var(--secondary-800);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500, #1b5a8d);
    box-shadow: 0 0 0 3px rgba(27, 90, 141, 0.1);
}

.form-input:disabled {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}

.form-input.is-invalid {
    border-color: #dc2626;
    background-color: #fef2f2;
}

.form-input.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

/* Section mot de passe */
.password-section {
    margin-top: 0.5rem;
}

.password-toggle-btn {
    background: white;
    border: 2px dashed var(--secondary-300, #d1d5db);
    padding: 1rem 1.25rem;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    font-weight: 600;
    color: var(--secondary-700, #374151);
    transition: all 0.3s;
    font-size: 0.95rem;
}

.password-toggle-btn:hover {
    border-color: var(--primary-500, #1b5a8d);
    color: var(--primary-600, #1b5a8d);
    background: var(--primary-50, #eff6ff);
}

.password-toggle-btn.active {
    border-style: solid;
    border-color: var(--primary-500, #1b5a8d);
    background: var(--primary-50, #eff6ff);
    color: var(--primary-700, #1d4ed8);
}

.password-toggle-btn i.fa-chevron-down {
    transition: transform 0.3s ease;
}

.password-toggle-btn.active i.fa-chevron-down {
    transform: rotate(180deg);
}

.password-form-container {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s;
    opacity: 0;
    margin-top: 0;
}

.password-form-container.open {
    max-height: 500px;
    opacity: 1;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px dashed var(--secondary-200, #e5e7eb);
}

.password-hint {
    font-size: 0.8rem;
    color: var(--secondary-500);
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Boutons */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--secondary-200, #e5e7eb);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25);
    min-width: 200px;
    text-decoration: none;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(27, 90, 141, 0.35);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: white;
    color: var(--secondary-700, #374151);
    border: 2px solid var(--secondary-300, #d1d5db);
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: 140px;
}

.btn-secondary:hover {
    background: var(--secondary-50, #f9fafb);
    border-color: var(--secondary-400, #9ca3af);
    color: var(--secondary-800);
}

/* Animations */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 640px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-primary, .btn-secondary {
        width: 100%;
    }

    .profile-photo-section {
        flex-direction: column;
        text-align: center;
    }

    .profile-info h1 {
        font-size: 1.5rem;
    }

    .profile-header-card {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .profile-photo-wrapper {
        width: 90px;
        height: 90px;
    }

    .profile-info h1 {
        font-size: 1.25rem;
    }
}
</style>

<script>
// Gestion de l'upload photo
function handlePhotoUpload(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB

    // Validation
    if (!file.type.match('image.*')) {
        if (window.toast) {
            window.toast.error('Format invalide', 'Veuillez sélectionner une image (JPG, PNG, WebP)');
        } else {
            alert('Veuillez sélectionner une image valide');
        }
        input.value = '';
        return;
    }

    if (file.size > maxSize) {
        if (window.toast) {
            window.toast.error('Fichier trop grand', 'La taille maximale est de 2 Mo');
        } else {
            alert('La taille maximale est de 2 Mo');
        }
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

        // Scroll vers la preview
        previewContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };
    reader.readAsDataURL(file);
}

function removePhotoSelection() {
    const input = document.getElementById('photoInput');
    const previewContainer = document.getElementById('newPhotoPreview');

    if (input) input.value = '';
    if (previewContainer) {
        previewContainer.classList.remove('show');
        setTimeout(() => previewContainer.style.display = 'none', 300);
    }
}

function togglePasswordSection() {
    const container = document.getElementById('passwordFormContainer');
    const btn = document.getElementById('passwordToggleBtn');

    if (!container || !btn) return;

    if (container.classList.contains('open')) {
        container.classList.remove('open');
        btn.classList.remove('active');
        // Vider les champs mot de passe quand on ferme
        container.querySelectorAll('input[type="password"]').forEach(input => input.value = '');
    } else {
        container.classList.add('open');
        btn.classList.add('active');
        // Focus après animation
        setTimeout(() => {
            const input = container.querySelector('input[name="current_password"]');
            if (input) input.focus();
        }, 400);
    }
}

// Validation et soumission du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const currentPass = form.querySelector('input[name="current_password"]')?.value || '';
            const newPass = form.querySelector('input[name="new_password"]')?.value || '';
            const confirmPass = form.querySelector('input[name="new_password_confirmation"]')?.value || '';

            // Validation côté client
            if (newPass && !currentPass) {
                e.preventDefault();
                if (window.toast) {
                    window.toast.warning('Mot de passe actuel requis', 'Veuillez saisir votre mot de passe actuel pour le changer');
                } else {
                    alert('Veuillez saisir votre mot de passe actuel pour le changer.');
                }
                togglePasswordSection();
                const input = form.querySelector('input[name="current_password"]');
                if (input) {
                    input.focus();
                    input.classList.add('is-invalid');
                }
                return false;
            }

            if (newPass && newPass !== confirmPass) {
                e.preventDefault();
                if (window.toast) {
                    window.toast.error('Mots de passe différents', 'Les mots de passe ne correspondent pas');
                } else {
                    alert('Les mots de passe ne correspondent pas.');
                }
                const input = form.querySelector('input[name="new_password_confirmation"]');
                if (input) {
                    input.focus();
                    input.classList.add('is-invalid');
                }
                return false;
            }

            if (newPass && newPass.length < 8) {
                e.preventDefault();
                if (window.toast) {
                    window.toast.warning('Mot de passe trop court', 'Le nouveau mot de passe doit contenir au moins 8 caractères');
                } else {
                    alert('Le nouveau mot de passe doit contenir au moins 8 caractères.');
                }
                const input = form.querySelector('input[name="new_password"]');
                if (input) {
                    input.focus();
                    input.classList.add('is-invalid');
                }
                return false;
            }

            // Animation de chargement
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Enregistrement...';
            }
        });
    }

    // Si erreurs de validation mot de passe, garder ouvert et focus
    @if($errors->has('current_password') || $errors->has('new_password'))
        setTimeout(() => {
            const input = document.querySelector('input[name="current_password"]');
            if (input) input.focus();
        }, 500);
    @endif

    // Gestion des classes is-invalid sur input
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>
@endsection