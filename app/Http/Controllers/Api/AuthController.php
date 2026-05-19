<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales no son válidas.'],
            ]);
        }

        $usuario = Auth::user()->load('persona');

        if (!$usuario->activo) {
            Auth::logout();
            return response()->json(['message' => 'Tu cuenta está desactivada.'], 403);
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        return response()->json([
            'token'  => $token,
            'usuario' => [
                'id_user'  => $usuario->id_user,
                'username' => $usuario->username,
                'rol'      => $usuario->rol ? $usuario->rol->nombre : null,
                'debe_cambiar_password' => $usuario->debe_cambiar_password,
                'persona'  => $usuario->persona ? [
                    'nombres' => $usuario->persona->nombres,
                    'primer_apellido' => $usuario->persona->primer_apellido,
                ] : null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load(['persona', 'rol']));
    }

    public function cambiarPassword(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $usuario = $request->user();

        // Verificar que no sea la misma contraseña
        if (Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'message' => 'La nueva contraseña no puede ser igual a la anterior.'
            ], 422);
        }

        $usuario->update([
            'password' => Hash::make($request->password),
            'debe_cambiar_password' => false
        ]);

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
