<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReporteService;
use App\Policies\ReportePolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Inscripcion;

class ReporteController extends Controller
{
    public function __construct(
        private readonly ReporteService $reporteService
    ) {}

    public function resumenFestividad(Request $request, int $festividadId): JsonResponse
    {
        $this->authorize('ver-reportes');

        return response()->json($this->reporteService->resumenFestividad($festividadId, $request->id_tipo_persona));
    }

    public function porBloque(Request $request, int $festividadId): JsonResponse
    {
        $this->authorize('ver-reportes');

        return response()->json($this->reporteService->porBloque($festividadId, $request->id_tipo_persona));
    }

    public function porFecha(Request $request, int $festividadId): JsonResponse
    {
        $this->authorize('ver-reportes');

        $request->validate([
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
        ]);

        return response()->json(
            $this->reporteService->porFecha($festividadId, $request->desde, $request->hasta, $request->id_tipo_persona)
        );
    }

    public function historialPersona(int $personaId): JsonResponse
    {
        $this->authorize('ver-reportes');

        return response()->json($this->reporteService->historialPersona($personaId));
    }

    public function datosGenerador(Request $request): JsonResponse
    {
        $this->authorize('ver-reportes');

        $query = Inscripcion::with(['persona.tipoPersona', 'festividad', 'bloque', 'tipoFraterno', 'categoriaCosto', 'pagos.registradoPor.persona'])
            ->when($request->festividad_id, fn($q, $fest) => $q->where('festividad_id', $fest))
            ->when($request->id_bloque, fn($q, $bloque) => $q->where('id_bloque', $bloque))
            ->when($request->id_tipo_fraterno, fn($q, $tipo) => $q->where('id_tipo_fraterno', $tipo))
            ->when($request->id_tipo_persona, fn($q, $tipoPersona) => $q->whereHas('persona', fn($pq) => $pq->where('id_tipo_persona', $tipoPersona)));

        if ($request->metodo_pago || $request->registrado_por_id) {
             $query->whereHas('pagos', function ($q) use ($request) {
                  if ($request->metodo_pago) $q->where('metodo_pago', $request->metodo_pago);
                  if ($request->registrado_por_id) $q->where('registrado_por', $request->registrado_por_id);
             });
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }
}
