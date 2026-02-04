@extends('layouts.app')

@section('title', 'Présentation - BHDM')

@section('content')

{{-- SECTION : NOTRE HISTOIRE & MISSION --}}
<div class="services_section" id="history-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6 mb-4 mb-md-0">
                <h1 class="services_taital mb-3">Notre Histoire & Mission</h1>
                <p class="services_text mb-3">
                    La Banque Humanitaire du Développement Mondial (BHDM) est née d'une transformation historique
                    du Colonial Development Corporation (1948), devenu British International Investment (BII).
                </p>
                <p class="services_text mb-4">
                    En 2023, le BII a créé la BHDM comme programme-phare d'investissement à impact, doté d'un fonds
                    rotatif de 50 milliards FCFA pour financer directement les entrepreneurs africains et lutter
                    contre la pauvreté par l'autonomisation économique.
                </p>
                <div class="moremore_bt">
                    <a href="#process">Découvrir notre processus</a>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="history-image-container">
                    @if(file_exists(public_path('images/mission-histoire-bhdm.png')))
                        <img src="{{ asset('images/mission-histoire-bhdm.png') }}" class="img-fluid history-hero-img" alt="Histoire BHDM">
                    @else
                        <div class="history-placeholder">
                            <div class="placeholder-content">
                                <i class="fas fa-history fa-3x mb-3"></i>
                                <h3 class="mb-2">1948 → 2023</h3>
                                <p class="mb-0">Du CDC à la BHDM</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : MODÈLE DE FINANCEMENT --}}
<div class="what_we_do_section">
    <div class="container">
        <div class="text-center mb-3">
            <h1 class="what_taital">Notre Modèle de Financement</h1>
            <p class="what_text">
                Un système durable qui crée un impact multiplicateur à travers un fonds qui se régénère continuellement.
            </p>
        </div>

        <div class="what_we_do_section_2">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="box_main h-100 active">
                        <div class="icon_1">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Fonds Initial</h3>
                        <p class="lorem_text">
                            50 milliards FCFA dédiés au financement des projets économiques en Afrique.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Bénéficiaires</h3>
                        <p class="lorem_text">
                            Entrepreneurs, agriculteurs, artisans et PME africains accompagnés.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-retweet fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Remboursement</h3>
                        <p class="lorem_text">
                            Capital + TPS (Taux de Participation Solidaire) recyclés dans le fonds.
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-project-diagram fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Nouveaux Projets</h3>
                        <p class="lorem_text">
                            Les fonds recyclés financent de nouveaux bénéficiaires, créant un cycle vertueux.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : VOTRE PARCOURS VERS LE FINANCEMENT (CARROUSEL) --}}
<div id="process" class="what_we_do_section">
    <div class="container">
       <div class="text-center mb-3">
            <h1 class="what_taital">VOTRE PARCOURS VERS LE FINANCEMENT</h1>
            <p class="what_text">
                5 étapes simples et transparentes pour transformer votre projet en réalité
            </p>
        </div>
<div class="what_we_do_section_2">
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
                {{-- Étape 1 --}}
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
                            <div class="carousel-visual">
                                @if(file_exists(public_path('images/step-1.png')))
                                    <img src="{{ asset('images/step-1.png') }}" class="img-fluid" alt="Inscription BHDM">
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

                {{-- Étape 2 --}}
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
                            <div class="carousel-visual">
                                @if(file_exists(public_path('images/step-2.png')))
                                    <img src="{{ asset('images/step-2.png') }}" class="img-fluid" alt="Soumission de projet">
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

                {{-- Étape 3 --}}
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
                            <div class="carousel-visual">
                                @if(file_exists(public_path('images/step-3.png')))
                                    <img src="{{ asset('images/step-3.png') }}" class="img-fluid" alt="Évaluation du projet">
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

                {{-- Étape 4 --}}
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
                            <div class="carousel-visual">
                                @if(file_exists(public_path('images/step-4.png')))
                                    <img src="{{ asset('images/step-4.png') }}" class="img-fluid" alt="Signature et financement">
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

                {{-- Étape 5 --}}
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
                            <div class="carousel-visual">
                                @if(file_exists(public_path('images/step-5.png')))
                                    <img src="{{ asset('images/step-5.png') }}" class="img-fluid" alt="Suivi et impact">
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

        {{-- SECTION BOUTON "COMMENCER MON PARCOURS" PARFAITEMENT CENTRÉ --}}
        <div class="start-journey-wrapper text-center mt-5">
            <div class="start-journey-btn-container">
                <a href="{{ route('register') }}" class="start-journey-btn" id="startJourneyBtn">
                    <span class="btn-text">Commencer mon parcours</span>
                    <span class="btn-icon">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

