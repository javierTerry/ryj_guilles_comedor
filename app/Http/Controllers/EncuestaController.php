<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Encuesta;
use App\Models\RegistroComedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EncuestaController extends Controller
{
    /**
     * Display the public survey page.
     */
    public function create()
    {
        return view('encuestas.create');
    }

    /**
     * Validate employee eligibility for answering today's survey via AJAX.
     */
    public function validarEmpleado(Request $request)
    {
        $request->validate([
            'numero_empleado' => 'required|string',
        ]);

        $numeroEmpleado = trim($request->numero_empleado);
        $today = Carbon::today()->format('Y-m-d');

        // 1. Validar que el empleado exista y esté activo
        $empleado = Empleado::where('numero_empleado', $numeroEmpleado)
            ->where('activo', true)
            ->first();

        if (!$empleado) {
            Log::channel('encuestas')->warning("Validación de encuesta fallida: Colaborador no encontrado o inactivo.", [
                'numero_empleado' => $numeroEmpleado
            ]);

            return response()->json([
                'success' => false,
                'type' => 'error',
                'title' => 'Colaborador No Encontrado',
                'message' => 'El número de empleado ingresado no existe o se encuentra inactivo. Por favor, verifica tus datos.'
            ], 404);
        }

        // 2. Validar que el comensal haya realizado su ingreso al comedor EL MISMO DÍA
        $registroIngreso = RegistroComedor::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if (!$registroIngreso) {
            Log::channel('encuestas')->warning("Validación de encuesta denegada: Sin ingreso a comedor el día de hoy.", [
                'numero_empleado' => $numeroEmpleado,
                'empleado_id' => $empleado->id,
                'nombre' => $empleado->nombre
            ]);

            return response()->json([
                'success' => false,
                'type' => 'warning',
                'title' => 'Ingreso Requerido',
                'message' => 'La encuesta es exclusivamente para usuarios que ya realizaron su ingreso al comedor el día de hoy.'
            ], 400);
        }

        // 3. Validar que no haya respondido la encuesta el día de hoy (1 encuesta diaria)
        $encuestaExistente = Encuesta::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($encuestaExistente) {
            Log::channel('encuestas')->info("Validación de encuesta denegada: Encuesta previamente completada hoy.", [
                'numero_empleado' => $numeroEmpleado,
                'empleado_id' => $empleado->id,
                'nombre' => $empleado->nombre
            ]);

            return response()->json([
                'success' => false,
                'type' => 'info',
                'title' => 'Encuesta Ya Realizada',
                'message' => 'Ya has registrado tu encuesta de satisfacción el día de hoy. ¡Agradecemos mucho tus comentarios!'
            ], 400);
        }

        Log::channel('encuestas')->info("Validación de encuesta exitosa.", [
            'numero_empleado' => $numeroEmpleado,
            'empleado_id' => $empleado->id,
            'nombre' => $empleado->nombre
        ]);

        return response()->json([
            'success' => true,
            'title' => '¡Datos Verificados!',
            'message' => "Hola {$empleado->nombre}, tus datos han sido validados correctamente. Ya puedes responder la encuesta.",
            'empleado' => [
                'id' => $empleado->id,
                'numero_empleado' => $empleado->numero_empleado,
                'nombre' => $empleado->nombre,
                'departamento' => $empleado->departamento ?? 'General',
                'puesto' => $empleado->puesto ?? 'Colaborador',
            ]
        ]);
    }

    /**
     * Store a newly created survey in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'calidad_alimentos' => 'required|integer|between:1,5',
            'limpieza_higiene' => 'required|integer|between:1,5',
            'temperatura_adecuada' => 'required|integer|between:1,5',
            'atencion_eficiencia' => 'required|integer|between:1,5',
            'presentacion' => 'required|integer|between:1,5',
            'comentarios' => 'nullable|string|max:1000',
        ]);

        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $empleado = Empleado::findOrFail($validated['empleado_id']);

        // Verificación de seguridad adicional
        $registroIngreso = RegistroComedor::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if (!$registroIngreso) {
            Log::channel('encuestas')->warning("Intento de guardar encuesta sin ingreso al comedor hoy.", [
                'empleado_id' => $empleado->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'La encuesta es exclusivamente para usuarios que ya realizaron su ingreso al comedor el día de hoy.'
            ], 400);
        }

        $encuestaExistente = Encuesta::where('empleado_id', $empleado->id)
            ->where('fecha', $today)
            ->first();

        if ($encuestaExistente) {
            Log::channel('encuestas')->warning("Intento duplicado de envío de encuesta el mismo día.", [
                'empleado_id' => $empleado->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ya has realizado la encuesta de satisfacción el día de hoy.'
            ], 400);
        }

        // Conversiones individuales: ((calificación / 5) * 100)
        $calidadConv = ($validated['calidad_alimentos'] / 5.0) * 100.0;
        $limpiezaConv = ($validated['limpieza_higiene'] / 5.0) * 100.0;
        $temperaturaConv = ($validated['temperatura_adecuada'] / 5.0) * 100.0;
        $atencionConv = ($validated['atencion_eficiencia'] / 5.0) * 100.0;
        $presentacionConv = ($validated['presentacion'] / 5.0) * 100.0;

        // Calificación Promedio Entero/Decimal
        $sumaCalificaciones = $validated['calidad_alimentos'] +
            $validated['limpieza_higiene'] +
            $validated['temperatura_adecuada'] +
            $validated['atencion_eficiencia'] +
            $validated['presentacion'];

        $calificacionFinal = round($sumaCalificaciones / 5.0, 2);

        // Campo Conversión Compuesto: ((calificacion / 5) * 100)
        $conversionFinal = round(($calificacionFinal / 5.0) * 100.0, 2);

        // Ponderaciones Internas de Negocio:
        // Calidad (30%), Limpieza (25%), Temperatura (20%), Atención (15%), Presentación (10%)
        $ponderacionSum = ($calidadConv * 0.30) +
            ($limpiezaConv * 0.25) +
            ($temperaturaConv * 0.20) +
            ($atencionConv * 0.15) +
            ($presentacionConv * 0.10);

        $ponderacionTotal = round($ponderacionSum, 2);

        $encuesta = Encuesta::create([
            'empleado_id' => $empleado->id,
            'fecha' => $today,
            'hora' => $now->format('H:i:s'),
            'fecha_hora' => $now,

            'calidad_alimentos' => $validated['calidad_alimentos'],
            'limpieza_higiene' => $validated['limpieza_higiene'],
            'temperatura_adecuada' => $validated['temperatura_adecuada'],
            'atencion_eficiencia' => $validated['atencion_eficiencia'],
            'presentacion' => $validated['presentacion'],

            'calidad_alimentos_conversion' => $calidadConv,
            'limpieza_higiene_conversion' => $limpiezaConv,
            'temperatura_adecuada_conversion' => $temperaturaConv,
            'atencion_eficiencia_conversion' => $atencionConv,
            'presentacion_conversion' => $presentacionConv,

            'calificacion' => $calificacionFinal,
            'conversion' => $conversionFinal,
            'ponderacion_total' => $ponderacionTotal,
            'comentarios' => $validated['comentarios'] ?? null,
        ]);

        Log::channel('encuestas')->info("Encuesta de satisfacción registrada con éxito.", [
            'encuesta_id' => $encuesta->id,
            'empleado_id' => $empleado->id,
            'numero_empleado' => $empleado->numero_empleado,
            'calificacion' => $calificacionFinal,
            'conversion' => $conversionFinal,
            'ponderacion_total' => $ponderacionTotal
        ]);

        return response()->json([
            'success' => true,
            'title' => '¡Encuesta Registrada!',
            'message' => 'Muchas gracias por tus comentarios. Tu opinión nos ayuda a mejorar el servicio del comedor todos los días.',
            'data' => [
                'id' => $encuesta->id,
                'calificacion' => $calificacionFinal,
                'conversion' => $conversionFinal
            ]
        ]);
    }
}
