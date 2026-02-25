@extends('admin.layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('page-subtitle', 'Configuration de la plateforme')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1 fw-bold" style="color: var(--admin-text);">Paramètres</h1>
            <p class="mb-0" style="color: var(--admin-text-muted);">Configurez les options générales de la plateforme</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" 
                    onclick="window.location.reload()"
                    style="border-radius: 10px; padding: 10px 20px; font-weight: 500; border-color: var(--admin-border); color: var(--admin-text-muted);">
                <i class="fas fa-undo"></i>
                <span>Réinitialiser</span>
            </button>
        </div>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" 
             style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--admin-success); border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
             style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--admin-danger); border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: invert(0.5);"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Paramètres généraux --}}
            <div class="col-lg-8">
                <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">
                            <i class="fas fa-cog me-2" style="color: var(--admin-accent);"></i>
                            Paramètres généraux
                        </h5>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Langue --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    <i class="fas fa-language me-2" style="color: var(--admin-accent);"></i>Langue
                                </label>
                                <select name="language" 
                                        class="form-select @error('language') is-invalid @enderror"
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="fr" {{ old('language', $settings->language) == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ old('language', $settings->language) == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language', $settings->language) == 'es' ? 'selected' : '' }}>Español</option>
                                    <option value="de" {{ old('language', $settings->language) == 'de' ? 'selected' : '' }}>Deutsch</option>
                                </select>
                                @error('language')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Fuseau horaire --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    <i class="fas fa-globe me-2" style="color: var(--admin-accent);"></i>Fuseau horaire
                                </label>
                                <select name="timezone" 
                                        class="form-select @error('timezone') is-invalid @enderror"
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="Africa/Abidjan" {{ old('timezone', $settings->timezone) == 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan (GMT)</option>
                                    <option value="Europe/Paris" {{ old('timezone', $settings->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                                    <option value="America/New_York" {{ old('timezone', $settings->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                    <option value="Asia/Tokyo" {{ old('timezone', $settings->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (JST)</option>
                                </select>
                                @error('timezone')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Devise --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    <i class="fas fa-coins me-2" style="color: var(--admin-accent);"></i>Devise
                                </label>
                                <select name="currency" 
                                        class="form-select @error('currency') is-invalid @enderror"
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="XOF" {{ old('currency', $settings->currency) == 'XOF' ? 'selected' : '' }}>XOF - Franc CFA (BCEAO)</option>
                                    <option value="XAF" {{ old('currency', $settings->currency) == 'XAF' ? 'selected' : '' }}>XAF - Franc CFA (BEAC)</option>
                                    <option value="EUR" {{ old('currency', $settings->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="USD" {{ old('currency', $settings->currency) == 'USD' ? 'selected' : '' }}>USD - Dollar US</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Thème --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    <i class="fas fa-palette me-2" style="color: var(--admin-accent);"></i>Thème
                                </label>
                                <select name="theme" 
                                        class="form-select @error('theme') is-invalid @enderror"
                                        style="border-radius: 10px; border-color: var(--admin-border); padding: 12px 16px; font-size: 0.9rem;">
                                    <option value="light" {{ old('theme', $settings->theme) == 'light' ? 'selected' : '' }}>Clair</option>
                                    <option value="dark" {{ old('theme', $settings->theme) == 'dark' ? 'selected' : '' }}>Sombre</option>
                                    <option value="auto" {{ old('theme', $settings->theme) == 'auto' ? 'selected' : '' }}>Automatique</option>
                                </select>
                                @error('theme')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Lignes par page --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-2" style="color: var(--admin-text); font-size: 0.9rem;">
                                    <i class="fas fa-list-ol me-2" style="color: var(--admin-accent);"></i>Lignes par page
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="border-radius: 10px 0 0 10px; border-color: var(--admin-border); background: var(--admin-bg);">
                                        <i class="fas fa-table" style="color: var(--admin-text-muted);"></i>
                                    </span>
                                    <input type="number" 
                                           name="rows_per_page" 
                                           class="form-control @error('rows_per_page') is-invalid @enderror"
                                           value="{{ old('rows_per_page', $settings->rows_per_page) }}"
                                           min="5" 
                                           max="100"
                                           style="border-radius: 0 10px 10px 0; border-color: var(--admin-border); padding: 12px 16px;">
                                </div>
                                <small style="color: var(--admin-text-muted); font-size: 0.8rem;">
                                    Nombre d'éléments affichés par page dans les listes
                                </small>
                                @error('rows_per_page')
                                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="col-lg-4">
                <div class="admin-card" style="padding: 0; overflow: hidden; border-radius: 16px;">
                    <div class="p-4 border-bottom" style="border-color: var(--admin-border) !important;">
                        <h5 class="mb-0 fw-bold" style="color: var(--admin-text);">
                            <i class="fas fa-bell me-2" style="color: var(--admin-accent);"></i>
                            Notifications
                        </h5>
                    </div>
                    
                    <div class="p-4">
                        <div class="d-flex flex-column gap-4">
                            {{-- Email --}}
                            <div class="d-flex align-items-center justify-content-between p-3" 
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-envelope" style="color: var(--admin-accent); font-size: 1.1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.95rem;">Email</div>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">Notifications par email</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="notification_email"
                                           id="notification_email" 
                                           value="1" 
                                           {{ old('notification_email', $settings->notification_email) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>

                            {{-- SMS --}}
                            <div class="d-flex align-items-center justify-content-between p-3" 
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-sms" style="color: var(--admin-success); font-size: 1.1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.95rem;">SMS</div>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">Notifications par SMS</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="notification_sms"
                                           id="notification_sms" 
                                           value="1" 
                                           {{ old('notification_sms', $settings->notification_sms) ? 'checked' : '' }}
                                           style="width: 44px; height: 24px; cursor: pointer;">
                                </div>
                            </div>

                            {{-- Push --}}
                            <div class="d-flex align-items-center justify-content-between p-3" 
                                 style="background: var(--admin-bg); border-radius: 12px; border: 1px solid var(--admin-border);">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-mobile-alt" style="color: var(--admin-warning); font-size: 1.1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--admin-text); font-size: 0.95rem;">Push</div>
                                        <small style="color: var(--admin-text-muted); font-size: 0.8rem;">Notifications push navigateur</small>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="notification_push"
                                           id="notification_push" 
                                           value="1" 
                                           {{ old('notification_push', $settings->notification_push) ? 'checked' : '' }}
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
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
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