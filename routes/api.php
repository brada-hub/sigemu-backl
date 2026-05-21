<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BloqueController;
use App\Http\Controllers\Api\FestividadController;
use App\Http\Controllers\Api\CategoriaCostoController;
use App\Http\Controllers\Api\TipoFraternoController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\InscripcionController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\FraternidadController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RolController;
use Illuminate\Support\Facades\Route;

// Públicas
Route::post('auth/login', [AuthController::class, 'login']);

// Protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/cambiar-password', [AuthController::class, 'cambiarPassword']);
    Route::get('auth/me',     [AuthController::class, 'me']);

    // Catálogos
    Route::apiResource('bloques',     BloqueController::class);
    Route::apiResource('festividades', FestividadController::class);
    Route::apiResource('festividades.categorias-costo', CategoriaCostoController::class)->shallow();
    Route::get('tipos-fraternos', [TipoFraternoController::class, 'index']);
    Route::get('fraternidades', [FraternidadController::class, 'index']);
    Route::post('roles', [RolController::class, 'store']);
    Route::get('roles', [RolController::class, 'index']);
    Route::put('roles/{rol}', [RolController::class, 'update']);
    Route::delete('roles/{rol}', [RolController::class, 'destroy']);
    Route::get('permisos', [RolController::class, 'permisos']);
    Route::apiResource('usuarios', UsuarioController::class);

    // Personas
    Route::apiResource('personas', PersonaController::class);
    Route::get('personas/{persona}/historial', [ReporteController::class, 'historialPersona']);
    Route::get('personas/{persona}/exportar', [ExportController::class, 'persona']);

    // Inscripciones por festividad
    Route::prefix('festividades/{festividad}')->group(function () {
        Route::get('inscripciones',          [InscripcionController::class, 'index']);
        Route::post('inscripciones',         [InscripcionController::class, 'store']);
        Route::get('reportes/resumen',       [ReporteController::class, 'resumenFestividad']);
        Route::get('reportes/por-bloque',    [ReporteController::class, 'porBloque']);
        Route::get('reportes/por-fecha',     [ReporteController::class, 'porFecha']);
        Route::get('exportar',               [ExportController::class, 'festividad']);
    });

    Route::get('inscripciones/{inscripcion}',          [InscripcionController::class, 'show']);
    Route::delete('inscripciones/{inscripcion}',       [InscripcionController::class, 'destroy']);


    // Pagos
    Route::get('pagos', [PagoController::class, 'globalIndex']);
    Route::get('reportes/generador', [ReporteController::class, 'datosGenerador']);
    Route::prefix('inscripciones/{inscripcion}')->group(function () {
        Route::get('pagos',          [PagoController::class, 'index']);
        Route::post('pagos',         [PagoController::class, 'store']);
        Route::delete('pagos/{pago}', [PagoController::class, 'destroy']);
        Route::get('pagos/{pago}/ticket', [PagoController::class, 'ticket']);
    });
});
