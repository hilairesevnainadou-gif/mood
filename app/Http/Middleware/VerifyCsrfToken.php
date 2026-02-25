<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
       '/kkiapay/callback',
        '/wallet/deposit/callback',
        'kkiapay/callback', // sans slash aussi
        'wallet/deposit/callback',
    ];
}
