@extends('layouts.app')

@section('title', 'Nos Services - BHDM')

@section('content')

{{-- ============================================
     BANNER SECTION (Identique à about)
============================================ --}}
<div class="services_section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6 mb-4 mb-md-0">
                <h1 class="services_taital mb-3">Nos Solutions Financières</h1>
                <p class="services_text mb-3">
                    Depuis 1948, nous accompagnons les entrepreneurs africains avec des solutions financières innovantes et un accompagnement personnalisé.
                </p>
                <p class="services_text mb-4">
                    Notre engagement : démocratiser l'accès au financement pour tous les entrepreneurs africains, des micro-entrepreneurs aux PME en croissance, avec des conditions adaptées et transparentes.
                </p>
                <div class="moremore_bt">
                    <a href="#services_carousel" class="btn-link">Découvrir nos services</a>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="contact-image-container">
                    @if(file_exists(public_path('images/services-banner-hero1.png')))
                        <img src="{{ asset('images/services-banner-hero1.png') }}" class="img-fluid contact-hero-img" alt="Services BHDM">
                    @else
                        <div class="contact-placeholder">
                            <div class="placeholder-content">
                                <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                                <h3 class="mb-2">Nos Services</h3>
                                <p class="mb-0">Solutions financières innovantes</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     WHAT WE DO SECTION - NOS SERVICES (CARROUSEL WEB 3 ÉLÉMENTS)
============================================ --}}
<div class="what_we_do_section" id="services_carousel">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital">Nos Services Financiers</h1>
            <p class="what_text">
                Des solutions adaptées à chaque étape de votre parcours entrepreneurial
            </p>
        </div>

        {{-- CARROUSEL POUR WEB (3 éléments par slide) --}}
        <div class="d-none d-lg-block">
            <div id="servicesCarousel" class="carousel slide" data-bs-ride="carousel">
                {{-- Indicateurs --}}
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="1"></button>
                </div>

                <div class="carousel-inner">
                    {{-- Slide 1 --}}
                    <div class="carousel-item active">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="box_main h-100">
                                    <div class="icon_1">
                                        <i class="fas fa-rocket fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Financement de Démarrage</h3>
                                    <p class="lorem_text">Capital initial avec accompagnement personnalisé pour nouveaux entrepreneurs. Idéal pour lancer votre activité avec un soutien adapté.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Montant: 500K à 5M FCFA</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Durée: 6 mois de TPS réduit</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Formation incluse</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box_main h-100 active">
                                    <div class="icon_1">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Financement de Croissance</h3>
                                    <p class="lorem_text">Accélérez votre développement avec un financement adapté aux entreprises en expansion. Optimisez votre potentiel de croissance.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Montant: 5 à 50M FCFA</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Période de grâce incluse</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Réseaux partenaires</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box_main h-100">
                                    <div class="icon_1">
                                        <i class="fas fa-tractor fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Financement Agricole</h3>
                                    <p class="lorem_text">Solutions spécialisées pour les agriculteurs : équipements, intrants, transformation. Boostez votre production agricole.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Montant: 1 à 30M FCFA</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Saisonnalité incluse</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Accompagnement technique</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Slide 2 --}}
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="box_main h-100">
                                    <div class="icon_1">
                                        <i class="fas fa-hammer fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Financement Artisanal</h3>
                                    <p class="lorem_text">Soutien aux artisans et petites entreprises manufacturières pour moderniser leurs ateliers. Valorisez votre savoir-faire.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Montant: 300K à 10M FCFA</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Accès aux foires</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Marketing digital</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box_main h-100">
                                    <div class="icon_1">
                                        <i class="fas fa-store fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Financement Commercial</h3>
                                    <p class="lorem_text">Pour les commerçants : fonds de roulement, stock, rénovation et développement. Développez votre activité commerciale.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Montant: 1 à 20M FCFA</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Flexibilité de remboursement</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Coaching commercial</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box_main h-100">
                                    <div class="icon_1">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
                                    </div>
                                    <h3 class="accounting_text">Formation & Accompagnement</h3>
                                    <p class="lorem_text">Programmes de formation pour renforcer les compétences entrepreneuriales. Développez vos compétences managériales.</p>
                                    <div class="service-details mt-3">
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Ateliers pratiques</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Modules en ligne 24/7</p>
                                        <p class="mb-1"><i class="fas fa-check-circle me-2"></i>Certificat de formation</p>
                                    </div>
                                    <div class="btn-container mt-3">
                                        <a href="{{ route('register') }}" class="service-btn">S'inscrire aux formations</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contrôles du carrousel --}}
                <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        </div>

        {{-- VERSION MOBILE (grille) --}}
        <div class="d-lg-none">
            <div class="what_we_do_section_2">
                <div class="row g-4">
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100">
                            <div class="icon_1">
                                <i class="fas fa-rocket fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Financement de Démarrage</h3>
                            <p class="lorem_text">Capital initial avec accompagnement personnalisé pour nouveaux entrepreneurs.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100 active">
                            <div class="icon_1">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Financement de Croissance</h3>
                            <p class="lorem_text">Accélérez votre développement avec un financement adapté aux entreprises en expansion.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100">
                            <div class="icon_1">
                                <i class="fas fa-tractor fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Financement Agricole</h3>
                            <p class="lorem_text">Solutions spécialisées pour les agriculteurs : équipements, intrants, transformation.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100">
                            <div class="icon_1">
                                <i class="fas fa-hammer fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Financement Artisanal</h3>
                            <p class="lorem_text">Soutien aux artisans et petites entreprises manufacturières pour moderniser leurs ateliers.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100">
                            <div class="icon_1">
                                <i class="fas fa-store fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Financement Commercial</h3>
                            <p class="lorem_text">Pour les commerçants : fonds de roulement, stock, rénovation et développement.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">Demander ce service</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="box_main h-100">
                            <div class="icon_1">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                            </div>
                            <h3 class="accounting_text">Formation & Accompagnement</h3>
                            <p class="lorem_text">Programmes de formation pour renforcer les compétences entrepreneuriales.</p>
                            <div class="btn-container mt-3">
                                <a href="{{ route('register') }}" class="service-btn">S'inscrire aux formations</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     SECTION : VOTRE PARCOURS VERS LE FINANCEMENT (CARROUSEL)
