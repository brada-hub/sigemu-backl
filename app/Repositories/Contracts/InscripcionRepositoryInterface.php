<?php

namespace App\Repositories\Contracts;

use App\Models\Inscripcion;
use Illuminate\Pagination\LengthAwarePaginator;

interface InscripcionRepositoryInterface
{
    public function paginarPorFestividad(int $festividadId, array $filtros, int $perPage = 15): LengthAwarePaginator;
    public function encontrar(int $id): Inscripcion;
    public function crear(array $datos): Inscripcion;
    public function actualizar(Inscripcion $inscripcion, array $datos): Inscripcion;
    public function actualizarEstadoPago(Inscripcion $inscripcion): void;
}
