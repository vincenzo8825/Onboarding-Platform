<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Gli admin sono sempre considerati approvati
        if (Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        // Per gli altri utenti (dipendenti, new_hire, ecc.) verifica se sono approvati
        if (!Auth::user()->is_approved) {
            return redirect()->route('waiting-approval');
        }

        return $next($request);
    }
}
