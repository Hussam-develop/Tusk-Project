<?php


namespace App\Repositories;

use App\Http\Requests\registerRequest;
use App\Models\Dentist;
use App\Models\LabManager;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{

    protected array $models = [
        'dentist'     => \App\Models\Dentist::class,
        'lab_manager' => \App\Models\LabManager::class,
    ];


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

    public function createUser(array $request_data, $guard)
    {

        $modelClass = $this->models[$guard];
        try {
            $request_data['password'] = Hash::make($request_data['password']);

            $user = $modelClass::create($request_data);
            //$token = JWTAuth::fromUser($user, ['guard' => $guard]);

            return $user;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function getAuthenticatedUser()
    {
        // Auth::shouldUse($guard);
        return Auth::user();
    }
}
