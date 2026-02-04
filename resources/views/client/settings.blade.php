@extends('layouts.client')

@section('title', 'Paramètres')

@section('content')
<div class="pwa-settings-page">
    {{-- Header --}}
    <div class="pwa-page-header">
        <div class="pwa-header-bg"></div>
        <div class="pwa-header-content">
            <a href="{{ Auth::user()->hasUploadedRequiredDocuments() ? route('client.dashboard') : route('client.documents.index') }}"
                class="pwa-back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="pwa-header-text">
                <h1>Paramètres</h1>
                <p>Gérez vos préférences</p>
            </div>
            <div class="pwa-header-icon">
                <i class="fas fa-cog"></i>
            </div>
        </div>
    </div>

    <form action="{{ route('client.settings.update') }}" method="POST" id="settingsForm" class="pwa-settings-form">
        @csrf
        @method('PUT')

        {{-- Section Notifications --}}
        <div class="pwa-settings-section">
            <div class="pwa-section-header">
                <i class="fas fa-bell text-warning"></i>
                <h2>Notifications</h2>
            </div>

            <div class="pwa-settings-card">
                <div class="pwa-setting-item">
                    <div class="pwa-setting-info">
                        <i class="fas fa-envelope text-primary"></i>
                        <div>
                            <h3>Notifications Email</h3>
                            <p>Recevoir les alertes par email</p>
                        </div>
                    </div>
                    <label class="pwa-toggle">
                        <input type="hidden" name="notification_email" value="0">
                        <input type="checkbox" name="notification_email" value="1"
                            {{ $userSettings->notification_email ? 'checked' : '' }}>
                        <span class="pwa-toggle-slider"></span>
                    </label>
                </div>

                <div class="pwa-setting-divider"></div>

                <div class="pwa-setting-item">
                    <div class="pwa-setting-info">
                        <i class="fas fa-sms text-success"></i>
                        <div>
                            <h3>Notifications SMS</h3>
                            <p>Recevoir les alertes par SMS</p>
                        </div>
                    </div>
                    <label class="pwa-toggle">
                        <input type="hidden" name="notification_sms" value="0">
                        <input type="checkbox" name="notification_sms" value="1"
                            {{ $userSettings->notification_sms ? 'checked' : '' }}>
                        <span class="pwa-toggle-slider"></span>
                    </label>
                </div>

                <div class="pwa-setting-divider"></div>

                <div class="pwa-setting-item">
                    <div class="pwa-setting-info">
                        <i class="fas fa-mobile-alt text-info"></i>
                        <div>
                            <h3>Push Notifications</h3>
                            <p>Notifications sur votre appareil</p>
                        </div>
                    </div>
                    <label class="pwa-toggle">
                        <input type="hidden" name="notification_push" value="0">
                        <input type="checkbox" name="notification_push" value="1"
                            {{ $userSettings->notification_push ? 'checked' : '' }}>
                        <span class="pwa-toggle-slider"></span>
                    </label>
                </div>

                <div class="pwa-setting-divider"></div>

                <div class="pwa-setting-item">
                    <div class="pwa-setting-info">
                        <i class="fas fa-newspaper text-secondary"></i>
                        <div>
                            <h3>Newsletter</h3>
                            <p>Recevoir les actualités et offres</p>
                        </div>
                    </div>
                    <label class="pwa-toggle">
                        <input type="hidden" name="newsletter_subscribed" value="0">
                        <input type="checkbox" name="newsletter_subscribed" value="1"
                            {{ $userSettings->newsletter_subscribed ? 'checked' : '' }}>
                        <span class="pwa-toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="pwa-settings-info">
            <i class="fas fa-info-circle"></i>
            <p>Certaines modifications nécessitent une reconnexion pour être appliquées.</p>
        </div>

        {{-- Espace standard pour les boutons fixes --}}
        <div style="height: 120px;"></div>
    </form>

    {{-- Barre de boutons fixe - CORRIGÉE --}}
    <div class="pwa-submit-bar">
        <a href="{{ Auth::user()->hasUploadedRequiredDocuments() ? route('client.dashboard') : route('client.documents.index') }}"
            class="pwa-btn-secondary">Annuler</a>
        <button type="submit" form="settingsForm" class="pwa-btn-primary">
            <i class="fas fa-save"></i>
            <span>Enregistrer</span>
        </button>
    </div>
</div>

<style>
/* ============================================
   PAGE PARAMÈTRES PWA - VERSION CORRIGÉE
   ============================================ */
