<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use Illuminate\Support\Facades\Log;
use Exception;

class RestoreDatabase extends Command
{
    protected $signature = 'database:restore {filePath}';
    protected $description = 'Restaura la base de datos desde un archivo SQL especificado';

    public function handle()
    {
        $filePath = $this->argument('filePath');
    
        if (!file_exists($filePath)) {
            $this->error("El archivo de respaldo SQL no existe en la ruta especificada.");
            return 1;
        }
    
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE', 'admsystemct');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPassword = env('DB_PASSWORD', '');
    
        $command = sprintf(
            'mysql -h %s -u %s %s %s < %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            $dbPassword ? '-p' . escapeshellarg($dbPassword) : '',
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );
    
        Log::info("Ejecutando el comando: $command"); // Mensaje de depuración
    
        try {
            // Ejecutar el comando de restauración
            exec($command, $output, $returnVar);
    
            Log::info("Salida de exec: " . implode("\n", $output));
            Log::info("Código de retorno de exec: " . $returnVar);
    
            if ($returnVar !== 0) {
                $this->error("Error al restaurar la base de datos. Código de error: $returnVar");
                return 1;
            }
    
            $this->info("La base de datos ha sido restaurada correctamente.");
            return 0;
        } catch (Exception $e) {
            $this->error("Error al ejecutar la restauración: " . $e->getMessage());
            return 1;
        }
    }
    
}