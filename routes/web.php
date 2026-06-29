<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\RegistroComedorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__ . '/auth.php';
