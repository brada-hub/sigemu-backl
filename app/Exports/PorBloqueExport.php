<?php

namespace App\Exports;

use App\Models\Inscripcion;
use App\Exports\Concerns\HasEstiloEncabezado;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class PorBloqueExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    use HasEstiloEncabezado;

    public function __construct(
        private readonly int $festividadId
    ) {}

    public function collection(): Collection
    {
        return Inscripcion::query()
            ->join('personas', 'inscripciones.persona_id', '=', 'personas.id')
            ->join('bloques', 'personas.bloque_id', '=', 'bloques.id')
            ->where('inscripciones.festividad_id', $this->festividadId)
            ->selectRaw('
                bloques.nombre as bloque,
                COUNT(inscripciones.id) as total_inscritos,
                SUM(CASE WHEN inscripciones.tipo_fraterno = "nuevo" THEN 1 ELSE 0 END) as nuevos,
                SUM(CASE WHEN inscripciones.tipo_fraterno = "antiguo" THEN 1 ELSE 0 END) as antiguos,
                SUM(inscripciones.monto_asignado) as total_esperado,
                SUM(inscripciones.total_pagado) as total_recaudado,
                SUM(inscripciones.monto_asignado - inscripciones.total_pagado) as total_pendiente,
                ROUND(SUM(inscripciones.total_pagado) / NULLIF(SUM(inscripciones.monto_asignado), 0) * 100, 2) as porcentaje
            ')
            ->groupBy('bloques.id', 'bloques.nombre')
            ->orderByDesc('total_recaudado')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Bloque',
            'Total inscritos',
            'Nuevos',
            'Antiguos',
            'Total esperado (Bs)',
            'Total recaudado (Bs)',
            'Total pendiente (Bs)',
            '% Avance',
        ];
    }

    public function map($fila): array
    {
        return [
            $fila->bloque,
            $fila->total_inscritos,
            $fila->nuevos,
            $fila->antiguos,
            number_format($fila->total_esperado, 2),
            number_format($fila->total_recaudado, 2),
            number_format($fila->total_pendiente, 2),
            $fila->porcentaje . '%',
        ];
    }

    public function title(): string
    {
        return 'Por bloque';
    }
}
