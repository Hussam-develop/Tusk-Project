<?php


namespace App\Services;

use App\Repositories\AuthRepositoryInterface;

class AuthService
{

    public function __construct(protected AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function login(string $guard, array $credentials): ?string
    {
        return $this->authRepo->login($guard, $credentials);
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
}