{{-- SECTION : GARANTIES INSTITUTIONNELLES --}}
<div class="what_we_do_section">
    <div class="container">
        <div class="text-center mb-3">
            <h1 class="what_taital">Nos Garanties Institutionnelles</h1>
            <p class="what_text">
                Transparence et sécurité totale pour tous nos bénéficiaires
            </p>
        </div>

        <div class="what_we_do_section_2">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Sécurité des Fonds</h3>
                        <p class="lorem_text">
                            Tous les fonds sont déposés dans des banques partenaires de rang A avec garantie de l'État.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Conformité Réglementaire</h3>
                        <p class="lorem_text">
                            Respect strict des réglementations bancaires internationales et normes de lutte anti-blanchiment.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Partenariats Stratégiques</h3>
                        <p class="lorem_text">
                            Collaboration avec institutions financières internationales et banques centrales africaines.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : IMPACT EN CHIFFRES --}}
<div class="project_section_2">
    <div class="container">
        <div class="text-center mb-3">
            <h1 class="what_taital">Notre Impact en Chiffres</h1>
            <p class="what_text">
                Des résultats concrets qui changent des vies en Afrique
            </p>
        </div>

        <div class="row text-center g-3">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-history fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">75 ans</h3>
                    <p class="yers_text">D'expérience depuis 1948</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="impact_box">
                    <i class="fas fa-money-bill-wave fa-2x mb-2" style="color: #ffffff;"></i>
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
                    <i class="fas fa-map-marker-alt fa-2x mb-2" style="color: #ffffff;"></i>
                    <h3 class="accounting_text_1">15 pays</h3>
                    <p class="yers_text">Afrique de l'Ouest et du Centre</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : APPEL À L'ACTION --}}
