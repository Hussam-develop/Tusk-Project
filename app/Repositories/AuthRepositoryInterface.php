<?php

namespace App\Repositories;

interface AuthRepositoryInterface
{
    public function login(string $guard, array $credentials): ?string;
    public function getAuthenticatedUser();
    public function logout();
    public function refresh();
}

