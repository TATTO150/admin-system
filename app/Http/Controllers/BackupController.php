<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    protected $bitacora;

    public function __construct(BitacoraController $bitacora)
    {
        
        $this->bitacora = $bitacora;

    }
   // Mostrar la lista de respaldos
   public function index()
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'RESPALDO')
                ->limit(1);
        })
        ->where('Permiso_Consultar', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(9, 'Intento de ingreso a la ventana de respaldo sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar respaldo');
    }

       $backups = Storage::files('backups');
       $this->bitacora->registrarEnBitacora(9, 'Ingreso a la ventana de respaldo', 'Ingreso');
       return view('backups.index', compact('backups'));
   }

   // Crear un respaldo
   public function create()
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'RESPALDO')
                ->limit(1);
        })
        ->where('Permiso_Insercion', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(9, 'Intento de crear respaldo sin permisos', 'Ingreso');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar solicitudes');
    }

    $date = date('Y_m_d_His');
    $sqlFilename = 'admsystemct.sql';
    $sqlFilePath = storage_path('app/backups/' . $sqlFilename);
    $zipFilename = 'railway_backup_' . $date . '.zip';
    $zipFilePath = storage_path('app/backups/' . $zipFilename);
    $zipFolderName = 'basededatos_' . $date; // Nombre de la carpeta dentro del ZIP

    // Crear la carpeta backups si no existe
    if (!Storage::exists('backups')) {
        Storage::makeDirectory('backups');
    }

    // Especifica la ruta completa al binario mysqldump en XAMPP
    $mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe'; // Ajusta según la ubicación exacta en tu sistema

    // Construir el comando mysqldump con las opciones para incluir rutinas y triggers
    $password = env('DB_PASSWORD');
    $command = sprintf(
        '%s --user=%s %s --host=%s --port=%s --routines --triggers %s > %s',
        escapeshellarg($mysqldumpPath),
        escapeshellarg(env('DB_USERNAME')),
        $password ? '--password=' . escapeshellarg($password) : '',
        escapeshellarg(env('DB_HOST')),
        escapeshellarg(env('DB_PORT')),
        escapeshellarg(env('DB_DATABASE')),
        escapeshellarg($sqlFilePath)
    );

    // Ejecutar el comando y capturar el resultado
    exec($command . ' 2>&1', $output, $resultCode);

    // Registrar la salida y el código de resultado
    Log::info('mysqldump command executed');
    Log::info('mysqldump output: ' . implode("\n", $output));
    Log::info('mysqldump result code: ' . $resultCode);

    // Verificar si el comando mysqldump fue exitoso
    if ($resultCode !== 0) {
        return redirect()->route('backups.index')->with('error', 'Error al crear el respaldo: ' . implode("\n", $output));
    }

    // Crear archivo ZIP con la estructura solicitada
    if ($this->createZipFile($sqlFilePath, $zipFilePath, $zipFolderName)) {
        // Eliminar el archivo SQL original después de comprimirlo
        Storage::delete('backups/' . $sqlFilename);

        // Redirigir de nuevo a la vista de index con un mensaje de éxito
        $this->bitacora->registrarEnBitacora(9, 'Nuevo respaldo creado', 'insertar');
        return redirect()->route('backups.index')->with('success', 'Respaldo creado exitosamente.');
    } else {
        return redirect()->route('backups.index')->with('error', 'Error al crear el archivo ZIP.');
    }
}
   

private function createZipFile($sqlFilePath, $zipFilePath,$zipFolderName)
{
    $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            $sqlFilename = basename($sqlFilePath); // Obtener solo el nombre del archivo
            $zip->addFile($sqlFilePath, $zipFolderName . '/' . $sqlFilename); // Añadir archivo dentro de la carpeta en el ZIP
            $zip->close();
            return true;
        } else {
            return false;
        }
}

   // Descargar un respaldo
   public function download($filename)
   {
       return Storage::download('backups/' . $filename);
   }

   // Eliminar un respaldo
   public function delete($filename)
   {
    $user = Auth::user();
    $roleId = $user->Id_Rol;

    // Verificar si el rol del usuario tiene el permiso de consulta en el objeto SOLICITUD
    $permisoConsultar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'RESPALDO')
                ->limit(1);
        })
        ->where('Permiso_Eliminacion', 'PERMITIDO')
        ->exists();

    if (!$permisoConsultar) {
        $this->bitacora->registrarEnBitacora(9, 'Intento de eliminar respaldo sin permisos', 'eliminar');
        return redirect()->route('dashboard')->withErrors('No tiene permiso para consultar solicitudes');
    }
       Storage::delete('backups/' . $filename);
       $this->bitacora->registrarEnBitacora(9, 'resplado eliminado', 'eliminada');
       return redirect()->route('backups.index')->with('success', 'Respaldo eliminado con éxito.');
   }

}
