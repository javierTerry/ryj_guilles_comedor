<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\RegistroComedor;
use App\Models\Encuesta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    /**
     * Muestra la vista del menú de reportes consultando todos los empleados
     * con su conteo total de visitas en orden descendente acotado por un rango de fecha (si aplica),
     * con rango de registros por página (25, 50, 75, 100).
     */
    public function index(Request $request)
    {
        // Obtener lista de departamentos únicos para el selector
        $departamentos = Empleado::select('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento');

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Consulta base de TODOS los empleados con el conteo de sus visitas al comedor acotado por rango de fecha
        $query = Empleado::withCount(['registrosComedor' => function ($q) use ($fechaInicio, $fechaFin) {
            if ($fechaInicio) {
                $q->where('fecha', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $q->where('fecha', '<=', $fechaFin);
            }
        }]);

        // Filtro por Búsqueda (Nombre o Número de Empleado)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        // Filtro por Departamento
        if ($request->filled('departamento')) {
            $query->where('departamento', $request->input('departamento'));
        }

        // Filtro por Estatus (Activo / Inactivo)
        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->where('activo', $estatus);
        }

        // Rango de registros por página (Default: 25. Opciones: 25, 50, 75, 100)
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [25, 50, 75, 100])) {
            $perPage = 25;
        }

        // Total de empleados filtrados
        $totalEmpleadosFiltrados = (clone $query)->count();

        // Suma total de visitas contabilizadas en el rango para los empleados consultados
        $totalVisitas = (clone $query)->get()->sum('registros_comedor_count');

        // Paginación dinámica ordenados por visitas descendente
        $empleados = $query->orderBy('registros_comedor_count', 'desc')
                           ->orderBy('nombre', 'asc')
                           ->paginate($perPage)
                           ->withQueryString();

        $hasFilters = $request->anyFilled(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin']) || $request->filled('per_page');

        // Trazabilidad en canal dedicado 'reportes'
        Log::channel('reportes')->info('Consulta de reporte de empleados realizada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'per_page' => $perPage,
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin', 'per_page'])),
            'total_empleados' => $totalEmpleadosFiltrados,
            'total_visitas' => $totalVisitas,
        ]);

        return view('reportes.index', compact(
            'empleados',
            'departamentos',
            'totalEmpleadosFiltrados',
            'totalVisitas',
            'hasFilters',
            'perPage',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Genera y descarga el archivo CSV con todos los empleados y sus métricas de visitas acotadas por fecha.
     */
    public function exportCsv(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Empleado::withCount(['registrosComedor' => function ($q) use ($fechaInicio, $fechaFin) {
            if ($fechaInicio) {
                $q->where('fecha', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $q->where('fecha', '<=', $fechaFin);
            }
        }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departamento')) {
            $query->where('departamento', $request->input('departamento'));
        }

        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->where('activo', $estatus);
        }

        $totalAExportar = (clone $query)->count();
        $totalVisitasExportadas = (clone $query)->get()->sum('registros_comedor_count');

        // Trazabilidad en canal dedicado 'reportes'
        Log::channel('reportes')->info('Exportación de archivo CSV de empleados generada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin'])),
            'total_registros_exportados' => $totalAExportar,
            'total_visitas_exportadas' => $totalVisitasExportadas,
        ]);

        $filename = 'reporte_empleados_visitas_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM para compatibilidad con Microsoft Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID Empleado',
                'Nº Empleado',
                'Nombre del Colaborador',
                'Correo Electrónico',
                'Departamento',
                'Puesto',
                'Estatus Empleado',
                'Total Visitas Comedor'
            ]);

            $query->orderBy('registros_comedor_count', 'desc')
                  ->orderBy('nombre', 'asc')
                  ->chunk(500, function ($empleados) use ($handle) {
                      foreach ($empleados as $emp) {
                          fputcsv($handle, [
                              $emp->id,
                              $emp->numero_empleado ?? '',
                              $emp->nombre ?? '',
                              $emp->correo ?? '',
                              $emp->departamento ?? 'Sin departamento',
                              $emp->puesto ?? 'Sin puesto',
                              $emp->activo ? 'Activo' : 'Inactivo',
                              $emp->registros_comedor_count ?? 0,
                          ]);
                      }
                  });

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Muestra la vista del reporte detallado de visitas con horario de ingreso al comedor.
     * Si no se aplican filtros de fecha, toma por defecto la semana actual.
     */
    public function visitas(Request $request)
    {
        $departamentos = Empleado::select('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento');

        // Si no se proporcionaron fechas explícitas, usar por defecto el rango de la semana actual
        $hasCustomDateFilter = $request->filled('fecha_inicio') || $request->filled('fecha_fin');

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfWeek()->format('Y-m-d'));

        // Consulta base sobre RegistroComedor con relación a Empleado
        $query = RegistroComedor::with('empleado')
            ->whereHas('empleado');

        if ($fechaInicio) {
            $query->whereDate('fecha', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->whereDate('fecha', '<=', $fechaFin);
        }

        // Filtro por Búsqueda (Nombre o Número de Empleado)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        // Filtro por Departamento
        if ($request->filled('departamento')) {
            $dept = $request->input('departamento');
            $query->whereHas('empleado', function ($q) use ($dept) {
                $q->where('departamento', $dept);
            });
        }

        // Filtro por Estatus (Activo / Inactivo)
        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->whereHas('empleado', function ($q) use ($estatus) {
                $q->where('activo', $estatus);
            });
        }

        // Rango de registros por página (Default: 25. Opciones: 25, 50, 75, 100)
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [25, 50, 75, 100])) {
            $perPage = 25;
        }

        // Total de visitas filtradas
        $totalVisitas = (clone $query)->count();

        // Paginación dinámica ordenada por fecha y hora de ingreso descendente
        $visitas = $query->orderBy('fecha_hora', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

        $hasFilters = $request->anyFilled(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin']) || $request->filled('per_page');

        // Trazabilidad en canal dedicado 'visitas'
        Log::channel('visitas')->info('Consulta de reporte de visitas al comedor realizada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'per_page' => $perPage,
            'fecha_inicio_usada' => $fechaInicio,
            'fecha_fin_usada' => $fechaFin,
            'es_semana_actual_default' => !$hasCustomDateFilter,
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin', 'per_page'])),
            'total_visitas' => $totalVisitas,
        ]);

        return view('reportes.visitas', compact(
            'visitas',
            'departamentos',
            'totalVisitas',
            'hasFilters',
            'hasCustomDateFilter',
            'perPage',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Genera y descarga el archivo CSV con las visitas detalladas con horario de ingreso.
     */
    public function exportVisitasCsv(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfWeek()->format('Y-m-d'));

        $query = RegistroComedor::with('empleado')->whereHas('empleado');

        if ($fechaInicio) {
            $query->whereDate('fecha', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->whereDate('fecha', '<=', $fechaFin);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departamento')) {
            $dept = $request->input('departamento');
            $query->whereHas('empleado', function ($q) use ($dept) {
                $q->where('departamento', $dept);
            });
        }

        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->whereHas('empleado', function ($q) use ($estatus) {
                $q->where('activo', $estatus);
            });
        }

        $totalVisitasExportadas = (clone $query)->count();

        // Trazabilidad en canal dedicado 'visitas'
        Log::channel('visitas')->info('Exportación de archivo CSV de reporte de visitas generada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin'])),
            'total_registros_exportados' => $totalVisitasExportadas,
        ]);

        $filename = 'reporte_visitas_comedor_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM para compatibilidad con Microsoft Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Nº Empleado',
                'Nombre del Colaborador',
                'Correo Electrónico',
                'Departamento',
                'Puesto',
                'Estatus Empleado',
                'Fecha de Visita',
                'Horario de Ingreso'
            ]);

            $query->orderBy('fecha_hora', 'desc')
                  ->chunk(500, function ($visitas) use ($handle) {
                      foreach ($visitas as $visita) {
                          $emp = $visita->empleado;
                          $fechaHora = $visita->fecha_hora ? Carbon::parse($visita->fecha_hora) : null;
                          fputcsv($handle, [
                              $emp->numero_empleado ?? '',
                              $emp->nombre ?? '',
                              $emp->correo ?? '',
                              $emp->departamento ?? 'Sin departamento',
                              $emp->puesto ?? 'Sin puesto',
                              $emp->activo ? 'Activo' : 'Inactivo',
                              $fechaHora ? $fechaHora->format('d/m/Y') : ($visita->fecha ? $visita->fecha->format('d/m/Y') : ''),
                              $fechaHora ? $fechaHora->format('H:i:s') : '',
                          ]);
                      }
                  });

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Muestra la vista del reporte detallado de encuestas de satisfacción del comedor.
     * Si no se aplican filtros de fecha, toma por defecto la semana actual.
     */
    public function encuestas(Request $request)
    {
        $departamentos = Empleado::select('departamento')
            ->whereNotNull('departamento')
            ->where('departamento', '!=', '')
            ->distinct()
            ->orderBy('departamento')
            ->pluck('departamento');

        // Si no se proporcionaron fechas explícitas, usar por defecto el rango de la semana actual
        $hasCustomDateFilter = $request->filled('fecha_inicio') || $request->filled('fecha_fin');

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfWeek()->format('Y-m-d'));

        // Consulta base sobre Encuesta con relación a Empleado
        $query = Encuesta::with('empleado')
            ->whereHas('empleado');

        if ($fechaInicio) {
            $query->whereDate('fecha', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->whereDate('fecha', '<=', $fechaFin);
        }

        // Filtro por Búsqueda (Nombre o Número de Empleado)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        // Filtro por Departamento
        if ($request->filled('departamento')) {
            $dept = $request->input('departamento');
            $query->whereHas('empleado', function ($q) use ($dept) {
                $q->where('departamento', $dept);
            });
        }

        // Filtro por Estatus (Activo / Inactivo)
        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->whereHas('empleado', function ($q) use ($estatus) {
                $q->where('activo', $estatus);
            });
        }

        // Rango de registros por página (Default: 25. Opciones: 25, 50, 75, 100)
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [25, 50, 75, 100])) {
            $perPage = 25;
        }

        // Métricas KPI para el encabezado del reporte
        $totalEncuestas = (clone $query)->count();
        $promedioCalificacion = $totalEncuestas > 0 ? round((clone $query)->avg('calificacion'), 2) : 0;
        $promedioConversion = $totalEncuestas > 0 ? round((clone $query)->avg('conversion'), 2) : 0;
        $promedioPonderado = $totalEncuestas > 0 ? round((clone $query)->avg('ponderacion_total'), 2) : 0;

        // Paginación ordenada por fecha y hora descendente
        $encuestas = $query->orderBy('fecha', 'desc')
                           ->orderBy('hora', 'desc')
                           ->paginate($perPage)
                           ->withQueryString();

        $hasFilters = $request->anyFilled(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin']) || $request->filled('per_page');

        // Trazabilidad en canal dedicado 'encuestas'
        Log::channel('encuestas')->info('Consulta de reporte de encuestas realizada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'per_page' => $perPage,
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin', 'per_page'])),
            'total_encuestas' => $totalEncuestas,
            'promedio_calificacion' => $promedioCalificacion,
            'promedio_conversion' => $promedioConversion
        ]);

        return view('reportes.encuestas', compact(
            'encuestas',
            'departamentos',
            'totalEncuestas',
            'promedioCalificacion',
            'promedioConversion',
            'promedioPonderado',
            'hasCustomDateFilter',
            'hasFilters',
            'perPage',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Genera y descarga el archivo CSV con las encuestas de satisfacción filtradas.
     */
    public function exportEncuestasCsv(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfWeek()->format('Y-m-d'));

        $query = Encuesta::with('empleado')->whereHas('empleado');

        if ($fechaInicio) {
            $query->whereDate('fecha', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->whereDate('fecha', '<=', $fechaFin);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('numero_empleado', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departamento')) {
            $dept = $request->input('departamento');
            $query->whereHas('empleado', function ($q) use ($dept) {
                $q->where('departamento', $dept);
            });
        }

        if ($request->filled('estatus')) {
            $estatus = $request->input('estatus') === '1';
            $query->whereHas('empleado', function ($q) use ($estatus) {
                $q->where('activo', $estatus);
            });
        }

        $totalExportar = (clone $query)->count();

        Log::channel('encuestas')->info('Exportación CSV de encuestas generada', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => $request->ip(),
            'filtros' => array_filter($request->only(['search', 'departamento', 'estatus', 'fecha_inicio', 'fecha_fin'])),
            'total_registros_exportados' => $totalExportar,
        ]);

        $filename = 'reporte_encuestas_satisfaccion_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8 Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID Encuesta',
                'Nº Empleado',
                'Nombre del Colaborador',
                'Departamento',
                'Puesto',
                'Estatus Empleado',
                'Fecha Encuesta',
                'Hora Encuesta',
                'Calidad Alimentos (1-5)',
                'Calidad %',
                'Limpieza e Higiene (1-5)',
                'Limpieza %',
                'Temperatura Adecuada (1-5)',
                'Temperatura %',
                'Atención y Eficiencia (1-5)',
                'Atención %',
                'Presentación (1-5)',
                'Presentación %',
                'Calificación Promedio (1-5)',
                'Conversión (%)',
                'Ponderado Total (%)',
                'Comentarios'
            ]);

            $query->orderBy('fecha', 'desc')
                  ->orderBy('hora', 'desc')
                  ->chunk(500, function ($encuestas) use ($handle) {
                      foreach ($encuestas as $enc) {
                          $emp = $enc->empleado;
                          fputcsv($handle, [
                              $enc->id,
                              $emp->numero_empleado ?? '',
                              $emp->nombre ?? '',
                              $emp->departamento ?? 'Sin departamento',
                              $emp->puesto ?? 'Sin puesto',
                              $emp->activo ? 'Activo' : 'Inactivo',
                              $enc->fecha ? $enc->fecha->format('d/m/Y') : '',
                              $enc->hora ?? '',
                              $enc->calidad_alimentos,
                              $enc->calidad_alimentos_conversion . '%',
                              $enc->limpieza_higiene,
                              $enc->limpieza_higiene_conversion . '%',
                              $enc->temperatura_adecuada,
                              $enc->temperatura_adecuada_conversion . '%',
                              $enc->atencion_eficiencia,
                              $enc->atencion_eficiencia_conversion . '%',
                              $enc->presentacion,
                              $enc->presentacion_conversion . '%',
                              $enc->calificacion,
                              $enc->conversion . '%',
                              $enc->ponderacion_total . '%',
                              $enc->comentarios ?? '',
                          ]);
                      }
                  });

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