============================================ --}}
<div id="process" class="what_we_do_section" style="background-color: #f8f9fa;">
    <div class="container">
       <div class="text-center mb-3">
            <h1 class="what_taital">VOTRE PARCOURS VERS LE FINANCEMENT</h1>
            <p class="what_text">
                5 étapes simples et transparentes pour transformer votre projet en réalité
            </p>
        </div>

        {{-- CARROUSEL DES ÉTAPES --}}
        <div id="processCarousel" class="carousel slide" data-bs-ride="carousel">
            {{-- Indicateurs --}}
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#processCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#processCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#processCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#processCarousel" data-bs-slide-to="3"></button>
                <button type="button" data-bs-target="#processCarousel" data-bs-slide-to="4"></button>
            </div>

            {{-- Contenu du carrousel --}}
            <div class="carousel-inner">
                {{-- Étape 1 (16:9) --}}
                <div class="carousel-item active">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="carousel-step-content">
                                <div class="step-number-badge">01</div>
                                <h3 class="step-title">Inscription & Immersion</h3>
                                <p class="step-description">
                                    Création de votre compte et accès à l'Espace Membre BHDM avec tableau de bord personnel.
                                </p>
                                <ul class="step-features">
                                    <li><i class="fas fa-check-circle"></i> Tableau de bord personnalisé</li>
                                    <li><i class="fas fa-check-circle"></i> Accès 24h/24, 7j/7</li>
                                    <li><i class="fas fa-check-circle"></i> Ressources éducatives gratuites</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="carousel-visual" style="padding-bottom: 56.25%;">
                                @if(file_exists(public_path('images/step-1-inscription.png')))
                                    <img src="{{ asset('images/step-1-inscription.png') }}" class="img-fluid" alt="Inscription BHDM" style="object-fit: cover;">
                                @else
                                    <div class="carousel-placeholder">
                                        <i class="fas fa-user-plus fa-4x"></i>
                                        <h4>Création de compte</h4>
                                        <p>Première étape vers votre financement</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 2 (4:3) --}}
                <div class="carousel-item">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="carousel-step-content">
                                <div class="step-number-badge">02</div>
                                <h3 class="step-title">Construction & Soumission</h3>
                                <p class="step-description">
                                    Formalisation de votre projet en 2 missions structurées.
                                </p>
                                <div class="mission-details">
                                    <div class="mission">
                                        <h5><i class="fas fa-play-circle"></i> Mission 1 : Formation</h5>
                                        <p>Module "Les 5 clés d'une activité viable"</p>
                                    </div>
                                    <div class="mission">
                                        <h5><i class="fas fa-file-alt"></i> Mission 2 : Formalisation</h5>
                                        <p>Choix du projet, téléchargement documents, formulaire "Cœur de Projet"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="carousel-visual" style="padding-bottom: 75%;">
                                @if(file_exists(public_path('images/step-2.png')))
                                    <img src="{{ asset('images/step-2.png') }}" class="img-fluid" alt="Soumission de projet" style="object-fit: cover;">
                                @else
                                    <div class="carousel-placeholder">
                                        <i class="fas fa-file-alt fa-4x"></i>
                                        <h4>Construction du projet</h4>
                                        <p>Structurez votre idée avec nos outils</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 3 (4:3) --}}
                <div class="carousel-item">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="carousel-step-content">
                                <div class="step-number-badge">03</div>
                                <h3 class="step-title">Étude & Décision</h3>
                                <p class="step-description">
                                    Analyse par notre Comité Local et calcul de votre TPS (Taux de Participation Solidaire).
                                </p>
                                <ul class="step-features">
                                    <li><i class="fas fa-check-circle"></i> Évaluation par experts locaux</li>
                                    <li><i class="fas fa-check-circle"></i> Calcul automatique du TPS</li>
                                    <li><i class="fas fa-check-circle"></i> Délai : 5-10 jours ouvrés</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="carousel-visual" style="padding-bottom: 75%;">
                                @if(file_exists(public_path('images/step-3.png')))
                                    <img src="{{ asset('images/step-3.png') }}" class="img-fluid" alt="Évaluation du projet" style="object-fit: cover;">
                                @else
                                    <div class="carousel-placeholder">
                                        <i class="fas fa-search fa-4x"></i>
                                        <h4>Évaluation du projet</h4>
                                        <p>Analyse par notre comité d'experts</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 4 (4:3) --}}
                <div class="carousel-item">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="carousel-step-content">
                                <div class="step-number-badge">04</div>
                                <h3 class="step-title">Convention & Financement</h3>
                                <p class="step-description">
                                    Signature électronique et déblocage des fonds avec plan de remboursement personnalisé.
                                </p>
                                <ul class="step-features">
                                    <li><i class="fas fa-check-circle"></i> Signature électronique sécurisée</li>
                                    <li><i class="fas fa-check-circle"></i> Virement Mobile Money ou bancaire</li>
                                    <li><i class="fas fa-check-circle"></i> Plan de remboursement adapté</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="carousel-visual" style="padding-bottom: 75%;">
                                @if(file_exists(public_path('images/step-4.png')))
                                    <img src="{{ asset('images/step-4.png') }}" class="img-fluid" alt="Signature et financement" style="object-fit: cover;">
                                @else
                                    <div class="carousel-placeholder">
                                        <i class="fas fa-handshake fa-4x"></i>
                                        <h4>Signature et financement</h4>
                                        <p>Recevez vos fonds en toute sécurité</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 5 (16:9) --}}
                <div class="carousel-item">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="carousel-step-content">
                                <div class="step-number-badge">05</div>
                                <h3 class="step-title">Suivi & Impact</h3>
                                <p class="step-description">
                                    Accompagnement continu et visualisation de votre contribution au fonds rotatif.
                                </p>
                                <ul class="step-features">
                                    <li><i class="fas fa-check-circle"></i> Suivi personnalisé par mentor</li>
                                    <li><i class="fas fa-check-circle"></i> Jauge de visualisation d'impact</li>
                                    <li><i class="fas fa-check-circle"></i> Accès à la communauté BHDM</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="carousel-visual" style="padding-bottom: 56.25%;">
                                @if(file_exists(public_path('images/step-5.png')))
                                    <img src="{{ asset('images/step-5.png') }}" class="img-fluid" alt="Suivi et impact" style="object-fit: cover;">
                                @else
                                    <div class="carousel-placeholder">
                                        <i class="fas fa-chart-line fa-4x"></i>
                                        <h4>Suivi et impact</h4>
                                        <p>Mesurez votre contribution au développement</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contrôles du carrousel --}}
            <button class="carousel-control-prev" type="button" data-bs-target="#processCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#processCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
            </button>
        </div>

        <div class="text-center mt-5">
            <div class="moremore_bt">
                <a href="{{ route('register') }}" class="btn-link">Commencer mon parcours</a>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     IMPACT SECTION (Identique à about)
