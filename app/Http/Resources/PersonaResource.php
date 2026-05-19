<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'nombre_completo' => $this->nombre_completo,
            'nombres'         => $this->nombres,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido'=> $this->segundo_apellido,
            'ci'              => $this->ci,
            'celular'         => $this->celular,
            'email'           => $this->email,
            'bloque'          => new BloqueResource($this->whenLoaded('bloque')),
            'created_at'      => $this->created_at->format('d/m/Y'),
        ];
    }
}
