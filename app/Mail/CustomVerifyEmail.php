<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Mailable
{
    use Queueable;

    public $user;
    public $url;
    public $expireMinutes;
    public $isEnterprise;
    public $memberId;

    public function __construct($user)
    {
        $this->user = $user;
        $this->url = $this->generateVerificationUrl($user);
        $this->expireMinutes = Config::get('auth.verification.expire', 60);
        $this->isEnterprise = $user->member_type === 'entreprise';
        $this->memberId = $user->member_id;
    }

    protected function generateVerificationUrl($user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name', 'BHDM - Banque Humanitaire')
            ),
            subject: 'Confirmez votre adresse email - BHDM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email',
            with: [
                'user' => $this->user,
                'url' => $this->url,
                'expireMinutes' => $this->expireMinutes,
                'isEnterprise' => $this->isEnterprise,
                'memberId' => $this->memberId,
            ],
        );
    }
}