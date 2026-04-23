<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (strtoupper((string) Auth::user()->role) !== 'ADMIN') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
