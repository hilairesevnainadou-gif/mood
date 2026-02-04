<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BHDM</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            min-height: 700px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }

        .login-right {
            flex: 1;
            background: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }

        .logo-img {
            height: 60px;
            width: auto;
            max-width: 200px;
            object-fit: contain;
        }

        .logo-text {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
        }

        .login-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .form-control {
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(74, 175, 255, 0.1);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .form-control-with-icon {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(27, 90, 141, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .login-links {
            text-align: center;
            margin-top: 20px;
        }

        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 40px 0;
        }

        .feature-list li {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1rem;
        }

        .feature-list i {
            color: var(--secondary-color);
            font-size: 1.5rem;
        }

        /* Carrousel des témoignages */
        .testimonial-carousel {
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
        }

        .testimonial-item {
            text-align: center;
            padding: 20px;
        }

        .testimonial-text {
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 1.1rem;
            min-height: 120px;
        }

        .testimonial-author {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .testimonial-author span {
            display: block;
            font-weight: normal;
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--secondary-color);
        }

        .carousel-indicators {
            bottom: -40px;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            margin: 0 5px;
        }

        .carousel-indicators button.active {
            background-color: var(--secondary-color);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.3);
        }

        .carousel-control-prev {
            left: -20px;
        }

        .carousel-control-next {
            right: -20px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border-left: 4px solid #28a745;
        }

        .password-toggle {
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            background: #f8f9fa;
        }

        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            .login-right {
                padding: 20px 15px;
            }

            .logo-text {
                font-size: 1.5rem;
            }

            .login-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Panneau gauche -->
        <div class="login-left">
            <div class="logo-container">
                <a href="{{ route('home') }}" class="logo">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="BHDM - Banque Humanitaire du Développement Mondial" class="logo-img">
                    @else
                        <!-- Fallback si le logo n'existe pas -->
                        <div class="logo-text">BHDM</div>
                        <div style="font-size: 0.8rem; margin-top: 5px;">
                            Banque Humanitaire du Développement Mondial
                        </div>
                    @endif
                </a>
            </div>

            <h2 class="mb-4">Bienvenue sur votre espace client</h2>
            <p>Accédez à votre portefeuille digital, gérez vos demandes de financement et suivez vos projets.</p>

            <ul class="feature-list">
                <li>
                    <i class="fas fa-shield-alt"></i>
                    <span>Espace sécurisé et crypté</span>
                </li>
                <li>
                    <i class="fas fa-bolt"></i>
                    <span>Accès instantané à vos fonds</span>
                </li>
                <li>
                    <i class="fas fa-chart-line"></i>
                    <span>Suivi en temps réel de vos projets</span>
                </li>
                <li>
                    <i class="fas fa-headset"></i>
                    <span>Support client dédié</span>
                </li>
            </ul>

            <!-- Carrousel des témoignages -->
            <div class="testimonial-carousel">
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Témoignage 1 -->
                        <div class="carousel-item active">
                            <div class="testimonial-item">
                                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                     alt="Fatou Diagne" class="testimonial-avatar">
                                <p class="testimonial-text">
                                    "Grâce à BHDM, j'ai pu développer mon entreprise de transformation agricole.
                                    L'interface est intuitive et l'accompagnement exceptionnel."
                                </p>
                                <div class="testimonial-author">
                                    Fatou Diagne
                                    <span>Entrepreneure, Thiès</span>
                                </div>
                            </div>
                        </div>

                        <!-- Témoignage 2 -->
                        <div class="carousel-item">
                            <div class="testimonial-item">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                     alt="Moussa Diallo" class="testimonial-avatar">
                                <p class="testimonial-text">
                                    "Le portefeuille digital m'a permis de gérer mes finances facilement.
                                    Les transferts sont instantanés et sécurisés."
                                </p>
                                <div class="testimonial-author">
                                    Moussa Diallo
                                    <span>Artisan, Dakar</span>
                                </div>
                            </div>
                        </div>

                        <!-- Témoignage 3 -->
                        <div class="carousel-item">
                            <div class="testimonial-item">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                     alt="Aminata Sarr" class="testimonial-avatar">
                                <p class="testimonial-text">
                                    "En tant que femme entrepreneure, BHDM m'a offert les opportunités
                                    dont j'avais besoin pour faire grandir mon commerce."
                                </p>
                                <div class="testimonial-author">
                                    Aminata Sarr
                                    <span>Commerçante, Saint-Louis</span>
                                </div>
                            </div>
                        </div>

                        <!-- Témoignage 4 -->
                        <div class="carousel-item">
                            <div class="testimonial-item">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                     alt="Cheikh Ndiaye" class="testimonial-avatar">
                                <p class="testimonial-text">
                                    "La rapidité de traitement des demandes de financement est impressionnante.
                                    Vraiment une plateforme au service des entrepreneurs."
                                </p>
                                <div class="testimonial-author">
                                    Cheikh Ndiaye
                                    <span>Agriculteur, Kaolack</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contrôles du carrousel -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>

                    <!-- Indicateurs -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Témoignage 1"></button>
                        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1" aria-label="Témoignage 2"></button>
                        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2" aria-label="Témoignage 3"></button>
                        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="3" aria-label="Témoignage 4"></button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <p><i class="fas fa-info-circle me-2"></i>Vous êtes nouveau ?
                    <a href="{{ route('register') }}" class="text-white fw-bold">Créez votre compte</a>
                </p>
            </div>
        </div>

        <!-- Panneau droit (Formulaire) -->
        <div class="login-right">
            <div>
                <h1 class="login-title">Connexion</h1>
                <p class="login-subtitle">Entrez vos identifiants pour accéder à votre compte</p>

                <!-- Messages d'erreur/succès -->
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Erreur d'authentification</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulaire -->
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold mb-2">Adresse email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control form-control-with-icon"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="votre@email.com"
                                   required
                                   autocomplete="email"
                                   autofocus>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold mb-2">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control form-control-with-icon"
                                   id="password"
                                   name="password"
                                   placeholder="Votre mot de passe"
                                   required
                                   autocomplete="current-password">
                            <span class="input-group-text password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Bouton de connexion -->
                    <button type="submit" class="btn btn-login mb-4" id="loginButton">
                        <span id="loginButtonText">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </span>
                        <span id="loginButtonLoading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Connexion en cours...
                        </span>
                    </button>

                    <!-- Lien d'inscription -->
                    <div class="login-links">
                        <p>Vous n'avez pas de compte ?
                            <a href="{{ route('register') }}">Inscrivez-vous gratuitement</a>
                        </p>
                    </div>
                </form>

                <!-- Séparateur -->
                <div class="position-relative my-4">
                    <hr>
                    <div class="position-absolute top-50 start-50 translate-middle bg-white px-3">
                        <span class="text-muted">ou</span>
                    </div>
                </div>

                <!-- Retour à l'accueil -->
                <div class="text-center">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Mot de passe oublié -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>
                        Réinitialisation du mot de passe
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="votre@email.com" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="forgotPasswordForm" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        Envoyer le lien
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Afficher/masquer le mot de passe
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Changer l'icône
                    const icon = this.querySelector('i');
                    if (type === 'text') {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }

            // Gestion du formulaire de connexion
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const loginButtonText = document.getElementById('loginButtonText');
            const loginButtonLoading = document.getElementById('loginButtonLoading');

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    // Désactiver le bouton pendant la soumission
                    loginButton.disabled = true;
                    loginButtonText.style.display = 'none';
                    loginButtonLoading.style.display = 'inline';
                });
            }

            // Gestion du formulaire mot de passe oublié
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');

            if (forgotPasswordForm) {
                forgotPasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const email = this.querySelector('input[type="email"]').value;

                    // Fermer le modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                    modal.hide();

                    // Afficher un message (simulation)
                    showToast('Un lien de réinitialisation a été envoyé à ' + email, 'info');
                });
            }

            // Auto-focus sur l'email
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }

            // Auto-rotation du carrousel
            const testimonialCarousel = document.getElementById('testimonialCarousel');
            if (testimonialCarousel) {
                const carousel = new bootstrap.Carousel(testimonialCarousel, {
                    interval: 5000, // Change toutes les 5 secondes
                    ride: 'carousel',
                    wrap: true
                });
            }

            // Fonction pour afficher des toasts
            function showToast(message, type = 'info') {
                // Créer un toast
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-bg-${type} border-0`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');

                toastEl.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;

                // Style de position
                toastEl.style.position = 'fixed';
                toastEl.style.top = '20px';
                toastEl.style.right = '20px';
                toastEl.style.zIndex = '9999';

                document.body.appendChild(toastEl);

                // Initialiser et montrer le toast
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();

                // Nettoyer après la fermeture
                toastEl.addEventListener('hidden.bs.toast', function() {
                    this.remove();
                });
            }

            // Vérifier si l'utilisateur est déjà connecté
            @auth
                window.location.href = '{{ route("client.dashboard") }}';
            @endauth
        });
    </script>
</body>
</html>
