<?php


namespace App\Repositories;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(string $guard, array $credentials): ?string
    {
        Auth::shouldUse($guard);

        if (!$token = Auth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    public function refresh(): ?string
    {
       // Auth::shouldUse($guard);

        try {
            return JWTAuth::parseToken()->refresh();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function logout(): void
    {
        //Auth::shouldUse($guard);
        Auth::logout();
    }


    public function getAuthenticatedUser()
    {
       // Auth::shouldUse($guard);
        return Auth::user();
    }
}
