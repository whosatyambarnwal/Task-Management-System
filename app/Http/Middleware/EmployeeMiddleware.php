<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isEmployee()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Employee access required.'], 403);
            }
            abort(403, 'Unauthorized. Employee access required.');
        }

        return $next($request);
    }
}
