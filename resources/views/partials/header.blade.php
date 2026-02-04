{{-- HEADER avec Navigation et Bannière intégrée --}}
<header class="header_section">
    <div class="header_container">

        {{-- NAVIGATION --}}
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">

                {{-- LOGO --}}
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="BHDM - Banque Humanitaire du Développement Mondial">
                </a>

                {{-- MENU BURGER MOBILE --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- MENU --}}
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('about') }}">Présentation</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('services') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('services') }}">Services</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>

                    {{-- ESPACE CLIENT --}}
                    <div class="d-none d-lg-block">
                        <div class="more_bt">
                            <a href="{{ route('login') }}" class="btn-link">Espace Client</a>
                        </div>
                    </div>

                    <div class="d-lg-none mt-3">
                        <div class="more_bt">
                            <a href="{{ route('login') }}" class="btn-link">Espace Client</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- HERO BANNER (HOME UNIQUEMENT) --}}
        @if(request()->routeIs('home'))
        <div class="hero_banner">
            <div class="hero_overlay"></div>

            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="banner_content text-center">
                            <h1 class="banner_title">
                                BANQUE HUMANITAIRE POUR LE<br>DÉVELOPPEMENT MONDIAL
                            </h1>

                            <p class="banner_description">
                                Un instrument financier humanitaire dédié à la lutte contre
                                la pauvreté, le chômage et l'exclusion financière.
                            </p>

                            <div class="banner_buttons">
                                <div class="more_bt">
                                    <a href="{{ route('about') }}" class="btn-link">Notre mission</a>
                                </div>
                                <div class="contact_bt">
                                    <a href="{{ route('login') }}" class="btn-link">Espace Client</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</header>

{{-- =========================
   STYLES HEADER COMPLETS
   ========================= --}}
<style>
/* ========== STYLES GÉNÉRAUX DES BOUTONS (IDENTIQUES AUX AUTRES PAGES) ========== */
.btn-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 12px 30px !important;
    border-radius: 40px !important;
    font-weight: bold !important;
    text-transform: uppercase !important;
    font-size: 16px !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    border: 2px solid transparent !important;
    min-width: auto !important;
    width: auto !important;
    white-space: nowrap !important;
    text-align: center !important;
    height: auto !important;
    line-height: 1.5 !important;
    box-sizing: border-box !important;
}

/* Boutons principaux */
.more_bt .btn-link {
    border-color: #ff5a58 !important;
    color: #000 !important;
    background-color: transparent !important;
}

.more_bt .btn-link:hover {
    background-color: #ff5a58 !important;
    color: white !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(255, 90, 88, 0.3) !important;
}

.contact_bt .btn-link {
    border-color: #4aafff !important;
    color: #181818 !important;
    background-color: transparent !important;
}

.contact_bt .btn-link:hover {
    background-color: #4aafff !important;
    color: white !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(74, 175, 255, 0.3) !important;
}

/* Boutons secondaires */
.moremore_bt .btn-link {
    border-color: #88c6f8 !important;
    color: #4aafff !important;
    background-color: transparent !important;
}

.moremore_bt .btn-link:hover {
    border-color: #ff5a58 !important;
    color: #181818 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(255, 90, 88, 0.2) !important;
}

/* Conteneurs de boutons */
.btn_main {
    display: flex !important;
    gap: 30px !important;
    flex-wrap: wrap !important;
    justify-content: center !important;
    align-items: center !important;
}

.more_bt, .contact_bt, .moremore_bt {
    flex: 0 0 auto !important;
    margin: 0 !important;
    padding: 0 !important;
    min-width: 220px !important;
    white-space: nowrap !important;
    display: inline-block !important;
}

/* Alignement parfait des boutons côte à côte */
.more_bt .btn-link,
.contact_bt .btn-link,
.moremore_bt .btn-link {
    height: 50px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    white-space: nowrap !important;
    text-align: center !important;
    padding: 12px 20px !important;
    font-size: 15px !important;
    text-decoration: none !important;
    width: 100% !important;
}

/* Style spécifique pour moremore_bt */
.moremore_bt a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 100% !important;
    width: 100% !important;
}

/* Animation au clic */
@keyframes clickAnimation {
    0% {
        transform: translateY(-2px) scale(1);
    }
    50% {
        transform: translateY(-2px) scale(0.95);
    }
    100% {
        transform: translateY(-2px) scale(1);
    }
}

.btn-link:active {
    animation: clickAnimation 0.2s ease !important;
}

/* ========== STYLES EXISTANTS DU HEADER (AVEC AJUSTEMENTS POUR LES NOUVEAUX BOUTONS) ========== */
.header_section {
    background: #0a1f44;
    color: white;
}

