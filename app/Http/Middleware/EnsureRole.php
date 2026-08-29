<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:siswa') or ->middleware('role:guru,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('choose-role');
        }

        if (! in_array(auth()->user()->role, $roles)) {
            // Redirect to the correct dashboard based on their actual role
            return match (auth()->user()->role) {
                'siswa' => redirect()->route('siswa.dashboard')->with('error', 'Akses tidak diizinkan.'),
                default => redirect()->route('dashboard')->with('error', 'Akses tidak diizinkan.'),
            };
        }

        return $next($request);
    }
}
