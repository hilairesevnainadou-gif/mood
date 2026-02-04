{{-- FOOTER COMPLET BHDM --}}
<footer class="footer_section layout_padding">
    <div class="container">
        <div class="row gy-4">
            {{-- Institution --}}
            <div class="col-lg-4 col-md-6 col-sm-12">
                <h4 class="about_text">BHDM</h4>

                <p class="dolor_text mb-3">
                    Programme d'impact du <strong>British International Investment (BII)</strong>,
                    transformant l'héritage du Colonial Development Corporation (1948) en
                    financement humanitaire durable.
                </p>

                <div class="location_text">
                    <i class="fas fa-globe-africa" style="margin-right: 10px; width: 16px;"></i>
                    <span class="padding_left_15">Afrique de l'Ouest & Centrale</span>
                </div>

                <div class="location_text">
                    <i class="fas fa-envelope" style="margin-right: 10px; width: 16px;"></i>
                    <span class="padding_left_15">contact@bhdm-bii.org</span>
                </div>
            </div>

            {{-- Notre Modèle --}}
            <div class="col-lg-4 col-md-6 col-sm-12">
                <h4 class="about_text">Notre Modèle</h4>
                <ul class="footer_links">
                    <li>Fonds rotatif 50 milliards FCFA</li>
                    <li>Taux de Participation Solidaire (TPS)</li>
                    <li>Financement sans intérêt</li>
                    <li>Cycle vertueux de réinvestissement</li>
                    <li>Autonomisation économique directe</li>
                </ul>
            </div>

            {{-- Liens Rapides --}}
            <div class="col-lg-4 col-md-6 col-sm-12">
                <h4 class="about_text">Liens Rapides</h4>
                <ul class="footer_links">
                    <li><a href="{{ route('home') }}">Accueil</a></li>
                    <li><a href="{{ route('about') }}">Notre Histoire</a></li>
                    <li><a href="{{ route('about') }}#process">Notre Processus</a></li>
                    <li><a href="{{ route('services') }}">Services & Financement</a></li>
                    <li><a href="{{ route('contact') }}">Contact & Support</a></li>
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="copyright_section mt-5 pt-4 border-top">
            <div class="copyright_text text-center">
                © {{ date('Y') }} Banque Humanitaire pour le Développement Mondial (BHDM) -
                Programme du British International Investment. Tous droits réservés.
            </div>
            <div class="legal_links text-center mt-2">
                <a href="#!" id="privacy-policy-link">Politique de confidentialité</a> |
                <a href="#!" id="terms-link">Conditions d'utilisation</a> |
                <a href="#!" id="legal-mentions-link">Mentions légales</a>
            </div>
        </div>
    </div>
</footer>

