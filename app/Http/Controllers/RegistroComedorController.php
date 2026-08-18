<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\RegistroComedor;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RegistroComedorController extends Controller
{
    /**
     * Display the scanning interface and today's history.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Get registrations of today order by recent first
        $registros = RegistroComedor::with('empleado')
            ->where('fecha', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('comedor.index', compact('registros'));
    }

    /**
     * Store a new dining access record.
     */
    public function store(Request $request)
    {
        // Validate input format
        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'Debe ingresar únicamente números.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
        ]);

        $numeroEmpleado = $request->input('numero_empleado');

        Log::channel('comedor')->info("Kiosco Comedor: Intento de ingreso de colaborador: {$numeroEmpleado}", [
            'ip' => $request->ip(),
            'numero_empleado' => $numeroEmpleado,
        ]);

        // 1. Find employee
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado) {
            Log::channel('comedor')->warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} no registrado.", [
                'ip' => $request->ip(),
                'numero_empleado' => $numeroEmpleado,
            ]);
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El número de empleado {$numeroEmpleado} no está registrado en el sistema.");
        }

        // 2. Check if active
        if (!$empleado->activo) {
            Log::channel('comedor')->warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.", [
                'ip' => $request->ip(),
                'empleado_id' => $empleado->id,
            ]);
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ({$numeroEmpleado}) está marcado como INACTIVO.");
        }

        $today = Carbon::today()->toDateString();
        $requireReservation = config('app.require_reservation', false);
        $horaReservada = 'Modo POC / Acceso Libre';

        // Lista de excepciones temporal (hardcoded) para números de empleado exentos de reservación
        $empleadosExentos = ['163', '501382', '202563', '501383']; // Reemplazar con los números reales necesarios
        $esExceptuado = in_array($numeroEmpleado, $empleadosExentos);

        if ($esExceptuado) {
            $requireReservation = false;
            $horaReservada = 'Excepción de Reservación';
            Log::channel('comedor')->info("Kiosco Comedor: Acceso por excepción (exento de reservación) para colaborador {$numeroEmpleado} ({$empleado->nombre})");
        }

        if ($requireReservation) {
            $horaActual = Carbon::now()->format('H:i');

            // 2.b. Check if the employee has an active reservation for today
            $reservacion = Reservacion::where('empleado_id', $empleado->id)
                ->where('fecha', $today)
                ->activas()
                ->first();

            // Si estamos en el horario de Acceso Libre (3:30 p.m. a 4:30 p.m. -> ventana 15:30 a 16:30) se permite el ingreso
            $esAccesoLibre = ($horaActual >= '15:30' && $horaActual <= '16:30');

            if (!$reservacion && !$esAccesoLibre) {
                Log::channel('comedor')->warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) no cuenta con reservación activa para hoy.", [
                    'ip' => $request->ip(),
                    'empleado_id' => $empleado->id,
                ]);
                return redirect()->route('comedor.index')
                    ->withInput()
                    ->with('error', "El empleado {$empleado->nombre} ({$numeroEmpleado}) no cuenta con una reservación activa registrada para el día de hoy.");
            }

            // 2.c. Check if the employee is within the reserved schedule window
            $horaReservada = $reservacion ? $reservacion->hora : '15:30';
            $valido = false;

            if ($esAccesoLibre) {
                $valido = true;
            } elseif ($horaReservada === '12:30') {
                $valido = ($horaActual >= '12:00' && $horaActual <= '13:15');
            } elseif ($horaReservada === '13:15') {
                $valido = ($horaActual >= '13:00' && $horaActual <= '14:00');
            } elseif ($horaReservada === '14:00') {
                $valido = ($horaActual >= '13:45' && $horaActual <= '14:45');
            } elseif ($horaReservada === '14:45') {
                $valido = ($horaActual >= '14:30' && $horaActual <= '15:30');
            } elseif ($horaReservada === '15:30') {
                $valido = ($horaActual >= '15:15' && $horaActual <= '16:30');
            }

            if (!$valido) {
                $formatoHora = [
                    '12:30' => '12:30 p.m. a 1:00 p.m. (Ventana: 12:00 a 13:15)',
                    '13:15' => '1:15 p.m. a 1:45 p.m. (Ventana: 13:00 a 14:00)',
                    '14:00' => '2:00 p.m. a 2:30 p.m. (Ventana: 13:45 a 14:45)',
                    '14:45' => '2:45 p.m. a 3:15 p.m. (Ventana: 14:30 a 15:30)',
                    '15:30' => '3:30 p.m. a 4:30 p.m. (Acceso Libre)',
                ];
                $ventana = $formatoHora[$horaReservada] ?? $horaReservada;
                Log::channel('comedor')->warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) reservó a las {$ventana} pero ingresó a las {$horaActual}.", [
                    'ip' => $request->ip(),
                    'empleado_id' => $empleado->id,
                    'hora_reservada' => $horaReservada,
                    'hora_actual' => $horaActual,
                ]);
                return redirect()->route('comedor.index')
                    ->withInput()
                    ->with('error', "Horario incorrecto. El empleado {$empleado->nombre} reservó para {$ventana}, pero está ingresando a las {$horaActual}.");
            }
        }

        // 3. Check if already registered today
        $alreadyEaten = RegistroComedor::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($alreadyEaten) {
            $horaRegistro = Carbon::parse($alreadyEaten->fecha_hora)->format('H:i:s');
            Log::channel('comedor')->warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya registró comida hoy a las {$horaRegistro}.", [
                'ip' => $request->ip(),
                'empleado_id' => $empleado->id,
                'hora_registro_previo' => $horaRegistro,
            ]);
            return redirect()->route('comedor.index')
                ->with('error_duplicated', "El empleado {$empleado->nombre} ({$numeroEmpleado}) ya registró su comida hoy a las {$horaRegistro}. Límite de 1 acceso diario.")
                ->with('duplicated_employee', $empleado);
        }

        // 4. Create registration
        $registro = RegistroComedor::create([
            'empleado_id' => $empleado->id,
            'fecha' => $today,
            'fecha_hora' => Carbon::now(),
        ]);

        // Get updated total visits for employee and total accesses today
        $totalVisitas = $empleado->registrosComedor()->count();
        $registrosHoyTotal = RegistroComedor::where('fecha', $today)->count();

        Log::channel('comedor')->info("Kiosco Comedor: Acceso registrado exitosamente para colaborador {$numeroEmpleado} ({$empleado->nombre}) [Horario/Modo: {$horaReservada}]. Nº Registro de Hoy: {$registrosHoyTotal}. Total visitas histórico: {$totalVisitas}", [
            'ip' => $request->ip(),
            'empleado_id' => $empleado->id,
            'registro_id' => $registro->id,
            'numero_registro_hoy' => $registrosHoyTotal,
            'total_visitas' => $totalVisitas,
        ]);

        return redirect()->route('comedor.index')
            ->with('success', "¡Registro exitoso! Comida registrada para {$empleado->nombre}.")
            ->with('last_registered', $empleado)
            ->with('last_registered_time', Carbon::now()->format('H:i:s'))
            ->with('last_registered_total', $totalVisitas)
            ->with('last_registered_today_total', $registrosHoyTotal);
    }
}
