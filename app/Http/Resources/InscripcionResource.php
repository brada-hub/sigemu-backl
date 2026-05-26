<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscripcionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total_pagado = $this->pagos ? $this->pagos->sum('monto_pagado') : 0;
        
        return [
            'id_inscripcion'     => $this->id_inscripcion,
            'persona_id'         => $this->persona_id,
            'id_tipo_fraterno'   => $this->id_tipo_fraterno,
            'id_bloque'          => $this->id_bloque,
            'categoria_costo_id' => $this->categoria_costo_id,
            'festividad_id'      => $this->festividad_id,
            'persona'            => $this->whenLoaded('persona'),
            'festividad'         => $this->whenLoaded('festividad'),
            'bloque'             => $this->whenLoaded('bloque'),
            'tipo_fraterno'      => $this->whenLoaded('tipoFraterno'),
            'categoria_costo'    => $this->whenLoaded('categoriaCosto'),
            'monto_asignado'     => (float) $this->monto_asignado,
            'total_pagado'       => (float) $total_pagado,
            'saldo_pendiente'    => (float) ($this->monto_asignado - $total_pagado),
            'porcentaje_pagado'  => $this->monto_asignado > 0 ? round(($total_pagado / $this->monto_asignado) * 100) : 0,
            'estado_pago'        => $this->estado_pago,
            'inscrito_at'        => $this->inscrito_at ? $this->inscrito_at->format('d/m/Y') : null,
            'pagos'              => PagoResource::collection($this->whenLoaded('pagos')),
        ];
    }
}
