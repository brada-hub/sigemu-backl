<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    public function index(): JsonResponse
    {
        // Idealmente aquí habría una autorización como $this->authorize('ver-roles');
        $roles = Rol::with('permisos')->get();
        return response()->json($roles);
    }

    public function permisos(): JsonResponse
    {
        return response()->json(Permiso::all());
    }

    public function update(Request $request, Rol $rol): JsonResponse
    {
        // Idealmente $this->authorize('editar-roles');
        
        $validated = $request->validate([
            'permisos' => ['array'],
            'permisos.*' => ['exists:permisos,id_permiso']
        ]);

        if (isset($validated['permisos'])) {
            $rol->permisos()->sync($validated['permisos']);
        }

        return response()->json($rol->load('permisos'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:rol,nombre',
        ]);

        $rol = Rol::create([
            'nombre' => $validated['nombre']
        ]);

        return response()->json($rol->load('permisos'), 201);
    }

    public function destroy(Rol $rol): JsonResponse
    {
        if (in_array(strtolower($rol->nombre), ['admin', 'tesorero', 'secretario'])) {
            return response()->json(['message' => 'No se puede eliminar un rol del sistema.'], 403);
        }
        if ($rol->usuarios()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un rol con usuarios asignados.'], 422);
        }
        $rol->permisos()->detach();
        $rol->delete();
        return response()->json(['message' => 'Rol eliminado correctamente.']);
    }
}
