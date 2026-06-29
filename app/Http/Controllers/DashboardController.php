<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\RegistroComedor;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with stats and graphs.
     */
    public function index()
    {
        // 1. KPI Metrics
        $totalEmpleados = Empleado::count();
        $empleadosActivos = Empleado::where('activo', true)->count();
        $empleadosInactivos = $totalEmpleados - $empleadosActivos;

        $todayStr = Carbon::today()->toDateString();
        $accesosHoy = RegistroComedor::where('fecha', $todayStr)->count();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $accesosMes = RegistroComedor::whereBetween('fecha', [$startOfMonth, $endOfMonth])->count();

        // 2. Daily Accesses Chart (Last 15 days)
        $dailyLabels = [];
        $dailyValues = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $dailyLabels[$dateStr] = $date->format('d/m');
            $dailyValues[$dateStr] = 0;
        }

        $rawDaily = RegistroComedor::selectRaw('fecha, count(*) as count')
            ->where('fecha', '>=', Carbon::now()->subDays(14)->toDateString())
            ->groupBy('fecha')
            ->get();

        foreach ($rawDaily as $row) {
            $dateKey = ($row->fecha instanceof Carbon) ? $row->fecha->toDateString() : (string)$row->fecha;
            if (isset($dailyValues[$dateKey])) {
                $dailyValues[$dateKey] = $row->count;
            }
        }

        // 3. Monthly Accesses Chart (Current year)
        $monthNames = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        $monthlyLabels = [];
        $monthlyValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[$m] = $monthNames[$m];
            $monthlyValues[$m] = 0;
        }

        $rawMonthly = RegistroComedor::selectRaw('MONTH(fecha) as month, count(*) as count')
            ->whereYear('fecha', Carbon::now()->year)
            ->groupBy('month')
            ->get();

        foreach ($rawMonthly as $row) {
            $monthNum = (int)$row->month;
            if (isset($monthlyValues[$monthNum])) {
                $monthlyValues[$monthNum] = $row->count;
            }
        }

        // 4. Hourly Distribution Chart (Peak Hours - grouping 6:00 to 20:00)
        $hourlyLabels = [];
        $hourlyValues = [];
        for ($h = 6; $h <= 20; $h++) {
            $hourlyLabels[$h] = sprintf('%02d:00', $h);
            $hourlyValues[$h] = 0;
        }

        $rawHourly = RegistroComedor::selectRaw('HOUR(fecha_hora) as hour, count(*) as count')
            ->groupBy('hour')
            ->get();

        foreach ($rawHourly as $row) {
            $hourNum = (int)$row->hour;
            if (isset($hourlyValues[$hourNum])) {
                $hourlyValues[$hourNum] = $row->count;
            }
        }

        // 5. Department Breakdown Chart
        $rawDepts = RegistroComedor::join('empleados', 'registro_comedors.empleado_id', '=', 'empleados.id')
            ->selectRaw('COALESCE(empleados.departamento, "Sin departamento") as dept, count(*) as count')
            ->groupBy('empleados.departamento')
            ->orderBy('count', 'desc')
            ->get();

        $deptLabels = [];
        $deptValues = [];
        foreach ($rawDepts as $dept) {
            $deptLabels[] = $dept->dept ?: 'Sin departamento';
            $deptValues[] = $dept->count;
        }

        return view('dashboard', [
            'totalEmpleados' => $totalEmpleados,
            'empleadosActivos' => $empleadosActivos,
            'empleadosInactivos' => $empleadosInactivos,
            'accesosHoy' => $accesosHoy,
            'accesosMes' => $accesosMes,

            'dailyLabels' => array_values($dailyLabels),
            'dailyValues' => array_values($dailyValues),

            'monthlyLabels' => array_values($monthlyLabels),
            'monthlyValues' => array_values($monthlyValues),

            'hourlyLabels' => array_values($hourlyLabels),
            'hourlyValues' => array_values($hourlyValues),

            'deptLabels' => $deptLabels,
            'deptValues' => $deptValues,
        ]);
    }
}
