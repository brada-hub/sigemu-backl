<?php

namespace App\Exports;

use App\Models\Pago;
use App\Exports\Concerns\HasEstiloEncabezado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistorialPersonaExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    use HasEstiloEncabezado;

    public function __construct(
        private readonly int    $personaId,
        private readonly string $nombrePersona
    ) {}

    public function query()
    {
        return Pago::query()
            ->with(['inscripcion.festividad', 'registradoPor'])
            ->whereHas('inscripcion', fn($q) => $q->where('persona_id', $this->personaId))
            ->orderBy('fecha_pago');
    }

    public function headings(): array
    {
        return [
            'Festividad',
            'Tipo fraterno',
            'Monto asignado (Bs)',
            'Fecha pago',
            'Monto pagado (Bs)',
            'Método',
            'Comprobante',
            'Registrado por',
        ];
    }

    public function map($pago): array
    {
        return [
            $pago->inscripcion->festividad->nombre,
            ucfirst($pago->inscripcion->tipo_fraterno),
            number_format($pago->inscripcion->monto_asignado, 2),
            $pago->fecha_pago->format('d/m/Y'),
            number_format($pago->monto, 2),
            ucfirst($pago->metodo),
            $pago->comprobante_referencia ?? '—',
            $pago->registradoPor->email,
        ];
    }

    public function title(): string
    {
        return "Historial - {$this->nombrePersona}";
    }
}
