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

        Log::info("Kiosco Comedor: Intento de ingreso de colaborador: {$numeroEmpleado}");

        // 1. Find employee
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado) {
            Log::warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} no registrado.");
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El número de empleado {$numeroEmpleado} no está registrado en el sistema.");
        }

        // 2. Check if active
        if (!$empleado->activo) {
            Log::warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) está inactivo.");
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ({$numeroEmpleado}) está marcado como INACTIVO.");
        }

        $today = Carbon::today()->toDateString();
        $requireReservation = config('app.require_reservation', false);
        $horaReservada = 'Modo POC / Acceso Libre';

        if ($requireReservation) {
            // 2.b. Check if the employee has a reservation for today
            $reservacion = Reservacion::where('empleado_id', $empleado->id)
                ->where('fecha', $today)
                ->first();

            if (!$reservacion) {
                Log::warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) no cuenta con reservación para hoy.");
                return redirect()->route('comedor.index')
                    ->withInput()
                    ->with('error', "El empleado {$empleado->nombre} ({$numeroEmpleado}) no cuenta con una reservación registrada para el día de hoy.");
            }

            // 2.c. Check if the employee is within the reserved schedule window
            $horaActual = Carbon::now()->format('H:i');
            $horaReservada = $reservacion->hora; // '12:30', '13:45', '14:45', '15:45'
            $valido = false;

            if ($horaReservada === '12:30') {
                $valido = ($horaActual >= '12:00' && $horaActual <= '13:30');
            } elseif ($horaReservada === '13:45') {
                $valido = ($horaActual >= '13:30' && $horaActual <= '14:30');
            } elseif ($horaReservada === '14:45') {
                $valido = ($horaActual >= '14:30' && $horaActual <= '15:45');
            } elseif ($horaReservada === '15:45') {
                $valido = ($horaActual >= '15:45' && $horaActual <= '17:00');
            }

            if (!$valido) {
                $formatoHora = [
                    '12:30' => '12:30 p.m. (Ventana: 12:00 a 13:30)',
                    '13:45' => '13:45 p.m. (Ventana: 13:30 a 14:30)',
                    '14:45' => '14:45 p.m. (Ventana: 14:30 a 15:45)',
                    '15:45' => '15:45 p.m. (Ventana: 15:45 a 17:00)',
                ];
                $ventana = $formatoHora[$horaReservada] ?? $horaReservada;
                Log::warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) reservó a las {$ventana} pero ingresó a las {$horaActual}.");
                return redirect()->route('comedor.index')
                    ->withInput()
                    ->with('error', "Horario incorrecto. El empleado {$empleado->nombre} reservó a las {$ventana}, pero está ingresando a las {$horaActual}.");
            }
        }

        // 3. Check if already registered today
        $alreadyEaten = RegistroComedor::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($alreadyEaten) {
            $horaRegistro = Carbon::parse($alreadyEaten->fecha_hora)->format('H:i:s');
            Log::warning("Kiosco Comedor: Acceso rechazado. Colaborador {$numeroEmpleado} ({$empleado->nombre}) ya registró comida hoy a las {$horaRegistro}.");
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

        // Get updated total visits
        $totalVisitas = $empleado->registrosComedor()->count();

        Log::info("Kiosco Comedor: Acceso registrado exitosamente para colaborador {$numeroEmpleado} ({$empleado->nombre}) [Horario/Modo: {$horaReservada}]. Total visitas histórico: {$totalVisitas}");

        return redirect()->route('comedor.index')
            ->with('success', "¡Registro exitoso! Comida registrada para {$empleado->nombre}.")
            ->with('last_registered', $empleado)
            ->with('last_registered_time', Carbon::now()->format('H:i:s'))
            ->with('last_registered_total', $totalVisitas);
    }
}
