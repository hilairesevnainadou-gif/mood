<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileDocumentsValidated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasAllRequiredDocuments()) {
            return redirect()->route('client.documents.index')
                ->with('warning', 'Vos documents obligatoires doivent être validés pour accéder à cette section.');
        }

        return $next($request);
    }
}
