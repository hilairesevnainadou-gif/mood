<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Non Trouvée - BHDM</title>

    <!-- Bootstrap CSS depuis CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome 6 COMPLETE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1b5a8d;
            --secondary-color: #ff5a58;
            --accent-color: #4aafff;
            --dark-color: #0a1f44;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a1f44 0%, #1b5a8d 100%);
            min-height: 100vh;
            color: white;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Header invisible */
        header {
            display: none !important;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            animation: float 20s infinite linear;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(20px); }
            100% { transform: translateY(0) translateX(0); }
        }

        .error-content {
            text-align: center;
            max-width: 800px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .error-icon {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .error-code {
            font-family: 'Rajdhani', sans-serif;
            font-size: 12rem;
            font-weight: 700;
            color: white;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 5px 5px 0 rgba(255, 90, 88, 0.3);
            position: relative;
            display: inline-block;
        }

        .error-code::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--secondary-color), transparent);
            border-radius: 2px;
        }

        .error-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .error-message {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-box {
            max-width: 500px;
            margin: 40px auto;
        }

        .search-input {
            position: relative;
        }

        .search-input input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border-radius: 50px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input input:focus {
            border-color: var(--secondary-color);
            background: rgba(255, 255, 255, 0.15);
        }

        .search-input button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--secondary-color);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .search-input button:hover {
            background: #ff3a38;
            transform: translateY(-50%) scale(1.1);
        }

        .error-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .btn-404 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            font-family: 'Rajdhani', sans-serif;
            min-width: 200px;
        }

        .btn-primary-404 {
            background-color: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .btn-primary-404:hover {
            background-color: #ff3a38;
            border-color: #ff3a38;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 90, 88, 0.3);
            color: white;
        }

        .btn-secondary-404 {
            background-color: transparent;
            color: white;
            border-color: white;
        }

        .btn-secondary-404:hover {
            background-color: white;
            color: var(--dark-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2);
        }

        .btn-404 i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .error-links {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .error-links h4 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: white;
        }

        .error-links ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .error-links li {
            display: inline-block;
        }

        .error-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-links a:hover {
            color: var(--secondary-color);
            transform: translateX(5px);
        }

        .error-links i {
            font-size: 0.9rem;
        }

        /* Animation de fond */
        .bg-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-animation span {
            position: absolute;
            display: block;
            background: rgba(255, 255, 255, 0.1);
            animation: animate 25s linear infinite;
            bottom: -150px;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        /* Footer invisible */
        footer {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-code {
                font-size: 8rem;
            }

            .error-title {
                font-size: 2rem;
            }

            .error-message {
                font-size: 1.1rem;
            }

            .error-content {
                padding: 30px 20px;
            }

            .error-actions {
                flex-direction: column;
                gap: 15px;
            }

            .btn-404 {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 576px) {
            .error-code {
                font-size: 6rem;
            }

            .error-title {
                font-size: 1.8rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .error-icon {
                font-size: 3rem;
            }

            .search-box {
                margin: 30px auto;
            }

            .error-links ul {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    {{-- HEADER INVISIBLE --}}
    <header style="display: none;"></header>

    {{-- CONTENU PRINCIPAL --}}
    <main>
        <div class="error-container">
            <div class="bg-animation"></div>

            <div class="error-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <h1 class="error-code">404</h1>

                <h2 class="error-title">Page Non Trouvée</h2>

                <p class="error-message">
                    Désolé, la page que vous recherchez semble avoir été déplacée, supprimée
                    ou n'existe pas. Vérifiez l'URL ou utilisez notre moteur de recherche
                    pour trouver ce que vous cherchez.
                </p>

                <div class="search-box">
                    <div class="search-input">
                        <input type="text" placeholder="Rechercher sur le site BHDM...">
                        <button type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="error-actions">
                    <a href="{{ route('home') }}" class="btn-404 btn-primary-404">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('contact') }}" class="btn-404 btn-secondary-404">
                        <i class="fas fa-headset"></i> Contactez-nous
                    </a>
                </div>

                <div class="error-links">
                    <h4>Liens utiles :</h4>
                    <ul>
                        <li><a href="{{ route('about') }}"><i class="fas fa-history"></i> Notre histoire</a></li>
                        <li><a href="{{ route('services') }}"><i class="fas fa-hand-holding-usd"></i> Nos services</a></li>
                        <li><a href="{{ route('contact') }}"><i class="fas fa-envelope"></i> Contact</a></li>
                        <li><a href="#"><i class="fas fa-question-circle"></i> FAQ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER INVISIBLE --}}
    <footer style="display: none;"></footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation pour le bouton de recherche
            const searchButton = document.querySelector('.search-input button');
            const searchInput = document.querySelector('.search-input input');

            searchButton.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    alert(`Recherche de: "${searchTerm}"\n\nCette fonctionnalité est en cours de développement.`);
                } else {
                    searchInput.focus();
                }
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });

            // Effet de frappe pour le message d'erreur
            const errorMessage = document.querySelector('.error-message');
            const originalText = errorMessage.textContent;
            errorMessage.textContent = '';

            let i = 0;
            function typeWriter() {
                if (i < originalText.length) {
                    errorMessage.textContent += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 20);
                }
            }

            // Démarrer l'animation après un délai
            setTimeout(typeWriter, 500);

            // Effet de particules supplémentaires
            const bgAnimation = document.querySelector('.bg-animation');

            // Créer les particules
            for (let i = 0; i < 20; i++) {
                const span = document.createElement('span');

                // Taille aléatoire entre 10px et 100px
                const size = Math.random() * 90 + 10;
                span.style.width = size + 'px';
                span.style.height = size + 'px';

                // Position aléatoire
                span.style.left = Math.random() * 100 + '%';

                // Délai d'animation aléatoire
                span.style.animationDelay = Math.random() * 20 + 's';
                span.style.animationDuration = Math.random() * 20 + 15 + 's';

                bgAnimation.appendChild(span);
            }

            // Animation des boutons
            const buttons = document.querySelectorAll('.btn-404');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.05)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