============================================ --}}
<div class="project_section_2">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital" style="color: white;">Notre Impact en Chiffres</h1>
            <p class="what_text" style="color: white;">
                Des résultats concrets qui changent des vies en Afrique
            </p>
        </div>

        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-hand-holding-usd fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">50 Mds</h3>
                    <p class="yers_text">FCFA de fonds rotatif</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-users fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">15 000+</h3>
                    <p class="yers_text">Projets financés</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-smile fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">98%</h3>
                    <p class="yers_text">Taux de satisfaction</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-globe-africa fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">15 pays</h3>
                    <p class="yers_text">Afrique de l'Ouest et du Centre</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================
     TESTIMONIALS CAROUSEL SECTION
============================================ --}}
<div class="team_section layout_padding">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital">Ils nous font confiance</h1>
            <p class="what_text_1">Découvrez les expériences de nos bénéficiaires à travers l'Afrique</p>
        </div>

        {{-- CARROUSEL DES TÉMOIGNAGES (3 sur desktop, 1 sur mobile) --}}
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
            {{-- Indicateurs --}}
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="3"></button>
            </div>

            <div class="carousel-inner">
                @php
                    $testimonials = [
                        [
                            'name' => 'Aminata Diop',
                            'role' => 'Boutique "Mode Élégance", Dakar, Sénégal',
                            'text' => 'Grâce au financement de démarrage BHDM, j\'ai ouvert ma boutique après 2 ans d\'économies infructueuses. Mon chiffre d\'affaires a triplé en 18 mois et j\'emploie maintenant 4 personnes.',
                            'rating' => 5.0
                        ],
                        [
                            'name' => 'Jean Koffi',
                            'role' => 'Agriculteur, Abidjan, Côte d\'Ivoire',
                            'text' => 'Le financement agricole m\'a permis d\'acheter un tracteur et d\'augmenter ma production de 300%. J\'exporte maintenant vers d\'autres pays et j\'ai créé 8 emplois dans ma région.',
                            'rating' => 4.9
                        ],
                        [
                            'name' => 'Fatoumata Bâ',
                            'role' => 'Artisane, Bamako, Mali',
                            'text' => 'La formation en gestion m\'a transformée. J\'ai doublé ma production tout en maintenant la qualité artisanale de mes produits. Je participe maintenant à des foires internationales.',
                            'rating' => 5.0
                        ],
                        [
                            'name' => 'Mohamed Sow',
                            'role' => 'Commerçant, Conakry, Guinée',
                            'text' => 'Avec le financement commercial, j\'ai triplé mon stock et ouvert une deuxième boutique en 18 mois. Une vraie transformation qui m\'a permis de scolariser mes 5 enfants!',
                            'rating' => 4.8
                        ],
                        [
                            'name' => 'Adama Traoré',
                            'role' => 'Éleveur, Ouagadougou, Burkina Faso',
                            'text' => 'Le financement m\'a permis d\'agrandir mon élevage et d\'investir dans des équipements modernes. Mon revenu a quadruplé et je forme maintenant d\'autres jeunes éleveurs.',
                            'rating' => 5.0
                        ],
                        [
                            'name' => 'Kadiatou Diallo',
                            'role' => 'Restauratrice, Abidjan, Côte d\'Ivoire',
                            'text' => 'Le financement de croissance m\'a aidée à ouvrir une deuxième succursale. Aujourd\'hui, j\'emploie 15 personnes et je contribue à la sécurité alimentaire de mon quartier.',
                            'rating' => 4.9
                        ],
                        [
                            'name' => 'Yao Gbédjé',
                            'role' => 'Transformateur agricole, Cotonou, Bénin',
                            'text' => 'Avec le financement BHDM, j\'ai pu acheter des machines de transformation de manioc. J\'emploie maintenant 12 personnes et je fournis les supermarchés locaux.',
                            'rating' => 5.0
                        ],
                        [
                            'name' => 'Awa Fofana',
                            'role' => 'Couturière, Lomé, Togo',
                            'text' => 'Le financement artisanal m\'a permis d\'acheter 5 machines à coudre modernes. Je forme maintenant des jeunes filles et nous avons créé une coopérative de 20 membres.',
                            'rating' => 4.9
                        ]
                    ];

                    // Organiser les témoignages en groupes de 3 pour desktop
                    $desktopGroups = array_chunk($testimonials, 3);
                @endphp

                @foreach($desktopGroups as $index => $group)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row g-4">
                            @foreach($group as $testimonial)
                                <div class="col-lg-4 col-md-4">
                                    <div class="testimonial-card text-center p-4 h-100">
                                        <div class="quote-icon mb-3">
                                            <i class="fas fa-quote-left"></i>
                                        </div>
                                        <p class="testimonial-text mb-4">{{ $testimonial['text'] }}</p>
                                        <div class="testimonial-author">
                                            <p class="readable_text mb-1">{{ $testimonial['name'] }}</p>
                                            <p class="readable_text_1 mb-3">{{ $testimonial['role'] }}</p>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($testimonial['rating']))
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i == ceil($testimonial['rating']) && fmod($testimonial['rating'], 1) > 0)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-2">{{ $testimonial['rating'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Contrôles du carrousel --}}
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
            </button>
        </div>
    </div>
