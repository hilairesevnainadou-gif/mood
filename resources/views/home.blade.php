@extends('layouts.app')

@section('title', 'Accueil - BHDM')

@section('content')

{{-- SECTION : PRÉSENTATION --}}
<div class="services_section layout_padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7 mb-4 mb-md-0">
                <h1 class="services_taital mb-2">De l'Héritage Colonial à l'Impact Humanitaire</h1>
                <p class="services_text mb-2">
                    La <strong>Banque Humanitaire pour le Développement Mondial (BHDM)</strong> est un programme d'impact
                    du British International Investment (BII), né d'une transformation historique du Colonial
                    Development Corporation (1948).
                </p>
                <p class="services_text mb-3">
                    En 2023, le BII a créé la BHDM comme instrument financier inédit avec un mandat humanitaire clair :
                    lutter contre la pauvreté par l'autonomisation économique directe, doté d'un fonds rotatif de
                    <strong>50 milliards FCFA</strong>.
                </p>
                <div class="moremore_bt">
                    <a href="{{ route('about') }}" class="btn-link">Découvrir notre histoire</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 text-center">
                <img src="{{ asset('images/banner-img5.png') }}" class="img-fluid" alt="Évolution BHDM" style="max-height: 250px; width: auto;">
            </div>
        </div>
    </div>
</div>

{{-- SECTION : NOTRE MODÈLE INNOVANT --}}
<div class="what_we_do_section layout_padding">
    <div class="container">
        <div class="text-center mb-3">
            <h1 class="what_taital">Notre Modèle de Financement Rotatif</h1>
            <p class="what_text">
                Un système durable où le micro-crédit productif devient une aide humanitaire durable
            </p>
        </div>

        <div class="what_we_do_section_2">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100 active">
                        <div class="icon_1">
                            <img src="{{ asset('images/icon-1.png') }}" alt="Fonds Initial">
                        </div>
                        <h3 class="accounting_text">Fonds Initial BII</h3>
                        <p class="lorem_text">
                            50 milliards FCFA de fonds de démarrage pour l'autonomisation économique
                            directe des entrepreneurs à la base.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <img src="{{ asset('images/icon-2.png') }}" alt="TPS Solidaire">
                        </div>
                        <h3 class="accounting_text">TPS Solidaire</h3>
                        <p class="lorem_text">
                            Le Taux de Participation Solidaire (1-10%) remplace les intérêts traditionnels
                            et est recyclé dans le fonds pour financer de nouveaux bénéficiaires.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <img src="{{ asset('images/icon-3.png') }}" alt="Cycle Vertueux">
                        </div>
                        <h3 class="accounting_text">Cycle Vertueux</h3>
                        <p class="lorem_text">
                            Chaque remboursement (capital + TPS) régénère le fonds, créant un impact
                            multiplicateur et une entraide systémique.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : NOTRE PROCESSUS --}}
<div class="project_section layout_padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="project_main">
                    <h1 class="services_taital mb-2">Notre Processus Opérationnel</h1>
                    <p class="services_text mb-2">
                        <strong>Phase 1 : Inscription & Immersion</strong><br>
                        Création de compte et accès à l'Espace Membre avec tableau de bord personnel.
                    </p>
                    <p class="services_text mb-2">
                        <strong>Phase 2 : Construction & Soumission</strong><br>
                        Formation obligatoire "Les 5 Clés d'une Activité Viable" puis formalisation du projet.
                    </p>
                    <p class="services_text mb-3">
                        <strong>Phase 3 : Étude & Décision</strong><br>
                        Analyse par notre Comité Local d'Impact et calcul du TPS personnalisé.
                    </p>
                    <div class="moremore_bt">
                        <a href="{{ route('about') }}#process" class="btn-link">Voir les 5 étapes complètes</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 order-lg-2 order-1 mb-4 mb-lg-0">
                <div class="how_it_works_image">
                    <img src="{{ asset('images/img-18.png') }}" class="img-fluid rounded shadow" alt="Processus BHDM">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : GOUVERNANCE --}}
