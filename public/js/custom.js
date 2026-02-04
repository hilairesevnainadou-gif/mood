// ========================================
// CUSTOM.JS - Scripts personnalisés BHDM
// ========================================

(function($) {
    'use strict';

    // Attendre que le DOM soit complètement chargé
    $(document).ready(function() {
        console.log('✅ Custom.js chargé');

        // ========================================
        // 1. SMOOTH SCROLL
        // ========================================
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            if (this.hash !== '') {
                const target = $(this.hash);
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 70
                    }, 800);
                }
            }
        });

        // ========================================
        // 2. NAVBAR SCROLL EFFECT
        // ========================================
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 100) {
                $('.header_section').addClass('scrolled');
            } else {
                $('.header_section').removeClass('scrolled');
            }
        });

        // ========================================
        // 3. OWL CAROUSEL (si présent)
        // ========================================
        if ($('.owl-carousel').length) {
            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    }
                }
            });
        }

        // ========================================
        // 4. BACK TO TOP BUTTON
        // ========================================
        const backToTop = $('<button>')
            .addClass('back-to-top')
            .html('<i class="fas fa-arrow-up"></i>')
            .appendTo('body')
            .hide();

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.fadeIn();
            } else {
                backToTop.fadeOut();
            }
        });

        backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 600);
        });

        // ========================================
        // 5. ANIMATIONS AU SCROLL (Fade In)
        // ========================================
        function checkScroll() {
            $('.fade-in-scroll').each(function() {
                const elementTop = $(this).offset().top;
                const viewportBottom = $(window).scrollTop() + $(window).height();

                if (elementTop < viewportBottom - 100) {
                    $(this).addClass('visible');
                }
            });
        }

        $(window).on('scroll', checkScroll);
        checkScroll(); // Initial check

        // ========================================
        // 6. FORM VALIDATION
        // ========================================
        $('form').on('submit', function(e) {
            let isValid = true;

            $(this).find('input[required], textarea[required]').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Validation email
            const emailInput = $(this).find('input[type="email"]');
            if (emailInput.length) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.val())) {
                    isValid = false;
                    emailInput.addClass('is-invalid');
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires correctement.');
            }
        });

        // ========================================
        // 7. LOADER (si présent)
        // ========================================
        $(window).on('load', function() {
            $('.loader').fadeOut('slow');
        });

        // ========================================
        // 8. MOBILE MENU AUTO CLOSE
        // ========================================
        $('.navbar-nav .nav-link').on('click', function() {
            if ($(window).width() < 992) {
                $('.navbar-collapse').collapse('hide');
            }
        });

        console.log('✅ Tous les scripts custom sont initialisés');
    });

})(jQuery);

// ========================================
// STYLES POUR BACK TO TOP
// ========================================
const style = document.createElement('style');
style.textContent = `
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        z-index: 999;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .back-to-top:hover {
        background: linear-gradient(45deg, #2a5298, #1e3c72);
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }

    .fade-in-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .fade-in-scroll.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .header_section.scrolled {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);
