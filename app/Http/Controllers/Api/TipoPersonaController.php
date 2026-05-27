<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoPersona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoPersonaController extends Controller
{
    public function index(): JsonResponse
    {
        $tiposPersona = TipoPersona::orderBy('nombre')->get();
        return response()->json($tiposPersona);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|unique:tipo_persona,nombre',
            'descripcion' => 'nullable|string'
        ]);

        $tipoPersona = TipoPersona::create([
            'nombre' => strtoupper($request->nombre),
            'descripcion' => $request->descripcion
        ]);

        return response()->json($tipoPersona, 201);
    }

    public function show(int $id): JsonResponse
    {
        $tipoPersona = TipoPersona::findOrFail($id);
        return response()->json($tipoPersona);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tipoPersona = TipoPersona::findOrFail($id);
        
        $request->validate([
            'nombre' => 'string|unique:tipo_persona,nombre,'.$id.',id_tipo_persona',
            'descripcion' => 'nullable|string'
        ]);

        $tipoPersona->update([
            'nombre' => $request->has('nombre') ? strtoupper($request->nombre) : $tipoPersona->nombre,
            'descripcion' => $request->descripcion ?? $tipoPersona->descripcion,
        ]);

        return response()->json($tipoPersona);
    }

    public function destroy(int $id): JsonResponse
    {
        $tipoPersona = TipoPersona::findOrFail($id);
        $tipoPersona->delete();
        return response()->json(['message' => 'Tipo de persona eliminado correctamente.']);
    }
}
