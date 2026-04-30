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
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Autenticación'],
        summary: 'Registrar un nuevo usuario',
        description: 'Crea una cuenta y devuelve el token de acceso en la misma respuesta.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Diego Moreno'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'diego@mobius.dev'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario registrado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'token', type: 'string', example: '1|sanctum_plain_text_token'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'El correo electrónico es obligatorio.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: new OA\AdditionalProperties(
                                type: 'array',
                                items: new OA\Items(type: 'string')
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $newUser = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        // Emitimos el token inmediatamente para evitar un segundo roundtrip de login
        // tras el registro (flujo común en SPA y apps móviles).
        $accessToken = $newUser->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($newUser),
            'token' => $accessToken,
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Autenticación'],
        summary: 'Iniciar sesión',
        description: 'Valida credenciales y devuelve un token de Sanctum.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'cliente@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'token', type: 'string', example: '2|sanctum_plain_text_token'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Credenciales inválidas o error de validación',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Las credenciales ingresadas no son válidas.'),
                    ]
                )
            ),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        // Respondemos 422 para mantener el contrato de errores de validación del API.
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
