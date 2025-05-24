<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request);
        return ApiResponse::success($user, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request);

        return ApiResponse::success([
            'access_token' => $data['token'],
            'token_type' => 'Bearer',
            'user' => $data['user'],
        ], 'Login successful');
    }

    public function profile(Request $request)
    {
        return ApiResponse::success($request->user());
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->authService->updateProfile($request->validated());
        return ApiResponse::success($user, 'Profile updated successfully');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return ApiResponse::success(null, 'Logged out successfully');
    }
}
