<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un respaldo diario comprimido de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de respaldo de la base de datos...');

        $connection = config('database.default');
        $backupDir = storage_path('app/backups');

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $date = date('Y-m-d_H-i-s');
        $filename = "backup_comedor_{$date}";

        try {
            if ($connection === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                $backupFile = "{$backupDir}/{$filename}.sqlite";

                if (file_exists($dbPath)) {
                    copy($dbPath, $backupFile);
                    if (function_exists('exec')) {
                        \exec("gzip -f " . \escapeshellarg($backupFile));
                        $backupFile .= ".gz";
                    }
                    $this->info("Respaldo SQLite creado exitosamente: {$backupFile}");
                } else {
                    $this->error("No se encontró el archivo de base de datos SQLite en: {$dbPath}");
                    return 1;
                }
            } elseif ($connection === 'mysql' || $connection === 'mariadb') {
                $host = config('database.connections.mysql.host', '127.0.0.1');
                $port = config('database.connections.mysql.port', '3306');
                $database = config('database.connections.mysql.database');
                $username = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');

                $backupFile = "{$backupDir}/{$filename}.sql.gz";

                $passwordFlag = !empty($password) ? '-p' . \escapeshellarg($password) : '';
                $command = sprintf(
                    'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
                    \escapeshellarg($host),
                    \escapeshellarg($port),
                    \escapeshellarg($username),
                    $passwordFlag,
                    \escapeshellarg($database),
                    \escapeshellarg($backupFile)
                );

                Log::channel('backups')->info("Command: {$command}");

                if (!function_exists('exec')) {
                    $this->error("La función 'exec' de PHP está deshabilitada en php.ini.");
                    Log::channel('backups')->error("La función 'exec' está deshabilitada en el servidor (disable_functions).");
                    return 1;
                }

                \exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    $this->error("Error al ejecutar mysqldump (código {$returnVar}).");
                    Log::channel('backups')->error("Error en respaldo de BD mysqldump", ['code' => $returnVar]);
                    return 1;
                }

                $this->info("Respaldo MySQL creado exitosamente: {$backupFile}");
            } else {
                $this->warn("Driver de base de datos no soportado automáticamente para respaldo: {$connection}");
                Log::channel('backups')->warning("Driver de base de datos no soportado automáticamente: {$connection}");
                return 1;
            }

            // Limpieza de respaldos antiguos (más de 30 días)
            $this->cleanOldBackups($backupDir, 30);

            Log::channel('backups')->info("Respaldo diario de BD ejecutado correctamente: {$filename}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Excepción durante el respaldo: " . $e->getMessage());
            Log::channel('backups')->error("Excepción en respaldo diario de BD: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Elimina respaldos antiguos que superen un número dado de días.
     */
    private function cleanOldBackups(string $directory, int $days = 30)
    {
        $files = glob("{$directory}/backup_comedor_*.gz");
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 86400 * $days) {
                    unlink($file);
                    $this->info("Respaldos antiguos eliminados: " . basename($file));
                }
            }
        }
    }
}
