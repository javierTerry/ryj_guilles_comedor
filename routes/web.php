<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\RegistroComedorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reservaciones.create');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Gestión de Empleados
    Route::get('empleados/template', [EmpleadoController::class, 'downloadTemplate'])->name('empleados.template');
    Route::post('empleados/import', [EmpleadoController::class, 'import'])->name('empleados.import');
    Route::resource('empleados', EmpleadoController::class)->except(['create', 'show', 'edit']);
    Route::patch('empleados/{empleado}/toggle', [EmpleadoController::class, 'toggleStatus'])->name('empleados.toggle');
});

// Rutas Públicas para Control de Comedor
Route::get('comedor', [RegistroComedorController::class, 'index'])->name('comedor.index');
Route::post('comedor/registrar', [RegistroComedorController::class, 'store'])->name('comedor.registrar');

// Rutas Públicas para Reservaciones
Route::get('reservar', [ReservacionController::class, 'create'])->name('reservaciones.create');
Route::post('reservar', [ReservacionController::class, 'store'])->name('reservaciones.store');
Route::get('reservar/empleado/{numero_empleado}', [ReservacionController::class, 'getEmpleadoInfo'])->name('reservaciones.empleado_info');

require __DIR__ . '/auth.php';