<div class="client_section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h1 class="what_taital mb-2">Prêt à transformer votre projet ?</h1>
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
    /* ========== STYLES POUR LA SECTION HISTOIRE ========== */
    #history-section {
        padding: 60px 0 40px 0;
        background-color: #f8fafc;
    }




    #history-section .services_taital::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 3px;

        border-radius: 2px;
    }

    #history-section .services_text {
        font-size: 16px;
        line-height: 1.7;
        color: #555;
        margin-bottom: 15px;
    }

    /* Conteneur image */
    .history-image-container {
        position: relative;
        padding: 15px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Image avec ratio 4:3 */
    .history-hero-img {
        width: 100%;
        max-width: 450px;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        object-fit: cover;
        aspect-ratio: 4/3;
    }

    .history-hero-img:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    /* Placeholder stylisé */
    .history-placeholder {
        width: 100%;
        max-width: 450px;
        height: 338px; /* 450 * 0.75 pour maintenir ratio 4:3 */
        background: linear-gradient(135deg, #1b5a8d 0%, #2a5298 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(27, 90, 141, 0.2);
    }

    .placeholder-content {
        text-align: center;
        color: white;
        padding: 30px;
    }

    .placeholder-content i {
        opacity: 0.9;
    }

    .placeholder-content h3 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .placeholder-content p {
        font-size: 16px;
        opacity: 0.9;
    }

    /* Bouton section histoire */
    #history-section  {
        display: inline-block;
        margin-top: 10px;
    }

    #history-section  a {
        background: linear-gradient(135deg, #1b5a8d 0%, #2a5298 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(27, 90, 141, 0.2);
    }

    #history-section  a:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(27, 90, 141, 0.3);
        background: linear-gradient(135deg, #2a5298 0%, #1b5a8d 100%);
        color: white;
        text-decoration: none;
    }

    /* ========== CORRECTION CONFLIT TITRE CARROUSEL ========== */
    #process .what_taital {
        font-size: 26px !important;
        text-align: center !important;
        text-transform: uppercase !important;
        font-weight: bold !important;
        margin-bottom: 10px !important;
        width: auto !important;
        float: none !important;
        padding-top: 0 !important;
        position: relative;
        display: block;
    }
    #process .what_taital::after {
        display: none !important;
    }
    #process .what_text {
        font-size: 16px !important;
        color: #666 !important;
        text-align: center !important;
        margin-bottom: 20px !important;
        width: auto !important;
        float: none !important;
        margin-top: 0 !important;
        margin-left: 0 !important;
        display: block;
    }

    /* Réduction des espacements entre sections */
    .what_we_do_section,
    .project_section,
    .client_section {
        padding: 40px 0 !important;
    }

    .project_section_2 {
        padding: 40px 0 !important;
    }

    .project_section.pt-0 {
        padding-top: 20px !important;
    }

    /* Ajustement des espacements internes */
    .what_we_do_section_2 {
        margin-top: 15px;
    }

    /* Ajustement des marges des titres */
    .what_taital {
        margin-bottom: 10px !important;
        font-size: 26px;
    }

    .what_text {
        margin-bottom: 20px !important;
        font-size: 16px;
    }

    /* ============================================
       CARROUSEL STYLES (IDENTIQUES AU CODE EXISTANT)
    ============================================= */
    #processCarousel {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-top: 15px;
    }

    #processCarousel .carousel-inner {
        padding: 0 40px;
    }

    #processCarousel .carousel-item {
        transition: transform 0.6s ease-in-out !important;
    }

    /* Indicateurs du carrousel */
    #processCarousel .carousel-indicators {
        bottom: -40px !important;
    }

    #processCarousel .carousel-indicators button {
        width: 12px !important;
        height: 12px !important;
        border-radius: 50% !important;
        background-color: #1b5a8d !important;
        opacity: 0.5 !important;
        margin: 0 5px !important;
        transition: all 0.3s ease !important;
    }

    #processCarousel .carousel-indicators button.active {
        opacity: 1 !important;
        transform: scale(1.2) !important;
    }

    #processCarousel .carousel-indicators button:hover {
        opacity: 0.8 !important;
    }

    /* ========== BOUTONS DU CARROUSEL (MÊME STYLE QUE LES AUTRES BOUTONS) ========== */
    #processCarousel .carousel-control-prev,
    #processCarousel .carousel-control-next {
        width: 50px !important;
        height: 50px !important;
        background-color: #1b5a8d !important;
        border-radius: 50% !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        opacity: 1 !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(27, 90, 141, 0.2) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 2px solid transparent !important;
    }

    #processCarousel .carousel-control-prev {
        left: -25px !important;
    }

    #processCarousel .carousel-control-next {
        right: -25px !important;
    }

    #processCarousel .carousel-control-prev:hover,
    #processCarousel .carousel-control-next:hover {
        background-color: #2a5298 !important;
        transform: translateY(-50%) scale(1.1) !important;
        box-shadow: 0 6px 20px rgba(27, 90, 141, 0.3) !important;
        opacity: 1 !important;
        border-color: #1b5a8d !important;
    }

    #processCarousel .carousel-control-prev-icon,
    #processCarousel .carousel-control-next-icon {
        width: 20px !important;
        height: 20px !important;
        background-size: 100% 100% !important;
        background-position: center !important;
        filter: brightness(0) invert(1) !important;
        transition: all 0.3s ease !important;
    }

    /* Animation spécifique au hover */
    #processCarousel .carousel-control-prev::before,
    #processCarousel .carousel-control-next::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: transparent;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    #processCarousel .carousel-control-prev:hover::before,
    #processCarousel .carousel-control-next:hover::before {
        border-color: rgba(255, 255, 255, 0.3);
        width: calc(100% + 8px);
        height: calc(100% + 8px);
    }

    /* Animation de transition fluide */
    #processCarousel .carousel-item {
        transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out !important;
    }

    #processCarousel .carousel-item-next:not(.carousel-item-start),
    #processCarousel .active.carousel-item-end {
        transform: translateX(100%) !important;
    }

    #processCarousel .carousel-item-prev:not(.carousel-item-end),
    #processCarousel .active.carousel-item-start {
        transform: translateX(-100%) !important;
    }

    /* ========== BOUTON "COMMENCER MON PARCOURS" PARFAITEMENT CENTRÉ ========== */
    .start-journey-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 0 20px 0;
    }

    .start-journey-btn-container {
        display: inline-block;
        text-align: center;
    }

    .start-journey-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff5a58 0%, #ff8a8a 100%);
        color: white;
        padding: 16px 40px;
        border-radius: 50px;
        font-size: 18px;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 6px 20px rgba(255, 90, 88, 0.3);
        position: relative;
        overflow: hidden;
        min-width: 280px;
        max-width: 100%;
        white-space: nowrap;
        z-index: 1;
    }

    /* Texte du bouton */
    .start-journey-btn .btn-text {
        display: inline-block;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    /* Icône du bouton */
    .start-journey-btn .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        transform: translateX(0);
    }

    /* Effet de survol - Jeu de couleurs et animation */
    .start-journey-btn:hover {
        background: linear-gradient(135deg, #4aafff 0%, #88c6f8 100%);
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(74, 175, 255, 0.4);
        color: white;
    }

    .start-journey-btn:hover .btn-text {
        transform: translateX(-5px);
    }

    .start-journey-btn:hover .btn-icon {
        transform: translateX(5px);
    }

    /* Animation au clic */
    .start-journey-btn:active {
        transform: translateY(-1px) scale(0.98);
        box-shadow: 0 4px 15px rgba(255, 90, 88, 0.5);
        transition: all 0.1s ease;
    }

    /* Effet de pulsation au survol */
    .start-journey-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
        z-index: -1;
    }

    .start-journey-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    /* Animation de clic */
    @keyframes clickAnimation {
        0% {
            transform: translateY(-4px) scale(1);
            box-shadow: 0 12px 25px rgba(74, 175, 255, 0.4);
        }
        50% {
            transform: translateY(-4px) scale(0.95);
            box-shadow: 0 8px 15px rgba(74, 175, 255, 0.6);
        }
        100% {
            transform: translateY(-4px) scale(1);
            box-shadow: 0 12px 25px rgba(74, 175, 255, 0.4);
        }
    }

    /* Effet de vague au clic */
    .start-journey-btn.clicked {
        animation: clickAnimation 0.3s ease;
    }

    /* Contenu du carrousel */
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
        transition: transform 0.3s ease;
    }

    .step-number-badge:hover {
        transform: rotate(5deg) scale(1.05);
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
        transition: transform 0.3s ease;
    }

    .step-features li:hover {
        transform: translateX(5px);
    }

    .step-features li i {
        color: #1b5a8d;
        margin-right: 10px;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .step-features li:hover i {
        color: #2a5298;
    }

    .mission-details {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .mission-details:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .mission {
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .mission:hover {
        transform: translateX(5px);
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
        transition: transform 0.3s ease;
    }

    .mission:hover h5 i {
        transform: scale(1.2);
    }

    .mission p {
        color: #666;
        font-size: 14px;
        margin: 0;
        padding-left: 26px;
    }

    .carousel-visual {
        padding: 15px;
        text-align: center;
    }

    .carousel-visual img {
        max-height: 300px;
        width: auto;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.5s ease;
    }

    .carousel-visual img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .carousel-placeholder {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 8px;
        padding: 40px 20px;
        color: #1b5a8d;
        text-align: center;
        height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
    }

    .carousel-placeholder:hover {
        background: linear-gradient(135deg, #e9ecef, #f8f9fa);
        transform: translateY(-5px);
    }

    .carousel-placeholder i {
        margin-bottom: 15px;
        transition: transform 0.5s ease;
    }

    .carousel-placeholder:hover i {
        transform: rotate(360deg);
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

    /* ============================================
       BOUTONS PARFAITEMENT ALIGNÉS (IDENTIQUES AU CODE EXISTANT)
    ============================================= */
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



    /* Conteneurs de boutons */
    .btn_main {
        display: flex !important;
        gap: 30px !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        align-items: center !important;
    }

    .more_bt, .contact_bt {
        flex: 0 0 auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Alignement parfait des boutons côte à côte */
    .more_bt .btn-link,
    .contact_bt .btn-link,
    .btn-link {
        height: 50px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Section CTA boutons */
    .client_section .d-flex {
        gap: 110px !important;
    }


    /* Ajustement des espacements entre éléments */
    .mb-4 {
        margin-bottom: 1rem !important;
    }

    .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    /* Uniformiser les cartes avec la couleur principale */
    .what_we_do_section .box_main {
        background-color: #1b5a8d !important;
        color: #ffffff !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        padding: 20px 15px !important;
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
    }

    .lorem_text {
        font-size: 14px !important;
        line-height: 1.4 !important;
    }

    /* Section Impact en chiffres */
    .impact_box {
        padding: 20px 10px;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .impact_box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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

    /* ========== ANIMATIONS (IDENTIQUES AU CODE EXISTANT) ========== */
    #processCarousel .carousel-item,
    .carousel-item {
        transition: transform 0.6s ease-in-out !important;
    }

    .box_main, .impact_box {
        transition: all 0.3s ease !important;
    }

    .box_main:hover {
        transform: translateY(-3px) !important;
    }

    /* Smooth loading */
    .services_section,.what_we_do_section, .project_section_2, .client_section {
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
    .client_section { animation-delay: 0.3s; }

    /* Animation pour les boutons du carrousel au chargement */
    @keyframes pulse {
        0% { transform: translateY(-50%) scale(1); }
        50% { transform: translateY(-50%) scale(1.05); }
        100% { transform: translateY(-50%) scale(1); }
    }

    #processCarousel .carousel-control-prev,
    #processCarousel .carousel-control-next {
        animation: pulse 2s infinite;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 992px) {
        #history-section .services_taital {
            font-size: 24px;
        }

        .history-hero-img {
            max-width: 400px;
        }

        .history-placeholder {
            max-width: 400px;
            height: 300px;
        }

        #processCarousel .carousel-inner {
            padding: 0 20px;
        }

        #processCarousel .carousel-control-prev,
        #processCarousel .carousel-control-next {
            width: 45px !important;
            height: 45px !important;
        }

        #processCarousel .carousel-control-prev {
            left: -15px !important;
        }

        #processCarousel .carousel-control-next {
            right: -15px !important;
        }

        .start-journey-btn {
            padding: 14px 35px;
            font-size: 16px;
            min-width: 250px;
        }
    }

    @media (max-width: 768px) {
        #history-section {
            padding: 40px 0 30px 0;
        }

        .history-image-container {
            margin-top: 30px;
            padding: 0 15px;
        }

        #history-section .services_taital {
            text-align: center;
            font-size: 22px;
        }

        #history-section .services_taital::after {
            left: 50%;
            transform: translateX(-50%);
        }

        #history-section .services_text {
            text-align: center;
        }

        #history-section  {
            text-align: center;
            display: block;
        }

        .history-hero-img {
            max-width: 100%;
        }

        .history-placeholder {
            max-width: 100%;
            height: 250px;
        }
