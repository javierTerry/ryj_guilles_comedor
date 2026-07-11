<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservacionController extends Controller
{
    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        return view('reservaciones.create');
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar el formato inicial del número de empleado, fecha y hora
        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
            'fecha' => 'required|date',
            'hora' => ['required', Rule::in(['12:30', '13:45', '14:45'])],
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
            'fecha.required' => 'Debe seleccionar una fecha.',
            'fecha.date' => 'La fecha seleccionada no es válida.',
            'hora.required' => 'Debe seleccionar un horario.',
            'hora.in' => 'El horario seleccionado no es válido.',
        ]);

        $numeroEmpleado = $request->input('numero_empleado');
        $fecha = $request->input('fecha');
        $hora = $request->input('hora');

        // 2. Verificar que el empleado existe y está activo
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El número de empleado ingresado no se encuentra registrado en el sistema.');
        }

        if (!$empleado->activo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El empleado se encuentra inactivo. Comuníquese con administración.');
        }

        // 3. Verificar si ya tiene una reservación para ese día
        $existeReservacion = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->exists();

        if ($existeReservacion) {
            return redirect()->back()
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ya tiene una reservación registrada para la fecha {$fecha}.");
        }

        // 4. Crear la reservación
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => $fecha,
            'hora' => $hora,
        ]);

        return redirect()->route('reservaciones.create')
            ->with('success', "¡Reservación exitosa! Se ha registrado el horario {$hora} p.m. el {$fecha} para {$empleado->nombre}.");
    }
}
