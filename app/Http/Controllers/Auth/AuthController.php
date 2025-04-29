<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use app\Traits\handleResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use handleResponseTrait;

    public function __construct(protected AuthService $authService)
    {
        $this->authService = $authService;
    }

    ///////////////Login with multi guard

    public function login(LoginRequest $request)
    {

        $data = $request->validated();

        $token = $this->authService->login($data['guard'], [
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$token) {
            return $this->returnErrorMessage('Invalid credentials', 401);
        }
         $reponseData['access_token']=$token;
         $reponseData['expires_at']=JWTAuth::factory()->getTTL() * 60;

        return $this->returnData('data', $reponseData, 'Login Successfully', 200);
    }

    ///////////////Logout with multi guard

    public function logout(LoginRequest $request): JsonResponse
    {
        $this->authService->logout($request->get('guard'));

        return $this->returnSuccessMessage('Successfully logged out', 200);
    }

   //////////////////Refresh a token.

    public function refresh(): JsonResponse
    {
        $token = $this->authService->refresh();

        if (!$token) {
            return response()->json(['error' => 'Token refresh failed'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }


   ////////////////// User profile.

    public function profile(): JsonResponse
    {

        $user = $this->authService->profile();

        return $this->returnData('profile', $user, 'Generated User Profile Successfully', 200);
    }
}
