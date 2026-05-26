<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInscripcionRequest;
use App\Http\Resources\InscripcionResource;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use App\Services\InscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $inscripcionRepository,
        private readonly InscripcionService $inscripcionService
    ) {}

    public function index(Request $request, int $festividadId)
    {
        $perPage = $request->input('per_page', 15);
        if ($perPage == 0) {
            $perPage = 10000;
        }

        $inscripciones = $this->inscripcionRepository->paginarPorFestividad(
            $festividadId,
            $request->only(['estado_pago', 'id_tipo_fraterno', 'id_bloque', 'buscar']),
            $perPage
        );
        return InscripcionResource::collection($inscripciones);
    }

    public function store(StoreInscripcionRequest $request): InscripcionResource
    {
        $inscripcion = $this->inscripcionService->inscribir($request->validated());
        return new InscripcionResource($inscripcion->load(['persona', 'festividad', 'categoriaCosto', 'bloque', 'tipoFraterno']));
    }

    public function show(int $id): InscripcionResource
    {
        return new InscripcionResource($this->inscripcionRepository->encontrar($id));
    }

    public function update(Request $request, int $id): InscripcionResource
    {
        $datos = $request->validate([
            'id_tipo_fraterno' => 'required|exists:tipo_fraterno,id_tipo_fraterno',
            'id_bloque' => 'required|exists:bloques,id_bloque',
            'categoria_costo_id' => 'required|exists:categorias_costo,id_categoria_costo',
            'monto_asignado' => 'required|numeric|min:0'
        ]);

        $inscripcion = $this->inscripcionRepository->encontrar($id);
        $inscripcion = $this->inscripcionRepository->actualizar($inscripcion, $datos);
        
        return new InscripcionResource($inscripcion->load(['persona', 'festividad', 'categoriaCosto', 'bloque', 'tipoFraterno']));
    }

    public function destroy(int $id): JsonResponse
    {
        $inscripcion = $this->inscripcionRepository->encontrar($id);
        
        if ($inscripcion->pagos()->exists()) {
            return response()->json(['message' => 'No se puede retirar un fraterno que ya tiene pagos registrados. Debe anular los pagos primero.'], 422);
        }

        $this->inscripcionRepository->eliminar($id);
        
        return response()->json(['message' => 'Fraterno retirado de la festividad correctamente.']);
    }
}

