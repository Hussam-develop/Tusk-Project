<?php


namespace App\Services;

use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{

    public function __construct(protected AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function login(string $guard, array $credentials): ?string
    {
        Auth::shouldUse($guard);
        if (!$token = Auth::attempt($credentials)) {
            return null;
        }

        return $token;
    }

    public function register(array $data, string $guard)
    {
        $user = $this->authRepo->createUser($data, $guard);
        $token = JWTAuth::fromUser($user, ['guard' => $guard]);

        //  $token = auth($guard)->login($user);
        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function refresh(): ?string
    {
        return $this->authRepo->refresh();
    }

    public function logout(): void
    {
        $this->authRepo->logout();
    }

    public function profile()
    {
        return $this->authRepo->getAuthenticatedUser();
    }
    public function download_profile_image()
    {
        return $this->authRepo->download_profile_image();
    }
    public function edit_profile_image($request)
    {
        return $this->authRepo->edit_profile_image($request);
    }
}
