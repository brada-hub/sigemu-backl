<?php

namespace App\Repositories;

use App\Models\Inscripcion;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InscripcionRepository implements InscripcionRepositoryInterface
{
    public function paginarPorFestividad(int $festividadId, array $filtros, int $perPage = 15): LengthAwarePaginator
    {
        return Inscripcion::with(['persona', 'bloque', 'tipoFraterno', 'categoriaCosto'])
            ->where('festividad_id', $festividadId)
            ->when(isset($filtros['estado_pago']), fn($q) => $q->where('estado_pago', $filtros['estado_pago']))
            ->when(isset($filtros['id_tipo_fraterno']), fn($q) => $q->where('id_tipo_fraterno', $filtros['id_tipo_fraterno']))
            ->when(isset($filtros['id_bloque']), fn($q) => $q->where('id_bloque', $filtros['id_bloque']))
            ->when(isset($filtros['buscar']), function ($q) use ($filtros) {
                $buscar = $filtros['buscar'];
                $q->whereHas('persona', function ($qp) use ($buscar) {
                    $qp->where('nombres', 'like', "%{$buscar}%")
                       ->orWhere('primer_apellido', 'like', "%{$buscar}%")
                       ->orWhere('segundo_apellido', 'like', "%{$buscar}%")
                       ->orWhere('ci', 'like', "%{$buscar}%");
                });
            })
            ->paginate($perPage);
    }

    public function encontrar(int $id): Inscripcion
    {
        return Inscripcion::with(['persona', 'festividad', 'bloque', 'tipoFraterno', 'categoriaCosto', 'pagos'])->findOrFail($id);
    }

    public function crear(array $datos): Inscripcion
    {
        return Inscripcion::create($datos);
    }

    public function actualizar(Inscripcion $inscripcion, array $datos): Inscripcion
    {
        $inscripcion->update($datos);
        return $inscripcion->fresh();
    }

    public function eliminar(int $id): void
    {
        $inscripcion = $this->encontrar($id);
        $inscripcion->delete();
    }

    public function actualizarEstadoPago(Inscripcion $inscripcion): void
    {
        $totalPagado = $inscripcion->pagos()->sum('monto_pagado');
        $montoAsignado = $inscripcion->monto_asignado;

        if ($totalPagado >= $montoAsignado) {
            $estado = 'Pagado';
        } elseif ($totalPagado > 0) {
            $estado = 'Parcial';
        } else {
            $estado = 'Pendiente';
        }

        $inscripcion->update(['estado_pago' => $estado]);
    }
}
