<?php

namespace App\Exports;

use App\Models\Festividad;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteFestividadCompletoExport implements WithMultipleSheets
{
    public function __construct(
        private readonly int    $festividadId,
        private readonly string $desde,
        private readonly string $hasta
    ) {}

    public function sheets(): array
    {
        $festividad = Festividad::findOrFail($this->festividadId);

        return [
            new ResumenFestividadExport($this->festividadId, $festividad->nombre),
            new PorBloqueExport($this->festividadId),
            new PorFechaExport($this->festividadId, $this->desde, $this->hasta),
        ];
    }
}