<!-- Modal pour les politiques -->
<div class="modal fade policy-modal" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="policyModalLabel">Politique de confidentialité</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="privacy-content">
                    <h4 class="section-title">Politique de confidentialité BHDM</h4>
                    <p><strong>Dernière mise à jour : {{ date('d/m/Y') }}</strong></p>

                    <p class="section-title">1. Collecte des informations</p>
                    <p>La Banque Humanitaire pour le Développement Mondial (BHDM) collecte les informations suivantes :</p>
                    <ul>
                        <li>Informations personnelles (nom, prénom, adresse email, téléphone)</li>
                        <li>Informations professionnelles (secteur d'activité, taille d'entreprise, statut juridique)</li>
                        <li>Informations financières nécessaires aux demandes de financement (chiffre d'affaires, besoins)</li>
                        <li>Données de navigation sur notre site web (cookies, pages visitées)</li>
                        <li>Historique des interactions avec notre service client</li>
                    </ul>

                    <p class="section-title">2. Utilisation des informations</p>
                    <p>Les informations collectées sont utilisées exclusivement pour :</p>
                    <ul>
                        <li>Traiter et évaluer vos demandes de financement</li>
                        <li>Vous fournir des informations personnalisées sur nos services</li>
                        <li>Améliorer nos services et l'expérience utilisateur sur notre site</li>
                        <li>Respecter nos obligations légales et réglementaires (lutte contre le blanchiment)</li>
                        <li>Vous informer des opportunités de financement adaptées à votre profil</li>
                        <li>Assurer la sécurité et l'intégrité de nos systèmes</li>
                    </ul>

                    <p class="section-title">3. Protection des informations</p>
                    <p>Nous mettons en œuvre des mesures de sécurité avancées :</p>
                    <ul>
                        <li>Chiffrement SSL/TLS pour toutes les transmissions de données</li>
                        <li>Stockage sécurisé sur serveurs avec accès restreint</li>
                        <li>Audits réguliers de sécurité par des experts indépendants</li>
                        <li>Formation de notre personnel à la protection des données</li>
                        <li>Sauvegardes régulières et plan de reprise d'activité</li>
                    </ul>

                    <p class="section-title">4. Partage des informations</p>
                    <p>Nous ne partageons vos informations qu'avec :</p>
                    <ul>
                        <li>Nos partenaires financiers dans le cadre de votre demande de financement</li>
                        <li>Les autorités réglementaires lorsque requis par la loi</li>
                        <li>Nos prestataires techniques sous stricte confidentialité</li>
                        <li>Jamais à des fins commerciales ou publicitaires</li>
                    </ul>

                    <p class="section-title">5. Vos droits (RGPD & LOI 2018-07)</p>
                    <p>Conformément à la réglementation, vous disposez des droits suivants :</p>
                    <ul>
                        <li><strong>Droit d'accès :</strong> Accéder à toutes vos données personnelles</li>
                        <li><strong>Droit de rectification :</strong> Corriger les données inexactes</li>
                        <li><strong>Droit à l'effacement :</strong> Demander la suppression de vos données</li>
                        <li><strong>Droit à la limitation :</strong> Limiter le traitement de vos données</li>
                        <li><strong>Droit à la portabilité :</strong> Récupérer vos données dans un format standard</li>
                        <li><strong>Droit d'opposition :</strong> Vous opposer au traitement pour motifs légitimes</li>
                        <li><strong>Droit de retrait :</strong> Retirer votre consentement à tout moment</li>
                    </ul>

                    <p class="section-title">6. Cookies</p>
                    <p>Nous utilisons trois types de cookies :</p>
                    <ul>
                        <li><strong>Cookies essentiels :</strong> Nécessaires au fonctionnement du site</li>
                        <li><strong>Cookies analytiques :</strong> Pour améliorer nos services (Google Analytics anonymisé)</li>
                        <li><strong>Cookies de préférences :</strong> Pour mémoriser vos choix (langue, région)</li>
                    </ul>
                    <p>Vous pouvez configurer votre navigateur pour refuser les cookies non essentiels.</p>

                    <p class="section-title">7. Conservation des données</p>
                    <p>Vos données sont conservées :</p>
                    <ul>
                        <li><strong>Demandes de financement :</strong> 10 ans après la dernière interaction</li>
                        <li><strong>Données de navigation :</strong> 13 mois maximum</li>
                        <li><strong>Newsletters :</strong> Jusqu'à votre désinscription</li>
                        <li><strong>Obligations légales :</strong> Durée requise par la loi</li>
                    </ul>

                    <p class="section-title">8. Transferts internationaux</p>
                    <p>Vos données peuvent être transférées au Royaume-Uni (BII) avec garanties appropriées :</p>
                    <ul>
                        <li>Clauses contractuelles types de la Commission Européenne</li>
                        <li>Évaluation d'adéquation de la Commission</li>
                        <li>Mesures de sécurité renforcées</li>
                    </ul>

                    <p class="section-title">9. Contact & DPO</p>
                    <p>Pour exercer vos droits ou toute question :</p>
                    <p><strong>Délégué à la Protection des Données :</strong> M. Samuel Johnson</p>
                    <p><strong>Email :</strong> dpo@bhdm-bii.org</p>
                    <p><strong>Téléphone :</strong> +221 33 800 00 00</p>
                    <p><strong>Adresse :</strong> BHDM - Immeuble Baobab, Avenue Léopold Sédar Senghor, Dakar, Sénégal</p>
                    <p><strong>Délai de réponse :</strong> 30 jours maximum</p>

                    <p class="section-title">10. Réclamation</p>
                    <p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez déposer une réclamation auprès de :</p>
                    <p><strong>Commission des Données Personnelles du Sénégal (CDP)</strong></p>
                    <p>Immeuble Le Lab, Sacré Cœur 3, Dakar</p>
                    <p>Email : contact@cdp.sn | Tél : +221 33 889 99 00</p>
                </div>

                <div id="terms-content" style="display: none;">
                    <h4 class="section-title">Conditions Générales d'Utilisation BHDM</h4>
                    <p><strong>Date d'entrée en vigueur : {{ date('d/m/Y') }}</strong></p>

                    <p class="section-title">1. Préambule</p>
                    <p>Les présentes Conditions Générales d'Utilisation (CGU) régissent l'utilisation du site web et des services de la Banque Humanitaire pour le Développement Mondial (BHDM), programme du British International Investment.</p>

                    <p class="section-title">2. Acceptation des CGU</p>
                    <p>En accédant au site www.bhdm-bii.org, vous déclarez :</p>
                    <ul>
                        <li>Avoir pris connaissance des présentes CGU</li>
                        <li>Les accepter sans réserve</li>
                        <li>Être majeur et pleinement capable de contracter</li>
                        <li>Agir dans un cadre professionnel ou entrepreneurial</li>
                    </ul>

                    <p class="section-title">3. Objet du site</p>
                    <p>Le site BHDM a pour objet :</p>
                    <ul>
                        <li>Présenter le modèle de financement humanitaire BHDM</li>
                        <li>Permettre le dépôt de demandes de financement</li>
                        <li>Informer sur les conditions d'éligibilité</li>
                        <li>Mettre à disposition les outils de suivi de dossier</li>
                        <li>Diffuser des ressources éducatives sur le financement solidaire</li>
                    </ul>

                    <p class="section-title">4. Propriété intellectuelle</p>
                    <p>Tous les éléments du site sont protégés :</p>
                    <ul>
                        <li><strong>Marques :</strong> BHDM®, TPS® sont des marques déposées</li>
                        <li><strong>Contenus :</strong> Textes, images, vidéos, logos sont propriété exclusive</li>
                        <li><strong>Modèles :</strong> Le modèle de financement rotatif est protégé par secret d'affaires</li>
                        <li><strong>Logiciels :</strong> Les outils de simulation sont protégés par droit d'auteur</li>
                    </ul>
                    <p>Toute reproduction nécessite une autorisation écrite préalable.</p>

                    <p class="section-title">5. Responsabilités</p>
                    <p><strong>5.1 Responsabilités de l'utilisateur :</strong></p>
                    <ul>
                        <li>Fournir des informations exactes et complètes</li>
                        <li>Maintenir la confidentialité de ses identifiants</li>
                        <li>Ne pas tenter de contourner les sécurités du site</li>
                        <li>Ne pas utiliser le site à des fins illicites</li>
                    </ul>

                    <p><strong>5.2 Responsabilités de la BHDM :</strong></p>
                    <ul>
                        <li>Maintenir le site accessible dans la mesure du possible</li>
                        <li>Traiter les demandes dans des délais raisonnables</li>
                        <li>Protéger les données personnelles des utilisateurs</li>
                        <li>Fournir des informations claires et transparentes</li>
                    </ul>

                    <p class="section-title">6. Processus de financement</p>
                    <p>Le dépôt d'une demande implique :</p>
                    <ul>
                        <li><strong>Étape 1 :</strong> Pré-qualification automatique (24h)</li>
                        <li><strong>Étape 2 :</strong> Analyse documentaire (7 jours ouvrés)</li>
                        <li><strong>Étape 3 :</strong> Entretien avec un conseiller (sous 15 jours)</li>
                        <li><strong>Étape 4 :</strong> Décision du comité (30 jours maximum)</li>
                        <li><strong>Étape 5 :</strong> Déblocage des fonds (72h après signature)</li>
                    </ul>
                    <p><strong>Attention :</strong> Le dépôt ne garantit pas l'octroi du financement.</p>

                    <p class="section-title">7. Taux de Participation Solidaire (TPS)</p>
                    <p>Le TPS est un mécanisme unique :</p>
                    <ul>
                        <li><strong>Taux :</strong> 2% du financement accordé</li>
                        <li><strong>Paiement :</strong> Échelonné sur la durée du financement</li>
                        <li><strong>Affectation :</strong> 100% réinvesti dans le fonds rotatif</li>
                        <li><strong>Transparence :</strong> Suivi public des réinvestissements</li>
                        <li><strong>Engagement :</strong> Obligation contractuelle</li>
                    </ul>

                    <p class="section-title">8. Limitations de garantie</p>
                    <p>La BHDM ne garantit pas :</p>
                    <ul>
                        <li>La disponibilité permanente du site</li>
                        <li>L'absence d'erreurs ou d'interruptions</li>
                        <li>La réussite de tout projet financé</li>
                        <li>La rentabilité des investissements</li>
                        <li>L'évolution favorable des marchés</li>
                    </ul>

                    <p class="section-title">9. Résiliation</p>
                    <p>La BHDM peut résilier l'accès en cas de :</p>
                    <ul>
                        <li>Violation des présentes CGU</li>
                        <li>Fausses déclarations dans une demande</li>
                        <li>Utilisation frauduleuse du site</li>
                        <li>Non-paiement du TPS dû</li>
                        <li>Activité illégale détectée</li>
                    </ul>

                    <p class="section-title">10. Droit applicable & Litiges</p>
                    <p><strong>Loi applicable :</strong> Droit sénégalais, notamment :</p>
                    <ul>
                        <li>Loi n° 2008-12 sur la protection des données</li>
                        <li>Code des obligations civiles et commerciales</li>
                        <li>Règlement OHADA sur les sûretés</li>
                        <li>Conventions internationales ratifiées</li>
                    </ul>
                    <p><strong>Tribunal compétent :</strong> Tribunal de Grande Instance de Dakar</p>
                    <p><strong>Médiation préalable obligatoire</strong> auprès du Médiateur de la BHDM (contact@mediateur-bhdm.org)</p>

                    <p class="section-title">11. Modifications des CGU</p>
                    <p>La BHDM se réserve le droit de modifier les CGU :</p>
                    <ul>
                        <li>Information des utilisateurs par email 30 jours avant</li>
                        <li>Publication sur le site avec version datée</li>
                        <li>Acceptation tacite par la poursuite de l'utilisation</li>
                        <li>Possibilité de refuser les modifications en cessant d'utiliser le site</li>
                    </ul>
                </div>

                <div id="legal-content" style="display: none;">
                    <h4 class="section-title">Mentions Légales BHDM</h4>

                    <p class="section-title">1. Identification de l'éditeur</p>
                    <p><strong>Dénomination sociale :</strong> Banque Humanitaire pour le Développement Mondial (BHDM)</p>
                    <p><strong>Statut :</strong> Programme d'impact du British International Investment (BII)</p>
                    <p><strong>Siège social :</strong> Immeuble Baobab, Avenue Léopold Sédar Senghor, Dakar, Sénégal</p>
                    <p><strong>Représentant légal :</strong> Dr. Amadou Diallo, Directeur Exécutif</p>
                    <p><strong>Capital social :</strong> 50 milliards FCFA (entièrement libéré)</p>
                    <p><strong>RCS Dakar :</strong> B 2023 12345</p>
                    <p><strong>NINEA :</strong> 0123456789</p>
                    <p><strong>Agrément :</strong> Agrément BCEAO n°A2023-BHDM du 15/03/2023</p>

                    <p class="section-title">2. Contacts</p>
                    <p><strong>Téléphone :</strong> +221 33 800 00 00</p>
                    <p><strong>Email :</strong> contact@bhdm-bii.org</p>
                    <p><strong>Support technique :</strong> support@bhdm-bii.org</p>
                    <p><strong>Service financier :</strong> finance@bhdm-bii.org</p>
                    <p><strong>Médiateur :</strong> mediateur@bhdm-bii.org</p>
                    <p><strong>Horaires :</strong> Lundi-Vendredi, 8h30-17h30 (GMT)</p>

                    <p class="section-title">3. Directeur de la publication</p>
                    <p><strong>Nom :</strong> Dr. Amadou Diallo</p>
                    <p><strong>Fonction :</strong> Directeur Exécutif de la BHDM</p>
                    <p><strong>Email :</strong> a.diallo@bhdm-bii.org</p>

                    <p class="section-title">4. Hébergement</p>
                    <p><strong>Hébergeur :</strong> OVH SAS</p>
                    <p><strong>Adresse :</strong> 2 rue Kellermann, 59100 Roubaix, France</p>
                    <p><strong>Téléphone :</strong> +33 9 72 10 10 07</p>
                    <p><strong>SIRET :</strong> 424 761 419 00045</p>
                    <p><strong>Code APE :</strong> 6311Z</p>
                    <p><strong>Directeur de la publication OVH :</strong> Octave KLABA</p>

                    <p class="section-title">5. Conception & développement</p>
                    <p><strong>Agence technique :</strong> Département Digital BHDM</p>
                    <p><strong>Chef de projet :</strong> Fatoumata Sy</p>
                    <p><strong>Développement Frontend :</strong> Équipe UX/UI BHDM</p>
                    <p><strong>Développement Backend :</strong> Équipe Technique BHDM</p>
                    <p><strong>Audit sécurité :</strong> Deloitte Cybersecurity Africa</p>
                    <p><strong>Tests :</strong> QALab Dakar</p>

                    <p class="section-title">6. Propriété intellectuelle</p>
                    <p><strong>Marques déposées :</strong></p>
                    <ul>
                        <li>BHDM® - INPI: 2345678 - Classe 36</li>
                        <li>TPS® (Taux de Participation Solidaire) - INPI: 2345679 - Classe 36</li>
                        <li>Financement Rotatif Humanitaire® - OAPI: 123456</li>
                    </ul>
                    <p><strong>Copyright :</strong> © {{ date('Y') }} BHDM - Tous droits réservés</p>
                    <p><strong>Licences :</strong></p>
                    <ul>
                        <li>Framework Laravel - Licence MIT</li>
                        <li>Bootstrap - Licence MIT</li>
                        <li>Font Awesome - Licence CC BY 4.0</li>
                        <li>jQuery - Licence MIT</li>
                    </ul>

                    <p class="section-title">7. Responsabilité éditoriale</p>
                    <p>Conformément à la loi sénégalaise n° 2017-27 sur le Code de la Presse :</p>
                    <ul>
                        <li>Les contenus sont vérifiés et validés par le Comité Éditorial BHDM</li>
                        <li>Droit de réponse garanti sous 48h</li>
                        <li>Rectifications publiées dans les mêmes conditions que l'information initiale</li>
                        <li>Archivage électronique des publications pendant 5 ans</li>
                    </ul>

                    <p class="section-title">8. Données personnelles</p>
                    <p><strong>Délégué à la Protection des Données :</strong> M. Samuel Johnson</p>
                    <p><strong>Email DPO :</strong> dpo@bhdm-bii.org</p>
                    <p><strong>Déclaration CNIL/CDP :</strong> N° 123456 du 20/04/2023</p>
                    <p><strong>Politique cookies :</strong> Voir politique de confidentialité</p>

                    <p class="section-title">9. Activité réglementée</p>
                    <p>La BHDM exerce sous les agréments suivants :</p>
                    <ul>
                        <li><strong>BCEAO :</strong> Agrément d'établissement financier n°A2023-BHDM</li>
                        <li><strong>CREPMF :</strong> Autorisation de gestion de fonds n°G2023-045</li>
                        <li><strong>APSF :</strong> Autorisation de services de paiement n°PSP-789</li>
                        <li><strong>ACPR :</strong> Autorisation d'activité transfrontalière n°TF-456</li>
                    </ul>

                    <p class="section-title">10. Informations financières</p>
                    <p><strong>Fonds propres :</strong> 50 milliards FCFA</p>
                    <p><strong>Ratio de solvabilité :</strong> 18,5% (exigence: 10%)</p>
                    <p><strong>Auditeur financier :</strong> PricewaterhouseCoopers Sénégal</p>
                    <p><strong>Commissaire aux comptes :</strong> KPMG Afrique Francophone</p>
                    <p><strong>Exercice social :</strong> Du 1er janvier au 31 décembre</p>
                    <p><strong>Publication des comptes :</strong> Site web et Journal Officiel sous 6 mois</p>

                    <p class="section-title">11. Assurance responsabilité</p>
                    <p><strong>Assureur :</strong> AXA Assurance Sénégal</p>
                    <p><strong>Police n° :</strong> AXA-RC-2023-7890</p>
                    <p><strong>Couverture :</strong> 10 milliards FCFA</p>
                    <p><strong>Période :</strong> Du 01/01/2023 au 31/12/2023</p>

                    <p class="section-title">12. Consommation</p>
                    <p>Conformément aux dispositions du Code de la Consommation sénégalais :</p>
                    <ul>
                        <li>Droit de rétractation : 14 jours pour les services à distance</li>
                        <li>Garantie des vices cachés : 6 mois</li>
                        <li>Médiation : Service gratuit du Médiateur BHDM</li>
                        <li>Information précontractuelle obligatoire</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="print-policy">
                    <i class="fas fa-print me-2"></i>Imprimer
                </button>
                <button type="button" class="btn btn-success" id="download-policy">
                    <i class="fas fa-download me-2"></i>Télécharger PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bandeau Cookies - SIMPLIFIÉ -->
<div class="cookie-consent" id="cookieConsent" style="display: none;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7 col-sm-12 mb-3 mb-md-0">
                <h5 class="mb-1" style="color: white;">🍪 Gestion des cookies</h5>
                <p class="mb-0" style="font-size: 14px; color: rgba(255, 255, 255, 0.9);">
                    Nous utilisons des cookies essentiels pour le fonctionnement du site et des cookies analytiques pour améliorer nos services.
                    Vous pouvez personnaliser vos préférences ou tout accepter.
                </p>
            </div>
            <div class="col-lg-4 col-md-5 col-sm-12 text-md-end">
                <button class="btn btn-sm btn-outline-light me-2 mb-2" id="customize-cookies">
                    <i class="fas fa-cog me-1"></i>Personnaliser
                </button>
                <button class="btn btn-sm btn-danger me-2 mb-2" id="reject-cookies">
                    <i class="fas fa-times me-1"></i>Refuser
                </button>
                <button class="btn btn-sm btn-success mb-2" id="accept-all-cookies">
                    <i class="fas fa-check me-1"></i>Tout accepter
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ---------------------------------------------------------------------
   FOOTER STYLES COMPLETS
--------------------------------------------------------------------- */
.footer_section {
    background: linear-gradient(135deg, #0a1f44 0%, #1b5a8d 100%);
    color: white;
    padding: 60px 0 30px;
    position: relative;
    overflow: hidden;
}

.footer_section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff5a58, #4aafff, #ff5a58);
    z-index: 1;
}

.footer_section .about_text {
    color: white;
    font-size: 1.3rem;
    margin-bottom: 20px;
    font-weight: 600;
    position: relative;
    padding-bottom: 10px;
    font-family: 'Rajdhani', sans-serif;
}

.footer_section .about_text::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 3px;
    background: #ff5a58;
    border-radius: 2px;
}

