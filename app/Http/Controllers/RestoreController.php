<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
class RestoreController extends Controller
{
    public function index()
    {
        return view('restore.index'); // Renderiza la vista del formulario de restauración
    }

    public function restoreFromBackup()
    {
        // Define el directorio donde están los archivos de respaldo
        $backupDirectory = storage_path('app/backups');
    
        // Busca un archivo que comience con 'admsystemct' y tenga la extensión .sql
        $files = glob($backupDirectory . '/admsystemct*.sql');
    
        // Si no se encuentra ningún archivo que coincida
        if (empty($files)) {
            return back()->with('status', 'No se encontró ningún archivo de respaldo que coincida.');
        }
    
        // Toma el primer archivo que coincida con el patrón
        $filePath = $files[0];
    
        try {
            // Llama al comando Artisan para restaurar la base de datos
            Artisan::call('database:restore', ['filePath' => $filePath]);
    
            // Mensaje de éxito en el log
            Log::info('Restauración de la base de datos completada correctamente.');
    
            // Cierra la sesión del usuario
            Auth::logout();
    
            // Redirige al usuario a la página de inicio de sesión con un mensaje de éxito
            return redirect('/login')->with('success', 'La restauración se completó correctamente. Por favor, vuelve a iniciar sesión.');
    
        } catch (\Exception $e) {
            // Loguea cualquier error en el proceso de restauración
            Log::error('Error al restaurar la base de datos: ' . $e->getMessage());
    
            // Redirige de regreso con el mensaje de error
            return back()->with('status', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }
}
