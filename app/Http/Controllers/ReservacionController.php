<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\FreeBooking;

class ReservacionController extends Controller
{
    /**
     * Determine if application is running in POC mode (flexible for testing).
     * require_reservation = true  => Modo Normal (Estricto, cumple todas las reglas de negocio).
     * require_reservation = false => Modo POC (Permite hacer reservaciones y cancelaciones libremente para pruebas).
     */
    private function isPocMode(): bool
    {
        return !config('app.require_reservation', false);
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request)
    {
        $timezone = config('app.timezone', 'America/Mexico_City');
        $today = Carbon::today($timezone)->toDateString();
        $now = Carbon::now($timezone);
        $isPoc = $this->isPocMode();

        // 1. Consulta de Disponibilidad de Reserva Libre activa
        $reservasLibresActivas = FreeBooking::where('status', FreeBooking::STATUS_ACTIVO)
            ->whereDate('booking_date', '>=', $today)
            ->orderBy('booking_date', 'asc')
            ->get();

        $activeFreeBooking = $reservasLibresActivas->first();

        // Fechas autorizadas: Hoy siempre está disponible
        $allowedDates = [$today];
        if ($reservasLibresActivas->isNotEmpty()) {
            foreach ($reservasLibresActivas as $fb) {
                $dateStr = $fb->booking_date->format('Y-m-d');
                if (!in_array($dateStr, $allowedDates, true)) {
                    $allowedDates[] = $dateStr;
                }
            }
        }

        // Determinar fecha seleccionada (por query param o por defecto hoy)
        $fecha = $request->query('fecha', $today);
        if (!in_array($fecha, $allowedDates, true)) {
            $fecha = $today;
        }

        $isToday = ($fecha === $today);
        $reservasAbiertas = $isPoc || !$isToday || $now->gte(Carbon::today($timezone)->setTime(8, 0));

        $count1230 = Reservacion::where('fecha', $fecha)->where('hora', '12:30')->where('estatus', 'activa')->count();
        $count1315 = Reservacion::where('fecha', $fecha)->where('hora', '13:15')->where('estatus', 'activa')->count();
        $count1400 = Reservacion::where('fecha', $fecha)->where('hora', '14:00')->where('estatus', 'activa')->count();
        $count1445 = Reservacion::where('fecha', $fecha)->where('hora', '14:45')->where('estatus', 'activa')->count();

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
                'habilitado' => $libres1230 > 0 && ($isPoc || !$isToday || ($reservasAbiertas && $now->lt(Carbon::today($timezone)->setTime(12, 15)))),
                'mensaje' => $isPoc ? ($libres1230 <= 0 ? "120/120" : "{$libres1230} libres") : (!$isToday ? ($libres1230 <= 0 ? "120/120" : "{$libres1230} libres") : ($now->lt(Carbon::today($timezone)->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(Carbon::today($timezone)->setTime(12, 15)) ? "{$count1230}/120" : ($libres1230 <= 0 ? "120/120" : 'libres'))))
            ],
            '13:15' => [
                'etiqueta' => '1:15 p.m. a 1:45 p.m.',
                'libres' => max(0, $libres1315),
                'reservados' => $count1315,
                'capacidad' => 140,
                'habilitado' => $libres1315 > 0 && ($isPoc || !$isToday || ($reservasAbiertas && $now->lt(Carbon::today($timezone)->setTime(13, 0)))),
                'mensaje' => $isPoc ? ($libres1315 <= 0 ? "140/140" : "{$libres1315} libres") : (!$isToday ? ($libres1315 <= 0 ? "140/140" : "{$libres1315} libres") : ($now->lt(Carbon::today($timezone)->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(Carbon::today($timezone)->setTime(13, 0)) ? "{$count1315}/140" : ($libres1315 <= 0 ? "140/140" : 'libres'))))
            ],
            '14:00' => [
                'etiqueta' => '2:00 p.m. a 2:30 p.m.',
                'libres' => max(0, $libres1400),
                'reservados' => $count1400,
                'capacidad' => 140,
                'habilitado' => $libres1400 > 0 && ($isPoc || !$isToday || ($reservasAbiertas && $now->lt(Carbon::today($timezone)->setTime(13, 45)))),
                'mensaje' => $isPoc ? ($libres1400 <= 0 ? "140/140" : "{$libres1400} libres") : (!$isToday ? ($libres1400 <= 0 ? "140/140" : "{$libres1400} libres") : ($now->lt(Carbon::today($timezone)->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(Carbon::today($timezone)->setTime(13, 45)) ? "{$count1400}/140" : ($libres1400 <= 0 ? "140/140" : 'libres'))))
            ],
            '14:45' => [
                'etiqueta' => '2:45 p.m. a 3:15 p.m.',
                'libres' => max(0, $libres1445),
                'reservados' => $count1445,
                'capacidad' => 140,
                'habilitado' => $libres1445 > 0 && ($isPoc || !$isToday || ($reservasAbiertas && $now->lt(Carbon::today($timezone)->setTime(14, 30)))),
                'mensaje' => $isPoc ? ($libres1445 <= 0 ? "140/140" : "{$libres1445} libres") : (!$isToday ? ($libres1445 <= 0 ? "140/140" : "{$libres1445} libres") : ($now->lt(Carbon::today($timezone)->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : ($now->gte(Carbon::today($timezone)->setTime(14, 30)) ? "{$count1445}/140" : ($libres1445 <= 0 ? "140/140" : 'libres'))))
            ],
            '15:30' => [
                'etiqueta' => '3:30 p.m. a 4:30 p.m.',
                'libres' => 'Acceso Libre',
                'reservados' => 0,
                'capacidad' => 'Libre',
                'habilitado' => $isPoc || !$isToday || ($reservasAbiertas && $now->lt(Carbon::today($timezone)->setTime(15, 15))),
                'mensaje' => $isPoc || !$isToday ? 'Acceso libre' : ($now->lt(Carbon::today($timezone)->setTime(8, 0)) ? 'Inicia 8:00 a.m.' : 'Acceso libre')
            ],
        ];

