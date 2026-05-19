<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Festividad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FestividadController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Festividad::orderByDesc('fecha_inicio')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|string|in:Activa,Inactiva,Finalizada,Planificada'
        ]);

        $data['nombre'] = strtoupper($request->nombre);
        $festividad = Festividad::create($data);
        return response()->json($festividad, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Festividad::findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $festividad = Festividad::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'string|in:Activa,Inactiva,Finalizada,Planificada'
        ]);

        if ($request->has('nombre')) $data['nombre'] = strtoupper($request->nombre);
        $festividad->update($data);
        return response()->json($festividad);
    }

    public function destroy(int $id): JsonResponse
    {
        $festividad = Festividad::findOrFail($id);
        $festividad->delete();
        return response()->json(['message' => 'Festividad eliminada']);
    }
}
