<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $personaId = $this->route('persona');

        return [
            'bloque_id'        => ['sometimes', 'integer', 'exists:bloques,id'],
            'nombres'          => ['sometimes', 'string', 'max:100'],
            'primer_apellido'  => ['sometimes', 'string', 'max:80'],
            'segundo_apellido' => ['nullable', 'string', 'max:80'],
            'ci'               => ['sometimes', 'string', 'max:20', Rule::unique('personas', 'ci')->ignore($personaId)],
            'celular'          => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:150', Rule::unique('personas', 'email')->ignore($personaId)],
        ];
    }
}
