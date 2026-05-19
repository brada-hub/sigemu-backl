<?php

namespace App\Repositories\Contracts;

use App\Models\Persona;
use Illuminate\Pagination\LengthAwarePaginator;

interface PersonaRepositoryInterface
{
    public function paginar(array $filtros, int $perPage = 15): LengthAwarePaginator;
    public function encontrar(int $id): Persona;
    public function crear(array $datos): Persona;
    public function actualizar(Persona $persona, array $datos): Persona;
    public function eliminar(Persona $persona): void;
}
