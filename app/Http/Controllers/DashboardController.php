<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\RegistroComedor;
use Carbon\Carbon;

use App\Mail\DashboardReportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with stats and graphs.
     */
    public function index()
    {
        // Trazabilidad de acceso al menú Dashboard
        Log::channel('dashboard')->info('Acceso al menú Dashboard realizado', [
            'usuario_id' => auth()->id(),
            'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
            'ip' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // 1. KPI Metrics
        $totalEmpleados = Empleado::count();
        $empleadosActivos = Empleado::where('activo', true)->count();
        $empleadosInactivos = $totalEmpleados - $empleadosActivos;

        $todayStr = Carbon::today()->toDateString();
        $accesosHoy = RegistroComedor::where('fecha', $todayStr)->count();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $accesosMes = RegistroComedor::whereBetween('fecha', [$startOfMonth, $endOfMonth])->count();

        // Cálculo de Promedio Diario considerando únicamente días laborables (Lunes a Viernes) del mes en curso
        $now = Carbon::now();
        $startOfMonthObj = $now->copy()->startOfMonth();
        $todayObj = $now->copy();

        $diasLaborablesTranscurridos = 0;
        for ($dateIter = $startOfMonthObj->copy(); $dateIter->lte($todayObj); $dateIter->addDay()) {
            if ($dateIter->isWeekday()) {
                $diasLaborablesTranscurridos++;
            }
        }

        $accesosMesLaborables = RegistroComedor::whereBetween('fecha', [$startOfMonthObj->toDateString(), $todayObj->toDateString()])
            ->whereRaw('WEEKDAY(fecha) < 5')
            ->count();

        $promedioDiario = $diasLaborablesTranscurridos > 0 ? round($accesosMesLaborables / $diasLaborablesTranscurridos, 1) : 0;

        // 2. Daily Accesses Chart (Last 15 days - Filtrado exclusivamente para días laborables Lunes a Viernes)
        $dailyLabels = [];
        $dailyValues = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            if ($date->isWeekday()) {
                $dateStr = $date->toDateString();
                $dailyLabels[$dateStr] = $date->format('d/m');
                $dailyValues[$dateStr] = 0;
            }
        }

        $rawDaily = RegistroComedor::selectRaw('fecha, count(*) as count')
            ->where('fecha', '>=', Carbon::now()->subDays(14)->toDateString())
            ->whereRaw('WEEKDAY(fecha) < 5')
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

        // 4. Hourly Distribution Chart (Peak Hours - acotado de 12:00 a 17:00 hrs)
        $hourlyLabels = [];
        $hourlyValues = [];
        for ($h = 12; $h <= 17; $h++) {
            $hourlyLabels[$h] = sprintf('%02d:00', $h);
            $hourlyValues[$h] = 0;
        }

        $rawHourly = RegistroComedor::selectRaw('HOUR(fecha_hora) as hour, count(*) as count')
            ->whereRaw('HOUR(fecha_hora) BETWEEN 12 AND 17')
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
            'promedioDiario' => $promedioDiario,

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

    /**
     * Send dashboard report via email.
     */
    public function sendReportEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'periodo' => 'nullable|string|in:Diario,Semanal,Mensual',
            'notas' => 'nullable|string|max:500',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo válida.',
        ]);

        try {
            $totalEmpleados = Empleado::count();
            $empleadosActivos = Empleado::where('activo', true)->count();
            $empleadosInactivos = $totalEmpleados - $empleadosActivos;

            $todayStr = Carbon::today()->toDateString();
            $accesosHoy = RegistroComedor::where('fecha', $todayStr)->count();

            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $accesosMes = RegistroComedor::whereBetween('fecha', [$startOfMonth, $endOfMonth])->count();
            $promedioDiario = $accesosMes > 0 ? round($accesosMes / Carbon::now()->day, 1) : 0;

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

            $stats = [
                'totalEmpleados' => $totalEmpleados,
                'empleadosActivos' => $empleadosActivos,
                'empleadosInactivos' => $empleadosInactivos,
                'accesosHoy' => $accesosHoy,
                'accesosMes' => $accesosMes,
                'promedioDiario' => $promedioDiario,
                'deptLabels' => $deptLabels,
                'deptValues' => $deptValues,
            ];

            $periodo = $validated['periodo'] ?? 'Diario';
            $notas = $validated['notas'] ?? null;

            Mail::to($validated['email'])->send(new DashboardReportMail($stats, $periodo, $notas));

            $logData = [
                'accion' => 'envio_correo_dashboard',
                'destinatario' => $validated['email'],
                'periodo' => $periodo,
                'notas' => $notas,
                'usuario_id' => auth()->id(),
                'usuario_nombre' => auth()->user()->name ?? 'Desconocido',
                'ip' => $request->ip(),
                'timestamp' => now()->toDateTimeString(),
            ];

            Log::channel('dashboard')->info("Envío de correo con reporte del Dashboard realizado exitosamente", $logData);
            Log::channel('reportes')->info("Envío de correo con reporte del Dashboard realizado", $logData);

            return response()->json([
                'success' => true,
                'message' => "¡El reporte de estadísticas ha sido enviado exitosamente a {$validated['email']}!"
            ]);
        } catch (\Exception $e) {
            $errorData = [
                'accion' => 'envio_correo_dashboard_fallo',
                'destinatario' => $validated['email'] ?? null,
                'error' => $e->getMessage(),
                'usuario_id' => auth()->id(),
                'ip' => $request->ip(),
            ];

            Log::channel('dashboard')->error("Fallo al enviar correo con reporte del Dashboard", $errorData);
            Log::channel('reportes')->error("Fallo al enviar correo con reporte del Dashboard", $errorData);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un problema al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }
}
