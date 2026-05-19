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
            'nro_comprobante' => ['nullable', 'string', 'max:100'],
            'observaciones'   => ['nullable', 'string', 'max:500'],
        ];
    }
}
