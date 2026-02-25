@extends('admin.layouts.app')

@section('title', 'Nouvel Administrateur')
@section('page-title', 'Créer un administrateur')
@section('page-subtitle', 'Ajouter un nouveau compte administrateur')

@section('content')
    <div class="data-card" data-aos="fade-up">
        <div class="card-header">
            <div class="header-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="header-content">
                <h3>Informations de l'administrateur</h3>
                <p>Remplissez les informations pour créer un nouveau compte administrateur</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store-admin') }}" class="admin-form">
            @csrf
            
            <div class="form-grid">
                <!-- Nom et Prénom -->
                <div class="form-group">
                    <label for="first_name" class="form-label">
                        <i class="fa-solid fa-user"></i>
                        Prénom <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}"
                           required>
                    @error('first_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">
                        <i class="fa-solid fa-user"></i>
                        Nom <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}"
                           required>
                    @error('last_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email et Téléphone -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fa-solid fa-envelope"></i>
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">
                        <i class="fa-solid fa-phone"></i>
                        Téléphone
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fa-solid fa-lock"></i>
                        Mot de passe <span class="required">*</span>
                    </label>
                    <div class="password-input">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('password')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text">Minimum 8 caractères, avec majuscules, minuscules, chiffres et symboles</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fa-solid fa-lock"></i>
                        Confirmer le mot de passe <span class="required">*</span>
                    </label>
                    <div class="password-input">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-control"
                               required>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('password_confirmation')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Genre -->
                <div class="form-group">
                    <label for="gender" class="form-label">
                        <i class="fa-solid fa-venus-mars"></i>
                        Genre
                    </label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="">Sélectionner...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Homme</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femme</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <!-- Poste -->
                <div class="form-group">
                    <label for="job_title" class="form-label">
                        <i class="fa-solid fa-briefcase"></i>
                        Poste / Fonction
                    </label>
                    <input type="text" 
                           id="job_title" 
                           name="job_title" 
                           class="form-control"
                           value="{{ old('job_title', 'Administrateur') }}">
                </div>

                <!-- Adresse -->
                <div class="form-group full-width">
                    <label for="address" class="form-label">
                        <i class="fa-solid fa-location-dot"></i>
                        Adresse
                    </label>
                    <input type="text" 
                           id="address" 
                           name="address" 
                           class="form-control"
                           value="{{ old('address') }}">
                </div>

                <div class="form-group">
                    <label for="city" class="form-label">
                        <i class="fa-solid fa-city"></i>
                        Ville
                    </label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           class="form-control"
                           value="{{ old('city') }}">
                </div>

                <div class="form-group">
                    <label for="postal_code" class="form-label">
                        <i class="fa-solid fa-map-pin"></i>
                        Code postal
                    </label>
                    <input type="text" 
                           id="postal_code" 
                           name="postal_code" 
                           class="form-control"
                           value="{{ old('postal_code') }}">
                </div>

                <!-- Super Admin -->
                <div class="form-group full-width">
                    <label class="form-check-label">
                        <input type="checkbox" 
                               name="is_super_admin" 
                               value="1"
                               {{ old('is_super_admin') ? 'checked' : '' }}
                               class="form-check-input">
                        <span class="checkmark"></span>
                        <span class="label-text">
                            <i class="fa-solid fa-crown text-warning"></i>
                            Accès Super Administrateur
                        </span>
                        <small class="help-text">Donne tous les privilèges sur la plateforme</small>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fa-solid fa-user-plus"></i>
                    Créer l'administrateur
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .card-header {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        padding: 24px;
        border-bottom: 1px solid var(--color-gray-200);
        margin-bottom: 0;
    }

    .header-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--color-warning), #d97706);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .header-content h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-gray-900);
        margin: 0 0 4px 0;
    }

    .header-content p {
        color: var(--color-gray-500);
        margin: 0;
        font-size: 0.95rem;
    }

    .admin-form {
        padding: 32px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--color-gray-700);
    }

    .form-label i {
        color: var(--color-gray-400);
        width: 16px;
    }

    .required {
        color: var(--color-danger);
    }

    .form-control {
        padding: 12px 16px;
        border: 2px solid var(--color-gray-200);
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: var(--color-gray-50);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--color-danger);
        background: #fef2f2;
    }

    .invalid-feedback {
        color: var(--color-danger);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-text {
        font-size: 0.8rem;
        color: var(--color-gray-500);
    }

    .password-input {
        position: relative;
    }

    .password-input .form-control {
        padding-right: 48px;
    }

    .btn-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--color-gray-400);
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-toggle-password:hover {
        color: var(--color-gray-600);
        background: var(--color-gray-100);
    }

    .form-check-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--color-gray-50);
        border-radius: 12px;
        border: 2px solid var(--color-gray-200);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-check-label:hover {
        border-color: var(--color-gray-300);
    }

    .form-check-input {
        display: none;
    }

    .checkmark {
        width: 24px;
        height: 24px;
        border: 2px solid var(--color-gray-300);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .form-check-input:checked + .checkmark {
        background: var(--color-warning);
        border-color: var(--color-warning);
    }

    .form-check-input:checked + .checkmark::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        color: white;
        font-size: 0.875rem;
    }

    .label-text {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--color-gray-800);
    }

    .help-text {
        display: block;
        font-size: 0.8rem;
        color: var(--color-gray-500);
        font-weight: 400;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 16px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--color-gray-200);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .btn-secondary {
        background: var(--color-gray-100);
        color: var(--color-gray-700);
        border: 2px solid var(--color-gray-200);
    }

    .btn-secondary:hover {
        background: var(--color-gray-200);
        border-color: var(--color-gray-300);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--color-primary), #2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            flex-direction: column;
            text-align: center;
        }
        
        .form-actions {
            flex-direction: column-reverse;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush