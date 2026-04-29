<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $newUser = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        // Usamos Sanctum por su ligereza para autenticación de APIs en apps SPA y Mobile.
        $accessToken = $newUser->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($newUser),
            'token' => $accessToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Las credenciales ingresadas no son válidas.',
            ], 422);
        }

        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $accessToken = $authenticatedUser->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($authenticatedUser),
            'token' => $accessToken,
        ]);
    }
}
