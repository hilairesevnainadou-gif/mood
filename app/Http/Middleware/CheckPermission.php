<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        // Liste des routes publiques qui ne nécessitent pas d'authentification
        $publicRoutes = [
            'home',
            'about',
            'services',
            'contact',
            'login',
            'register',
            'password.forgot',
            'password.reset',
            'services.detail'
        ];

        // Si c'est une route publique, laisser passer
        if (in_array($request->route()->getName(), $publicRoutes)) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Si aucune permission n'est spécifiée, autoriser l'accès
        if ($permission === null) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
