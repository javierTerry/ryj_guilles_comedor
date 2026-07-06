<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\EmpleadoLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Empleado::query()->withCount('registrosComedor');

        // Filter by Search (name or employee number)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        // Filter by Department
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->input('departamento'));
        }

        // Filter by Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('activo', true);
            } elseif ($status === 'inactive') {
                $query->where('activo', false);
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
                'digits:10',
                'unique:empleados,numero_empleado',
            ],
            'nombre' => 'required|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'puesto' => 'nullable|string|max:255',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.digits' => 'El número de empleado debe tener exactamente 10 dígitos.',
            'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

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
                'departamento' => $empleado->departamento,
                'puesto' => $empleado->puesto,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return redirect()->route('empleados.index')->with('success', 'Empleado creado exitosamente.');
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
                'digits:10',
                Rule::unique('empleados', 'numero_empleado')->ignore($empleado->id),
            ],
            'nombre' => 'required|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'puesto' => 'nullable|string|max:255',
        ], [
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.numeric' => 'El número de empleado debe ser puramente numérico.',
            'numero_empleado.digits' => 'El número de empleado debe tener exactamente 10 dígitos.',
            'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

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

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Toggle the active status of the employee.
     */
    public function toggleStatus(Empleado $empleado)
    {
        $oldStatus = $empleado->activo;
        $empleado->update([
            'activo' => !$empleado->activo,
        ]);

        EmpleadoLog::create([
            'user_id' => auth()->id(),
            'empleado_id' => $empleado->id,
            'empleado_numero' => $empleado->numero_empleado,
            'empleado_nombre' => $empleado->nombre,
            'action' => 'cambiar_estado',
            'details' => json_encode([
                'activo' => $empleado->activo ? 'Activo' : 'Inactivo',
                'anterior' => $oldStatus ? 'Activo' : 'Inactivo'
            ], JSON_UNESCAPED_UNICODE)
        ]);

        $statusMessage = $empleado->activo ? 'activado' : 'desactivado';
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

        $columns = ['numero_empleado', 'nombre', 'departamento', 'puesto'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for Excel UTF-8
            fputcsv($file, $columns);
            fputcsv($file, ['1234567890', 'Juan Perez Lopez', 'Produccion', 'Operador A']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from CSV.
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

        $successCount = 0;
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
                return trim(str_replace(['"', "'"], '', $h));
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
                $departamento = trim($row['departamento'] ?? '');
                $puesto = trim($row['puesto'] ?? '');

                if (empty($numeroEmpleado) || empty($nombre)) {
                    $errors[] = "Fila {$rowNumber}: El número de empleado y el nombre son obligatorios.";
                    continue;
                }

                if (!is_numeric($numeroEmpleado) || strlen($numeroEmpleado) !== 10) {
                    $errors[] = "Fila {$rowNumber}: El número de empleado '{$numeroEmpleado}' debe ser numérico de exactamente 10 dígitos.";
                    continue;
                }

                $exists = Empleado::where('numero_empleado', $numeroEmpleado)->exists();
                if ($exists) {
                    $errors[] = "Fila {$rowNumber}: El número de empleado '{$numeroEmpleado}' ya está registrado.";
                    continue;
                }

                $empleado = Empleado::create([
                    'numero_empleado' => $numeroEmpleado,
                    'nombre' => $nombre,
                    'departamento' => $departamento ?: null,
                    'puesto' => $puesto ?: null,
                    'activo' => true,
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
                        'departamento' => $empleado->departamento,
                        'puesto' => $empleado->puesto,
                    ], JSON_UNESCAPED_UNICODE)
                ]);

                $successCount++;
            }
            fclose($handle);
        }

        $message = "Se importaron {$successCount} empleados con éxito.";
        if (count($errors) > 0) {
            return redirect()->route('empleados.index')
                ->with('success', $message)
                ->withErrors($errors);
        }

        return redirect()->route('empleados.index')->with('success', $message);
    }
}
