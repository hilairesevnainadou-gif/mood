<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre ticket de support</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1b5a8d, #2c7ac9);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .ticket-info {
            background: #f8f9fa;
            border-left: 4px solid #1b5a8d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 4px 4px 0;
        }
        .ticket-number {
            font-weight: bold;
            color: #1b5a8d;
        }
        .message-box {
            background: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .message-box h3 {
            margin-top: 0;
            color: #495057;
        }
        .btn {
            display: inline-block;
            background: #1b5a8d;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #144670;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Nouvelle Réponse à votre Ticket</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $user->first_name ?? $user->name }},</p>

            <p>L'administrateur a répondu à votre demande de support.</p>

            <div class="ticket-info">
                <p><span class="ticket-number">Ticket #{{ $ticket->ticket_number }}</span></p>
                <p><strong>Sujet :</strong> {{ $ticket->subject }}</p>
                <p><strong>Date :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="message-box">
                <h3>Message de l'administrateur :</h3>
                <p>{{ nl2br(e($replyMessage)) }}</p>
            </div>

            <p>Pour consulter l'historique complet de votre ticket et répondre, cliquez sur le bouton ci-dessous :</p>

            <center>
                <a href="{{ $ticketUrl }}" class="btn">Voir mon ticket</a>
            </center>

            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                Si vous avez d'autres questions, n'hésitez pas à répondre directement dans votre espace client.
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement par {{ config('app.name') }}.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
