<?php

namespace App\Repositories;

use App\Models\Pago;
use App\Repositories\Contracts\PagoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PagoRepository implements PagoRepositoryInterface
{
    public function paginarPorInscripcion(int $inscripcionId): LengthAwarePaginator
    {
        return Pago::with('registradoPor.persona')
            ->where('inscripcion_id', $inscripcionId)
            ->orderByDesc('fecha_pago')
            ->paginate(20);
    }

    public function crear(array $datos): Pago
    {
        return Pago::create($datos);
    }

    public function eliminar(Pago $pago): void
    {
        $pago->delete();
    }

    public function sumaPorFestividad(int $festividadId): float
    {
        return Pago::whereHas('inscripcion', fn($q) => $q->where('festividad_id', $festividadId))
            ->sum('monto_pagado'); // changed monto to monto_pagado
    }
}
