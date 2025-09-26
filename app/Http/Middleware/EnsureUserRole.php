<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (! $user && Filament::hasPanel()) {
            $user = Filament::auth()->user();

            if ($user) {
                Auth::setUser($user);
            }
        }

        if (! $user || $user->role !== $role) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'auth' => 'Недостатньо прав для доступу до цього розділу.',
            ]);
        }

        return $next($request);
    }
}