        Log::channel('reservas_horarios')->info("Reservaciones: Consulta de disponibilidad realizada (Modo POC: " . ($isPoc ? 'SI' : 'NO') . ").", [
            'fecha' => $fecha,
            'tiene_reserva_libre_activa' => $reservasLibresActivas->isNotEmpty(),
            'fechas_permitidas' => $allowedDates,
            '12:30' => "{$count1230}/120",
            '13:15' => "{$count1315}/140",
            '14:00' => "{$count1400}/140",
            '14:45' => "{$count1445}/140",
        ]);

        return view('reservaciones.create', compact(
            'horariosStatus',
            'reservasAbiertas',
            'isPoc',
            'activeFreeBooking',
            'reservasLibresActivas',
            'allowedDates',
            'fecha',
            'today'
        ));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $timezone = config('app.timezone', 'America/Mexico_City');
        $today = Carbon::today($timezone)->toDateString();
        $now = Carbon::now($timezone);
        $isPoc = $this->isPocMode();

        // 1. Sanitización previa de entrada
        $rawFecha = $request->filled('fecha') ? trim((string) $request->input('fecha')) : $today;
        $request->merge([
            'fecha' => $rawFecha,
            'numero_empleado' => $request->filled('numero_empleado') ? trim((string) $request->input('numero_empleado')) : null,
            'correo' => $request->filled('correo') ? strtolower(trim((string) $request->input('correo'))) : null,
        ]);

