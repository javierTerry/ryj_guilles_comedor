<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\RegistroComedor;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('comedor.index', compact('registros'));
    }

    /**
     * Store a new dining room visit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_empleado' => 'required|numeric|max_digits:10',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'Debe ingresar únicamente números.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
        ]);

        $numeroEmpleado = $request->input('numero_empleado');

        // 1. Find employee
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)->first();

        if (!$empleado) {
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El número de empleado {$numeroEmpleado} no está registrado en el sistema.");
        }

        // 2. Check if active
        if (!$empleado->activo) {
            return redirect()->route('comedor.index')
                ->withInput()
                ->with('error', "El empleado {$empleado->nombre} ({$numeroEmpleado}) está marcado como INACTIVO.");
        }

        $today = Carbon::today()->toDateString();

        // 3. Check if already registered today
        $alreadyEaten = RegistroComedor::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($alreadyEaten) {
            $horaRegistro = Carbon::parse($alreadyEaten->fecha_hora)->format('H:i:s');
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

        return redirect()->route('comedor.index')
            ->with('success', "¡Registro exitoso! Comida registrada para {$empleado->nombre}.")
            ->with('last_registered', $empleado)
            ->with('last_registered_time', Carbon::now()->format('H:i:s'))
            ->with('last_registered_total', $totalVisitas);
    }
}
