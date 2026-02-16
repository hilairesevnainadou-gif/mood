<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande rejetée</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; margin: 20px 0; }
        .reason { background: #fee2e2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; color: #6b7280; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Information importante</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $user->first_name }},</p>

            <p>Après examen attentif de votre demande de financement <strong>n°{{ $fundingRequest->request_number }}</strong>, nous sommes au regret de vous informer que celle-ci n'a pas pu être approuvée.</p>

            <div class="reason">
                <strong>Motif du rejet :</strong><br>
                {{ $rejectionReason }}
            </div>

            <p>Si vous souhaitez obtenir de plus amples informations ou discuter de votre dossier, n'hésitez pas à contacter notre équipe support.</p>

            <a href="{{ route('client.support.index') }}" class="button">Contacter le support</a>

            <p style="margin-top: 30px;">
                Vous pouvez également soumettre une nouvelle demande avec des informations complémentaires.
            </p>
        </div>

        <div class="footer">
            <p>Cordialement,<br>L'équipe BHDM</p>
        </div>
    </div>
</body>
</html>
