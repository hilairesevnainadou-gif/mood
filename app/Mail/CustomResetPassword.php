<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class CustomResetPassword extends Mailable
{
    use Queueable;

    public $user;
    public $token;
    public $resetUrl;
    public $expireMinutes;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
        $this->expireMinutes = config('auth.passwords.users.expire', 60);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name', 'BHDM - Banque Humanitaire')
            ),
            subject: 'Reinitialisation de votre mot de passe - BHDM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'user' => $this->user,
                'url' => $this->resetUrl,
                'expireMinutes' => $this->expireMinutes,
            ],
        );
    }
}