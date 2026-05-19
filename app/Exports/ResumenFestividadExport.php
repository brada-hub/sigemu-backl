<?php

namespace App\Exports;

use App\Models\Inscripcion;
use App\Exports\Concerns\HasEstiloEncabezado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ResumenFestividadExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    use HasEstiloEncabezado;

    public function __construct(
        private readonly int    $festividadId,
        private readonly string $nombreFestividad
    ) {}

    public function query()
    {
        return Inscripcion::query()
            ->with(['persona.bloque', 'categoriaCosto'])
            ->where('festividad_id', $this->festividadId)
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'N°',
            'CI',
            'Nombre completo',
            'Bloque',
            'Tipo',
            'Categoría',
            'Monto asignado (Bs)',
            'Total pagado (Bs)',
            'Saldo pendiente (Bs)',
            '% Pagado',
            'Estado de pago',
            'Fecha inscripción',
        ];
    }

    public function map($inscripcion): array
    {
        static $numero = 0;
        $numero++;

        $saldo      = $inscripcion->saldo_pendiente;
        $porcentaje = $inscripcion->porcentaje_pagado;

        return [
            $numero,
            $inscripcion->persona->ci,
            $inscripcion->persona->nombre_completo,
            $inscripcion->persona->bloque->nombre,
            ucfirst($inscripcion->tipo_fraterno),
            $inscripcion->categoriaCosto->nombre,
            number_format($inscripcion->monto_asignado, 2),
            number_format($inscripcion->total_pagado, 2),
            number_format($saldo, 2),
            $porcentaje . '%',
            $saldo <= 0 ? 'Al día' : 'Con deuda',
            $inscripcion->inscrito_at->format('d/m/Y'),
        ];
    }

    public function title(): string
    {
        return 'Resumen general';
    }
}
