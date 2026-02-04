<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'BHDM - Banque Humanitaire du Développement Mondial')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Bootstrap CSS depuis CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6 COMPLETE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- CSS Locaux (après Bootstrap pour override) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <!-- Préchargement Font Awesome pour performance -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>

    @stack('styles')
</head>

<body>
    {{-- HEADER --}}
    @include('partials.header')

    {{-- CONTENU PRINCIPAL --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    @include('partials.footer')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Scripts personnalisés -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mCustomScrollbar.concat.min.js') }}"></script>

    @stack('scripts')

    <!-- SCRIPT POUR LE MENU BURGER FONCTIONNEL -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {


        // Vérifier que Font Awesome est chargé
        if (typeof FontAwesome === 'undefined') {

            // Fallback pour les icônes
            setTimeout(checkIcons, 1000);
        }

        function checkIcons() {
            const icons = document.querySelectorAll('.fab, .fas, .far, .fal');
            let missingIcons = 0;

            icons.forEach(icon => {
                const computedStyle = window.getComputedStyle(icon, '::before');
                const content = computedStyle.content;
                if (content === 'none' || content === '""' || content === "''") {
                    missingIcons++;
                    console.warn('Icône manquante:', icon.className);

                    // Fallback pour les icônes critiques
                    if (icon.classList.contains('fa-facebook-f')) {
                        icon.innerHTML = 'f';
                        icon.style.fontFamily = 'Arial, sans-serif';
                        icon.style.fontWeight = 'bold';
                    }
                    if (icon.classList.contains('fa-twitter')) {
                        icon.innerHTML = '𝕏';
                        icon.style.fontFamily = 'Arial, sans-serif';
                    }
                    if (icon.classList.contains('fa-linkedin-in')) {
                        icon.innerHTML = 'in';
                        icon.style.fontFamily = 'Arial, sans-serif';
                        icon.style.fontWeight = 'bold';
                    }
                    if (icon.classList.contains('fa-youtube')) {
                        icon.innerHTML = '▶';
                        icon.style.fontFamily = 'Arial, sans-serif';
                    }
                    if (icon.classList.contains('fa-instagram')) {
                        icon.innerHTML = '📷';
                        icon.style.fontFamily = 'Arial, sans-serif';
                    }
                    if (icon.classList.contains('fa-envelope')) {
                        icon.innerHTML = '✉';
                        icon.style.fontFamily = 'Arial, sans-serif';
                    }
                    if (icon.classList.contains('fa-globe-africa')) {
                        icon.innerHTML = '🌍';
                        icon.style.fontFamily = 'Arial, sans-serif';
                    }
                }
            });

            if (missingIcons > 0) {

                // Réessayer de charger Font Awesome
                loadFontAwesomeFallback();
            } else {

        }

        function loadFontAwesomeFallback() {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css';
            link.integrity = 'sha512-Q5G8e1z3fNtP+4R+17WvK+0k8+Rk8vJhE9tG9Uh+qUYhQqM7Kt8MvE2O9m3K9l8w4O4vO8J9z5M5L5K5O5K5A==';
            link.crossOrigin = 'anonymous';
            link.referrerPolicy = 'no-referrer';
            document.head.appendChild(link);

            console.log('🔄 Chargement alternatif de Font Awesome...');
        }

        // Vérification initiale des icônes
        setTimeout(checkIcons, 500);

        // Vérifier que Bootstrap est chargé
        if (typeof bootstrap === 'undefined') {

            // Fallback manuel pour le menu burger
            const toggler = document.querySelector('.navbar-toggler');
            const collapse = document.querySelector('.navbar-collapse');

            if (toggler && collapse) {
                toggler.addEventListener('click', function() {
                    collapse.classList.toggle('show');
                    const isExpanded = this.getAttribute('aria-expanded') === 'true' || false;
                    this.setAttribute('aria-expanded', !isExpanded);
                });

                // Fermer le menu quand on clique sur un lien
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        collapse.classList.remove('show');
                        toggler.setAttribute('aria-expanded', 'false');
                    });
                });
            }
        } else {

            // Initialiser le menu burger Bootstrap
            const toggler = document.querySelector('.navbar-toggler');
            const collapse = document.querySelector('.navbar-collapse');

            if (toggler && collapse) {
                // Animation pour l'icône burger
                toggler.addEventListener('click', function() {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';

                    // Animation de l'icône
                    const icon = this.querySelector('.navbar-toggler-icon');
                    if (icon) {
                        if (isExpanded) {
                            icon.style.transform = 'rotate(0deg)';
                        } else {
                            icon.style.transform = 'rotate(90deg)';
                        }
                    }
                });

                // Fermer le menu quand on clique sur un lien (mobile)
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            const bsCollapse = bootstrap.Collapse.getInstance(collapse);
                            if (bsCollapse) {
                                bsCollapse.hide();
                            }
                            // Réinitialiser l'icône
                            const icon = toggler.querySelector('.navbar-toggler-icon');
                            if (icon) {
                                icon.style.transform = 'rotate(0deg)';
                            }
                        }
                    });
                });
            }
        }

        // Animation au clic pour tous les boutons avec classe btn-link
        const allButtons = document.querySelectorAll('.btn-link');

        allButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Animation de clic
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'clickAnimation 0.2s ease';
                }, 10);

                // Effet de ripple
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
                `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Animation CSS pour l'effet ripple et clic
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
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

            /* Fallback pour Font Awesome */
            .fa-facebook-f.fallback:before { content: "f"; font-family: Arial, sans-serif; font-weight: bold; }
            .fa-twitter.fallback:before { content: "𝕏"; font-family: Arial, sans-serif; }
            .fa-linkedin-in.fallback:before { content: "in"; font-family: Arial, sans-serif; font-weight: bold; }
            .fa-youtube.fallback:before { content: "▶"; font-family: Arial, sans-serif; }
            .fa-instagram.fallback:before { content: "📷"; font-family: Arial, sans-serif; }
            .fa-envelope.fallback:before { content: "✉"; font-family: Arial, sans-serif; }
            .fa-globe-africa.fallback:before { content: "🌍"; font-family: Arial, sans-serif; }
            .fa-print.fallback:before { content: "🖨"; font-family: Arial, sans-serif; }
            .fa-download.fallback:before { content: "⬇"; font-family: Arial, sans-serif; }
            .fa-cog.fallback:before { content: "⚙"; font-family: Arial, sans-serif; }
            .fa-times.fallback:before { content: "✕"; font-family: Arial, sans-serif; }
            .fa-check.fallback:before { content: "✓"; font-family: Arial, sans-serif; }
            .fa-cookie-bite.fallback:before { content: "🍪"; font-family: Arial, sans-serif; }
            .fa-info-circle.fallback:before { content: "ⓘ"; font-family: Arial, sans-serif; }
        `;
        document.head.appendChild(style);

        // Animation pour la navbar au scroll
        let lastScrollTop = 0;
        const navbar = document.querySelector('.navbar');

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 100) {
                navbar.style.background = 'rgba(10, 31, 68, 0.98)';
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
                navbar.style.padding = '10px 0';
            } else {
                navbar.style.background = 'rgba(10, 31, 68, 0.95)';
                navbar.style.boxShadow = 'none';
                navbar.style.padding = '20px 0';
            }

            // Hide/show navbar on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                navbar.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;
        });

        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');

                if (href === '#' || href === '#!') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Fermer le menu mobile si on clique en dehors
        document.addEventListener('click', function(event) {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navbarToggler = document.querySelector('.navbar-toggler');

            if (window.innerWidth < 992 &&
                navbarCollapse &&
                navbarCollapse.classList.contains('show') &&
                !navbarCollapse.contains(event.target) &&
                !navbarToggler.contains(event.target)) {

                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        });
    });
    </script>
</body>
</html>
