<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roleCodes): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $hasRole = $user->roles()
            ->whereIn('code', $roleCodes)
            ->exists();

        if (!$hasRole) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
