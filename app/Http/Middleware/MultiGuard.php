<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle($request, Closure $next, ...$guards)
        {
            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    Auth::shouldUse($guard); // Switch the active guard
                    return $next($request);
                }
            }

            return response()->json(['error'=>'you should login']);
        }

}
