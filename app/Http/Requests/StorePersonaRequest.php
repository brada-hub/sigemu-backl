<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_bloque'        => ['nullable', 'integer', 'exists:bloques,id_bloque'],
            'nombres'          => ['required', 'string', 'max:100'],
            'primer_apellido'  => ['required', 'string', 'max:80'],
            'segundo_apellido' => ['nullable', 'string', 'max:80'],
            'ci'               => ['required', 'string', 'max:20', 'unique:persona,ci'],
            'celular'          => ['nullable', 'string', 'max:20'],
            'correo_personal'  => ['nullable', 'email', 'max:150', 'unique:persona,correo_personal'],
        ];
    }
}
