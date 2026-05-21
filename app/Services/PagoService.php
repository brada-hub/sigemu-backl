<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Inscripcion;
use App\Repositories\Contracts\PagoRepositoryInterface;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PagoService
{
    public function __construct(
        private readonly PagoRepositoryInterface $pagoRepository,
        private readonly InscripcionRepositoryInterface $inscripcionRepository
    ) {}

    public function registrar(Inscripcion $inscripcion, array $datos, int $usuarioId): Pago
    {
        $montoNuevo = (float) $datos['monto_pagado'];
        $totalPagado = $inscripcion->pagos()->sum('monto_pagado');
        $saldo = $inscripcion->monto_asignado - $totalPagado;

        if ($montoNuevo <= 0) {
            throw new InvalidArgumentException('El monto del pago debe ser mayor a cero.');
        }

        if ($montoNuevo > $saldo) {
            throw new InvalidArgumentException(
                "El monto Bs {$montoNuevo} supera el saldo pendiente de Bs {$saldo}."
            );
        }

        return DB::transaction(function () use ($inscripcion, $datos, $usuarioId) {
            if (empty($datos['nro_comprobante'])) {
                $count = Pago::withTrashed()->whereHas('inscripcion', function($q) use ($inscripcion) {
                    $q->where('festividad_id', $inscripcion->festividad_id);
                })->count();
                $datos['nro_comprobante'] = str_pad((string)($count + 1), 4, '0', STR_PAD_LEFT);
            }

            $pago = $this->pagoRepository->crear([
                ...$datos,
                'inscripcion_id' => $inscripcion->id_inscripcion,
                'registrado_por' => $usuarioId,
            ]);

            // Actualiza el campo estado_pago
            $this->inscripcionRepository->actualizarEstadoPago($inscripcion);

            return $pago;
        });
    }

    public function eliminar(Pago $pago): void
    {
        DB::transaction(function () use ($pago) {
            $inscripcion = $pago->inscripcion;
            $this->pagoRepository->eliminar($pago);
            $this->inscripcionRepository->actualizarEstadoPago($inscripcion);
        });
    }
}