<div class="impact_gallery layout_padding">
    <div class="container">
        <div class="text-center mb-3">
            <h1 class="what_taital">Notre Gouvernance à 3 Niveaux</h1>
            <p class="what_text">
                Une structure garantissant transparence et ancrage local
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="impact_card">
                    <div class="impact_card_content">
                        <h4><i class="fas fa-globe-africa"></i> Conseil d'Orientation Stratégique</h4>
                        <p>Composé de membres du BII, d'experts en développement humanitaire et de représentants de la société civile africaine. Valide la stratégie et l'allocation des fonds.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="impact_card">
                    <div class="impact_card_content">
                        <h4><i class="fas fa-users"></i> Comités Locaux d'Impact</h4>
                        <p>Experts locaux (économistes, chefs d'entreprise, représentants associatifs) dans chaque pays d'intervention. Pouvoir décisif sur la sélection finale des projets.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="impact_card">
                    <div class="impact_card_content">
                        <h4><i class="fas fa-cogs"></i> Secrétariat Technique</h4>
                        <p>Équipe opérationnelle gérant la plateforme, l'instruction administrative des dossiers et le suivi des partenaires (IMF, ONG).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : IMPACT EN CHIFFRES --}}
<div class="project_section_2 layout_padding">
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
<div class="client_section layout_padding">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <h1 class="what_taital mb-2">Rejoignez la plateforme BHDM</h1>
                    <p class="dummy_text mx-auto mb-3" style="max-width: 700px;">
                        Créez votre portefeuille dynamique dès aujourd'hui et bénéficiez d'un accompagnement
                        financier structuré, sécurisé et transparent.
                    </p>
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-110">
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
    /* ========== STYLES POUR TOUS LES BOUTONS ========== */
    /* Style de base pour TOUS les boutons .btn-link */
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

    /* Boutons bleu clair (.moremore_bt) */
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

    /* Boutons rouges (.more_bt) */
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

    /* Boutons bleus (.contact_bt) */
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
    .more_bt, .contact_bt, .moremore_bt {
        min-width: 220px;
        white-space: nowrap;
        display: inline-block;
    }

    /* Alignement parfait des boutons côte à côte */
    .more_bt a.btn-link,
    .contact_bt a.btn-link,
    .moremore_bt a.btn-link {
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

    /* Espacement entre boutons */
    .gap-110 {
        gap: 110px !important;
    }

    /* ========== STYLES EXISTANTS DE LA PAGE ========== */
    /* Réduction des espacements */
    .layout_padding {
        padding: 40px 0 !important;
    }

    /* Ajustement des marges */
    .services_taital {
        margin-bottom: 10px !important;
    }

    .what_taital {
        margin-bottom: 10px !important;
    }

    .what_text {
        margin-bottom: 20px !important;
    }

    .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .mb-4 {
        margin-bottom: 1rem !important;
    }

    /* Uniformiser les cartes avec la couleur principale */
    .what_we_do_section .box_main {
        background-color: #1b5a8d !important;
        color: #ffffff !important;
        padding: 20px 15px !important;
    }

    .what_we_do_section .box_main.active {
        background-color: #ff5a58 !important;
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

    /* Section Gouvernance */
    .impact_gallery {
        background-color: #f8f9fa;
    }

    .impact_card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .impact_card_content h4 {
        color: #1b5a8d;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .impact_card_content h4 i {
        margin-right: 10px;
    }

    /* Section Impact en chiffres */
    .impact_box {
        padding: 20px 10px;
        color: #ffffff;
    }

    .impact_box i {
        display: block;
        margin: 0 auto 10px;
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
    @media (max-width: 768px) {
        .layout_padding {
            padding: 30px 0 !important;
        }

        .services_taital,
        .what_taital {
            font-size: 22px;
        }

        .what_text,
        .services_text {
            font-size: 14px;
        }

        .more_bt,
        .contact_bt,
        .moremore_bt {
            min-width: 200px;
        }

        .more_bt a.btn-link,
        .contact_bt a.btn-link,
        .moremore_bt a.btn-link {
            padding: 10px 15px !important;
            font-size: 14px !important;
        }

        .client_section .d-flex {
            gap: 15px !important;
            flex-direction: column !important;
            width: 100% !important;
        }

        .accounting_text_1 {
            font-size: 22px !important;
        }
    }

    @media (max-width: 576px) {
        .services_taital,
        .what_taital {
            font-size: 20px;
        }

        .what_text,
        .services_text {
            font-size: 13px;
        }

        .more_bt,
        .contact_bt,
        .moremore_bt {
            min-width: 180px;
            width: 100%;
            max-width: 250px;
        }
    }
</style>
@endsection