.footer_section .dolor_text {
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.6;
    margin-bottom: 15px;
    font-size: 0.95rem;
    text-align: justify;
}

.footer_section .dolor_text strong {
    color: white;
    font-weight: 600;
}

.footer_section .location_text {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.footer_section .location_text:hover {
    color: white;
    transform: translateX(5px);
}

.footer_section .location_text i {
    color: #ff5a58;
    font-size: 1rem;
    min-width: 20px;
}

/* Footer Links */
.footer_section .footer_links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer_section .footer_links li {
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 12px;
    position: relative;
    padding-left: 20px;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    cursor: pointer;
}

.footer_section .footer_links li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: #ff5a58;
    transition: transform 0.3s ease;
}

.footer_section .footer_links li:hover {
    color: white;
    transform: translateX(5px);
}

.footer_section .footer_links li:hover::before {
    transform: translateX(3px);
    color: #4aafff;
}

.footer_section .footer_links li a {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
}

.footer_section .footer_links li a:hover {
    color: white;
    text-decoration: none;
}

/* Copyright */
.footer_section .copyright_section {
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    padding-top: 20px;
    margin-top: 40px;
}

.footer_section .copyright_text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    margin-bottom: 5px;
    line-height: 1.5;
}

.legal_links {
    font-size: 0.85rem;
}

