<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Policies\ReportePolicy;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private readonly ExportService $exportService
    ) {}

    // GET /api/festividades/{festividad}/exportar?desde=2025-01-01&hasta=2025-12-31
    public function festividad(Request $request, int $festividadId): BinaryFileResponse
    {
        $this->authorize('exportar', ReportePolicy::class);

        $request->validate([
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
        ]);

        return $this->exportService->reporteFestividad(
            $festividadId,
            $request->desde,
            $request->hasta
        );
    }

    // GET /api/personas/{persona}/exportar
    public function persona(int $personaId): BinaryFileResponse
    {
        $this->authorize('exportar', ReportePolicy::class);

        return $this->exportService->historialPersona($personaId);
    }
}
