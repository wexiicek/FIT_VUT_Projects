<?php

namespace App\Http\Middleware;

use Closure;

class isDirector
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (@auth()->user()->role == 'admin' || @auth()->user()->role == 'director') {
            return $next($request);
        }
        return abort(401);
    }
}