.legal_links a {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0 8px;
    position: relative;
}

.legal_links a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 8px;
    right: 8px;
    height: 1px;
    background: #ff5a58;
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.legal_links a:hover {
    color: white;
}

.legal_links a:hover::after {
    transform: scaleX(1);
}

/* Modal de politique */
.policy-modal {
    font-family: 'Poppins', sans-serif;
}

.policy-modal .modal-header {
    background: linear-gradient(135deg, #0a1f44 0%, #1b5a8d 100%);
    color: white;
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.policy-modal .modal-title {
    font-weight: 700;
    font-size: 1.5rem;
    font-family: 'Rajdhani', sans-serif;
}

.policy-modal .modal-body {
    max-height: 60vh;
    overflow-y: auto;
    padding: 2rem;
    background: #f8f9fa;
}

.policy-modal .section-title {
    color: #1b5a8d;
    font-size: 1.2rem;
    margin-top: 25px;
    margin-bottom: 15px;
    font-weight: 600;
    border-bottom: 2px solid #ff5a58;
    padding-bottom: 8px;
    font-family: 'Rajdhani', sans-serif;
}

.policy-modal ul {
    padding-left: 25px;
    margin-bottom: 20px;
}

.policy-modal ul li {
    margin-bottom: 10px;
    line-height: 1.6;
    position: relative;
}

.policy-modal ul li::before {
    content: '•';
    color: #ff5a58;
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
}

.policy-modal strong {
    color: #1b5a8d;
    font-weight: 600;
}

.policy-modal a {
    color: #1b5a8d;
    text-decoration: none;
    border-bottom: 1px dotted #1b5a8d;
}

.policy-modal a:hover {
    text-decoration: none;
    border-bottom: 1px solid #1b5a8d;
}

.policy-modal .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 1rem 2rem;
    background: white;
}

/* Bandeau Cookies - SIMPLIFIÉ */
.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(10, 31, 68, 0.98);
    color: white;
    padding: 15px 0;
    z-index: 9999;
    border-top: 3px solid #ff5a58;
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}

