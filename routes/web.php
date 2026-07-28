<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\RegistroComedorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservacionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EncuestaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reservaciones.create');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/send-email', [DashboardController::class, 'sendReportEmail'])->middleware(['auth'])->name('dashboard.send_email');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Gestión de Empleados
    Route::get('empleados/template', [EmpleadoController::class, 'downloadTemplate'])->name('empleados.template');
    Route::post('empleados/import', [EmpleadoController::class, 'import'])->name('empleados.import');
    Route::resource('empleados', EmpleadoController::class)->except(['create', 'show', 'edit']);
    Route::patch('empleados/{empleado}/toggle', [EmpleadoController::class, 'toggleStatus'])->name('empleados.toggle');

    // Rutas para Reportes de Visitas, Encuestas y Exportación CSV
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/exportar', [ReporteController::class, 'exportCsv'])->name('reportes.export');
    Route::get('reportes/visitas', [ReporteController::class, 'visitas'])->name('reportes.visitas');
    Route::get('reportes/visitas/exportar', [ReporteController::class, 'exportVisitasCsv'])->name('reportes.visitas_export');
    Route::get('reportes/encuestas', [ReporteController::class, 'encuestas'])->name('reportes.encuestas');
    Route::get('reportes/encuestas/exportar', [ReporteController::class, 'exportEncuestasCsv'])->name('reportes.encuestas_export');
    Route::get('reportes/isu', [ReporteController::class, 'isu'])->name('reportes.isu');
});

// Rutas Públicas para Control de Comedor
Route::get('comedor', [RegistroComedorController::class, 'index'])->name('comedor.index');
Route::post('comedor/registrar', [RegistroComedorController::class, 'store'])->name('comedor.registrar');

// Rutas Públicas para Reservaciones
Route::get('reservar', [ReservacionController::class, 'create'])->name('reservaciones.create');
Route::post('reservar', [ReservacionController::class, 'store'])->name('reservaciones.store');
Route::get('reservar/empleado/{numero_empleado}', [ReservacionController::class, 'getEmpleadoInfo'])->name('reservaciones.empleado_info');

// Rutas Públicas para Encuesta de Satisfacción del Comedor
Route::get('encuesta', [EncuestaController::class, 'create'])->name('encuestas.create');
Route::post('encuesta/validar', [EncuestaController::class, 'validarEmpleado'])->name('encuestas.validar');
Route::post('encuesta/guardar', [EncuestaController::class, 'store'])->name('encuestas.store');

require __DIR__ . '/auth.php';
