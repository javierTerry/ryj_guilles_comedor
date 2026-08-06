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

        $count1230 = Reservacion::where('fecha', $fecha)->where('hora', '12:30')->count();
        $count1315 = Reservacion::where('fecha', $fecha)->where('hora', '13:15')->count();
        $count1400 = Reservacion::where('fecha', $fecha)->where('hora', '14:00')->count();
        $count1445 = Reservacion::where('fecha', $fecha)->where('hora', '14:45')->count();

        $libres1230 = 120 - $count1230;
        $libres1315 = 140 - $count1315;
        $libres1400 = 140 - $count1400;
        $libres1445 = 140 - $count1445;

        $horariosStatus = [
            '12:30' => [
                'etiqueta' => '12:30 p.m. a 1:00 p.m.',
                'libres' => max(0, $libres1230),
                'reservados' => $count1230,
                'capacidad' => 120,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(12, 15)) && $libres1230 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(12, 15)) ? "{$count1230}/120" : ($libres1230 <= 0 ? "120/120" : 'libres'))
            ],
            '13:15' => [
                'etiqueta' => '1:15 p.m. a 1:45 p.m.',
                'libres' => max(0, $libres1315),
                'reservados' => $count1315,
                'capacidad' => 140,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(13, 0)) && $libres1315 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(13, 0)) ? "{$count1315}/140" : ($libres1315 <= 0 ? "140/140" : 'libres'))
            ],
            '14:00' => [
                'etiqueta' => '2:00 p.m. a 2:30 p.m.',
                'libres' => max(0, $libres1400),
                'reservados' => $count1400,
                'capacidad' => 140,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(13, 45)) && $libres1400 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(13, 45)) ? "{$count1400}/140" : ($libres1400 <= 0 ? "140/140" : 'libres'))
            ],
            '14:45' => [
                'etiqueta' => '2:45 p.m. a 3:15 p.m.',
                'libres' => max(0, $libres1445),
                'reservados' => $count1445,
                'capacidad' => 140,
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(14, 30)) && $libres1445 > 0,
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(14, 30)) ? "{$count1445}/140" : ($libres1445 <= 0 ? "140/140" : 'libres'))
            ],
            '15:30' => [
                'etiqueta' => '3:30 p.m. a 4:00 p.m.',
                'libres' => 'Acceso Libre',
                'reservados' => 0,
                'capacidad' => 'Libre',
                'habilitado' => $reservasAbiertas && $now->lt(\Carbon\Carbon::today()->setTime(15, 15)),
                'mensaje' => $now->lt(\Carbon\Carbon::today()->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(\Carbon\Carbon::today()->setTime(15, 15)) ? 'Acceso libre' : 'Acceso libre')
            ],
        ];

        Log::channel('reservas_horarios')->info("Reservaciones: Consulta de disponibilidad de horarios realizada.", [
            'fecha' => $fecha,
            '12:30' => "{$count1230}/120",
            '13:15' => "{$count1315}/140",
            '14:00' => "{$count1400}/140",
            '14:45' => "{$count1445}/140",
        ]);

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

        Log::channel('reservas_horarios')->info("Reservaciones: Intento de reservación iniciado para colaborador: {$numeroEmpleado} con correo: {$correo} en horario: {$hora}");

        // A. Validar que la reserva sea después de las 8:00 a.m.
        $now = \Carbon\Carbon::now();
        if ($now->lt(\Carbon\Carbon::today()->setTime(8, 0))) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Reservaciones no iniciadas aún (antes de las 8:00 a.m.).");
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
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. El horario de reservación para las {$hora} ya ha expirado (límite superado).");
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

        // 2. Verificar cupo por horario (excepto 15:30 que es acceso libre)
        if ($hora !== '15:30') {
            $capacidades = [
                '12:30' => 120,
                '13:15' => 140,
                '14:00' => 140,
                '14:45' => 140,
            ];
            $capacidadMaxima = $capacidades[$hora] ?? 120;

            $reservasCount = Reservacion::where('fecha', $fecha)
                ->where('hora', $hora)
                ->count();

            if ($reservasCount >= $capacidadMaxima) {
                Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado por cupo límite ({$capacidadMaxima}) alcanzado en horario {$hora} para colaborador: {$numeroEmpleado}");
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El horario seleccionado ya tiene el límite de {$capacidadMaxima} lugares ocupados para el día de hoy.");
            }
        }

        // 3. Verificar que el empleado existe y está activo
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== strtolower($correo)) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} o correo {$correo} no pertenecen al registro.");
            return redirect()->back()
                ->withInput()
                ->with('error', 'El número de empleado o correo no pertenecen al registro.');
        }

        if (!$empleado->activo) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
            return redirect()->back()
                ->withInput()
                ->with('error', 'El empleado se encuentra inactivo. Comuníquese con administración.');
        }

        // 4. Verificar si ya tiene una reservación para ese día
        $reservacionExistente = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->first();

        if ($reservacionExistente) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya cuenta con una reservación para hoy.");
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

        Log::channel('reservas_horarios')->info("Reservaciones: Reservación creada exitosamente para colaborador {$numeroEmpleado} ({$empleado->nombre}) en horario {$hora} p.m.");

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
        Log::channel('reservas_horarios')->info("Reservaciones: Consulta AJAX iniciada para colaborador: {$numeroEmpleado} y correo: {$correo}");

        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== strtolower($correo)) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} o correo {$correo} no pertenecen al registro.");
            return response()->json([
                'success' => false,
                'message' => 'El número de empleado o correo no pertenecen al registro.'
            ]);
        }

        if (!$empleado->activo) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
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
            Log::channel('reservas_horarios')->warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya tiene reservación para hoy a las {$reservacionHoy->hora}.");
            return response()->json([
                'success' => false,
                'already_reserved' => true,
                'hora_reservada' => $reservacionHoy->hora,
                'message' => "El colaborador {$empleado->nombre} ya cuenta con una reservación hoy para el horario de las {$reservacionHoy->hora} p.m."
            ]);
        }

        Log::channel('reservas_horarios')->info("Reservaciones: Consulta AJAX exitosa. Colaborador {$numeroEmpleado} ({$empleado->nombre}) verificado.");

        return response()->json([
            'success' => true,
            'nombre' => $empleado->nombre
        ]);
    }
}
