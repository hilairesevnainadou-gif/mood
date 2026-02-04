<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->hasVerifiedEmail()) {
            // Stocker l'URL demandée pour rediriger après vérification
            session(['url.intended' => $request->url()]);
            
            // Rediriger vers la page de vérification d'email
            return redirect()->route('verification.notice')
                ->with('warning', 'Veuillez vérifier votre adresse email avant d\'accéder à cette page.');
        }

        return $next($request);
    }
}