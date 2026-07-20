<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
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
}
