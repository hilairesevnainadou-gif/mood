<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification email - BHDM</title>
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
        
        .next-steps {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 25px;
            margin: 30px 0;
        }
        
        .next-steps h3 {
            color: #166534;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .next-steps ol {
            margin-left: 20px;
            color: #166534;
            font-size: 14px;
        }
        
        .next-steps li {
            margin-bottom: 8px;
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
            <div class="logo-subtext">Banque Humanitaire du Developpement Mondial</div>
        </div>
        
        <!-- Corps -->
        <div class="email-body">
            @if($isEnterprise)
                <h1 class="email-title">Bienvenue, {{ $user->company_name }} !</h1>
            @else
                <h1 class="email-title">Bienvenue, {{ $user->first_name }} !</h1>
            @endif
            
            <p class="welcome-text">
                Votre inscription sur la <strong>Banque Humanitaire du Developpement Mondial (BHDM)</strong> a ete enregistree avec succes.
            </p>
            
            <div class="info-panel">
                <h3>Recapitulatif de votre compte</h3>
                <table class="info-table">
                    <tr>
                        <td>Type de compte</td>
                        <td>{{ $isEnterprise ? 'Entreprise' : 'Particulier' }}</td>
                    </tr>
                    <tr>
                        <td>ID Membre</td>
                        <td>{{ $memberId }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Telephone</td>
                        <td>{{ $user->phone }}</td>
                    </tr>
                    @if($isEnterprise)
                    <tr>
                        <td>Entreprise</td>
                        <td>{{ $user->company_name }}</td>
                    </tr>
                    <tr>
                        <td>Secteur</td>
                        <td>{{ $user->sector }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div class="info-panel">
                <h3>Action requise</h3>
                <p>Pour finaliser votre inscription, veuillez confirmer votre adresse email :</p>
            </div>
            
            <div class="btn-container">
                <a href="{{ $url }}" class="btn-primary">
                    Verifier mon adresse email
                </a>
            </div>
            
            <div class="link-box">
                <strong>Lien alternatif :</strong><br>
                <a href="{{ $url }}">{{ $url }}</a>
            </div>
            
            <div class="warning-box">
                <strong>Expiration :</strong> Ce lien expire dans {{ $expireMinutes }} minutes.
            </div>
            
            <div class="alert-box">
                <strong>Securite :</strong> Si vous n'etes pas a l'origine de cette inscription, ignorez cet email.
            </div>
            
            @if($isEnterprise)
            <div class="next-steps">
                <h3>Prochaines etapes pour votre entreprise</h3>
                <ol>
                    <li>Verification email (en cours)</li>
                    <li>Validation de votre dossier (24-48h)</li>
                    <li>Acces a votre espace entreprise</li>
                </ol>
            </div>
            @endif
            
            <p style="margin-top: 35px; color: #5a6c7d; font-size: 14px;">
                Merci de votre confiance,<br>
                <strong style="color: #1b5a8d;">L'equipe BHDM</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} BHDM - Banque Humanitaire du Developpement Mondial</p>
            <p>Tous droits reserves</p>
        </div>
    </div>
</body>
</html>