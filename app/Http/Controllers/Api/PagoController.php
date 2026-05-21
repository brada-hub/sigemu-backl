<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Resources\PagoResource;
use App\Models\Pago;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use App\Repositories\Contracts\PagoRepositoryInterface;
use App\Services\PagoService;
use Barryvdh\DomPDF\Facade\Pdf;
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

    /**
     * Genera un PDF del ticket/comprobante de pago.
     */
    public function ticket(int $inscripcionId, int $pagoId)
    {
        $pago = Pago::with(['inscripcion.persona', 'inscripcion.festividad', 'inscripcion.bloque', 'inscripcion.tipoFraterno', 'registradoPor.persona'])
            ->where('inscripcion_id', $inscripcionId)
            ->findOrFail($pagoId);

        $inscripcion = $pago->inscripcion;
        $persona = $inscripcion->persona;
        $festividad = $inscripcion->festividad;

        // Nombre completo del fraterno
        $fraternoNombre = trim(
            ($persona->nombres ?? '') . ' ' .
            ($persona->primer_apellido ?? '') . ' ' .
            ($persona->segundo_apellido ?? '')
        );
        $fraternoNombre = mb_strtoupper($fraternoNombre);

        // Usuario que registró el pago
        $usuario = $pago->registradoPor;
        $usuarioRegistro = 'Sistema';
        if ($usuario && $usuario->persona) {
            $usuarioRegistro = trim($usuario->persona->nombres . ' ' . ($usuario->persona->primer_apellido ?? ''));
        } elseif ($usuario) {
            $usuarioRegistro = $usuario->username;
        }

        // Histórico de pagos: Sumamos solo los pagos anteriores o iguales a este pago exacto
        $pagosAnteriores = Pago::where('inscripcion_id', $inscripcionId)
            ->where('id_pagos', '<', $pago->id_pagos)
            ->sum('monto_pagado');
            
        $totalPagadoHastaAhora = $pagosAnteriores + $pago->monto_pagado;
        $saldoPendiente = $inscripcion->monto_asignado - $totalPagadoHastaAhora;
        $saldoAnterior = $inscripcion->monto_asignado - $pagosAnteriores;

        // Ruta del logo embebida como base64 para que DomPDF la renderice
        $logoFile = public_path('images/sigemu.png');
        $logoBase64 = '';
        if (file_exists($logoFile)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile));
        }

        $data = [
            'nro_comprobante'  => $pago->nro_comprobante ?? 'S/N',
            'fraterno_nombre'  => $fraternoNombre ?: 'SIN NOMBRE',
            'ci_fraterno'      => $persona->ci ?? 'N/A',
            'festividad'       => mb_strtoupper($festividad->nombre ?? 'N/A'),
            'metodo_pago'      => $pago->metodo_pago,
            'bloque_nombre'    => mb_strtoupper($inscripcion->bloque->nombre ?? 'SIN BLOQUE'),
            'tipo_fraterno'    => mb_strtoupper($inscripcion->tipoFraterno->nombre ?? 'NUEVO'),
            'monto_asignado'   => number_format((float) $inscripcion->monto_asignado, 0, ',', '.'),
            'pagos_anteriores' => number_format((float) $pagosAnteriores, 0, ',', '.'),
            'saldo_anterior'   => number_format((float) max(0, $saldoAnterior), 0, ',', '.'),
            'monto_pagado'     => number_format((float) $pago->monto_pagado, 0, ',', '.'),
            'saldo_pendiente'  => number_format(max(0, $saldoPendiente), 0, ',', '.'),
            'observaciones'    => $pago->observaciones ?: 'NINGUNA',
            'fecha_pago'       => $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '',
            'hora_pago'        => $pago->created_at ? $pago->created_at->format('H:i') : '',
            'usuario_registro' => $usuarioRegistro,
            'logo_path'        => $logoBase64,
        ];

        $pdf = Pdf::loadView('pdf.ticket-pago', $data);
        $pdf->setPaper('letter', 'portrait');

        $filename = "ticket_pago_{$pago->nro_comprobante}.pdf";

        return $pdf->stream($filename);
    }
}
