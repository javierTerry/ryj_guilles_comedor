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
        $now = \Carbon\Carbon::now();
        $reservasAbiertas = $now->gte(\Carbon\Carbon::today()->setTime(8, 0));

        $libres1230 = 120 - Reservacion::where('fecha', $fecha)->where('hora', '12:30')->count();
        $libres1315 = 120 - Reservacion::where('fecha', $fecha)->where('hora', '13:15')->count();
        $libres1400 = 120 - Reservacion::where('fecha', $fecha)->where('hora', '14:00')->count();
        $libres1445 = 120 - Reservacion::where('fecha', $fecha)->where('hora', '14:45')->count();

        $horariosStatus = [
            '12:30' => [
                'etiqueta' => '12:30 p.m. a 1:00 p.m.',
                'libres' => max(0, $libres1230),
                'capacidad' => 120,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(12, 15)) && $libres1230 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(12, 15)) ? 'Cerrado' : ($libres1230 <= 0 ? 'Lleno' : 'libres'))
            ],
            '13:15' => [
                'etiqueta' => '1:15 p.m. a 1:45 p.m.',
                'libres' => max(0, $libres1315),
                'capacidad' => 120,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(13, 0)) && $libres1315 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(13, 0)) ? 'Cerrado' : ($libres1315 <= 0 ? 'Lleno' : 'libres'))
            ],
            '14:00' => [
                'etiqueta' => '2:00 p.m. a 2:30 p.m.',
                'libres' => max(0, $libres1400),
                'capacidad' => 120,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(13, 45)) && $libres1400 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(13, 45)) ? 'Cerrado' : ($libres1400 <= 0 ? 'Lleno' : 'libres'))
            ],
            '14:45' => [
                'etiqueta' => '2:45 p.m. a 3:15 p.m.',
                'libres' => max(0, $libres1445),
                'capacidad' => 120,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(14, 30)) && $libres1445 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(14, 30)) ? 'Cerrado' : ($libres1445 <= 0 ? 'Lleno' : 'libres'))
            ],
            '15:30' => [
                'etiqueta' => '3:30 p.m. a 4:00 p.m.',
                'libres' => 'Acceso Libre',
                'capacidad' => 'Libre',
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(15, 15)),
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(15, 15)) ? 'Cerrado' : 'Acceso libre')
            ],
        ];

        return view('reservaciones.create', compact('horariosStatus', 'reservasAbiertas'));
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

        // A. Validar que la reserva sea después de las 8:00 a.m.
        $now = \Carbon\Carbon::now();
        if ($now->lt(\Carbon\Carbon::today()->setTime(8, 0))) {
            Log::warning("Reservaciones: Intento rechazado. Reservaciones no iniciadas aún (antes de las 8:00 a.m.).");
            return redirect()->back()
                ->withInput()
                ->with('error', 'El horario para empezar la reserva solo puede ser después de las 8:00 a.m.');
        }

        // B. Validar anticipación de 15 minutos para el horario seleccionado
        $limites = [
            '12:30' => \Carbon\Carbon::today()->setTime(12, 15),
            '13:15' => \Carbon\Carbon::today()->setTime(13, 0),
            '14:00' => \Carbon\Carbon::today()->setTime(13, 45),
            '14:45' => \Carbon\Carbon::today()->setTime(14, 30),
            '15:30' => \Carbon\Carbon::today()->setTime(15, 15),
        ];

        if (isset($limites[$hora]) && $now->gte($limites[$hora])) {
            Log::warning("Reservaciones: Intento rechazado. El horario de reservación para las {$hora} ya ha expirado (límite superado).");
            return redirect()->back()
                ->withInput()
                ->with('error', "El tiempo límite para reservar el horario seleccionado ({$hora}) ha expirado.");
        }

        // 1. Validar el formato inicial del número de empleado, correo y la hora
        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
            'correo' => 'required|email|max:255',
            'hora' => ['required', Rule::in(['12:30', '13:15', '14:00', '14:45', '15:30'])],
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'hora.required' => 'Debe seleccionar un horario.',
            'hora.in' => 'El horario seleccionado no es válido.',
        ]);

        // 2. Verificar cupo (máximo 120 por horario, excepto 15:30 que es acceso libre)
        if ($hora !== '15:30') {
            $reservasCount = Reservacion::where('fecha', $fecha)
                ->where('hora', $hora)
                ->count();

            if ($reservasCount >= 120) {
                Log::warning("Reservaciones: Intento rechazado por cupo límite (120) alcanzado en horario {$hora} para colaborador: {$numeroEmpleado}");
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El horario seleccionado ya tiene el límite de 120 lugares ocupados para el día de hoy.");
            }
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
