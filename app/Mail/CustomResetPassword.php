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

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: '🔑 Réinitialisation de votre mot de passe - BHDM',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reset-password',
            with: [
                'user' => $this->user,
                'url' => $this->resetUrl,
                'expireMinutes' => config('auth.passwords.users.expire', 60),
            ],
        );
    }
}