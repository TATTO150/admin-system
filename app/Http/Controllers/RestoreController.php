<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDO;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;


class RestoreController extends Controller
{
  

    public function index()
    {
        return view('restore.index'); // Renderiza la vista del formulario de restauración
    }

    public function restoreFromBackup()
    {
        // Define la ruta completa del archivo SQL en `storage/app/backups`
        $filePath = storage_path('app/backups/admsystemct.sql');

        // Verifica si el archivo existe
        if (!file_exists($filePath)) {
            return back()->with('status', 'El archivo de respaldo no se encontró.');
        }

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
    
    
    
    
  

