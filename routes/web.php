<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\BlockedController;
use App\Http\Controllers\BitaControlador;
use App\Http\Controllers\ValidarOtpReseteoController;
use Illuminate\Auth\Events\Login;
use  App\Http\Controllers\BitacoraController;
use App\Http\Controllers\histcontraseñacontrolador;
use App\Http\Controllers\FechaController;
use App\Http\Controllers\ProyectoControlador;
use App\Models\histcontrasena;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\EquipoControlador;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use App\Http\Controllers\AsignadorEquipoController;
use App\Http\Controllers\TipoEquipoControlador;
use App\Http\Controllers\EstadoAsignacionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\EstadoUsuarioController;
use App\Http\Controllers\TipoAsignacionController;

Route::get('/', function () {
    return view('welcome');
 });
 

 Route::get('/bloqueo', [BlockedController::class, 'show'])->name('bloqueo');

/*Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});*/


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/two-factor-challenge', [ConfirmedTwoFactorAuthenticationController::class, 'store'])->name('two-factor.confirm');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
        })->middleware(['auth', 'signed'])->name('verification.verify');

   
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

        Route::get('/profile', function () {
            // Only verified users may access this route...
            })->middleware(['auth', 'verified']);

             //Redirige a la vista de bloqueo
        Route::get('/bloqueo', function () {
            return view('auth.bloqueo');
        })->name('bloqueo');

        //Redirige a la vista de autenticacion en dos pasos
    Route::get('/two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('otp');

     // Ruta para mostrar la vista del código OTP
     Route::get('/otp', function () {
        return view('auth.otp');
    })->name('otp.show');

    // Ruta para manejar la verificación del OTP y redirigir a la vista de restablecimiento de la contraseña

    Route::post('/otp/verify', [ValidarOtpReseteoController::class, 'verify'])->name('otp.verify');
    
   
    Route::get('/two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('two-factor.login');


    /*rutas bitacora*/ 
    Route::get('/bitacora/pdf', [BitacoraController::class, 'pdf'])->name('bitacora.pdf');
    Route::resource('bitacora',  BitacoraController::class);
    Route::post('/bitacora/creacion-usuario', [BitacoraController::class, 'registrarCreacionUsuario']);
    Route::post('/bitacora/eliminacion-usuario', [BitacoraController::class, 'registrarEliminacionUsuario']);
    Route::post('/bitacora/actualizacion-usuario', [BitacoraController::class, 'registrarActualizacionUsuario']);
    Route::post('/bitacora/ingreso-sistema', [BitacoraController::class, 'registrarIngresoSistema']);
    Route::post('/bitacora/error-validacion-contrasena', [BitacoraController::class, 'registrarErrorValidacionContrasena']);
    Route::post('/bitacora/bloqueo-cuenta', [BitacoraController::class, 'registrarBloqueoCuenta']);
    Route::post('/bitacora/entrada-pantalla-principal', [BitacoraController::class, 'registrarEntradaPantallaPrincipal']);
    Route::post('/bitacora/uso-safeauth', [BitacoraController::class, 'registrarUsoSafeAuth']);
    Route::post('/bitacora/fallo-safeauth', [BitacoraController::class, 'registrarFalloSafeAuth']);

    /*rutas hist <contrasena*/
    Route::post('/users', [histcontraseñacontrolador::class, 'contrasena']);
  /*fecha*/
  Route::get('/fecha-tres-meses', [FechaController::class, 'fechaHastaTresMeses']);
 

  
  // Listar todos los equipos
  Route::get('/equipos', [EquipoControlador::class, 'index'])->name('equipos.index');
  // Generar PDF de equipos
  Route::get('/equipos/pdf', [EquipoControlador::class, 'pdf'])->name('equipos.pdf');
  // Mostrar formulario para crear un nuevo equipo
  Route::get('/equipos/crear', [EquipoControlador::class, 'crear'])->name('equipos.crear');
  // Insertar un nuevo equipo
  Route::post('/equipos', [EquipoControlador::class, 'insertar'])->name('equipos.insertar');
  // Mostrar formulario para editar un equipo existente
  Route::get('/equipos/{COD_EQUIPO}/editar', [EquipoControlador::class, 'edit'])->name('equipos.edit');
  // Actualizar un equipo existente
  Route::put('/equipos/{COD_EQUIPO}', [EquipoControlador::class, 'update'])->name('equipos.update');
// Generar PDF de equipos (general y por estado)
// Rutas para reportes de equipos
// Rutas para reportes
Route::post('equipos/reporte/estado', [EquipoControlador::class, 'generarReporteEstado'])->name('equipos.reporte.estado');
Route::post('equipos/reporte/fecha', [EquipoControlador::class, 'generarReporteFecha'])->name('equipos.reporte.fecha');
Route::post('equipos/reporte/general', [EquipoControlador::class, 'generarReporteGeneral'])->name('equipos.reporte.general');


// Rutas para el módulo de asignaciones de equipo
Route::get('/asignaciones/crear', [AsignadorEquipoController::class, 'create'])->name('asignaciones.crear');
Route::post('/asignaciones', [AsignadorEquipoController::class, 'store'])->name('asignaciones.store');
Route::get('/asignaciones/{id}', [AsignadorEquipoController::class, 'show'])->name('asignaciones.show');
Route::get('/asignaciones/{id}/editar', [AsignadorEquipoController::class, 'edit'])->name('asignaciones.edit');
Route::put('/asignaciones/{id}', [AsignadorEquipoController::class, 'update'])->name('asignaciones.update');
Route::delete('/asignaciones/{id}', [AsignadorEquipoController::class, 'destroy'])->name('asignaciones.destroy');
Route::get('/asignaciones', [AsignadorEquipoController::class, 'index'])->name('asignaciones.index');
Route::get('/mantenimiento/crear', [AsignadorEquipoController::class, 'crearMantenimiento'])->name('mantenimiento.crear');
Route::post('/mantenimiento/store', [AsignadorEquipoController::class, 'storeMantenimiento'])->name('mantenimiento.store');
// routes/web.php
Route::put('asignaciones/finalizar/{id}', [AsignadorEquipoController::class, 'finalizar'])->name('asignaciones.finalizar');
Route::put('mantenimientos/finalizar/{id}', [AsignadorEquipoController::class, 'finalizar'])->name('mantenimientos.finalizar');
// routes/web.php
Route::put('asignaciones/finalizar/{id}', [AsignadorEquipoController::class, 'finalizar'])->name('asignaciones.finalizar');
Route::put('mantenimientos/finalizar/{id}', [AsignadorEquipoController::class, 'finalizar'])->name('mantenimientos.finalizar');
Route::put('asignaciones/inactivar/{id}', [AsignadorEquipoController::class, 'inactivar'])->name('asignaciones.inactivar');

Route::get('/asignaciones/prueba', [AsignadorEquipoController::class, 'indexDePrueba'])->name('asignaciones.prueba');



Route::post('/asignaciones', [AsignadorEquipoController::class, 'store'])->name('asignaciones.store');

Route::post('/asignaciones/general', [AsignadorEquipoController::class, 'generalPost'])->name('asignaciones.general');
Route::post('/asignaciones/reporte-tipo', [AsignadorEquipoController::class, 'generarReportePorTipo'])->name('asignaciones.tipo_asignacion');
Route::post('/asignaciones/reporte-estado', [AsignadorEquipoController::class, 'generarReportePorEstado'])->name('asignaciones.estado_asignacion');
Route::post('/asignaciones/reporte-proyecto', [AsignadorEquipoController::class, 'generarReportePorProyecto'])->name('asignaciones.proyecto');
Route::post('/asignaciones/reporte-empleado', [AsignadorEquipoController::class, 'generarReportePorEmpleado'])->name('asignaciones.empleado');
Route::put('/asignaciones/eliminar/{id}', [AsignadorEquipoController::class, 'eliminarAsignacion'])->name('asignaciones.eliminar');
Route::get('asignaciones/gestionar/{id}', [AsignadorEquipoController::class, 'gestionar'])->name('asignaciones.gestionar');
Route::put('asignaciones/gestionar/{id}', [AsignadorEquipoController::class, 'gestionar'])->name('asignaciones.gestionar');
Route::put('/asignaciones/actualizarEstado/{id}', [AsignadorEquipoController::class, 'actualizarEstado'])->name('asignaciones.actualizarEstado');




Route::resource('tiposasignacion', TipoAsignacionController::class);
// Ruta para mostrar todos los tipos de asignación (index)
Route::get('tiposasignacion', [TipoAsignacionController::class, 'index'])->name('tiposasignacion.index');
// Ruta para mostrar el formulario de creación de un nuevo tipo de asignación (create)
Route::get('tiposasignacion/create', [TipoAsignacionController::class, 'create'])->name('tiposasignacion.create');
// Ruta para guardar un nuevo tipo de asignación (store)
Route::post('tiposasignacion', [TipoAsignacionController::class, 'store'])->name('tiposasignacion.store');
// Ruta para mostrar el formulario de edición de un tipo de asignación existente (edit)
Route::get('tiposasignacion/{id}/edit', [TipoAsignacionController::class, 'edit'])->name('tiposasignacion.edit');
// Ruta para actualizar un tipo de asignación existente (update)
Route::put('tiposasignacion/{id}', [TipoAsignacionController::class, 'update'])->name('tiposasignacion.update');
Route::get('/tipo-asignacion/report', [TipoAsignacionController::class, 'generateReport'])->name('tipo_asignacion.report');






Route::get('/tipo-equipo/report', [TipoEquipoControlador::class, 'generateReport'])->name('tipo_equipo.report');
Route::resource('tipo_equipo', TipoEquipoControlador::class);
// Mostrar lista de tipos de equipo
Route::get('tipoequipo', [TipoEquipoControlador::class, 'index'])->name('tipoequipo.index');

// Mostrar formulario para crear un nuevo tipo de equipo
Route::get('tipoequipo/create', [TipoEquipoControlador::class, 'create'])->name('tipoequipo.create');

// Guardar un nuevo tipo de equipo
Route::post('tipoequipo', [TipoEquipoControlador::class, 'store'])->name('tipoequipo.store');

// Mostrar formulario para editar un tipo de equipo existente
Route::get('tipoequipo/{id}/edit', [TipoEquipoControlador::class, 'edit'])->name('tipoequipo.edit');

// Actualizar un tipo de equipo existente
Route::put('tipoequipo/{id}', [TipoEquipoControlador::class, 'update'])->name('tipoequipo.update');

// Eliminar un tipo de equipo existente
Route::delete('tipoequipo/{id}', [TipoEquipoControlador::class, 'destroy'])->name('tipoequipo.destroy');
Route::get('tipo_equipo/check-deletion/{id}', [TipoEquipoControlador::class, 'checkDeletion']);

            
// Ruta para mostrar el listado de registros (index)
Route::get('/estado_asignacion', [EstadoAsignacionController::class, 'index'])->name('estado_asignacion.index');

// Ruta para mostrar el formulario de creación de un nuevo registro (create)
Route::get('/estado_asignacion/create', [EstadoAsignacionController::class, 'create'])->name('estado_asignacion.create');

// Ruta para guardar un nuevo registro en la base de datos (store)
Route::post('/estado_asignacion', [EstadoAsignacionController::class, 'store'])->name('estado_asignacion.store');

// Ruta para mostrar el formulario de edición de un registro existente (edit)
Route::get('/estado_asignacion/{id}/edit', [EstadoAsignacionController::class, 'edit'])->name('estado_asignacion.edit');

// Ruta para actualizar un registro existente en la base de datos (update)
Route::put('/estado_asignacion/{id}', [EstadoAsignacionController::class, 'update'])->name('estado_asignacion.update');

// Ruta para eliminar un registro existente (destroy)
Route::delete('/estado_asignacion/{id}', [EstadoAsignacionController::class, 'destroy'])->name('estado_asignacion.destroy');
Route::get('/estado-asignacion/report', [EstadoAsignacionController::class, 'generateReport'])->name('estado_asignacion.report');

// Ruta para mostrar la lista de estados de usuario (index)
Route::get('/estado_usuarios', [EstadoUsuarioController::class, 'index'])->name('estado_usuarios.index');

// Ruta para mostrar el formulario de creación de un nuevo estado de usuario (create)
Route::get('/estado_usuarios/create', [EstadoUsuarioController::class, 'create'])->name('estado_usuarios.create');

// Ruta para almacenar un nuevo estado de usuario (store)
Route::post('/estado_usuarios', [EstadoUsuarioController::class, 'store'])->name('estado_usuarios.store');

// Ruta para mostrar el formulario de edición de un estado de usuario existente (edit)
Route::get('/estado_usuarios/{id}/edit', [EstadoUsuarioController::class, 'edit'])->name('estado_usuarios.edit');

// Ruta para actualizar un estado de usuario existente (update)
Route::put('/estado_usuarios/{id}', [EstadoUsuarioController::class, 'update'])->name('estado_usuarios.update');

// Ruta para eliminar un estado de usuario (destroy)
Route::delete('/estado_usuarios/{id}', [EstadoUsuarioController::class, 'destroy'])->name('estado_usuarios.destroy');
Route::get('/estado_usuarios/reporte', [EstadoUsuarioController::class, 'reporte'])->name('estado_usuarios.reporte');



Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
Route::delete('/backups/delete/{filename}', [BackupController::class, 'delete'])->name('backups.delete');


Route::get('/usuarios/depurar', function () {
    // Aquí puedes pasar cualquier dato adicional si es necesario
    return view('usuarios.depurar');
});