        // 2. Validación estricta con verificación de fecha de Reserva Libre activa
        $request->validate([
            'numero_empleado' => 'required|numeric|digits_between:1,10',
            'correo' => 'required|email|max:255',
            'hora' => ['required', Rule::in(['12:30', '13:15', '14:00', '14:45', '15:30'])],
            'fecha' => [
                'required',
                'date_format:Y-m-d',
                function (string $attribute, mixed $value, \Closure $fail) use ($today): void {
                    if ($value < $today) {
                        $fail('No se pueden realizar reservaciones para fechas pasadas.');
                        return;
                    }

                    // Si la fecha es distinta a hoy, debe existir una Reserva Libre con estatus activo
                    if ($value !== $today) {
                        $reservaLibreActiva = FreeBooking::where('booking_date', $value)
                            ->where('status', FreeBooking::STATUS_ACTIVO)
                            ->exists();

                        if (!$reservaLibreActiva) {
                            $fail('La fecha seleccionada no corresponde a una reserva libre activa autorizada.');
                        }
                    }
                },
            ],
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.digits_between' => 'El número de empleado no debe exceder los 10 dígitos.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'hora.required' => 'Debe seleccionar un horario.',
            'hora.in' => 'El horario seleccionado no es válido.',
            'fecha.required' => 'La fecha de reservación es obligatoria.',
            'fecha.date_format' => 'El formato de la fecha debe ser AAAA-MM-DD.',
        ]);

        $fecha = (string) $request->input('fecha');
        $numeroEmpleado = (string) $request->input('numero_empleado');
        $correo = (string) $request->input('correo');
        $hora = (string) $request->input('hora');

        Log::channel('reservas_horarios')->info("Reservaciones: Intento de reservación iniciado para colaborador: {$numeroEmpleado} con correo: {$correo} en fecha: {$fecha} y horario: {$hora}");

        // En modo normal ($isPoc === false), aplicar restricciones estrictas de horario sólo si la fecha es hoy
        if (!$isPoc && $fecha === $today) {
            // A. Validar que la reserva sea después de las 8:00 a.m.
            if ($now->lt(Carbon::today($timezone)->setTime(8, 0))) {
                Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Reservaciones no iniciadas aún (antes de las 8:00 a.m.).");
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El horario para empezar la reserva solo puede ser después de las 8:00 a.m.');
            }

            // B. Validar anticipación de 15 minutos para el horario seleccionado
            $limites = [
                '12:30' => Carbon::today($timezone)->setTime(12, 15),
                '13:15' => Carbon::today($timezone)->setTime(13, 0),
                '14:00' => Carbon::today($timezone)->setTime(13, 45),
                '14:45' => Carbon::today($timezone)->setTime(14, 30),
                '15:30' => Carbon::today($timezone)->setTime(15, 15),
            ];

            if (isset($limites[$hora]) && $now->gte($limites[$hora])) {
                Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. El horario de reservación para las {$hora} ya ha expirado (límite superado).");
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El tiempo límite para reservar el horario seleccionado ({$hora}) ha expirado.");
            }
        }

        // 3. Verificar cupo por horario para la fecha seleccionada (excepto 15:30 que es acceso libre)
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
                ->where('estatus', 'activa')
                ->count();

