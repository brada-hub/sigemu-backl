<?php

namespace App\Services;

use App\Models\Festividad;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\TipoFraterno;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    public function resumenFestividad(int $festividadId, ?int $idTipoPersona = null): array
    {
        $festividad = Festividad::findOrFail($festividadId);

        $inscripciones = Inscripcion::with(['pagos', 'persona.sexo', 'bloque', 'tipoFraterno'])
            ->where('festividad_id', $festividadId)
            ->when($idTipoPersona, fn($q) => $q->whereHas('persona', fn($pq) => $pq->where('id_tipo_persona', $idTipoPersona)))
            ->get();

        $totalInscritos = $inscripciones->count();
        $totalEsperado = $inscripciones->sum('monto_asignado');
        $totalRecaudado = $inscripciones->sum(fn($ins) => $ins->pagos->sum('monto_pagado'));
        $totalPendiente = $totalEsperado - $totalRecaudado;

        // Por bloque (Enriquecido)
        $porBloque = $inscripciones->groupBy('id_bloque')->map(function($grupo) {
            $totalPagado = $grupo->sum(fn($ins) => $ins->pagos->sum('monto_pagado'));
            $montoAsignado = $grupo->sum('monto_asignado');
            return [
                'bloque' => $grupo->first()->bloque->nombre ?? 'Sin bloque',
                'total_inscritos' => $grupo->count(),
                'total_esperado' => $montoAsignado,
                'total_recaudado' => $totalPagado,
                'eficiencia' => $montoAsignado > 0 ? round(($totalPagado / $montoAsignado) * 100) : 0
            ];
        })->sortByDesc('total_recaudado')->values()->toArray();

        // Tipos
        $tipos = $inscripciones->groupBy('id_tipo_fraterno')->map(fn($g) => $g->count());
        $tipoNuevo = TipoFraterno::where('nombre', 'NUEVO')->orWhere('nombre', 'Nuevo')->first()->id_tipo_fraterno ?? null;
        $tipoAntiguo = TipoFraterno::where('nombre', 'ANTIGUO')->orWhere('nombre', 'Antiguo')->first()->id_tipo_fraterno ?? null;

        $nuevos = $tipos->get($tipoNuevo, 0);
        $antiguos = $tipos->get($tipoAntiguo, 0);

        // Género (Ajustado para evitar errores si no hay sexo definido)
        $distribucionGenero = $inscripciones->groupBy(function($ins) {
            return $ins->persona->sexo->sexo ?? 'Sin definir';
        })->map(fn($g) => $g->count());

        // Métodos de Pago
        $pagosFestividad = Pago::whereHas('inscripcion', fn($q) => $q->where('festividad_id', $festividadId))->get();
        $metodosPago = $pagosFestividad->groupBy('metodo_pago')->map(fn($g) => [
            'cantidad' => $g->count(),
            'total' => $g->sum('monto_pagado')
        ]);

        // Alertas
        $alertas = $inscripciones->filter(fn($ins) => $ins->estado_pago !== 'Pagado')
            ->map(function ($ins) {
                $totalPagado = $ins->pagos->sum('monto_pagado');
                $porcentaje = $ins->monto_asignado > 0 ? round(($totalPagado / $ins->monto_asignado) * 100) : 0;
                return [
                    'persona_id' => $ins->persona_id,
                    'nombre' => trim($ins->persona->nombres . ' ' . $ins->persona->primer_apellido),
                    'bloque' => $ins->bloque->nombre ?? 'Sin bloque',
                    'monto_asignado' => $ins->monto_asignado,
                    'total_pagado' => $totalPagado,
                    'pendiente' => $ins->monto_asignado - $totalPagado,
                    'porcentaje' => $porcentaje,
                    'nivel' => $porcentaje === 0.0 ? 'urgente' : ($porcentaje < 50 ? 'moderado' : 'leve')
                ];
            })->sortBy('porcentaje')->take(8)->values()->toArray();

        // Actividad (Inscripciones Recientes + Pagos Recientes)
        $pagosRecientes = Pago::whereHas('inscripcion', fn($q) => $q->where('festividad_id', $festividadId))
            ->with(['inscripcion.persona', 'inscripcion.bloque'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($pago) => [
                'tipo' => 'pago',
                'nombre' => trim($pago->inscripcion->persona->nombres . ' ' . $pago->inscripcion->persona->primer_apellido),
                'bloque' => $pago->inscripcion->bloque->nombre ?? 'Sin bloque',
                'monto' => $pago->monto_pagado,
                'metodo' => $pago->metodo_pago,
                'fecha' => $pago->created_at->diffForHumans()
            ]);

        return [
            'festividad'       => $festividad->nombre,
            'total_inscritos'  => $totalInscritos,
            'total_esperado'   => $totalEsperado,
            'total_recaudado'  => $totalRecaudado,
            'total_pendiente'  => $totalPendiente,
            'porcentaje_avance'=> $totalEsperado > 0 ? round(($totalRecaudado / $totalEsperado) * 100, 2) : 0,
            'recaudacion_por_bloque' => $porBloque,
            'nuevos'           => $nuevos,
            'antiguos'         => $antiguos,
            'generos'          => $distribucionGenero,
            'metodos_pago'     => $metodosPago,
            'alertas'          => $alertas,
            'actividad'        => $pagosRecientes,
        ];
    }

    public function porBloque(int $festividadId, ?int $idTipoPersona = null): array
    {
        $inscripciones = Inscripcion::with(['pagos', 'bloque'])
            ->where('festividad_id', $festividadId)
            ->when($idTipoPersona, fn($q) => $q->whereHas('persona', fn($pq) => $pq->where('id_tipo_persona', $idTipoPersona)))
            ->get();

        return $inscripciones->groupBy('id_bloque')->map(function($grupo) {
            $totalPagado = $grupo->sum(fn($ins) => $ins->pagos->sum('monto_pagado'));
            $montoAsignado = $grupo->sum('monto_asignado');
            return [
                'bloque' => $grupo->first()->bloque->nombre ?? 'Sin bloque',
                'total_inscritos' => $grupo->count(),
                'total_esperado' => $montoAsignado,
                'total_recaudado' => $totalPagado,
                'total_pendiente' => $montoAsignado - $totalPagado
            ];
        })->sortByDesc('total_recaudado')->values()->toArray();
    }

    public function porFecha(int $festividadId, string $desde, string $hasta, ?int $idTipoPersona = null): array
    {
        $query = Pago::whereHas('inscripcion', function($q) use ($festividadId, $idTipoPersona) {
            $q->where('festividad_id', $festividadId);
            if ($idTipoPersona) {
                $q->whereHas('persona', fn($pq) => $pq->where('id_tipo_persona', $idTipoPersona));
            }
        })
            ->whereBetween('fecha_pago', [$desde, $hasta]);

        return $query
            ->selectRaw('DATE(fecha_pago) as fecha, SUM(monto_pagado) as total, COUNT(*) as cantidad_pagos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    public function historialPersona(int $personaId): array
    {
        return Inscripcion::where('persona_id', $personaId)
            ->with(['festividad', 'pagos', 'categoriaCosto', 'tipoFraterno'])
            ->get()
            ->map(function($inscripcion) {
                $totalPagado = $inscripcion->pagos->sum('monto_pagado');
                return [
                    'festividad'      => $inscripcion->festividad->nombre,
                    'tipo_fraterno'   => $inscripcion->tipoFraterno->nombre ?? '',
                    'monto_asignado'  => $inscripcion->monto_asignado,
                    'total_pagado'    => $totalPagado,
                    'saldo_pendiente' => $inscripcion->monto_asignado - $totalPagado,
                    'pagos'           => $inscripcion->pagos->map(fn($p) => [
                        'fecha'  => $p->fecha_pago->format('d/m/Y'),
                        'monto'  => $p->monto_pagado,
                        'metodo' => $p->metodo_pago,
                    ]),
                ];
            })
            ->toArray();
    }
}
