<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class GuardFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected array $guards = ['admin', 'lab_manager', 'dentist', 'secretary', 'accountant', 'inventory_employee']; // List all guards using JWT

    /*
       How middleware work ?

       1- in postman when you write : Authorization: Bearer <JWT>
       then :
            -> If the token belongs to "admin", Auth::guard('admin') is auto-set
            -> If it belongs to "api" user, Auth::guard('api') is set
    */
    public function handle($request, Closure $next)
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        foreach ($this->guards as $guard) {
            try {
                Auth::shouldUse($guard); ///Auth::user()==Dentist
                $user = Auth::user();

                if ($user) {
                    return $next($request); // guard is now active
                }
            } catch (JWTException $e) {
                // Continue to next guard
                continue;
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
