<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        if (auth()->user() && auth()->user()->role === $role) {
            return $next($request);
        }
        if (auth()->user() && (auth()->user()->role=='personnel' || auth()->user()->role=='distributor')) {
            return $next($request);
        }
       
       
    
        return redirect('/not_Access')->with('error', 'Unauthorized Access');
    }
}