            if ($reservasCount >= $capacidadMaxima) {
                Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado por cupo límite ({$capacidadMaxima}) alcanzado en fecha {$fecha} y horario {$hora} para colaborador: {$numeroEmpleado}");
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El horario seleccionado ya tiene el límite de {$capacidadMaxima} lugares ocupados para el día {$fecha}.");
            }
        }

        // 4. Verificar que el empleado existe y está activo
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== $correo) {
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

        // 5. Verificar si ya tiene una reservación ACTIVA para esa fecha específica
        $reservacionExistente = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->where('estatus', 'activa')
            ->first();

        if ($reservacionExistente) {
            Log::channel('reservas_horarios')->warning("Reservaciones: Intento rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya cuenta con una reservación activa para la fecha {$fecha}.");
            return redirect()->back()
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ya tiene una reservación registrada para el {$fecha} en el horario de las {$reservacionExistente->hora} p.m.");
        }

        // 6. Crear la reservación activa para la fecha indicada
        Reservacion::create([
            'empleado_id' => $empleado->id,
            'fecha' => $fecha,
            'hora' => $hora,
            'estatus' => 'activa',
        ]);

        $formattedFecha = Carbon::parse($fecha, $timezone)->format('d/m/Y');
        Log::channel('reservas_horarios')->info("Reservaciones: Reservación creada exitosamente para colaborador {$numeroEmpleado} ({$empleado->nombre}) en fecha {$fecha} y horario {$hora} p.m.");

        return redirect()->route('reservaciones.create', ['fecha' => $fecha])
            ->with('success_reservation', [
                'empleado' => $empleado->nombre,
                'hora' => $hora,
                'fecha' => $fecha,
            ])
            ->with('success', "¡Reservación exitosa! Se ha registrado el horario {$hora} p.m. para {$empleado->nombre} para el día {$formattedFecha}.");
    }

    /**
     * Get employee information by employee number.
     */
    public function getEmpleadoInfo(Request $request, $numeroEmpleado)
    {
        $timezone = config('app.timezone', 'America/Mexico_City');
        $correo = strtolower(trim($request->query('correo') ?? ''));
        $targetDate = $request->query('fecha')
            ? Carbon::parse($request->query('fecha'), $timezone)->toDateString()
            : Carbon::today($timezone)->toDateString();

        Log::channel('reservas_horarios')->info("Reservaciones: Consulta AJAX iniciada para colaborador: {$numeroEmpleado}, fecha: {$targetDate} y correo: {$correo}");

        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== $correo) {
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

        // Verificar si ya tiene una reservación ACTIVA registrada para la fecha objetivo
        $reservacionFecha = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $targetDate)
            ->where('estatus', 'activa')
            ->first();

        if ($reservacionFecha) {
            $formattedDate = Carbon::parse($targetDate, $timezone)->format('d/m/Y');
            Log::channel('reservas_horarios')->warning("Reservaciones: Consulta AJAX fallida. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya tiene reservación activa para {$targetDate} a las {$reservacionFecha->hora}.");
            return response()->json([
                'success' => false,
                'already_reserved' => true,
                'hora_reservada' => $reservacionFecha->hora,
                'fecha_reservada' => $formattedDate,
                'message' => "El colaborador {$empleado->nombre} ya cuenta con una reservación para el {$formattedDate} en el horario de las {$reservacionFecha->hora} p.m."
            ]);
        }

        Log::channel('reservas_horarios')->info("Reservaciones: Consulta AJAX exitosa. Colaborador {$numeroEmpleado} ({$empleado->nombre}) verificado.");

        return response()->json([
            'success' => true,
            'nombre' => $empleado->nombre
        ]);
    }

    /**
     * Muestra la vista del submenú para cancelar reservaciones.
     */
    public function cancelView()
    {
        $fecha = Carbon::today()->toDateString();
        $now = Carbon::now();
        $isPoc = $this->isPocMode();

        $reservasAbiertas = $isPoc || $now->gte(Carbon::today()->setTime(8, 0));

        $count1230 = Reservacion::where('fecha', $fecha)->where('hora', '12:30')->where('estatus', 'activa')->count();
        $count1315 = Reservacion::where('fecha', $fecha)->where('hora', '13:15')->where('estatus', 'activa')->count();
        $count1400 = Reservacion::where('fecha', $fecha)->where('hora', '14:00')->where('estatus', 'activa')->count();
        $count1445 = Reservacion::where('fecha', $fecha)->where('hora', '14:45')->where('estatus', 'activa')->count();

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
                'habilitado' => $libres1230 > 0 && ($isPoc || ($reservasAbiertas && $now->lt(Carbon::today()->setTime(12, 0)))),
                'mensaje' => $isPoc ? ($libres1230 <= 0 ? "Lleno" : "{$libres1230} libres") : ($now->gte(Carbon::today()->setTime(12, 0)) ? 'No disponible' : ($libres1230 <= 0 ? "Lleno" : "{$libres1230} libres"))
            ],
            '13:15' => [
                'etiqueta' => '1:15 p.m. a 1:45 p.m.',
                'libres' => max(0, $libres1315),
                'reservados' => $count1315,
                'capacidad' => 140,
                'habilitado' => $libres1315 > 0 && ($isPoc || ($reservasAbiertas && $now->lt(Carbon::today()->setTime(12, 45)))),
                'mensaje' => $isPoc ? ($libres1315 <= 0 ? "Lleno" : "{$libres1315} libres") : ($now->gte(Carbon::today()->setTime(12, 45)) ? 'No disponible' : ($libres1315 <= 0 ? "Lleno" : "{$libres1315} libres"))
            ],
            '14:00' => [
                'etiqueta' => '2:00 p.m. a 2:30 p.m.',
                'libres' => max(0, $libres1400),
                'reservados' => $count1400,
                'capacidad' => 140,
                'habilitado' => $libres1400 > 0 && ($isPoc || ($reservasAbiertas && $now->lt(Carbon::today()->setTime(13, 30)))),
                'mensaje' => $isPoc ? ($libres1400 <= 0 ? "Lleno" : "{$libres1400} libres") : ($now->gte(Carbon::today()->setTime(13, 30)) ? 'No disponible' : ($libres1400 <= 0 ? "Lleno" : "{$libres1400} libres"))
            ],
            '14:45' => [
                'etiqueta' => '2:45 p.m. a 3:15 p.m.',
                'libres' => max(0, $libres1445),
                'reservados' => $count1445,
                'capacidad' => 140,
                'habilitado' => $libres1445 > 0 && ($isPoc || ($reservasAbiertas && $now->lt(Carbon::today()->setTime(14, 15)))),
                'mensaje' => $isPoc ? ($libres1445 <= 0 ? "Lleno" : "{$libres1445} libres") : ($now->gte(Carbon::today()->setTime(14, 15)) ? 'No disponible' : ($libres1445 <= 0 ? "Lleno" : "{$libres1445} libres"))
            ],
            '15:30' => [
                'etiqueta' => '3:30 p.m. a 4:30 p.m.',
                'libres' => 'Acceso Libre',
                'reservados' => 0,
                'capacidad' => 'Libre',
                'habilitado' => $isPoc || ($reservasAbiertas && $now->lt(Carbon::today()->setTime(15, 0))),
                'mensaje' => $now->gte(Carbon::today()->setTime(15, 0)) ? 'No disponible' : 'Acceso libre'
            ],
        ];

        Log::channel('cancelaciones')->info("Cancelaciones: Vista de cancelación de reservaciones abierta.");

        return view('reservaciones.cancel', compact('horariosStatus', 'reservasAbiertas', 'isPoc'));
    }

    /**
     * Endpoint AJAX para buscar la reservación activa de un colaborador.
     */
    public function buscarReservacion(Request $request)
    {
        $numeroEmpleado = trim($request->input('numero_empleado'));
        $correo = strtolower(trim($request->input('correo')));
        $isPoc = $this->isPocMode();

        Log::channel('cancelaciones')->info("Cancelaciones: Búsqueda AJAX iniciada para colaborador: {$numeroEmpleado} con correo: {$correo}");

        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== $correo) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Búsqueda rechazada. Colaborador {$numeroEmpleado} o correo {$correo} no coinciden.");
            return response()->json([
                'success' => false,
                'message' => 'El número de colaborador o el correo electrónico no pertenecen al registro.'
            ]);
        }

        if (!$empleado->activo) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Búsqueda rechazada. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
            return response()->json([
                'success' => false,
                'message' => 'El colaborador se encuentra inactivo. Comuníquese con administración.'
            ]);
        }

        $fecha = Carbon::today()->toDateString();
        $reservacionActiva = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->where('estatus', 'activa')
            ->first();

        if (!$reservacionActiva) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Sin reservación activa para hoy del colaborador {$numeroEmpleado} ({$empleado->nombre}).");
            return response()->json([
                'success' => false,
                'message' => "El colaborador {$empleado->nombre} no tiene ninguna reservación activa para el día de hoy."
            ]);
        }

        // Evaluar la regla de negocio de los 30 minutos de anticipación
        $now = Carbon::now();
        $limitesCancelacion = [
            '12:30' => Carbon::today()->setTime(12, 0),
            '13:15' => Carbon::today()->setTime(12, 45),
            '14:00' => Carbon::today()->setTime(13, 30),
            '14:45' => Carbon::today()->setTime(14, 15),
            '15:30' => Carbon::today()->setTime(15, 0),
        ];

        $hora = $reservacionActiva->hora;
        $limiteTime = $limitesCancelacion[$hora] ?? Carbon::today()->setTime(12, 0);

        // En Modo POC ($isPoc === true), se permite cancelar sin bloqueo por tiempo
        $permiteCancelar = $isPoc || $now->lt($limiteTime);

        if (!$permiteCancelar) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Intento de cancelación rechazado por regla de 30 minutos. Colaborador: {$numeroEmpleado}, Horario: {$hora}, Hora Actual: {$now->toTimeString()}");
        } else {
            Log::channel('cancelaciones')->info("Cancelaciones: Reservación activa encontrada ID {$reservacionActiva->id} para {$empleado->nombre} ({$numeroEmpleado}).");
        }

        return response()->json([
            'success' => true,
            'reservacion_id' => $reservacionActiva->id,
            'empleado_nombre' => $empleado->nombre,
            'hora_reservada' => $reservacionActiva->hora,
            'fecha_reservada' => Carbon::parse($reservacionActiva->fecha)->format('d/m/Y'),
            'permite_cancelar' => $permiteCancelar,
            'message' => $permiteCancelar ? 'Reservación encontrada.' : "La reservación para las {$hora} p.m. ya no se puede cancelar ni modificar porque el tiempo límite (al menos 30 minutos de anticipación) ha vencido."
        ]);
    }

    /**
     * Procesa la cancelación de reservación.
     */
    public function cancelStore(Request $request)
    {
        $numeroEmpleado = trim($request->input('numero_empleado'));
        $correo = strtolower(trim($request->input('correo')));
        $isPoc = $this->isPocMode();

        Log::channel('cancelaciones')->info("Cancelaciones: Intento de cancelación de reservación para colaborador: {$numeroEmpleado} con correo: {$correo}");

        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
            'correo' => 'required|email|max:255',
        ]);

        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado || strtolower(trim($empleado->correo)) !== $correo || !$empleado->activo) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Proceso fallido. Datos inválidos o colaborador inactivo.");
            return redirect()->route('reservaciones.cancel_view')
                ->with('error', 'El número de colaborador o correo no pertenecen al registro o el usuario está inactivo.');
        }

        $fecha = Carbon::today()->toDateString();
        $reservacionActiva = Reservacion::where('empleado_id', $empleado->id)
            ->where('fecha', $fecha)
            ->where('estatus', 'activa')
            ->first();

        if (!$reservacionActiva) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Proceso fallido. No se encontró ninguna reservación activa.");
            return redirect()->route('reservaciones.cancel_view')
                ->with('error', 'No se encontró ninguna reservación activa para cancelar.');
        }

        // Re-validación de 30 minutos de anticipación (en modo normal $isPoc === false)
        $now = Carbon::now();
        $limitesCancelacion = [
            '12:30' => Carbon::today()->setTime(12, 0),
            '13:15' => Carbon::today()->setTime(12, 45),
            '14:00' => Carbon::today()->setTime(13, 30),
            '14:45' => Carbon::today()->setTime(14, 15),
            '15:30' => Carbon::today()->setTime(15, 0),
        ];

        $horaActualReservada = $reservacionActiva->hora;
        $limiteTime = $limitesCancelacion[$horaActualReservada] ?? Carbon::today()->setTime(12, 0);

        if (!$isPoc && $now->gte($limiteTime)) {
            Log::channel('cancelaciones')->warning("Cancelaciones: Límite de 30 minutos superado para el horario {$horaActualReservada}. Colaborador: {$numeroEmpleado}");
            return redirect()->route('reservaciones.cancel_view')
                ->with('error', "La reservación para el horario de las {$horaActualReservada} p.m. ya no puede ser cancelada (requiere al menos 30 minutos de anticipación).");
        }

        // Marcar reservación existente como 'cancelada'
        $reservacionActiva->update(['estatus' => 'cancelada']);

        Log::channel('cancelaciones')->info("Cancelaciones: Reservación ID {$reservacionActiva->id} del horario {$horaActualReservada} p.m. marcada como CANCELADA exitosamente para {$empleado->nombre} ({$numeroEmpleado}). Lugar liberado.");

        return redirect()->route('reservaciones.cancel_view')
            ->with('success', "Su reservación para las {$horaActualReservada} p.m. ha sido cancelada exitosamente. Se ha liberado el espacio en el comedor.");
    }
}
