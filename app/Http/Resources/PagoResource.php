<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_pagos'               => $this->id_pagos,
            'monto_pagado'           => (float) $this->monto_pagado,
            'fecha_pago'             => $this->fecha_pago ? $this->fecha_pago->format('d/m/Y') : null,
            'metodo_pago'            => $this->metodo_pago,
            'nro_comprobante'        => $this->nro_comprobante,
            'observaciones'          => $this->observaciones,
            'registrado_por'         => $this->whenLoaded('registradoPor', fn() => [
                'id_user'  => $this->registradoPor->id_user,
                'username' => $this->registradoPor->username,
                'persona'  => [
                    'nombres' => $this->registradoPor->persona?->nombres,
                    'primer_apellido' => $this->registradoPor->persona?->primer_apellido,
                ]
            ]),
            'inscripcion_id'         => $this->inscripcion_id,
            'inscripcion'            => $this->whenLoaded('inscripcion', fn() => [
                'id_inscripcion' => $this->inscripcion->id_inscripcion,
                'persona' => [
                    'nombres' => $this->inscripcion->persona?->nombres,
                    'primer_apellido' => $this->inscripcion->persona?->primer_apellido,
                    'ci' => $this->inscripcion->persona?->ci,
                ],
                'festividad' => [
                    'nombre' => $this->inscripcion->festividad?->nombre,
                ],
                'bloque' => [
                    'nombre' => $this->inscripcion->bloque?->nombre,
                ],
                'tipo_fraterno' => [
                    'nombre' => $this->inscripcion->tipoFraterno?->nombre,
                ],
            ]),
            'hora_pago' => $this->created_at ? $this->created_at->format('H:i') : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
