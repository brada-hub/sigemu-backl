<?php

namespace App\Services;

use App\Exports\ReporteFestividadCompletoExport;
use App\Exports\HistorialPersonaExport;
use App\Models\Persona;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    // Reporte completo de festividad (multi-hoja)
    public function reporteFestividad(int $festividadId, string $desde, string $hasta): BinaryFileResponse
    {
        $nombre = "reporte_festividad_{$festividadId}_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ReporteFestividadCompletoExport($festividadId, $desde, $hasta),
            $nombre
        );
    }

    // Historial individual de un fraterno
    public function historialPersona(int $personaId): BinaryFileResponse
    {
        $persona = Persona::findOrFail($personaId);
        $nombre  = "historial_{$persona->ci}_" . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new HistorialPersonaExport($personaId, $persona->nombre_completo),
            $nombre
        );
    }
}
