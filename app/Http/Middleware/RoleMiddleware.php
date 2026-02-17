<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $roles = collect($roles)
            ->flatMap(fn($role) => explode(',', $role))
            ->map(fn($role) => trim($role))
            ->filter()
            ->values()
            ->toArray();

        if (empty($roles)) {
            abort(403);
        }

        $hasRole = $user->role()
            ->whereIn('name', $roles)
            ->exists();

        if (!$hasRole) {
            abort(403);
        }

        return $next($request);
    }
}
