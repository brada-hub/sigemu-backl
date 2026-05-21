<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'monto_pagado'    => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'      => ['required', 'date', 'before_or_equal:today'],
            'metodo_pago'     => ['required', 'in:Efectivo,Transferencia,QR'],
            'nro_comprobante' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    $inscripcionId = $this->route('inscripcion');
                    $inscripcion = \App\Models\Inscripcion::find($inscripcionId);
                    if (!$inscripcion) {
                        return;
                    }
                    $exists = \App\Models\Pago::whereHas('inscripcion', function ($q) use ($inscripcion) {
                        $q->where('festividad_id', $inscripcion->festividad_id);
                    })
                    ->where('nro_comprobante', $value)
                    ->exists();

                    if ($exists) {
                        $fail("El número de comprobante '{$value}' ya ha sido registrado en esta festividad.");
                    }
                }
            ],
            'observaciones'   => ['nullable', 'string', 'max:500'],
        ];
    }
}
