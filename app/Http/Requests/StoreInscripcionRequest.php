<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscripcionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'persona_id'         => ['required', 'integer', 'exists:persona,id_persona'],
            'festividad_id'      => ['required', 'integer', 'exists:festividad,id_festividad'],
            'id_bloque'          => ['required', 'integer', 'exists:bloques,id_bloque'],
            'id_tipo_fraterno'   => ['required', 'integer', 'exists:tipo_fraterno,id_tipo_fraterno'],
            'categoria_costo_id' => ['required', 'integer', 'exists:categorias_costo,id_categoria_costo'],
            'monto_asignado'     => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
