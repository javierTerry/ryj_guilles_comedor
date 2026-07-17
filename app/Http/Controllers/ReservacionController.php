<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ReservacionController extends Controller
{
    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $fecha = \Carbon\Carbon::today()->toDateString();
        
        $horarios = [
            '12:30' => 180 - Reservacion::where('fecha', $fecha)->where('hora', '12:30')->count(),
            '13:45' => 180 - Reservacion::where('fecha', $fecha)->where('hora', '13:45')->count(),
            '14:45' => 180 - Reservacion::where('fecha', $fecha)->where('hora', '14:45')->count(),
            '15:45' => 180 - Reservacion::where('fecha', $fecha)->where('hora', '15:45')->count(),
        ];

        return view('reservaciones.create', compact('horarios'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $fecha = \Carbon\Carbon::today()->toDateString();
        $numeroEmpleado = $request->input('numero_empleado');
        $correo = trim($request->input('correo'));
        $hora = $request->input('hora');

        Log::info("Reservaciones: Intento de reservación iniciado para colaborador: {$numeroEmpleado} con correo: {$correo} en horario: {$hora}");

        // 1. Validar el formato inicial del número de empleado, correo y la hora
        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
            'correo' => 'required|email|max:255',
            'hora' => ['required', Rule::in(['12:30', '13:45', '14:45', '15:45'])],
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'hora.required' => 'Debe seleccionar un horario.',
            'hora.in' => 'El horario seleccionado no es válido.',
        ]);

        // 2. Verificar cupo (máximo 180 por horario)
        $reservasCount = Reservacion::where('fecha', $fecha)
            ->where('hora', $hora)
            ->count();

        if ($reservasCount >= 180) {
            Log::warning("Reservaciones: Intento rechazado por cupo límite (180) alcanzado en horario {$hora} para colaborador: {$numeroEmpleado}");
            return redirect()->back()
                ->withInput()
                ->with('error', "El horario {$hora} p.m. ya tiene el límite de 180 lugares ocupados para el día de hoy.");
        }

        // 3. Verificar que el empleado existe y está activo
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== strtolower($correo)) {
            Log::warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} o correo {$correo} no pertenecen al registro.");
            return redirect()->back()
                ->withInput()
                ->with('error', 'El número de empleado o correo no pertenecen al registro.');
        }

        if (!$empleado->activo) {
            Log::warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
            return redirect()->back()
                ->withInput()
                ->with('error', 'El empleado se encuentra inactivo. Comuníquese con administración.');
        }

        // 4. Verificar si ya tiene una reservación para ese día
        $reservacionExistente = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->first();

        if ($reservacionExistente) {
            Log::warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya cuenta con una reservación para hoy.");
            return redirect()->back()
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ya tiene una reservación registrada hoy para el horario de las {$reservacionExistente->hora} p.m.");
        }

        // 5. Crear la reservación
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => $fecha,
            'hora' => $hora,
        ]);

        Log::info("Reservaciones: Reservación creada exitosamente para colaborador {$numeroEmpleado} ({$empleado->nombre}) en horario {$hora} p.m.");

        return redirect()->route('reservaciones.create')
            ->with('success_reservation', [
                'empleado' => $empleado->nombre,
                'hora' => $hora,
                'fecha' => $fecha,
            ])
            ->with('success', "¡Reservación exitosa! Se ha registrado el horario {$hora} p.m. para {$empleado->nombre}.");
    }

    /**
     * Get employee information by employee number.
     */
    public function getEmpleadoInfo(Request $request, $numeroEmpleado)
    {
        $correo = trim($request->query('correo') ?? '');
        Log::info("Reservaciones: Consulta AJAX iniciada para colaborador: {$numeroEmpleado} y correo: {$correo}");

        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== strtolower($correo)) {
            Log::warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} o correo {$correo} no pertenecen al registro.");
            return response()->json([
                'success' => false,
                'message' => 'El número de empleado o correo no pertenecen al registro.'
            ]);
        }

        if (!$empleado->activo) {
            Log::warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
            return response()->json([
                'success' => false,
                'message' => 'El colaborador se encuentra inactivo. Comuníquese con administración.'
            ]);
        }

        // Verificar si ya tiene una reservación registrada para hoy
        $today = \Carbon\Carbon::today()->toDateString();
        $reservacionHoy = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($reservacionHoy) {
            Log::warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya tiene reservación para hoy a las {$reservacionHoy->hora}.");
            return response()->json([
                'success' => false,
                'already_reserved' => true,
                'hora_reservada' => $reservacionHoy->hora,
                'message' => "El colaborador {$empleado->nombre} ya cuenta con una reservación hoy para el horario de las {$reservacionHoy->hora} p.m."
            ]);
        }

        Log::info("Reservaciones: Consulta AJAX exitosa. Colaborador {$numeroEmpleado} ({$empleado->nombre}) verificado.");

        return response()->json([
            'success' => true,
            'nombre' => $empleado->nombre
        ]);
    }
}
