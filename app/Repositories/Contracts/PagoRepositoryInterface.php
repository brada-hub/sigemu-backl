<?php

namespace App\Repositories\Contracts;

use App\Models\Pago;
use Illuminate\Pagination\LengthAwarePaginator;

interface PagoRepositoryInterface
{
    public function paginarPorInscripcion(int $inscripcionId): LengthAwarePaginator;
    public function crear(array $datos): Pago;
    public function eliminar(Pago $pago): void;
    public function sumaPorFestividad(int $festividadId): float;
}
