@extends('layouts.app')

@section('title', 'Contact - BHDM')

@section('content')

{{-- SECTION : CONTACT HERO --}}
<div class="services_section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6 mb-4 mb-md-0">
                <h1 class="services_taital mb-3">Contactez la BHDM</h1>
                <p class="services_text mb-3">
                    Vous avez des questions sur notre programme de financement ? Vous souhaitez en savoir plus sur nos services
                    ou obtenir un accompagnement personnalisé ? Notre équipe est à votre écoute.
                </p>
                <p class="services_text mb-4">
                    Nous nous engageons à répondre à toutes vos demandes dans les <strong>24 heures ouvrées</strong>.
                    Utilisez le formulaire ou contactez-nous directement par les moyens indiqués ci-dessous.
                </p>
                <div class="moremore_bt">
                    <a href="#contact-form" class="btn-link">Écrire un message</a>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="contact-image-container">
                    @if(file_exists(public_path('images/contact-hero.png')))
                        <img src="{{ asset('images/contact-hero.png') }}" class="img-fluid contact-hero-img" alt="Contact BHDM">
                    @else
                        <div class="contact-placeholder">
                            <div class="placeholder-content">
                                <i class="fas fa-headset fa-3x mb-3"></i>
                                <h3 class="mb-2">Contactez-nous</h3>
                                <p class="mb-0">Notre équipe vous répond</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : INFORMATIONS DE CONTACT --}}
<div class="what_we_do_section">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital">Nos Coordonnées</h1>
            <p class="what_text">
                Plusieurs moyens pour nous contacter selon votre besoin
            </p>
        </div>

        <div class="what_we_do_section_2">
            <div class="row">
                {{-- Adresse --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100 active">
                        <div class="icon_1">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Siège Social</h3>
                        <p class="lorem_text">
                            Immeuble BII - Plateau<br>
                            01 BP 1234 Abidjan 01<br>
                            Côte d'Ivoire
                        </p>
                        <div class="moremore_bt_1">
                            <a href="https://maps.google.com/?q=Plateau+Abidjan+Cote+d'Ivoire" target="_blank">
                                Voir sur la carte
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Téléphone --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-phone-alt fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Téléphone</h3>
                        <p class="lorem_text">
                            <strong>Support technique :</strong><br>
                            +225 27 20 21 22 23
                        </p>
                        <p class="lorem_text">
                            <strong>Service commercial :</strong><br>
                            +225 27 20 21 22 24
                        </p>
                        <div class="moremore_bt_1 mt-3">
                            <a href="tel:+2252720212223">
                                Appeler maintenant
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Email</h3>
                        <p class="lorem_text">
                            <strong>Support :</strong><br>
                            support@bhdm.org
                        </p>
                        <p class="lorem_text">
                            <strong>Commercial :</strong><br>
                            commercial@bhdm.org
                        </p>
                        <div class="moremore_bt_1 mt-3">
                            <a href="mailto:support@bhdm.org">
                                Envoyer un email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : FORMULAIRE DE CONTACT --}}
