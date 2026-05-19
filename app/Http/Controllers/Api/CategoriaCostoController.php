<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaCosto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaCostoController extends Controller
{
    public function index(int $festividadId): JsonResponse
    {
        return response()->json(CategoriaCosto::with('tipoFraterno')
            ->where('festividad_id', $festividadId)
            ->get());
    }

    public function store(Request $request, int $festividadId): JsonResponse
    {
        $data = $request->validate([
            'id_tipo_fraterno' => 'required|exists:tipo_fraterno,id_tipo_fraterno',
            'nombre' => 'required|string',
            'monto_total' => 'required|numeric|min:0'
        ]);

        $data['festividad_id'] = $festividadId;
        $data['nombre'] = strtoupper($request->nombre);
        $categoria = CategoriaCosto::create($data);
        
        return response()->json($categoria->load('tipoFraterno'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(CategoriaCosto::with(['festividad', 'tipoFraterno'])->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $categoria = CategoriaCosto::findOrFail($id);
        $data = $request->validate([
            'id_tipo_fraterno' => 'exists:tipo_fraterno,id_tipo_fraterno',
            'nombre' => 'string',
            'monto_total' => 'numeric|min:0'
        ]);

        if ($request->has('nombre')) $data['nombre'] = strtoupper($request->nombre);
        $categoria->update($data);
        return response()->json($categoria->fresh('tipoFraterno'));
    }

    public function destroy(int $id): JsonResponse
    {
        $categoria = CategoriaCosto::findOrFail($id);
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada']);
    }
}
