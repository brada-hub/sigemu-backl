<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Resources\PagoResource;
use App\Models\Pago;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use App\Repositories\Contracts\PagoRepositoryInterface;
use App\Services\PagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function __construct(
        private readonly PagoRepositoryInterface $pagoRepository,
        private readonly InscripcionRepositoryInterface $inscripcionRepository,
        private readonly PagoService $pagoService
    ) {}

    public function index(int $inscripcionId)
    {
        $pagos = $this->pagoRepository->paginarPorInscripcion($inscripcionId);
        return PagoResource::collection($pagos);
    }

    public function globalIndex(Request $request)
    {
        $query = Pago::with(['inscripcion.persona', 'inscripcion.festividad', 'inscripcion.bloque', 'inscripcion.tipoFraterno', 'registradoPor.persona'])
            ->when($request->buscar, function ($q, $buscar) {
                $q->whereHas('inscripcion.persona', function ($qp) use ($buscar) {
                    $qp->where('nombres', 'like', "%{$buscar}%")
                       ->orWhere('primer_apellido', 'like', "%{$buscar}%")
                       ->orWhere('segundo_apellido', 'like', "%{$buscar}%")
                       ->orWhere('ci', 'like', "%{$buscar}%");
                });
            })
            ->when($request->metodo_pago, fn($q, $metodo) => $q->where('metodo_pago', $metodo))
            ->when($request->id_bloque, function ($q, $bloque) {
                $q->whereHas('inscripcion', function ($qi) use ($bloque) {
                    $qi->where('id_bloque', $bloque);
                });
            })
            ->when($request->festividad_id, function ($q, $fest) {
                $q->whereHas('inscripcion', function ($qi) use ($fest) {
                    $qi->where('festividad_id', $fest);
                });
            })
            ->when($request->id_tipo_fraterno, function ($q, $tipo) {
                $q->whereHas('inscripcion', function ($qi) use ($tipo) {
                    $qi->where('id_tipo_fraterno', $tipo);
                });
            })
            ->when($request->registrado_por_id, fn($q, $id) => $q->where('registrado_por', $id))
            ->when($request->fecha_inicio, fn($q, $f) => $q->whereDate('fecha_pago', '>=', $f))
            ->when($request->fecha_fin, fn($q, $f) => $q->whereDate('fecha_pago', '<=', $f))
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->boolean('export')) {
            return PagoResource::collection($query->get());
        }
            
        return PagoResource::collection($query->paginate($request->input('per_page', 15)));
    }

    public function store(StorePagoRequest $request, int $inscripcionId): PagoResource
    {
        $inscripcion = $this->inscripcionRepository->encontrar($inscripcionId);
        $data = $request->validated();
        if (isset($data['observaciones'])) $data['observaciones'] = strtoupper($data['observaciones']);
        
        $pago = $this->pagoService->registrar($inscripcion, $data, auth()->id());
        return new PagoResource($pago->load('registradoPor.persona'));
    }

    public function destroy(int $inscripcionId, int $pagoId): JsonResponse
    {
        $pago = Pago::where('inscripcion_id', $inscripcionId)->findOrFail($pagoId);
        $this->pagoService->eliminar($pago);
        return response()->json(['message' => 'Pago eliminado y saldo recalculado.']);
    }
}