<div class="contact_section layout_padding" id="contact-form">
    <div class="container-fluid">
        <div class="text-center mb-5">
            <h1 class="what_taital">Écrivez-nous un message</h1>
            <p class="amet_text">
                Remplissez ce formulaire pour nous contacter directement. Nous vous répondrons dans les plus brefs délais.
            </p>
        </div>

        <div class="contact_section2">
            <div class="row">
                <div class="col-md-6 padding_left_0">
                    <div class="mail_section">
                        <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                            @csrf

                            <input type="text" class="mail_text_1" placeholder="Nom complet *" name="name" required>
                            <input type="tel" class="mail_text_1" placeholder="Téléphone" name="phone">
                            <input type="email" class="mail_text_1" placeholder="Adresse email *" name="email" required>

                            <select class="mail_text_1" name="subject" required style="
                                background-color: transparent;
                                border: none;
                                border-bottom: 1px solid #050000;
                                color: #666666;
                                font-size: 18px;
                                padding: 20px 0;
                                width: 100%;
                                appearance: none;
                                background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 16 16\"><path fill=\"none\" stroke=\"%23666666\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"m2 5 6 6 6-6\"/></svg>');
                                background-repeat: no-repeat;
                                background-position: right center;
                                background-size: 16px 12px;
                            ">
                                <option value="">Sélectionnez un sujet *</option>
                                <option value="information">Demande d'information</option>
                                <option value="support">Support technique</option>
                                <option value="partnership">Partenariat</option>
                                <option value="complaint">Réclamation</option>
                                <option value="other">Autre</option>
                            </select>

                            <textarea class="massage_text" placeholder="Votre message *" rows="5" name="message" required></textarea>

                            <div class="send_bt">
                                <button type="submit" class="btn-send">
                                    <span class="btn-text">Envoyer le message</span>
                                    <span class="btn-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6 padding_0">
                    <div class="contact-visual">
                        @if(file_exists(public_path('images/contact-form.png')))
                            <img src="{{ asset('images/contact-form.png') }}" class="img-fluid" alt="Formulaire de contact">
                        @else
                            <div class="contact-form-placeholder">
                                <i class="fas fa-comments fa-4x mb-3"></i>
                                <h4>Écrivez-nous</h4>
                                <p>Nous sommes à votre écoute</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : FAQ --}}
<div class="what_we_do_section">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital">Questions Fréquentes</h1>
            <p class="what_text">
                Trouvez rapidement des réponses à vos interrogations
            </p>
        </div>

        <div class="what_we_do_section_2">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Délai de réponse</h3>
                        <p class="lorem_text">
                            Notre comité local d'impact étudie chaque projet dans un délai de <strong>5 à 10 jours ouvrés</strong>. Vous recevrez une notification par email dès qu'une décision est prise.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Documents nécessaires</h3>
                        <p class="lorem_text">
                            Vous aurez besoin de : pièce d'identité valide, justificatif de domicile, attestation fiscale, et le formulaire "Cœur de Projet" complété.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Calcul du TPS</h3>
                        <p class="lorem_text">
                            Le Taux de Participation Solidaire varie entre 1% et 10% selon la taille du projet, son secteur d'activité et son impact social.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="box_main h-100">
                        <div class="icon_1">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3 class="accounting_text">Suivi en ligne</h3>
                        <p class="lorem_text">
                            Oui, dès votre inscription, vous avez accès à un tableau de bord personnel qui vous permet de suivre en temps réel chaque étape de votre dossier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SECTION : HORAIRES --}}