.pwa-settings-page {
    padding: 0 0 1rem 0;
    max-width: 100%;
    background: var(--secondary-50, #f8fafc);
    min-height: 100vh;
    position: relative;
}

/* Header Mobile */
.pwa-page-header {
    background: linear-gradient(135deg, var(--primary-600, #1b5a8d) 0%, var(--primary-800, #113a61) 100%);
    padding: 1.25rem;
    padding-top: calc(1.25rem + env(safe-area-inset-top, 0px));
    margin: -1rem -1rem 1.25rem -1rem;
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

/* Formulaire */
.pwa-settings-form {
    padding: 0 1rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Sections avec animation */
.pwa-settings-section {
    animation: slideIn 0.4s ease-out forwards;
    opacity: 0;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pwa-section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding: 0 0.5rem;
}

.pwa-section-header i {
    font-size: 1.25rem;
}

.pwa-section-header h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--secondary-800, #1f2937);
    margin: 0;
}

/* Cards */
.pwa-settings-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--secondary-200, #e5e7eb);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
    padding: 0.5rem 0;
}

/* Items */
.pwa-setting-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    gap: 1rem;
    transition: transform 0.15s ease;
}

.pwa-setting-info {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    flex: 1;
    min-width: 0;
}

.pwa-setting-info > i {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    background: var(--secondary-100, #f3f4f6);
    flex-shrink: 0;
}

.pwa-setting-info h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--secondary-800, #1f2937);
    margin: 0 0 0.2rem 0;
}

.pwa-setting-info p {
    font-size: 0.78rem;
    color: var(--secondary-500, #6b7280);
    margin: 0;
}

.pwa-setting-divider {
    height: 1px;
    background: var(--secondary-100, #f3f4f6);
    margin: 0 1.25rem;
}

/* Toggle Switch iOS Style */
.pwa-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
    flex-shrink: 0;
}

.pwa-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.pwa-toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: var(--secondary-300, #d1d5db);
    transition: .3s;
    border-radius: 34px;
}

.pwa-toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.pwa-toggle input:checked + .pwa-toggle-slider {
    background-color: var(--primary-500, #1b5a8d);
}

.pwa-toggle input:checked + .pwa-toggle-slider:before {
    transform: translateX(22px);
}

.pwa-toggle input:focus + .pwa-toggle-slider {
    box-shadow: 0 0 0 2px rgba(27, 90, 141, 0.2);
}

/* Info Box */
.pwa-settings-info {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.875rem;
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    color: #1e40af;
    margin: 0 0.25rem;
    animation: slideIn 0.4s ease-out 0.1s forwards;
    opacity: 0;
}

.pwa-settings-info i {
    font-size: 1.125rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.pwa-settings-info p {
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.4;
}

/* ============================================
   BARRE DE BOUTONS FIXE - CORRECTION Z-INDEX
   ============================================ */
.pwa-submit-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    /* Z-INDEX ÉLEVÉ pour être au-dessus de app-bottom-nav (z-index: 1000) */
    z-index: 1100 !important;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 0.75rem 1rem;
    padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));
    border-top: 1px solid var(--secondary-200, #e5e7eb);
    display: flex;
    gap: 0.75rem;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    /* Bordures arrondies en haut pour l'esthétique */
    border-radius: 20px 20px 0 0;
    /* Marges latérales pour ne pas coller aux bords */
    margin: 0 0.5rem;
    box-sizing: border-box;
}

/* MOBILE : Positionnement au-dessus de la navbar bottom */
@media (max-width: 991px) {
    .pwa-submit-bar {
        /* 56px = hauteur de app-bottom-nav + 8px d'espace */
        bottom: 64px;
        bottom: calc(56px + env(safe-area-inset-bottom, 0px) + 8px);
        margin: 0 0.75rem;
    }

    /* Espace de sécurité pour le contenu scrollable */
    .pwa-settings-page {
        padding-bottom: calc(56px + 140px) !important;
    }
}

/* Très petits écrans */
@media (max-width: 480px) {
    .pwa-submit-bar {
        bottom: calc(56px + env(safe-area-inset-bottom, 0px) + 12px);
        padding: 0.625rem 0.875rem;
        margin: 0 0.5rem;
        gap: 0.5rem;
    }

    .pwa-btn-primary,
    .pwa-btn-secondary {
        height: 44px;
        font-size: 0.9rem;
    }
}

/* Boutons */
.pwa-btn-primary,
.pwa-btn-secondary {
    flex: 1;
    padding: 0.75rem 1rem;
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
    height: 48px;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.pwa-btn-primary {
    background: linear-gradient(135deg, var(--primary-500, #1b5a8d) 0%, var(--primary-600, #164a77) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(27, 90, 141, 0.25);
}

.pwa-btn-primary:active {
    transform: scale(0.96) translateY(1px);
    box-shadow: 0 2px 8px rgba(27, 90, 141, 0.2);
}

.pwa-btn-secondary {
    background: var(--secondary-100, #f3f4f6);
    color: var(--secondary-700, #374151);
    border: 1.5px solid var(--secondary-200, #e5e7eb);
}

.pwa-btn-secondary:active {
    background: var(--secondary-200, #e5e7eb);
    transform: scale(0.96) translateY(1px);
}

/* ============================================
   DESKTOP RESPONSIVE
   ============================================ */
@media (min-width: 992px) {
    .pwa-settings-page {
        max-width: 600px;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    .pwa-submit-bar {
        position: relative;
        bottom: auto;
        left: auto;
        right: auto;
        margin-top: 2rem;
        border-top: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        background: white;
        backdrop-filter: none;
        padding: 1rem;
        border-radius: 16px;
        z-index: auto;
        margin: 2rem 1rem 0 1rem;
        justify-content: flex-end;
    }

    .pwa-btn-primary,
    .pwa-btn-secondary {
        flex: 0 0 auto;
        min-width: 140px;
        height: 44px;
    }

    .pwa-btn-primary {
        order: 2;
    }

    .pwa-btn-secondary {
        order: 1;
        background: white;
    }

    /* Supprimer l'espace réservé sur desktop */
    .pwa-settings-form > div:last-of-type[style*="height"] {
        display: none;
    }
}

/* ============================================
   SMALL MOBILE OPTIMIZATIONS
   ============================================ */
@media (max-width: 375px) {
    .pwa-setting-item {
        padding: 0.875rem 1rem;
    }

    .pwa-setting-info > i {
        width: 34px;
        height: 34px;
        font-size: 0.9rem;
    }

    .pwa-setting-info h3 {
        font-size: 0.85rem;
    }

    .pwa-setting-info p {
        font-size: 0.7rem;
    }

    .pwa-btn-primary,
    .pwa-btn-secondary {
        font-size: 0.85rem;
        height: 42px;
        padding: 0.625rem;
    }

    .pwa-btn-primary i,
    .pwa-btn-secondary i {
        font-size: 0.9rem;
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .pwa-settings-page {
        background: #1a1a1a;
    }

    .pwa-settings-card,
    .pwa-submit-bar {
        background: #2d2d2d;
        border-color: #404040;
    }

    .pwa-section-header h2,
    .pwa-setting-info h3 {
        color: #e0e0e0;
    }

    .pwa-setting-info p {
        color: #888;
    }

    .pwa-setting-divider {
        background: #404040;
    }

    .pwa-btn-secondary {
        background: #3a3a3a;
        border-color: #404040;
        color: #e0e0e0;
    }

    .pwa-submit-bar {
        background: rgba(45, 45, 45, 0.98);
        border-color: #404040;
    }

    @media (min-width: 992px) {
        .pwa-btn-secondary {
            background: #2d2d2d;
        }

        .pwa-submit-bar {
            background: #2d2d2d;
        }
    }
}

/* Animation sections stagger */
.pwa-settings-section:nth-child(1) { animation-delay: 0.05s; }
.pwa-settings-section:nth-child(2) { animation-delay: 0.1s; }
.pwa-settings-info { animation-delay: 0.15s; }
</style>

@push('scripts')
<script>
// ============================================
// FONCTION GLOBALE MANQUANTE - Correction erreur profile:580
// ============================================
window.togglePasswordForm = function(formId) {
    const form = document.getElementById(formId || 'passwordChangeForm');
    if (form) {
        // Toggle classe d-none (Bootstrap) ou style display
        if (form.classList.contains('d-none')) {
            form.classList.remove('d-none');
            form.style.display = 'block';
            // Smooth scroll vers le formulaire
            setTimeout(() => {
                form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        } else {
            form.classList.add('d-none');
            form.style.display = 'none';
        }
    } else {
        console.warn('Formulaire non trouvé:', formId);
    }
};

// ============================================
// Gestion du formulaire
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    const submitBtn = document.querySelector('.pwa-btn-primary');
    const originalBtnContent = submitBtn.innerHTML;

    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Enregistrement...</span>';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.8';
    });

    // Feedback haptique visuel sur les toggles
    const toggles = document.querySelectorAll('.pwa-toggle input[type="checkbox"]');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const item = this.closest('.pwa-setting-item');
            item.style.transform = 'scale(0.98)';
            setTimeout(() => {
                item.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Vérifier la visibilité de la barre de boutons
    function checkSubmitBar() {
        const submitBar = document.querySelector('.pwa-submit-bar');
        const bottomNav = document.getElementById('bottomNav');

        if (window.innerWidth <= 991 && bottomNav) {
            // S'assurer que la barre est visible au-dessus du menu
            submitBar.style.display = 'flex';
        }
    }

    checkSubmitBar();
    window.addEventListener('resize', checkSubmitBar);
});
@endpush
@endsection