/* NAVBAR */
.navbar {
    padding: 20px 0;
    background: rgba(10, 31, 68, 0.95);
    backdrop-filter: blur(10px);
}

/* LOGO RESPONSIVE */
.logo img {
    height: 85px; /* Desktop */
    width: auto;
    max-width: 100%;
    transition: transform 0.3s ease;
}

.logo img:hover {
    transform: scale(1.05);
}

/* NAV LINKS */
.nav-link {
    color: rgba(255,255,255,0.9) !important;
    padding: 8px 15px;
    border-radius: 25px;
    transition: 0.3s;
}

.nav-link:hover {
    background: rgba(255,255,255,0.15);
    color: #fff !important;
}

.nav-item.active .nav-link {
    background: rgba(255,255,255,0.2);
    font-weight: 600;
}

/* HERO */
.hero_banner {
    min-height: 70vh;
    background: url("{{ asset('images/banner-img22.png') }}") center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
}

.hero_overlay {
    position: absolute;
    inset: 0;
    background: rgba(10,31,68,0.85);
}

.banner_content {
    position: relative;
    z-index: 2;
}

.banner_title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    line-height: 1.2;
}

.banner_description {
    font-size: 1.2rem;
    margin-bottom: 25px;
    line-height: 1.6;
}

.banner_buttons {
    display: flex;
    gap: 30px !important;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

/* Ajustement pour les boutons dans la hero */
.hero_banner .more_bt,
.hero_banner .contact_bt {
    min-width: 220px !important;
    margin: 0 !important;
}

/* Style spécifique pour le bouton Espace Client dans la navbar */
.navbar .more_bt {
    min-width: 180px !important;
}

.navbar .more_bt .btn-link {
    padding: 8px 20px !important;
    font-size: 14px !important;
    height: 40px !important;
    min-width: auto !important;
}

/* Animation pour le header */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar {
    animation: fadeInDown 0.8s ease-out;
}

.hero_banner .banner_content {
    animation: fadeInDown 1s ease-out 0.3s both;
}

/* ========== RESPONSIVE ========== */
/* Tablette */
@media (max-width: 991px) {
    .logo img {
        height: 75px;
    }

    .navbar .more_bt {
        min-width: 200px !important;
    }

    .navbar .more_bt .btn-link {
        padding: 10px 25px !important;
        font-size: 15px !important;
    }

    .banner_title {
        font-size: 2.5rem;
    }

    .banner_description {
        font-size: 1.1rem;
    }

    .hero_banner .more_bt,
    .hero_banner .contact_bt {
        min-width: 200px !important;
    }
}

/* Mobile */
@media (max-width: 575px) {
    .logo img {
        height: 90px;
    }

    .navbar .more_bt {
        min-width: 100% !important;
        margin-top: 15px !important;
    }

    .navbar .more_bt .btn-link {
        width: 100% !important;
        justify-content: center;
    }

    .banner_title {
        font-size: 2rem;
        padding: 0 10px;
    }

    .banner_description {
        font-size: 1rem;
        padding: 0 15px;
    }

    .banner_buttons {
        gap: 15px !important;
        flex-direction: column;
        align-items: center;
        width: 100%;
        padding: 0 20px;
    }

    .hero_banner .more_bt,
    .hero_banner .contact_bt {
        min-width: 100% !important;
        max-width: 300px !important;
        width: 100% !important;
    }

    .hero_banner .more_bt .btn-link,
    .hero_banner .contact_bt .btn-link {
        width: 100% !important;
    }
}

/* Très petits mobiles */
@media (max-width: 375px) {
    .banner_title {
        font-size: 1.8rem;
    }

    .banner_description {
        font-size: 0.95rem;
    }

    .navbar .more_bt .btn-link {
        padding: 8px 15px !important;
        font-size: 13px !important;
    }

    .hero_banner .more_bt,
    .hero_banner .contact_bt {
        min-width: 100% !important;
    }
}
</style>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('Header BHDM chargé avec logo optimisé');

    // Animation au clic pour tous les boutons du header
    const headerButtons = document.querySelectorAll('.header_section .btn-link');

    headerButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Animation de clic
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'clickAnimation 0.2s ease';
            }, 10);

            // Effet de ripple (optionnel)
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.7);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                width: ${size}px;
                height: ${size}px;
                top: ${y}px;
                left: ${x}px;
                z-index: 1;
            `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Animation CSS pour l'effet ripple
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Animation d'apparition des liens du menu
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach((link, index) => {
        link.style.animation = `fadeInDown 0.5s ease-out ${index * 0.1}s both`;
    });
});
</script>
