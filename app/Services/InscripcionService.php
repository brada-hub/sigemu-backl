<?php

namespace App\Services;

use App\Models\Inscripcion;
use App\Models\CategoriaCosto;
use App\Repositories\Contracts\InscripcionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InscripcionService
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $inscripcionRepository
    ) {}

    public function inscribir(array $datos): Inscripcion
    {
        $categoria = CategoriaCosto::findOrFail($datos['categoria_costo_id']);

        if ($categoria->id_tipo_fraterno != $datos['id_tipo_fraterno']) {
            throw new InvalidArgumentException(
                "La categoría '{$categoria->nombre}' no aplica para este tipo de fraterno."
            );
        }

        return DB::transaction(function () use ($datos, $categoria) {
            return $this->inscripcionRepository->crear([
                'persona_id'         => $datos['persona_id'],
                'festividad_id'      => $datos['festividad_id'],
                'id_bloque'          => $datos['id_bloque'],
                'id_tipo_fraterno'   => $datos['id_tipo_fraterno'],
                'categoria_costo_id' => $datos['categoria_costo_id'],
                'monto_asignado'     => $datos['monto_asignado'] ?? $categoria->monto_total,
                'estado_pago'        => 'Pendiente',
                'inscrito_at'        => now(),
            ]);
        });
    }
}
