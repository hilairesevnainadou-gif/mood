<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 8px; }
        .content { padding: 20px; }
        .details { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #2c5282; }
        .status-validated { color: #38a169; }
        .status-rejected { color: #e53e3e; }
        .status-cancelled { color: #dd6b20; }
        .reason { background: #fed7d7; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e53e3e; }
        .footer { text-align: center; padding: 20px; color: #718096; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $user->name }},</p>

            @if($status === 'validated')
                <p>Nous vous confirmons que votre demande de retrait a été <strong class="status-validated">validée et traitée</strong>.</p>
            @elseif($status === 'rejected')
                <p>Nous regrettons de vous informer que votre demande de retrait a été <strong class="status-rejected">rejetée</strong>.</p>
                <p>Le montant a été recrédité sur votre compte.</p>
            @else
                <p>Votre demande de retrait a été <strong class="status-cancelled">annulée</strong>.</p>
                <p>Le montant a été recrédité sur votre compte.</p>
            @endif

            <div class="details">
                <p><strong>Référence :</strong> {{ $transaction->reference }}</p>
                <p><strong>Montant :</strong> <span class="amount">{{ $transaction->formatted_amount }}</span></p>
                <p><strong>Date de demande :</strong> {{ $transaction->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Méthode :</strong> {{ $transaction->payment_method === 'mobile_money' ? 'Mobile Money' : 'Virement bancaire' }}</p>

                @if($transaction->payment_method === 'mobile_money' && isset($transaction->metadata['phone_number']))
                    <p><strong>Numéro :</strong> {{ $transaction->metadata['phone_number'] }}</p>
                @elseif(isset($transaction->metadata['bank_name']))
                    <p><strong>Banque :</strong> {{ $transaction->metadata['bank_name'] }}</p>
                    <p><strong>Compte :</strong> {{ $transaction->metadata['account_number'] }}</p>
                @endif
            </div>

            @if($reason)
            <div class="reason">
                <strong>Motif :</strong><br>
                {{ $reason }}
            </div>
            @endif

            @if($status === 'validated')
                <p>Les fonds ont été transférés selon votre méthode choisie. Le délai de réception dépend de votre opérateur/banque.</p>
            @else
                <p>Si vous avez des questions, n'hésitez pas à contacter notre support.</p>
            @endif
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} Votre Application</p>
        </div>
    </div>
</body>
</html>
