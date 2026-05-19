<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bloque;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloqueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Bloque::with('fraternidad')->orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'id_fraternidad' => 'required|exists:fraternidad,id_fraternidad'
        ]);

        $data['nombre'] = strtoupper($request->nombre);
        $bloque = Bloque::create($data);
        return response()->json($bloque->load('fraternidad'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Bloque::with('fraternidad')->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bloque = Bloque::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'string',
            'id_fraternidad' => 'exists:fraternidad,id_fraternidad'
        ]);

        if ($request->has('nombre')) $data['nombre'] = strtoupper($request->nombre);
        $bloque->update($data);
        return response()->json($bloque->fresh('fraternidad'));
    }

    public function destroy(int $id): JsonResponse
    {
        $bloque = Bloque::findOrFail($id);
        $bloque->delete();
        return response()->json(['message' => 'Bloque eliminado']);
    }
}
