<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use App\Models\RegistroComedor;
use App\Mail\DashboardReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDashboardReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:send-report {email? : Correo electrónico del destinatario} {--periodo=Diario : Frecuencia del reporte}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía de forma recurrente o bajo demanda el reporte de estadísticas del comedor por correo electrónico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address', 'admin@empresa.com');
        $periodo = $this->option('periodo');

        $this->info("Generando reporte de comedor ({$periodo}) para enviar a: {$email}...");

        try {
            // Recopilar estadísticas de negocio
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

            Mail::to($email)->send(new DashboardReportMail($stats, $periodo, 'Reporte automático del sistema de comedor.'));

            Log::channel('dashboard')->info("Comando Scheduler/Artisan: envío de reporte de comedor ejecutado", [
                'destinatario' => $email,
                'periodo' => $periodo
            ]);
            Log::channel('reportes')->info("Comando Scheduler/Artisan: envío de reporte de comedor ejecutado", [
                'destinatario' => $email,
                'periodo' => $periodo
            ]);
            $this->info("¡Reporte enviado exitosamente a {$email}!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::channel('dashboard')->error("Comando Scheduler/Artisan: fallo al enviar reporte de comedor", [
                'destinatario' => $email,
                'error' => $e->getMessage()
            ]);
            Log::channel('reportes')->error("Comando Scheduler/Artisan: fallo al enviar reporte de comedor", [
                'destinatario' => $email,
                'error' => $e->getMessage()
            ]);
            $this->error("Error al enviar el reporte: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