</div>

{{-- ============================================
     CTA FINAL SECTION
============================================ --}}
<div class="client_section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h1 class="what_taital mb-2">Prêt à concrétiser votre projet ?</h1>
                    <p class="dummy_text mx-auto mb-3" style="max-width: 700px;">
                        Rejoignez les milliers d'entrepreneurs africains qui ont déjà bénéficié du programme BHDM
                        avec un accompagnement financier structuré, sécurisé et transparent.
                    </p>
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-5">
                        <div class="more_bt">
                            <a href="{{ route('register') }}" class="btn-link">Soumettre mon projet</a>
                        </div>
                        <div class="contact_bt">
                            <a href="{{ route('contact') }}" class="btn-link">Nous contacter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ========== STYLES POUR LA PAGE SERVICES ========== */

    /* Section Services Hero */
    .services_section .services_taital {
        color: #1b5a8d;
        margin-bottom: 20px;
    }

    .services_section .services_taital::after {
        content: '';
        background-color: #ff5a58;
        position: absolute;
        width: 60px;
        text-align: center;
        right: initial;
        top: 0px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        height: 7px;
        left: 0;
        border-radius: 100px;
    }

    .services_section .services_text {
        font-size: 16px;
        line-height: 1.7;
        color: #555;
        margin-bottom: 15px;
    }

    /* Image 16:9 */
    .contact-image-container {
        position: relative;
        padding: 15px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .contact-hero-img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        object-fit: cover;
        aspect-ratio: 16/9;
    }

    .contact-hero-img:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .contact-placeholder {
        width: 100%;
        height: 0;
        padding-bottom: 56.25%;
        background: linear-gradient(135deg, #1b5a8d 0%, #2a5298 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(27, 90, 141, 0.2);
        position: relative;
    }

    .placeholder-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        padding: 30px;
    }

    /* Sections spacing */
    .what_we_do_section,
    .team_section,
    .client_section {
        padding: 40px 0 !important;
    }

    .project_section_2 {
        padding: 40px 0 !important;
    }

    /* Uniformiser les cartes de service */
    .what_we_do_section .box_main {
        background-color: #1b5a8d !important;
        color: #ffffff !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        padding: 20px 15px !important;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .what_we_do_section .box_main:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    /* Textes en blanc */
    .what_we_do_section .accounting_text,
    .what_we_do_section .lorem_text {
        color: #ffffff !important;
    }

    .accounting_text {
        font-size: 18px !important;
        margin: 10px 0 !important;
        text-align: center;
    }

    .lorem_text {
        font-size: 14px !important;
        line-height: 1.4 !important;
        text-align: center;
        flex-grow: 1;
    }

    /* Détails de service */
    .service-details {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        text-align: left;
    }

    .service-details p {
        color: rgba(255, 255, 255, 0.95) !important;
        font-size: 13px !important;
        margin-bottom: 8px !important;
        display: flex;
        align-items: center;
    }

    .service-details i {
        color: #ff5a58 !important;
        margin-right: 8px;
        font-size: 12px;
    }

    /* Boutons dans les cartes */
    .btn-container {
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
    }

    .service-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 8px 25px !important;
        border-radius: 40px !important;
        font-weight: bold !important;
        font-size: 14px !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        border: 2px solid white !important;
        color: white !important;
        background-color: transparent !important;
        min-width: auto !important;
        white-space: nowrap !important;
        text-align: center !important;
    }

    .service-btn:hover {
        background-color: white !important;
        color: #1b5a8d !important;
        transform: translateY(-2px) !important;
    }

    /* CARROUSEL DES SERVICES */
    #servicesCarousel {
        padding: 20px 0;
        margin-top: 20px;
    }

    #servicesCarousel .carousel-inner {
        padding: 0 40px;
    }

    #servicesCarousel .carousel-item {
        transition: transform 0.6s ease-in-out;
    }

    #servicesCarousel .carousel-control-prev,
    #servicesCarousel .carousel-control-next {
        width: 40px;
        height: 40px;
        background-color: #1b5a8d;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
    }

    #servicesCarousel .carousel-control-prev {
        left: -20px;
    }

    #servicesCarousel .carousel-control-next {
        right: -20px;
    }

    #servicesCarousel .carousel-control-prev:hover,
    #servicesCarousel .carousel-control-next:hover {
        opacity: 1;
    }

    #servicesCarousel .carousel-indicators {
        bottom: -40px;
    }

    #servicesCarousel .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #1b5a8d;
        opacity: 0.5;
        margin: 0 5px;
    }

    #servicesCarousel .carousel-indicators button.active {
        opacity: 1;
    }

    /* CARROUSEL DU PROCESSUS */
    #process .what_taital {
        font-size: 34px !important;
        color: #060606 !important;
        text-align: center !important;
        text-transform: uppercase !important;
        font-weight: bold !important;
        margin-bottom: 15px !important;
        width: 100% !important;
        float: none !important;
        padding-top: 0 !important;
        position: relative;
        display: block;
    }

    #process .what_taital::after {
        content: '';
        background-color: #ff5a58;
        position: absolute;
        width: 60px;
        text-align: center;
        right: 0;
        top: 0;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        height: 7px;
        left: 0;
        border-radius: 100px;
    }

    #process .what_text {
        font-size: 16px !important;
        color: #060606 !important;
        text-align: center !important;
        margin-bottom: 30px !important;
        width: 100% !important;
        float: none !important;
        margin-top: 0 !important;
        margin-left: 0 !important;
        display: block;
    }

    /* Styles pour le carrousel processus */
    #processCarousel {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-top: 15px;
    }

    #processCarousel .carousel-indicators {
        bottom: -50px;
    }

    #processCarousel .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #1b5a8d;
        opacity: 0.5;
        margin: 0 5px;
    }

    #processCarousel .carousel-indicators button.active {
        opacity: 1;
        background-color: #1b5a8d;
    }

    .carousel-step-content {
        padding: 20px;
    }

    .step-number-badge {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #1b5a8d, #2a5298);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        font-family: 'Rajdhani', sans-serif;
    }

    .step-title {
        font-size: 24px;
        color: #1b5a8d;
        margin-bottom: 15px;
        font-weight: 600;
        font-family: 'Rajdhani', sans-serif;
    }

    .step-description {
        color: #666;
        margin-bottom: 20px;
        font-size: 16px;
        line-height: 1.6;
    }

    .step-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .step-features li {
        padding: 8px 0;
        color: #555;
        font-size: 15px;
        display: flex;
        align-items: center;
    }

    .step-features li i {
        color: #1b5a8d;
        margin-right: 10px;
        font-size: 14px;
    }

    .mission-details {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .mission {
        margin-bottom: 15px;
    }

    .mission:last-child {
        margin-bottom: 0;
    }

    .mission h5 {
        color: #1b5a8d;
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .mission h5 i {
        margin-right: 8px;
    }

    .mission p {
        color: #666;
        font-size: 14px;
        margin: 0;
        padding-left: 26px;
    }

    .carousel-visual {
        position: relative;
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .carousel-visual img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #1b5a8d;
        text-align: center;
        padding: 20px;
    }

    .carousel-placeholder i {
        margin-bottom: 15px;
    }

    .carousel-placeholder h4 {
        font-size: 18px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .carousel-placeholder p {
        font-size: 14px;
        color: #666;
    }

    #processCarousel .carousel-control-prev,
    #processCarousel .carousel-control-next {
        width: 40px;
        height: 40px;
        background-color: #1b5a8d;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
    }

    #processCarousel .carousel-control-prev {
        left: -20px;
    }

    #processCarousel .carousel-control-next {
        right: -20px;
    }

    #processCarousel .carousel-control-prev:hover,
    #processCarousel .carousel-control-next:hover {
        opacity: 1;
    }

    /* CARROUSEL DES TÉMOIGNAGES */
    #testimonialsCarousel {
        padding: 20px 0;
        margin-top: 20px;
    }

    #testimonialsCarousel .carousel-inner {
        padding: 0 40px;
    }

    #testimonialsCarousel .carousel-item {
        transition: transform 0.6s ease-in-out;
    }

    #testimonialsCarousel .carousel-control-prev,
    #testimonialsCarousel .carousel-control-next {
        width: 40px;
        height: 40px;
        background-color: #1b5a8d;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
    }

    #testimonialsCarousel .carousel-control-prev {
        left: -20px;
    }

    #testimonialsCarousel .carousel-control-next {
        right: -20px;
    }

    #testimonialsCarousel .carousel-control-prev:hover,
    #testimonialsCarousel .carousel-control-next:hover {
        opacity: 1;
    }

    #testimonialsCarousel .carousel-indicators {
        bottom: -40px;
    }

    #testimonialsCarousel .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #1b5a8d;
        opacity: 0.5;
        margin: 0 5px;
    }

    #testimonialsCarousel .carousel-indicators button.active {
        opacity: 1;
    }

    .testimonial-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-top: 3px solid #1b5a8d;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 280px;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .quote-icon {
        color: #1b5a8d;
        font-size: 1.8rem;
        opacity: 0.3;
    }

    .testimonial-text {
        font-style: italic;
        color: #666;
        line-height: 1.6;
        flex-grow: 1;
        font-size: 0.95rem;
        min-height: 120px;
    }

    .testimonial-author {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .readable_text {
        font-weight: 600;
        color: #060606;
        margin-bottom: 5px;
        font-size: 1.1rem;
    }

    .readable_text_1 {
        color: #ff5a58;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .rating {
        color: #ffc107;
        font-size: 0.9rem;
    }

    .rating span {
        color: #666;
        font-size: 0.9rem;
    }

    /* Section Impact */
    .project_section_2 .box_main {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
    }

    .impact_box {
        padding: 20px 10px;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .impact_box:hover {
        transform: translateY(-5px);
    }

    .impact_box i {
        display: block;
        margin: 0 auto 10px;
        transition: transform 0.5s ease;
    }

    .impact_box:hover i {
        transform: scale(1.2) rotate(360deg);
    }

    .accounting_text_1 {
        font-size: 24px !important;
        margin: 5px 0 !important;
    }

    .yers_text {
        font-size: 14px !important;
        margin: 0 !important;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 992px) {
        .services_section .services_taital {
            font-size: 24px;
        }

        .contact-hero-img {
            max-width: 100%;
        }

        .contact-placeholder {
            max-width: 100%;
        }

        .step-number-badge {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        #servicesCarousel .carousel-control-prev {
            left: -10px;
        }

        #servicesCarousel .carousel-control-next {
            right: -10px;
        }

        #processCarousel .carousel-control-prev {
            left: -10px;
        }

        #processCarousel .carousel-control-next {
            right: -10px;
        }

        #testimonialsCarousel .carousel-control-prev {
            left: -10px;
        }

        #testimonialsCarousel .carousel-control-next {
            right: -10px;
        }
    }

    @media (max-width: 768px) {
        .services_section {
            padding: 40px 0 30px 0;
        }

        .contact-image-container {
            margin-top: 30px;
            padding: 0 15px;
        }

        .services_section .services_taital {
            text-align: center;
            font-size: 22px;
        }

        .services_section .services_taital::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .services_section .services_text {
            text-align: center;
        }

        .contact-hero-img {
            max-width: 100%;
        }

        .contact-placeholder {
            max-width: 100%;
        }

        .what_we_do_section,
        .team_section,
        .client_section {
            padding: 30px 0 !important;
        }

        .services_taital,
        .what_taital {
            font-size: 22px;
            margin-bottom: 8px !important;
        }

        .what_text,
        .services_text {
            font-size: 14px;
        }

        .box_main {
            padding: 20px 15px;
        }

        .moremore_bt,
        .contact_bt {
            min-width: 200px;
            width: 100% !important;
            max-width: 250px !important;
        }

        .moremore_bt a,
        .contact_bt a {
            padding: 10px 15px !important;
            font-size: 14px !important;
            width: 100% !important;
        }

        .client_section .d-flex {
            gap: 15px !important;
            flex-direction: column !important;
            width: 100% !important;
        }

        .service-btn {
            padding: 7px 20px !important;
            font-size: 13px !important;
        }

        .accounting_text_1 {
            font-size: 22px !important;
        }

        .yers_text {
            font-size: 13px !important;
        }

        /* Sur mobile, on force 1 témoignage par slide */
        #testimonialsCarousel .col-lg-4 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
    }

    @media (max-width: 576px) {
        .services_section .services_taital {
            font-size: 20px;
        }

        .services_section .services_text {
            font-size: 15px;
        }

        .contact-placeholder {
            height: 0;
            padding-bottom: 56.25%;
        }

        .placeholder-content h3 {
            font-size: 20px;
        }

        .placeholder-content p {
            font-size: 14px;
        }

        .services_taital,
        .what_taital {
            font-size: 20px;
        }

        .what_text,
        .services_text {
            font-size: 13px;
        }

        .moremore_bt,
        .contact_bt {
            min-width: 180px;
            width: 100%;
            max-width: 250px;
        }

        .moremore_bt a,
        .contact_bt a {
            width: 100%;
        }

        .service-details {
            padding: 10px;
        }

        .service-details p {
            font-size: 12px !important;
        }

        .service-btn {
            padding: 6px 18px !important;
            font-size: 12px !important;
        }
    }

    /* ========== ANIMATIONS ========== */
    .services_section,
    .what_we_do_section,
    .project_section_2,
    .team_section,
    .client_section {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease-out forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .what_we_do_section { animation-delay: 0.1s; }
    .project_section_2 { animation-delay: 0.2s; }
    .team_section { animation-delay: 0.3s; }
    .client_section { animation-delay: 0.4s; }

    /* Animation des carrousels */
    #servicesCarousel .carousel-item,
    #processCarousel .carousel-item,
    #testimonialsCarousel .carousel-item {
        transition: transform 0.6s ease-in-out;
    }

    .box_main, .testimonial-card, .impact_box {
        transition: all 0.3s ease !important;
    }

    .box_main:hover, .testimonial-card:hover {
        transform: translateY(-5px) !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Initialisation des carrousels
    const servicesCarousel = document.getElementById('servicesCarousel');
    const processCarousel = document.getElementById('processCarousel');
    const testimonialsCarousel = document.getElementById('testimonialsCarousel');

    if (servicesCarousel) {
        const carousel = new bootstrap.Carousel(servicesCarousel, {
            interval: 5000,
            wrap: true,
            pause: 'hover'
        });
    }

    if (processCarousel) {
        const carousel = new bootstrap.Carousel(processCarousel, {
            interval: 6000,
            wrap: true,
            pause: 'hover'
        });
    }

    if (testimonialsCarousel) {
        const carousel = new bootstrap.Carousel(testimonialsCarousel, {
            interval: 4000,
            wrap: true,
            pause: 'hover'
        });
    }

    // Animation pour les images au survol
    const images = document.querySelectorAll('.contact-hero-img');
    images.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        img.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Animation pour les boîtes au survol
    const boxes = document.querySelectorAll('.box_main');
    boxes.forEach(box => {
        box.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.icon_1 i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
            }
        });

        box.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.icon_1 i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });

    // Animation au scroll
    const sections = document.querySelectorAll('.what_we_do_section, .project_section_2, .team_section, .client_section');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, {
        threshold: 0.1
    });

    sections.forEach(section => {
        section.style.animationPlayState = 'paused';
        observer.observe(section);
    });

    // Animation pour les chiffres d'impact
    const impactBoxes = document.querySelectorAll('.impact_box');
    impactBoxes.forEach(box => {
        box.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(360deg)';
            }
        });

        box.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0)';
            }
        });
    });

    // Ajustement des boutons pour le responsive
    function optimizeButtons() {
        const buttons = document.querySelectorAll('.btn-link, .service-btn');
        const isMobile = window.innerWidth <= 768;

        buttons.forEach(button => {
            if (isMobile) {
                button.style.width = '100%';
                button.style.padding = '10px 15px';
                button.style.fontSize = '14px';
            } else {
                button.style.width = 'auto';
                button.style.padding = '12px 30px';
                button.style.fontSize = '16px';
            }
        });
    }

    window.addEventListener('resize', optimizeButtons);
    optimizeButtons();

    // Ajustement spécifique pour aligner parfaitement les boutons côte à côte
    function fixButtonAlignment() {
        const btnMain = document.querySelector('.client_section .d-flex');
        if (btnMain && window.innerWidth > 768) {
            const buttons = btnMain.querySelectorAll('.btn-link');
            let maxHeight = 0;

            buttons.forEach(button => {
                button.style.height = 'auto';
            });

            buttons.forEach(button => {
                maxHeight = Math.max(maxHeight, button.offsetHeight);
            });

            buttons.forEach(button => {
                button.style.height = maxHeight + 'px';
                button.style.display = 'flex';
                button.style.alignItems = 'center';
                button.style.justifyContent = 'center';
            });
        }
    }

    window.addEventListener('load', fixButtonAlignment);
    window.addEventListener('resize', fixButtonAlignment);
    setTimeout(fixButtonAlignment, 500);

    // Animation pour les images du carrousel processus
    const processImages = document.querySelectorAll('#processCarousel .carousel-visual img');
    processImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });

        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Animation pour les cartes de témoignages
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    testimonialCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const quoteIcon = this.querySelector('.quote-icon i');
            if (quoteIcon) {
                quoteIcon.style.transform = 'scale(1.2)';
                quoteIcon.style.opacity = '0.5';
            }
        });

        card.addEventListener('mouseleave', function() {
            const quoteIcon = this.querySelector('.quote-icon i');
            if (quoteIcon) {
                quoteIcon.style.transform = 'scale(1)';
                quoteIcon.style.opacity = '0.3';
            }
        });
    });
});
</script>
@endsection
