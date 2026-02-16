@component('mail::message')
{{-- Header avec logo --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="{{ asset('images/logo-email.png') }}" alt="BHDM" style="max-width: 200px;">
@endcomponent
@endslot

{{-- Titre personnalisé selon le type de compte --}}
@if ($isEnterprise)
# 🏢 Bienvenue, {{ $user->company_name }} !
@else
# 👋 Bienvenue, {{ $user->first_name }} !
@endif

{{-- Message de bienvenue --}}
Votre inscription sur la **Banque Humanitaire du Développement Mondial (BHDM)** a été enregistrée avec succès.

{{-- Informations du compte --}}
@component('mail::panel')
## 📋 Récapitulatif de votre compte

| Information | Valeur |
|-------------|--------|
| **Type de compte** | {{ $isEnterprise ? 'Entreprise' : 'Particulier' }} |
| **ID Membre** | `{{ $memberId }}` |
| **Email** | {{ $user->email }} |
| **Téléphone** | {{ $user->phone }} |
| **Date d'inscription** | {{ $user->created_at->format('d/m/Y à H:i') }} |

@if ($isEnterprise)
| **Entreprise** | {{ $user->company_name }} |
| **Secteur** | {{ $user->sector }} |
@endif
@endcomponent

{{-- Action requise --}}
## ⚠️ Action requise

Pour finaliser votre inscription et accéder à tous nos services, veuillez **confirmer votre adresse email** en cliquant sur le bouton ci-dessous :

@component('mail::button', ['url' => $url, 'color' => 'primary'])
✅ Vérifier mon adresse email
@endcomponent

{{-- Lien alternatif --}}
@component('mail::subcopy')
Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :
[{{ $url }}]({{ $url }})
@endcomponent

{{-- Expiration --}}
@component('mail::panel', ['color' => 'warning'])
⏰ **Ce lien expire dans {{ $expireMinutes }} minutes** (le {{ now()->addMinutes($expireMinutes)->format('d/m/Y à H:i') }}).
@endcomponent

{{-- Prochaines étapes --}}
@if ($isEnterprise)
## 🚀 Prochaines étapes pour votre entreprise

1. **Vérification email** (en cours)
2. Validation de votre dossier par notre comité (24-48h)
3. Attribution d'un conseiller dédié
4. Accès à votre espace entreprise et aux financements

@component('mail::button', ['url' => route('entreprise.dashboard'), 'color' => 'success'])
Accéder à mon espace entreprise
@endcomponent

@else
## 💼 Vos avantages membre

- Portefeuille électronique sécurisé
- Transferts internationaux à faible coût
- Accès aux programmes de microfinance
- Carte virtuelle gratuite

@component('mail::button', ['url' => route('dashboard'), 'color' => 'success'])
Accéder à mon compte
@endcomponent
@endif

{{-- Sécurité --}}
@component('mail::panel', ['color' => 'error'])
🔒 **Sécurité** : Si vous n'êtes pas à l'origine de cette inscription, veuillez ignorer cet email ou [nous contacter immédiatement]({{ route('contact') }}).
@endcomponent

{{-- Footer --}}
Merci de votre confiance,<br>
**L'équipe BHDM**

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} BHDM - Banque Humanitaire du Développement Mondial. Tous droits réservés.

[Conditions d'utilisation]({{ route('terms') }}) | [Politique de confidentialité]({{ route('privacy') }}) | [Nous contacter]({{ route('contact') }})

**Siège social :** [Votre adresse] | **Support :** support@bhdm.org | **Tél :** +XX XXX XXX XXX
@endcomponent
@endslot
@endcomponent