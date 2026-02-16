<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande approuvée</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; text-align: center; }
        .content { background: #f9fafb; padding: 30px; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #059669; }
        .button { display: inline-block; padding: 12px 24px; background: #059669; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { text-align: center; color: #6b7280; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Bonne nouvelle !</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $user->first_name }},</p>

            <p>Nous avons le plaisir de vous informer que votre demande de financement <strong>n°{{ $fundingRequest->request_number }}</strong> a été <strong>approuvée</strong>.</p>

            <h3>Détails de l'approbation :</h3>
            <ul>
                <li><strong>Montant approuvé :</strong> <span class="amount">{{ number_format($approvedAmount, 0, ',', ' ') }} FCFA</span></li>
                <li><strong>Frais d'inscription :</strong> {{ number_format($registrationFee, 0, ',', ' ') }} FCFA</li>
                <li><strong>Durée du financement :</strong> {{ $duration }} mois</li>
            </ul>

            @if($comments)
            <p><strong>Commentaires de l'administrateur :</strong><br>{{ $comments }}</p>
            @endif

            <p>Pour finaliser votre demande, veuillez procéder au paiement des frais d'inscription en cliquant sur le bouton ci-dessous :</p>

            <a href="{{ route('client.funding.show', $fundingRequest->id) }}" class="button">Procéder au paiement</a>

            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                Ce lien est valable 7 jours. Passé ce délai, votre demande pourra être annulée.
            </p>
        </div>

        <div class="footer">
            <p>Cordialement,<br>L'équipe BHDM</p>
        </div>
    </div>
</body>
</html>
