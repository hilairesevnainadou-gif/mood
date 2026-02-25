<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - BHDM</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f5f7fa;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .email-header {
            background: linear-gradient(135deg, #1b5a8d 0%, #0d3a5c 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo-text {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        
        .logo-subtext {
            color: #ffffff;
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .email-body {
            padding: 50px 40px;
        }
        
        .email-title {
            color: #1b5a8d;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .welcome-text {
            color: #5a6c7d;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        
        .info-panel {
            background-color: #f8fafc;
            border-left: 4px solid #1b5a8d;
            padding: 25px;
            margin: 30px 0;
        }
        
        .info-panel h3 {
            color: #1b5a8d;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        .info-table td:first-child {
            color: #5a6c7d;
            font-weight: 500;
            width: 45%;
        }
        
        .info-table td:last-child {
            color: #1b5a8d;
            font-weight: 600;
        }
        
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        
        .btn-primary {
            display: inline-block;
            background-color: #1b5a8d;
            color: #ffffff;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            background-color: #0d3a5c;
        }
        
        .link-box {
            background-color: #eef2f5;
            padding: 20px;
            border-radius: 4px;
            margin: 25px 0;
            word-break: break-all;
            font-size: 13px;
            color: #5a6c7d;
            border: 1px solid #dde3e8;
        }
        
        .link-box a {
            color: #1b5a8d;
            text-decoration: underline;
        }
        
        .warning-box {
            background-color: #fff8e6;
            border-left: 4px solid #e6a700;
            padding: 20px;
            margin: 25px 0;
            color: #8a6d3b;
            font-size: 14px;
        }
        
        .alert-box {
            background-color: #fdf2f2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin: 25px 0;
            color: #991b1b;
            font-size: 14px;
        }
        
        .security-box {
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 20px;
            margin: 25px 0;
            color: #0c4a6e;
            font-size: 14px;
        }
        
        .email-footer {
            background-color: #0d3a5c;
            color: #ffffff;
            text-align: center;
            padding: 30px;
            font-size: 13px;
        }
        
        .email-footer p {
            margin: 5px 0;
            opacity: 0.9;
        }
        
        @media (max-width: 600px) {
            .email-body { padding: 30px 20px; }
            .email-title { font-size: 18px; }
            .btn-primary { padding: 14px 30px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header avec logo texte -->
        <div class="email-header">
            <div class="logo-text">BHDM</div>
            <div class="logo-subtext">Banque Humanitaire du Développement Mondial</div>
        </div>
        
        <!-- Corps -->
        <div class="email-body">
            <h1 class="email-title">Réinitialisation du mot de passe</h1>
            
            <p class="welcome-text">
                Bonjour <strong>{{ $user->first_name ?? $user->name }}</strong>,
            </p>
            
            <p class="welcome-text">
                Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation du mot de passe pour votre espace de finance <strong>BHDM</strong>.
            </p>
            
            <div class="info-panel">
                <h3>Informations de compte</h3>
                <table class="info-table">
                    <tr>
                        <td>ID Membre</td>
                        <td>{{ $user->member_id }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="btn-container">
                <a href="{{ $url }}" class="btn-primary">
                    Réinitialiser mon mot de passe
                </a>
            </div>
            
            <div class="warning-box">
                <strong>Expiration :</strong> Ce lien expire dans {{ $expireMinutes }} minutes.
            </div>
            
            <div class="link-box">
                <strong>Lien alternatif :</strong><br>
                <a href="{{ $url }}">{{ $url }}</a>
            </div>
            
            <div class="security-box">
                <strong>Sécurité :</strong> Si vous n'êtes pas à l'origine de cette demande, aucune action supplémentaire n'est requise. Votre compte reste sécurisé.
            </div>
            
            <div class="alert-box">
                <strong>Important :</strong> Ne partagez jamais votre mot de passe. L'équipe BHDM ne vous demandera jamais votre mot de passe par email ou téléphone.
            </div>
            
            <p style="margin-top: 35px; color: #5a6c7d; font-size: 14px;">
                Merci de votre confiance,<br>
                <strong style="color: #1b5a8d;">L'équipe BHDM</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} BHDM - Banque Humanitaire du Développement Mondial</p>
            <p>Tous droits réservés</p>
        </div>
    </div>
</body>
</html>