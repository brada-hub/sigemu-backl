<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('ver-usuarios'); // Asumiendo que definiremos este gate

        $query = Usuario::with(['persona', 'rol']);
        
        if ($request->boolean('has_pagos')) {
            $query->whereHas('pagosRegistrados');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('crear-usuarios');

        $validated = $request->validate([
            'id_persona' => ['required', 'exists:persona,id_persona', 'unique:usuario,id_persona'],
            'username'   => ['required', 'string', 'unique:usuario,username'],
            'password'   => ['required', 'string', 'min:6'],
            'id_rol'     => ['required', 'exists:rol,id_rol'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['activo'] = true;

        $usuario = Usuario::create($validated);

        return response()->json($usuario->load(['persona', 'rol']), 201);
    }

    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('editar-usuarios', $usuario);

        $validated = $request->validate([
            'username' => ['required', 'string', Rule::unique('usuario')->ignore($usuario->id_user, 'id_user')],
            'password' => ['nullable', 'string', 'min:6'],
            'id_rol'   => ['required', 'exists:rol,id_rol'],
            'activo'   => ['required', 'boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return response()->json($usuario->load(['persona', 'rol']));
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->authorize('eliminar-usuarios', $usuario);

        $usuario->update(['activo' => false]);
        return response()->json(['message' => 'Usuario desactivado']);
    }

    public function roles(): JsonResponse
    {
        return response()->json(Rol::all());
    }
}