.services_section,
        .what_we_do_section,
        .project_section,
        .client_section,
        .project_section_2 {
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

        .step-title {
            font-size: 20px;
        }

        .step-description {
            font-size: 14px;
        }

        /* Carrousel responsive */
        #processCarousel .carousel-control-prev {
            left: -10px !important;
        }

        #processCarousel .carousel-control-next {
            right: -10px !important;
        }

        #processCarousel .carousel-indicators {
            bottom: -35px !important;
        }

        #processCarousel .carousel-control-prev,
        #processCarousel .carousel-control-next {
            width: 40px !important;
            height: 40px !important;
        }

        #processCarousel .carousel-control-prev-icon,
        #processCarousel .carousel-control-next-icon {
            width: 18px !important;
            height: 18px !important;
        }

        /* Bouton "Commencer mon parcours" responsive */
        .start-journey-btn {
            padding: 12px 30px;
            font-size: 15px;
            min-width: 220px;
        }

        .start-journey-btn .btn-text {
            margin-right: 8px;
        }

        /* Boutons responsive */
        .more_bt,
        .contact_bt,
       {
            min-width: 200px;
            width: 100% !important;
            max-width: 250px !important;
        }

        .more_bt a,
        .contact_bt a,
         {
            padding: 10px 15px !important;
            font-size: 14px !important;
            width: 100% !important;
        }

        .client_section .d-flex {
            gap: 15px !important;
            flex-direction: column !important;
            width: 100% !important;
        }

        .accounting_text_1 {
            font-size: 22px !important;
        }

        .yers_text {
            font-size: 13px !important;
        }
    }

    @media (max-width: 576px) {
        #history-section .services_taital {
            font-size: 20px;
        }

        #history-section .services_text {
            font-size: 15px;
        }

        .history-placeholder {
            height: 200px;
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

        .step-number-badge {
            width: 50px;
            height: 50px;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .step-title {
            font-size: 18px;
        }

        /* Bouton "Commencer mon parcours" mobile */
        .start-journey-btn {
            padding: 10px 25px;
            font-size: 14px;
            min-width: 200px;
            border-radius: 40px;
        }

        .start-journey-btn .btn-text {
            margin-right: 6px;
        }

        .start-journey-btn .btn-icon {
            font-size: 12px;
        }

        .more_bt,
        .contact_bt,
        {
            min-width: 180px;
            width: 100%;
            max-width: 250px;
        }

        .more_bt a,
        .contact_bt a,
         {
            width: 100%;
        }

        #processCarousel .carousel-control-prev,
        #processCarousel .carousel-control-next {
            width: 35px !important;
            height: 35px !important;
        }

        #processCarousel .carousel-control-prev-icon,
        #processCarousel .carousel-control-next-icon {
            width: 16px !important;
            height: 16px !important;
        }

        #processCarousel .carousel-control-prev {
            left: -5px !important;
        }

        #processCarousel .carousel-control-next {
            right: -5px !important;
        }

        #processCarousel .carousel-indicators button {
            width: 10px !important;
            height: 10px !important;
        }

        /* Ajustement du wrapper du bouton */
        .start-journey-wrapper {
            padding: 25px 0 15px 0;
        }
    }

    @media (max-width: 400px) {
        .start-journey-btn {
            min-width: 180px;
            padding: 8px 20px;
            font-size: 13px;
        }

        .start-journey-btn .btn-text {
            white-space: normal;
            line-height: 1.3;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation automatique du carrousel
    const processCarousel = document.getElementById('processCarousel');
    if (processCarousel) {
        const carousel = new bootstrap.Carousel(processCarousel, {
            interval: 5000, // Changement automatique toutes les 5 secondes
            wrap: true,
            pause: 'hover',
            touch: true
        });

        // Ajout d'une animation fluide
        processCarousel.addEventListener('slide.bs.carousel', function(event) {
            const slides = this.querySelectorAll('.carousel-item');
            slides.forEach(slide => {
                slide.style.transition = 'transform 0.6s ease-in-out, opacity 0.6s ease-in-out';
            });

            // Animation pour le badge de l'étape
            const activeSlide = event.relatedTarget;
            const stepBadge = activeSlide.querySelector('.step-number-badge');
            if (stepBadge) {
                stepBadge.style.animation = 'none';
                setTimeout(() => {
                    stepBadge.style.animation = 'bounce 0.6s ease';
                }, 10);
            }
        });

        // Animation au chargement initial
        setTimeout(() => {
            const initialBadge = processCarousel.querySelector('.carousel-item.active .step-number-badge');
            if (initialBadge) {
                initialBadge.style.animation = 'bounce 0.6s ease';
            }
        }, 500);
    }

    // Animation pour le bouton "Commencer mon parcours"
    const startJourneyBtn = document.getElementById('startJourneyBtn');
    if (startJourneyBtn) {
        // Animation au clic
        startJourneyBtn.addEventListener('click', function(e) {
            // Ajout de la classe pour l'animation de clic
            this.classList.add('clicked');

            // Retrait de la classe après l'animation
            setTimeout(() => {
                this.classList.remove('clicked');
            }, 300);

            // Animation de l'icône
            const icon = this.querySelector('.btn-icon i');
            if (icon) {
                icon.style.transition = 'transform 0.3s ease';
                icon.style.transform = 'translateX(10px)';

                setTimeout(() => {
                    icon.style.transform = 'translateX(0)';
                }, 300);
            }
        });

        // Animation au survol
        startJourneyBtn.addEventListener('mouseenter', function() {
            const text = this.querySelector('.btn-text');
            const icon = this.querySelector('.btn-icon i');

            if (text && icon) {
                text.style.transition = 'transform 0.3s ease';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
    }

    // Animation pour les sections (comme dans le code existant)
    const sections = document.querySelectorAll('.what_we_do_section, .project_section_2, .client_section');

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

    // Animation CSS pour le bounce du badge
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0) rotate(0);}
            40% {transform: translateY(-10px) rotate(5deg);}
            60% {transform: translateY(-5px) rotate(-5deg);}
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .carousel-step-content {
            animation: slideIn 0.5s ease-out;
        }
    `;
    document.head.appendChild(style);
});
</script>

@endsection
