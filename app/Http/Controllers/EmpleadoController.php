<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\EmpleadoLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Rango de la semana actual (Lunes a Viernes)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(4)->toDateString();

        $query = Empleado::query()->withCount(['registrosComedor' => function ($q) use ($startOfWeek, $endOfWeek) {
            $q->whereBetween('fecha', [$startOfWeek, $endOfWeek]);
        }]);

        // Filter by Search (name, employee number or email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        // Filter by Department
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->input('departamento'));
        }

        // Filter by Status (active, inactive, baja_definitiva)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active' || $status === 'activo') {
                $query->where('activo', true)->where(function($q) {
                    $q->where('estatus', 'activo')->orWhereNull('estatus');
                });
            } elseif ($status === 'inactive' || $status === 'inactivo') {
                $query->where(function($q) {
                    $q->where('estatus', 'inactivo')
                      ->orWhere(function($sub) {
                          $sub->where('activo', false)->where('estatus', '!=', 'baja_definitiva');
                      });
                });
            } elseif ($status === 'baja_definitiva') {
                $query->where('estatus', 'baja_definitiva');
            }
        }

        $empleados = $query->orderBy('nombre')->paginate(10)->withQueryString();

        // Get unique departments for the filter dropdown
        $departamentos = Empleado::whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->distinct()
            ->pluck('departamento')
            ->sort()
            ->values();

        $logs = EmpleadoLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        Log::channel('empleados')->info('Consulta de catálogo de empleados realizada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'filtros' => array_filter($request->only(['search', 'departamento', 'status'])),
            'total_resultados' => $empleados->total(),
        ]);

        return view('empleados.index', compact('empleados', 'departamentos', 'logs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_empleado' => [
                'required',
                'numeric',
                'max_digits:10',
                'unique:empleados,numero_empleado',
            ],
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255|unique:empleados,correo',
            'departamento' => 'nullable|string|max:255',
            'puesto' => 'nullable|string|max:255',
            'estatus' => 'nullable|string|in:activo,inactivo,baja_definitiva',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
            'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'estatus.in' => 'El estatus seleccionado no es válido.',
        ]);

        $estatus = $request->input('estatus', 'activo') ?: 'activo';
        $validated['estatus'] = $estatus;
        $validated['activo'] = ($estatus === 'activo');

        $empleado = Empleado::create($validated);

        EmpleadoLog::create([
            'user_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'empleado_numero' => $empleado->numero_empleado,
            'empleado_nombre' => $empleado->nombre,
            'action' => 'crear',
            'details' => json_encode([
                'nombre' => $empleado->nombre,
                'numero_empleado' => $empleado->numero_empleado,
                'correo' => $empleado->correo,
                'departamento' => $empleado->departamento,
                'puesto' => $empleado->puesto,
                'estatus' => $empleado->estatus,
                'activo' => $empleado->activo ? 'Activo' : 'Inactivo',
            ], JSON_UNESCAPED_UNICODE)
        ]);

        Log::channel('empleados')->info("Empleado creado exitosamente: {$empleado->nombre} ({$empleado->numero_empleado})", [
            'usuario_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'numero_empleado' => $empleado->numero_empleado,
            'estatus' => $empleado->estatus,
        ]);

        return redirect()->route('empleados.index')->with('success', 'Empleado dado de alta exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'numero_empleado' => [
                'required',
                'numeric',
                'max_digits:10',
                Rule::unique('empleados', 'numero_empleado')->ignore($empleado->id),
            ],
            'nombre' => 'required|string|max:255',
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('empleados', 'correo')->ignore($empleado->id),
            ],
            'departamento' => 'nullable|string|max:255',
            'puesto' => 'nullable|string|max:255',
            'estatus' => 'nullable|string|in:activo,inactivo,baja_definitiva',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.max_digits' => 'El número de empleado no debe exceder los 10 dígitos.',
            'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe ser una dirección válida.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'estatus.in' => 'El estatus seleccionado no es válido.',
        ]);

        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus');
            $validated['estatus'] = $estatus;
            $validated['activo'] = ($estatus === 'activo');
        }

        $original = $empleado->getOriginal();
        $empleado->update($validated);
        $changes = $empleado->getChanges();

        EmpleadoLog::create([
            'user_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'empleado_numero' => $empleado->numero_empleado,
            'empleado_nombre' => $empleado->nombre,
            'action' => 'actualizar',
            'details' => json_encode([
                'changes' => $changes,
                'original' => array_intersect_key($original, $changes)
            ], JSON_UNESCAPED_UNICODE)
        ]);

        Log::channel('empleados')->info("Empleado actualizado exitosamente: {$empleado->nombre} ({$empleado->numero_empleado})", [
            'usuario_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'cambios' => $changes,
        ]);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Toggle or change the status of the employee.
     */
    public function toggleStatus(Request $request, Empleado $empleado)
    {
        $oldStatus = $empleado->estatus ?? ($empleado->activo ? 'activo' : 'inactivo');
        
        if ($request->filled('estatus') && in_array($request->input('estatus'), ['activo', 'inactivo', 'baja_definitiva'])) {
            $newStatus = $request->input('estatus');
        } else {
            $newStatus = ($empleado->activo || $oldStatus === 'activo') ? 'inactivo' : 'activo';
        }

        $empleado->update([
            'estatus' => $newStatus,
            'activo' => ($newStatus === 'activo'),
        ]);

        EmpleadoLog::create([
            'user_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'empleado_numero' => $empleado->numero_empleado,
            'empleado_nombre' => $empleado->nombre,
            'action' => 'cambiar_estado',
            'details' => json_encode([
                'estatus' => $newStatus,
                'anterior' => $oldStatus,
                'activo' => $empleado->activo ? 'Activo' : 'Inactivo',
            ], JSON_UNESCAPED_UNICODE)
        ]);

        Log::channel('empleados')->info("Estatus de empleado modificado: {$empleado->numero_empleado} de '{$oldStatus}' a '{$newStatus}'", [
            'usuario_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'anterior' => $oldStatus,
            'nuevo' => $newStatus,
        ]);

        $statusLabels = [
            'activo' => 'activado',
            'inactivo' => 'desactivado',
            'baja_definitiva' => 'marcado como baja definitiva',
        ];

        $statusMessage = $statusLabels[$newStatus] ?? 'actualizado';
        return redirect()->route('empleados.index')->with('success', "Empleado {$statusMessage} exitosamente.");
    }

    /**
     * Download CSV template for employee import.
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=plantilla_empleados.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['numero_empleado', 'nombre', 'correo', 'departamento', 'puesto', 'estatus'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for Excel UTF-8
            fputcsv($file, $columns);
            fputcsv($file, ['1234567890', 'Juan Perez Lopez', 'juan.perez@empresa.com', 'Produccion', 'Operador A', 'activo']);
            fputcsv($file, ['0987654321', 'Maria Garcia Ruiz', 'maria.garcia@empresa.com', 'Logistica', 'Supervisora', 'inactivo']);
            fputcsv($file, ['1122334455', 'Carlos Sanchez Cruz', 'carlos.sanchez@empresa.com', 'Calidad', 'Inspector', 'baja_definitiva']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from CSV (Creates new or updates existing status/data).
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'csv_file.required' => 'Debe seleccionar un archivo CSV.',
            'csv_file.mimes' => 'El archivo debe tener formato .csv o .txt.',
            'csv_file.max' => 'El archivo no debe pesar más de 2MB.',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $createdCount = 0;
        $updatedCount = 0;
        $errors = [];
        
        if (($handle = fopen($path, 'r')) !== false) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle, 1000, ",");
            
            if (!$header || count($header) < 2) {
                fclose($handle);
                return redirect()->route('empleados.index')->withErrors(['csv_file' => 'El archivo CSV no tiene el formato correcto o está vacío.']);
            }

            $header = array_map(function($h) {
                return strtolower(trim(str_replace(['"', "'"], '', $h)));
            }, $header);

            $rowNumber = 1;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $rowNumber++;
                
                if (count($header) !== count($data)) {
                    $errors[] = "Fila {$rowNumber}: El número de columnas no coincide con la cabecera.";
                    continue;
                }

                $row = array_combine($header, $data);
                
                $numeroEmpleado = trim($row['numero_empleado'] ?? '');
                $nombre = trim($row['nombre'] ?? '');
                $correo = trim($row['correo'] ?? '');
                $departamento = trim($row['departamento'] ?? '');
                $puesto = trim($row['puesto'] ?? '');
                $rawStatus = strtolower(trim($row['estatus'] ?? ''));

                if (empty($numeroEmpleado) || empty($nombre) || empty($correo)) {
                    $errors[] = "Fila {$rowNumber}: El número de empleado, el nombre y el correo son obligatorios.";
                    continue;
                }

                if (!is_numeric($numeroEmpleado) || strlen($numeroEmpleado) > 10) {
                    $errors[] = "Fila {$rowNumber}: El número de empleado '{$numeroEmpleado}' debe ser numérico y no exceder los 10 dígitos.";
                    continue;
                }

                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Fila {$rowNumber}: El correo '{$correo}' no es una dirección válida.";
                    continue;
                }

                // Normalización de estatus (Por defecto: 'activo')
                if (in_array($rawStatus, ['', 'activo', 'activa', '1', 'active'])) {
                    $estatus = 'activo';
                } elseif (in_array($rawStatus, ['inactivo', 'inactiva', '0', 'inactive', 'desactivado', 'desactivada'])) {
                    $estatus = 'inactivo';
                } elseif (in_array($rawStatus, ['baja', 'baja_definitiva', 'baja definitiva', 'eliminado', 'eliminada'])) {
                    $estatus = 'baja_definitiva';
                } else {
                    $estatus = 'activo';
                }

                $esActivo = ($estatus === 'activo');

                $existing = Empleado::where('numero_empleado', $numeroEmpleado)->first();

                if ($existing) {
                    // Verificar si el correo está ocupado por otro empleado diferente
                    $emailConflict = Empleado::where('correo', $correo)
                        ->where('id', '!=', $existing->id)
                        ->exists();

                    if ($emailConflict) {
                        $errors[] = "Fila {$rowNumber}: El correo '{$correo}' ya está registrado con otro número de empleado.";
                        continue;
                    }

                    $original = $existing->getOriginal();
                    $existing->update([
                        'nombre' => $nombre,
                        'correo' => $correo,
                        'departamento' => $departamento ?: null,
                        'puesto' => $puesto ?: null,
                        'estatus' => $estatus,
                        'activo' => $esActivo,
                    ]);

                    $changes = $existing->getChanges();

                    EmpleadoLog::create([
                        'user_id' => auth()->id(),
                        'empleado_id' => $existing->id,
                        'empleado_numero' => $existing->numero_empleado,
                        'empleado_nombre' => $existing->nombre,
                        'action' => 'importar_actualizar',
                        'details' => json_encode([
                            'changes' => $changes,
                            'original' => array_intersect_key($original, $changes)
                        ], JSON_UNESCAPED_UNICODE)
                    ]);

                    $updatedCount++;
                } else {
                    // Verificar si el correo ya existe
                    $emailExists = Empleado::where('correo', $correo)->exists();
                    if ($emailExists) {
                        $errors[] = "Fila {$rowNumber}: El correo '{$correo}' ya está registrado con otro colaborador.";
                        continue;
                    }

                    $empleado = Empleado::create([
                        'numero_empleado' => $numeroEmpleado,
                        'nombre' => $nombre,
                        'correo' => $correo,
                        'departamento' => $departamento ?: null,
                        'puesto' => $puesto ?: null,
                        'estatus' => $estatus,
                        'activo' => $esActivo,
                    ]);

                    EmpleadoLog::create([
                        'user_id' => auth()->id(),
                        'empleado_id' => $empleado->id,
                        'empleado_numero' => $empleado->numero_empleado,
                        'empleado_nombre' => $empleado->nombre,
                        'action' => 'importar',
                        'details' => json_encode([
                            'nombre' => $empleado->nombre,
                            'numero_empleado' => $empleado->numero_empleado,
                            'correo' => $empleado->correo,
                            'departamento' => $empleado->departamento,
                            'puesto' => $empleado->puesto,
                            'estatus' => $empleado->estatus,
                            'activo' => $empleado->activo ? 'Activo' : 'Inactivo',
                        ], JSON_UNESCAPED_UNICODE)
                    ]);

                    $createdCount++;
                }
            }
            fclose($handle);
        }

        $totalProcesados = $createdCount + $updatedCount;
        $message = "Se procesaron {$totalProcesados} empleados con éxito ({$createdCount} nuevos registrados, {$updatedCount} actualizados/cambio de estatus).";

        Log::channel('empleados')->info('Carga masiva de empleados ejecutada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'nuevos_creados' => $createdCount,
            'actualizados' => $updatedCount,
            'total_procesados' => $totalProcesados,
            'errores_conteo' => count($errors),
        ]);

        if (count($errors) > 0) {
            return redirect()->route('empleados.index')
                ->with('success', $message)
                ->withErrors($errors);
        }

        return redirect()->route('empleados.index')->with('success', $message);
    }
}

