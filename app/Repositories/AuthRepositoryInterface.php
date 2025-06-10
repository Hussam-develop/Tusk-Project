<?php

namespace App\Repositories;

interface AuthRepositoryInterface
{
    // public function login(string $guard, array $credentials);
    public function createUser(array $request_data, $guard);
    public function getAuthenticatedUser();
    public function logout();
    public function refresh();
    public function download_profile_image();
    public function edit_profile_image($request);
}
