@component('mail::message')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('images/logo-email.png') }}" alt="BHDM" style="max-width: 200px;">
@endcomponent
@endslot

# 🔐 Réinitialisation du mot de passe

Bonjour {{ $user->first_name }},

Vous avez demandé la réinitialisation de votre mot de passe pour le compte **{{ $user->member_id }}** associé à l'email **{{ $user->email }}**.

@component('mail::panel')
## ⚠️ Action requise

Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe sécurisé :
@endcomponent

@component('mail::button', ['url' => $url, 'color' => 'primary'])
🔑 Réinitialiser mon mot de passe
@endcomponent

@component('mail::subcopy')
Ou copiez ce lien : [{{ $url }}]({{ $url }})
@endcomponent

@component('mail::panel', ['color' => 'warning'])
⏰ Ce lien expire dans {{ $expireMinutes }} minutes.
@endcomponent

@component('mail::panel', ['color' => 'error'])
🛡️ Si vous n'avez pas fait cette demande, ignorez cet email ou [contactez-nous]({{ route('contact') }}) immédiatement pour sécuriser votre compte.
@endcomponent

Cordialement,<br>
**L'équipe de sécurité BHDM**

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} BHDM - Sécurité renforcée SSL 256-bit
@endcomponent
@endslot
@endcomponent