<div class="project_section_2">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="what_taital" style="color: white;">Nos Horaires d'Ouverture</h1>
            <p class="what_text" style="color: white;">
                Disponible pour vous accompagner du lundi au vendredi
            </p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="box_main" style="background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="icon_1">
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                            <h4 class="accounting_text">Lundi - Vendredi</h4>
                            <p class="lorem_text">
                                08h00 - 18h00<br>
                                <small>Accueil physique et téléphonique</small>
                            </p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="icon_1">
                                <i class="fas fa-calendar-alt fa-2x"></i>
                            </div>
                            <h4 class="accounting_text">Samedi</h4>
                            <p class="lorem_text">
                                09h00 - 13h00<br>
                                <small>Support téléphonique uniquement</small>
                            </p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="icon_1">
                                <i class="fas fa-calendar-times fa-2x"></i>
                            </div>
                            <h4 class="accounting_text">Dimanche</h4>
                            <p class="lorem_text">
                                Fermé<br>
                                <small>Support email uniquement</small>
                            </p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="lorem_text">
                            <i class="fas fa-info-circle mr-2"></i> Notre plateforme en ligne est accessible 24h/24, 7j/7.
                        </p>
                    </div>
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
                    <h1 class="what_taital mb-2">Prêt à nous contacter ?</h1>
                    <p class="dummy_text mx-auto mb-3" style="max-width: 700px;">
                        Notre équipe est disponible pour répondre à toutes vos questions et vous accompagner dans votre projet.
                        Contactez-nous par téléphone, email ou via le formulaire ci-dessus.
                    </p>
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-5">
                        <div class="more_bt">
                            <a href="{{ route('register') }}" class="btn-link">Créer mon compte</a>
                        </div>
                        <div class="contact_bt">
                            <a href="tel:+2252720212223" class="btn-link">Nous appeler</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ========== STYLES POUR LA PAGE CONTACT (16:9) ========== */

    /* Section Contact Hero */
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

    /* Conteneur image 16:9 */
    .contact-image-container {
        position: relative;
        padding: 15px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Image avec ratio 16:9 */
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

    /* Placeholder stylisé 16:9 */
    .contact-placeholder {
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 ratio */
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

    /* Contact Section Form */
    .contact_section {
        width: 100%;
        float: left;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .contact_section2 {
        width: 100%;
        float: left;
        padding-top: 30px;
    }

    /* Formulaire */
    .mail_section {
        padding-left: 50px;
        padding-right: 20px;
    }

    /* BOUTON D'ENVOI CORRIGÉ */
    .btn-send {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff5a58 0%, #ff8a8a 100%);
        color: white;
        padding: 14px 40px;
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
        width: 100%;
        height: auto;
        margin-top: 20px;
    }

    .btn-send .btn-text {
        display: inline-block;
        transition: all 0.3s ease;
        margin-right: 10px;
    }

    .btn-send .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        transform: translateX(0);
    }

    /* Effet de survol - Jeu de couleurs et animation */
    .btn-send:hover {
        background: linear-gradient(135deg, #4aafff 0%, #88c6f8 100%);
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(74, 175, 255, 0.4);
        color: white;
    }

    .btn-send:hover .btn-text {
        transform: translateX(-5px);
    }

    .btn-send:hover .btn-icon {
        transform: translateX(5px);
    }

    /* Animation au clic */
    .btn-send:active {
        transform: translateY(-1px) scale(0.98);
        box-shadow: 0 4px 15px rgba(255, 90, 88, 0.5);
        transition: all 0.1s ease;
    }

    /* Effet de pulsation au survol */
    .btn-send::before {
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

    .btn-send:hover::before {
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
    .btn-send.clicked {
        animation: clickAnimation 0.3s ease;
    }

    .mail_text_1 {
        width: 100%;
        float: left;
        font-size: 18px;
        color: #666666;
        background-color: transparent !important;
        border-bottom: 1px solid #050000 !important;
        padding-right: 20px;
        border: 0px;
        padding-top: 20px;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .massage_text {
        width: 100%;
        float: left;
        font-size: 18px;
        color: #666666;
        background-color: transparent !important;
        border-bottom: 1px solid #050000 !important;
        padding-right: 20px;
        border: 0px;
        height: 150px;
        padding-top: 20px;
        resize: vertical;
    }

    .send_bt {
        width: 100%;
        float: left;
        margin-top: 40px;
        margin-bottom: 40px;
        text-align: center;
    }

    /* Contact Visual avec ratio 16:9 */
    .contact-visual {
        width: 100%;
        float: left;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 ratio */
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }

    .contact-visual img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .contact-visual img:hover {
        transform: scale(1.05);
    }

    .contact-form-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #1b5a8d;
        text-align: center;
        padding: 20px;
    }

    .contact-form-placeholder:hover {
        background: linear-gradient(135deg, #e9ecef, #f8f9fa);
    }

    .contact-form-placeholder i {
        margin-bottom: 15px;
        transition: transform 0.5s ease;
    }

    .contact-form-placeholder:hover i {
        transform: rotate(360deg);
    }

    .contact-form-placeholder h4 {
        font-size: 18px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .contact-form-placeholder p {
        font-size: 14px;
        color: #666;
    }

    /* Sections spacing */
    .what_we_do_section,
    .contact_section,
    .client_section {
        padding: 40px 0 !important;
    }

    .project_section_2 {
        padding: 40px 0 !important;
    }

    /* Uniformiser les cartes */
    .what_we_do_section .box_main {
        background-color: #1b5a8d !important;
        color: #ffffff !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        padding: 20px 15px !important;
        height: 100%;
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

    /* Section Horaires */
    .project_section_2 .box_main {
        background-color: rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
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

        .mail_section {
            padding-left: 30px;
        }

        .btn-send {
            padding: 12px 35px;
            font-size: 16px;
            min-width: 250px;
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

        .contact_section,
        .what_we_do_section,
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

        .mail_section {
            padding-left: 15px;
            padding-right: 15px;
        }

        .padding_left_0 {
            padding-left: 0px !important;
        }

        .padding_0 {
            padding: 0px !important;
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

        .btn-send {
            padding: 10px 30px;
            font-size: 15px;
            min-width: 220px;
        }

        .client_section .d-flex {
            gap: 15px !important;
            flex-direction: column !important;
            width: 100% !important;
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

        .mail_text_1, .massage_text {
            font-size: 16px;
        }

        .btn-send {
            padding: 8px 25px;
            font-size: 14px;
            min-width: 200px;
        }

        .btn-send .btn-text {
            margin-right: 6px;
        }

        .btn-send .btn-icon {
            font-size: 12px;
        }
    }

    /* ========== ANIMATIONS ========== */
    .services_section,
    .what_we_do_section,
    .project_section_2,
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
    .client_section { animation-delay: 0.3s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire de contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');

            // Réinitialiser les bordures
            requiredFields.forEach(field => {
                field.style.borderBottom = '1px solid #050000';
            });

            // Vérifier les champs requis
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderBottom = '2px solid #ff5a58';
                    isValid = false;
                }
            });

            // Vérifier l'email
            const emailField = this.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.value)) {
                    emailField.style.borderBottom = '2px solid #ff5a58';
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();

                // Afficher un message d'erreur
                let errorDiv = this.querySelector('.error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.style.color = '#ff5a58';
                    errorDiv.style.marginTop = '10px';
                    errorDiv.style.fontSize = '14px';
                    this.querySelector('.send_bt').parentNode.insertBefore(errorDiv, this.querySelector('.send_bt'));
                }
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez remplir correctement tous les champs obligatoires (*)';

                // Scroller vers l'erreur
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

                return false;
            }

            // Si tout est valide, montrer l'animation d'envoi
            const submitBtn = this.querySelector('.btn-send');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="btn-text">Envoi en cours...</span><span class="btn-icon"><i class="fas fa-spinner fa-spin"></i></span>';
            submitBtn.disabled = true;
            submitBtn.classList.add('clicked');

            // Le formulaire sera soumis normalement via Laravel
            return true;
        });
    }

    // Animation pour les images au survol
    const images = document.querySelectorAll('.contact-hero-img, .contact-visual img');
    images.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });

        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
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

    // Animation pour le bouton d'envoi
    const sendBtn = document.querySelector('.btn-send');
    if (sendBtn) {
        sendBtn.addEventListener('mouseenter', function() {
            const text = this.querySelector('.btn-text');
            const icon = this.querySelector('.btn-icon i');

            if (text && icon) {
                text.style.transition = 'transform 0.3s ease';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        sendBtn.addEventListener('click', function() {
            this.classList.add('clicked');
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
    }

    // Vérification en temps réel pour le formulaire
    const formFields = document.querySelectorAll('#contactForm input, #contactForm textarea, #contactForm select');
    if (formFields.length > 0) {
        formFields.forEach(field => {
            field.addEventListener('input', function() {
                if (this.hasAttribute('required') && this.value.trim()) {
                    this.style.borderBottom = '2px solid #28a745';
                } else if (this.hasAttribute('required')) {
                    this.style.borderBottom = '1px solid #050000';
                }

                // Supprimer le message d'erreur s'il existe
                const errorDiv = contactForm.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.remove();
                }
            });

            field.addEventListener('change', function() {
                if (this.hasAttribute('required') && this.value.trim()) {
                    this.style.borderBottom = '2px solid #28a745';
                }
            });
        });
    }
});
</script>

@endsection