.cookie-consent .btn {
    font-size: 13px;
    padding: 6px 15px;
    border-radius: 5px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.cookie-consent .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 991px) {
    .footer_section {
        padding: 50px 0 20px;
    }

    .footer_section .row > div {
        margin-bottom: 30px;
    }

    .footer_section .about_text {
        font-size: 1.2rem;
    }
}

@media (max-width: 767px) {
    .footer_section {
        padding: 40px 0 20px;
    }

    .footer_section .about_text {
        font-size: 1.1rem;
    }

    .policy-modal .modal-body {
        padding: 1.5rem;
    }

    .cookie-consent .col-md-7 {
        margin-bottom: 15px;
    }
}

@media (max-width: 575px) {
    .legal_links a {
        display: block;
        margin: 5px 0;
        padding: 5px 0;
    }

    .legal_links a::after {
        display: none;
    }

    .policy-modal .modal-body {
        padding: 1rem;
        font-size: 14px;
    }

    .policy-modal .section-title {
        font-size: 1.1rem;
    }
}
</style>

<script>
// ---------------------------------------------------------------------
// SCRIPT SIMPLIFIÉ POUR LE FOOTER BHDM
// ---------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des variables
    let currentPolicyType = 'privacy';
    const COOKIE_ACCEPTED_KEY = 'bhdm-cookie-accepted';
    const COOKIE_PREFERENCES_KEY = 'bhdm-cookie-preferences';

    // -----------------------------------------------------------------
    // 1. GESTION DES POLITIQUES (MODAL)
    // -----------------------------------------------------------------

    // Afficher le modal de politique
    function showPolicyModal(type) {
        currentPolicyType = type;
        const modal = new bootstrap.Modal(document.getElementById('policyModal'));
        const title = document.getElementById('policyModalLabel');
        const contents = document.querySelectorAll('#privacy-content, #terms-content, #legal-content');

        // Masquer tous les contenus
        contents.forEach(content => content.style.display = 'none');

        // Afficher le contenu correspondant
        switch(type) {
            case 'privacy':
                title.textContent = 'Politique de confidentialité BHDM';
                document.getElementById('privacy-content').style.display = 'block';
                break;
            case 'terms':
                title.textContent = 'Conditions Générales d\'Utilisation BHDM';
                document.getElementById('terms-content').style.display = 'block';
                break;
            case 'legal':
                title.textContent = 'Mentions Légales BHDM';
                document.getElementById('legal-content').style.display = 'block';
                break;
        }

        modal.show();
    }

    // Écouteurs pour les liens de politique
    document.getElementById('privacy-policy-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        showPolicyModal('privacy');
    });

    document.getElementById('terms-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        showPolicyModal('terms');
    });

    document.getElementById('legal-mentions-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        showPolicyModal('legal');
    });

    // -----------------------------------------------------------------
    // 2. IMPRESSION DES POLITIQUES
    // -----------------------------------------------------------------

    document.getElementById('print-policy')?.addEventListener('click', function() {
        window.print();
    });

    // -----------------------------------------------------------------
    // 3. GESTION DES COOKIES SIMPLIFIÉE
    // -----------------------------------------------------------------

    const cookieConsent = document.getElementById('cookieConsent');

    // Vérifier si l'utilisateur a déjà accepté les cookies
    function checkCookieConsent() {
        const accepted = localStorage.getItem(COOKIE_ACCEPTED_KEY);
        const preferences = JSON.parse(localStorage.getItem(COOKIE_PREFERENCES_KEY) || '{}');

        if (!accepted && !preferences.essential) {
            setTimeout(() => {
                showCookieConsent();
            }, 2000);
        } else {
            applyCookiePreferences(preferences);
        }
    }

    // Afficher le bandeau de consentement
    function showCookieConsent() {
        cookieConsent.style.display = 'block';
        cookieConsent.style.animation = 'slideUp 0.5s ease-out';

        // Écouteurs pour les boutons
        document.getElementById('accept-all-cookies').addEventListener('click', function() {
            acceptAllCookies();
        });

        document.getElementById('reject-cookies').addEventListener('click', function() {
            rejectAllCookies();
        });

        document.getElementById('customize-cookies').addEventListener('click', function() {
            showCookieSettingsModal();
        });
    }

    // Accepter tous les cookies
    function acceptAllCookies() {
        const preferences = {
            essential: true,
            analytics: true,
            preferences: true,
            marketing: false,
            timestamp: new Date().toISOString()
        };

        localStorage.setItem(COOKIE_ACCEPTED_KEY, 'true');
        localStorage.setItem(COOKIE_PREFERENCES_KEY, JSON.stringify(preferences));

        cookieConsent.style.animation = 'slideUp 0.5s ease-out reverse';
        setTimeout(() => {
            cookieConsent.style.display = 'none';
        }, 500);

        applyCookiePreferences(preferences);
        showToast('Préférences cookies enregistrées avec succès', 'success');
    }

    // Refuser tous les cookies (sauf essentiels)
    function rejectAllCookies() {
        const preferences = {
            essential: true,
            analytics: false,
            preferences: false,
            marketing: false,
            timestamp: new Date().toISOString()
        };

        localStorage.setItem(COOKIE_ACCEPTED_KEY, 'true');
        localStorage.setItem(COOKIE_PREFERENCES_KEY, JSON.stringify(preferences));

        cookieConsent.style.animation = 'slideUp 0.5s ease-out reverse';
        setTimeout(() => {
            cookieConsent.style.display = 'none';
        }, 500);

        applyCookiePreferences(preferences);
        showToast('Seuls les cookies essentiels ont été acceptés', 'info');
    }

    // Modal de paramètres de cookies SIMPLIFIÉ
    function showCookieSettingsModal() {
        // Créer le modal
        const modalHTML = `
            <div class="modal fade" id="cookieSettingsModal" tabindex="-1" aria-labelledby="cookieSettingsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cookieSettingsModalLabel">
                                <i class="fas fa-cookie-bite me-2"></i>Paramètres des cookies
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-4">
                                <p class="text-muted mb-0">Choisissez les cookies que vous acceptez :</p>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled>
                                <label class="form-check-label" for="essentialCookies">
                                    <strong>Cookies essentiels</strong>
                                    <small class="d-block text-muted">Nécessaires au fonctionnement du site (toujours activés)</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="analyticsCookies">
                                <label class="form-check-label" for="analyticsCookies">
                                    <strong>Cookies analytiques</strong>
                                    <small class="d-block text-muted">Pour améliorer nos services</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="preferenceCookies">
                                <label class="form-check-label" for="preferenceCookies">
                                    <strong>Cookies de préférences</strong>
                                    <small class="d-block text-muted">Pour mémoriser vos choix</small>
                                </label>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Vous pouvez modifier ces paramètres à tout moment</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="saveCookieSettingsBtn">Enregistrer</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Ajouter le modal au DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Initialiser le modal
        const modalEl = document.getElementById('cookieSettingsModal');
        const modal = new bootstrap.Modal(modalEl);

        // Charger les préférences existantes
        const preferences = JSON.parse(localStorage.getItem(COOKIE_PREFERENCES_KEY) || '{}');
        document.getElementById('analyticsCookies').checked = preferences.analytics || false;
        document.getElementById('preferenceCookies').checked = preferences.preferences || false;

        // Gérer la sauvegarde
        document.getElementById('saveCookieSettingsBtn').addEventListener('click', function() {
            const newPreferences = {
                essential: true,
                analytics: document.getElementById('analyticsCookies').checked,
                preferences: document.getElementById('preferenceCookies').checked,
                marketing: false,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem(COOKIE_ACCEPTED_KEY, 'true');
            localStorage.setItem(COOKIE_PREFERENCES_KEY, JSON.stringify(newPreferences));

            modal.hide();

            // Fermer le bandeau cookies
            cookieConsent.style.display = 'none';

            applyCookiePreferences(newPreferences);
            showToast('Vos préférences ont été enregistrées', 'success');

            // Supprimer le modal du DOM après la fermeture
            modalEl.addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        });

        // Nettoyer le modal lorsqu'il est fermé
        modalEl.addEventListener('hidden.bs.modal', function() {
            if (this.parentNode) {
                this.remove();
            }
        });

        // Afficher le modal
        modal.show();
    }

    // Appliquer les préférences cookies
    function applyCookiePreferences(preferences) {
        // Essentiels : toujours activés
        console.log('Cookies essentiels activés');

        // Analytiques
        if (preferences.analytics) {
            console.log('Cookies analytiques activés');
            // loadGoogleAnalytics();
        } else {
            console.log('Cookies analytiques désactivés');
        }

        // Préférences
        if (preferences.preferences) {
            console.log('Cookies de préférences activés');
            loadUserPreferences();
        }
    }

    // Charger les préférences utilisateur
    function loadUserPreferences() {
        const savedTheme = localStorage.getItem('bhdm-theme');
        const savedLanguage = localStorage.getItem('bhdm-language');

        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        if (savedLanguage) {
            // Logique pour changer la langue
        }
    }

    // -----------------------------------------------------------------
    // 4. FONCTIONS UTILITAIRES
    // -----------------------------------------------------------------

    // Afficher un toast de notification
    function showToast(message, type = 'info') {
        // Créer un toast simple
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-${type === 'success' ? 'success' : 'info'} border-0`;
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

        // Positionner le toast
        toastEl.style.position = 'fixed';
        toastEl.style.bottom = '20px';
        toastEl.style.right = '20px';
        toastEl.style.zIndex = '10000';

        document.body.appendChild(toastEl);

        // Initialiser le toast Bootstrap
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Supprimer après la fermeture
        toastEl.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    // -----------------------------------------------------------------
    // 5. INITIALISATION
    // -----------------------------------------------------------------

    // Vérifier et appliquer le consentement cookies
    checkCookieConsent();

    // Animation pour les liens du footer
    const footerLinks = document.querySelectorAll('.footer_links li');
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Téléchargement PDF (fonctionnalité avancée)
    document.getElementById('download-policy')?.addEventListener('click', function() {
        showToast('Cette fonctionnalité nécessite une bibliothèque PDF. Contactez support@bhdm-bii.org pour obtenir une copie.', 'info');
    });

    // Accessibilité - ajouter les attributs ARIA
    document.querySelectorAll('.footer_links a').forEach(link => {
        if (!link.getAttribute('aria-label')) {
            const text = link.textContent || 'Lien';
            link.setAttribute('aria-label', 'Aller à ' + text);
        }
    });
});
</script>
