<?php

namespace App\Repositories;

use App\Models\Persona;
use App\Repositories\Contracts\PersonaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonaRepository implements PersonaRepositoryInterface
{
    public function paginar(array $filtros, int $perPage = 15): LengthAwarePaginator
    {
        return Persona::with('sexo')
            ->when(isset($filtros['buscar']), function ($q) use ($filtros) {
                $q->where('nombres', 'like', "%{$filtros['buscar']}%")
                  ->orWhere('primer_apellido', 'like', "%{$filtros['buscar']}%")
                  ->orWhere('segundo_apellido', 'like', "%{$filtros['buscar']}%")
                  ->orWhere('ci', 'like', "%{$filtros['buscar']}%");
            })
            ->when(isset($filtros['id_sexo']), fn($q) => $q->where('id_sexo', $filtros['id_sexo']))
            ->orderBy('primer_apellido')
            ->paginate($perPage);
    }

    public function encontrar(int $id): Persona
    {
        return Persona::with('sexo')->findOrFail($id);
    }

    public function crear(array $datos): Persona
    {
        return Persona::create($datos);
    }

    public function actualizar(Persona $persona, array $datos): Persona
    {
        $persona->update($datos);
        return $persona->fresh('sexo');
    }

    public function eliminar(Persona $persona): void
    {
        $persona->delete();
    }
}
