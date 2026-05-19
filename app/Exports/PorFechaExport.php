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

class PorFechaExport implements
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
        private readonly string $desde,
        private readonly string $hasta
    ) {}

    public function query()
    {
        return Pago::query()
            ->with(['inscripcion.persona', 'registradoPor'])
            ->whereHas('inscripcion', fn($q) => $q->where('festividad_id', $this->festividadId))
            ->whereBetween('fecha_pago', [$this->desde, $this->hasta])
            ->orderBy('fecha_pago')
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'Fecha pago',
            'CI',
            'Fraterno',
            'Monto (Bs)',
            'Método',
            'Comprobante',
            'Registrado por',
            'Observaciones',
        ];
    }

    public function map($pago): array
    {
        return [
            $pago->fecha_pago->format('d/m/Y'),
            $pago->inscripcion->persona->ci,
            $pago->inscripcion->persona->nombre_completo,
            number_format($pago->monto, 2),
            ucfirst($pago->metodo),
            $pago->comprobante_referencia ?? '—',
            $pago->registradoPor->email,
            $pago->observaciones ?? '—',
        ];
    }

    public function title(): string
    {
        return 'Pagos por fecha';
    }
}
