<?php
use App\Http\Controllers\ValidarOtpReseteoController;
use Illuminate\Auth\Events\Login;
use  App\Http\Controllers\BitacoraController;
use App\Http\Controllers\histcontraseñacontrolador;
use App\Http\Controllers\FechaController;
use App\Models\histcontrasena;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AsignadorEquipoController;
use App\Http\Controllers\TipoEquipoControlador;
use App\Http\Controllers\EstadoAsignacionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\EstadoUsuarioController;
use App\Http\Controllers\TipoAsignacionController;
use App\Http\Controllers\GestionMantenimientoControlador;
use App\Http\Controllers\AreaControlador;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ControllersLogin\ConfirmedTwoFactorAuthenticationController;
use Illuminate\Support\MessageBag;
use Laravel\Fortify\Features;
use App\Http\Controllers\ControllersLogin\AutenticarSesionController;
use App\Http\Controllers\ControllersLogin\ConfirmablePasswordController;
use App\Http\Controllers\ControllersLogin\ConfirmedPasswordStatusController;
use App\Http\Controllers\ControllersLogin\EmailVerificationNotificationController;
use App\Http\Controllers\ControllersLogin\EmailVerificationPromptController;
use App\Http\Controllers\ControllersLogin\NewPasswordController;
use App\Http\Controllers\ControllersLogin\PasswordController;
use App\Http\Controllers\ControllersLogin\PasswordResetLinkController;
use App\Http\Controllers\ControllersLogin\ProfileInformationController;
use App\Http\Controllers\ControllersLogin\RecoveryCodeController;
use App\Http\Controllers\ControllersLogin\RegistrarUsuarioController;
use App\Http\Controllers\ControllersLogin\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\ControllersLogin\TwoFactorAuthenticationController;
use App\Http\Controllers\ControllersLogin\TwoFactorQrCodeController;
use App\Http\Controllers\ControllersLogin\TwoFactorSecretKeyController;
use App\Http\Controllers\ControllersLogin\VerifyEmailController;
use Laravel\Fortify\RoutePath;
use App\Http\Controllers\Auth\BlockedController;
use App\Http\Controllers\EstadoEmpleadoControlador;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BitaControlador;
use App\Http\Controllers\CargosControlador;
use App\Http\Controllers\ComprasControlador;
use App\Http\Controllers\ConfrimacionContrasenaController;
use App\Http\Controllers\EmpleadosControlador;
use App\Http\Controllers\EmpleadoProyectoControlador;
use App\Http\Controllers\EquipoControlador;
use App\Http\Controllers\GastosControlador;
use App\Http\Controllers\MantenimientoControlador;
use App\Http\Controllers\PlanillaControlador;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ResetearContrasenaController;
use App\Http\Controllers\ProyectoControlador;
use App\Http\Controllers\RolControlador;
use App\Http\Controllers\SolicitudesControlador;
use App\Models\proyectos;
use App\Models\EmpleadoProyectos;
use Laravel\Jetstream\Rules\Role;
use App\Http\Controllers\AsignacionEquipoController;
use App\Http\Controllers\EmpleadosPlanillascontrolador;
use App\Http\Controllers\MantenimientosControlador;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\PermisosControlador;
use App\Http\Controllers\GestionSolicitudesControlador;
use App\Http\Controllers\PagoPlanillasControlador;
use App\Models\Asignacion_Equipo;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EstadoProyectoControllador;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\DeduccionControlador;




Route::get('/', function () {
    return view('welcome');
 });
 
 Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {
    $enableViews = config('fortify.views', true);

   // Authentication...
   if ($enableViews) {
    Route::get(RoutePath::for('login', '/login'), [AutenticarSesionController::class, 'create'])
        ->middleware(['guest:'.config('fortify.guard')])
        ->name('login');
}
Route::get('/usuarios/{Id_usuario}/mostrar', [RegistrarUsuarioController::class, 'show'])->name('usuarios.show');

Route::get('/autoregistro-notificacion', function () {
    return view('auth.autoregistro-notificacion');
})->name('autoregistro-notificacion');

Route::get('/confirmación-restablecimiento-contraseña', function () {
    return view('auth.password-reset-confirmation');
})->name('password.reset.confirmation');

Route::get('/bloqueo', [BlockedController::class, 'show'])->name('bloqueo');

//Redirige a la vista de bloqueo
Route::get('/bloqueo', function () {
    return view('auth.bloqueo');
})->name('bloqueo');

Route::get('/unica-sesion', function () {
    return view('auth.unica-sesion');
})->name('unica.sesion');

$twoFactorLimiter = config('fortify.limiters.two-factor');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');


    $limiter = config('fortify.limiters.login');
    
    Route::post(RoutePath::for('login', '/login'), [AutenticarSesionController::class, 'store'])
    ->middleware(array_filter([
        'guest:'.config('fortify.guard'),
        $limiter ? 'throttle:'.$limiter : null,
    ]));



Route::post(RoutePath::for('logout', '/logout'), [AutenticarSesionController::class, 'destroy'])
    ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
    ->name('logout');

    // Password Reset...
        // Ruta para mostrar la vista del código OTP
        Route::get('/otp', function () {
            return view('auth.otp');
        })->name('otp.show');
    
        // Ruta para manejar la verificación del OTP y redirigir a la vista de restablecimiento de la contraseña

        Route::post('/otp', [ResetearContrasenaController::class, 'verify'])->name('otp.verify');

        Route::get('password/reset', [ResetearContrasenaController::class, 'sendResetLink'])->name('password.send_link');
        Route::post('password/send_link', [ResetearContrasenaController::class, 'sendResetLink'])->name('password.send_link');
        Route::get('password/reset/{token}', [ResetearContrasenaController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/updatepassword', [ResetearContrasenaController::class, 'resetPassword'])->name('contra.update');
        Route::get('/solicitar-nueva-contrasena', [ResetearContrasenaController::class, 'showEmailForm'])->name('solicitar-nueva-contrasena');

// Ruta para manejar el restablecimiento de la contraseña
Route::post('/password/update', [ResetearContrasenaController::class, 'reset'])->name('password.update');

    // Registration...
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get(RoutePath::for('register', '/register'), [RegistrarUsuarioController::class, 'create'])
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('register');
        }

        Route::get('/two-factor-authenticator-form', function () {
            return view('profile.show');
        })->name('two-factor.authenticator');
       

Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('password/confirm', [ConfrimacionContrasenaController::class, 'show'])->name('password.confirm');
        Route::post('password/confirm', [ConfrimacionContrasenaController::class, 'store']);

        Route::post(RoutePath::for('register', '/register'), [RegistrarUsuarioController::class, 'store'])
            ->middleware(['guest:'.config('fortify.guard')]);
    }


    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put(RoutePath::for('user-profile-information.update', '/user/profile-information'), [ProfileInformationController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put(RoutePath::for('user-password.update', '/user/password'), [PasswordController::class, 'update'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
            ->name('user-password.update');
    }

    // Password Confirmation...
    if ($enableViews) {
        Route::get(RoutePath::for('password.confirm', '/user/confirm-password'), [ConfirmablePasswordController::class, 'show'])
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')]);
    }

    Route::get(RoutePath::for('password.confirmation', '/user/confirmed-password-status'), [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirmation');

    Route::post(RoutePath::for('password.confirm', '/user/confirm-password'), [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirm');

    // Two Factor Authentication...

    //Redirige a la vista de autenticacion en dos pasos
    Route::get('/two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('otp');

    Route::get('/two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('two-factor.login');

    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get(RoutePath::for('two-factor.login', '/two-factor-challenge'), [TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('two-factor.login');
        }

        Route::post(RoutePath::for('two-factor.login', '/two-factor-challenge'), [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:'.config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]));

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm']
            : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];

        Route::post(RoutePath::for('two-factor.enable', '/user/two-factor-authentication'), [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

            Route::post('/two-factor-challenge', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->middleware(['auth', 'throttle:6,1'])
            ->name('two-factor.confirm');

        Route::delete(RoutePath::for('two-factor.disable', '/user/two-factor-authentication'), [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get(RoutePath::for('two-factor.qr-code', '/user/two-factor-qr-code'), [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get(RoutePath::for('two-factor.secret-key', '/user/two-factor-secret-key'), [TwoFactorSecretKeyController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get(RoutePath::for('two-factor.recovery-codes', '/user/two-factor-recovery-codes'), [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post(RoutePath::for('two-factor.recovery-codes', '/user/two-factor-recovery-codes'), [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
            Route::get('login', [RecoveryCodeController::class, 'showlogin'])->name('login');
    }

});

 Route::get('/bloqueo', [BlockedController::class, 'show'])->name('bloqueo');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/two-factor-challenge', [ConfirmedTwoFactorAuthenticationController::class, 'store'])->name('two-factor.confirm');
}, 'verified');

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

/*restaurar*/
Route::get('/restore', [RestoreController::class, 'index'])->name('restore.index');

Route::post('/restore-from-backup', [RestoreController::class, 'restoreFromBackup'])->middleware('auth');

Route::get('/usuarios/depurar', function () {
    // Aquí puedes pasar cualquier dato adicional si es necesario
    return view('usuarios.depurar');
});


//Rutas que trabajan el protocolo middleware web
Route::middleware(['web'])->group(function () {
    Route::get('/usuarios/pdf', [RegistrarUsuarioController::class, 'pdf'])->name('usuarios.pdf');
    Route::get('/usuarios', [RegistrarUsuarioController::class, 'index'])->name('usuarios.index');
    
    // Ruta para mostrar el formulario de crear usuario
    Route::get('/usuarios/crear', [RegistrarUsuarioController::class, 'crear'])->name('usuarios.crear');
    
    // Ruta para insertar un nuevo usuario
    Route::post('/usuarios', [RegistrarUsuarioController::class, 'insertar'])->name('usuarios.insertar');
    
    // Ruta para eliminar un usuario
    Route::delete('/usuarios/{Id_usuario}', [RegistrarUsuarioController::class, 'destroy'])->name('usuarios.destroy');
    
    // Ruta para mostrar el formulario de editar usuario
    Route::get('/usuarios/{Id_usuario}/edit', [RegistrarUsuarioController::class, 'edit'])->name('usuarios.edit');
    
    // Ruta para actualizar un usuario existente
    Route::put('/usuarios/{Id_usuario}', [RegistrarUsuarioController::class, 'update'])->name('usuarios.update');


   /////////////////////////////////////////////*ESTADO PROYECTOS*////////////////////////////////////////
  //Route::resource('estado_proyecto', EstadoProyectoControllador::class);
  Route::get('/estado_proyecto/reporte', [EstadoProyectoControllador::class, 'generatePdf'])->name('estado_proyecto.pdf');  //RUTA DE PDF
  // Mostrar la lista de estados de proyecto
Route::get('estado_proyecto', [EstadoProyectoControllador::class, 'index'])->name('estado_proyecto.index');
// Mostrar el formulario para crear un nuevo estado de proyecto
Route::get('estado_proyecto/create', [EstadoProyectoControllador::class, 'create'])->name('estado_proyecto.create');
// Guardar un nuevo estado de proyecto
Route::post('estado_proyecto', [EstadoProyectoControllador::class, 'store'])->name('estado_proyecto.store');
// Mostrar el formulario para editar un estado de proyecto existente
Route::get('estado_proyecto/{estadoProyecto}/edit', [EstadoProyectoControllador::class, 'edit'])->name('estado_proyecto.edit');
// Actualizar un estado de proyecto existente
Route::post('estado_proyecto/{estadoProyecto}', [EstadoProyectoControllador::class, 'update'])->name('estado_proyecto.update');
// Eliminar un estado de proyecto
Route::post('estado_proyecto/{estadoProyecto}/delete', [EstadoProyectoControllador::class, 'destroy'])->name('estado_proyecto.destroy');
// Eliminar un estado de proyecto
Route::delete('estado_proyecto/{estadoProyecto}', [EstadoProyectoControllador::class, 'destroy'])->name('estado_proyecto.destroy');
Route::get('estado_proyecto/{id}/check', [EstadoProyectoControllador::class, 'checkEstado'])->name('estado_proyecto.check');




    /////////////////////////////////////////////*ROLES*////////////////////////////////////////
    Route::get('/roles/pdf', [RolControlador::class, 'pdf'])->name('roles.pdf');
    Route::get('/roles', [RolControlador::class, 'index'])->name('roles.index');
    Route::get('/roles/crear', [RolControlador::class, 'crear'])->name('roles.crear');
    Route::post('/roles', [RolControlador::class, 'insertar'])->name('roles.insertar');
    Route::delete('/roles/{Id_Rol}', [RolControlador::class, 'destroy'])->name('roles.destroy');
    Route::get('/roles/{Id_Rol}', [RolControlador::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{Id_Rol}', [RolControlador::class, 'update'])->name('roles.update');

/////////////////////////////////////////////*PROYECTOS*///////////////////////////////////////////
    Route::get('/proyectos/pdf', [ProyectoControlador::class, 'pdf'])->name('proyectos.pdf'); //PDF REPORTE GENERAL
    Route::get('/proyectos/pdf/estado', [ProyectoControlador::class, 'pdfEstado'])->name('proyectos.pdf.estado');//PDF REPORTE ESTADO
    Route::get('/proyectos/pdf/fecha', [ProyectoControlador::class, 'pdfFecha'])->name('proyectos.pdf.fecha'); //PDF REPORTE FECHA
    Route::get('/proyectos/pdfGeneral/{proyectoId}', [ProyectoControlador::class, 'reporteGeneral'])->name('reporte.proyecto.general');

    Route::get('/proyectos/pdf/{id}', [ProyectoControlador::class, 'pdfproyecto'])->name('proyecto.pdf');  //ESTA FUNCIONA BIEN, ESTA ES LA RUTA DEL REPORTE ESCALICHE
    Route::post('/proyectos/activar/{id}', [ProyectoControlador::class, 'activar'])->name('proyectos.activar');//PARA LOS ESTADOS DE SUSPENDIDO A ACTIVO
    Route::post('/proyectos/restaurar/{COD_PROYECTO}', [ProyectoControlador::class, 'restaurar'])->name('proyectos.restaurar'); //RUTA DE ACTIVAR LOS PROYECTOS INACTIVOS


   // Route::post('/proyectos/{proyecto}/gestionar-empleados', [ProyectoControlador::class, 'gestionarEmpleados'])->name('proyectos.gestionarEmpleados');//DESIGNAR 

   
  //Route::get('/proyectos/empleado_proyectos', [ProyectoControlador::class, 'empleados'])->name('proyectos.empleado_proyectos');//RUTA DE TONI PARA EMPLEADO
   Route::get('/proyectos', [ProyectoControlador::class, 'index'])->name('proyectos.index');
    Route::get('/proyectos/crear', [ProyectoControlador::class, 'crear'])->name('proyectos.crear');
    Route::post('/proyectos', [ProyectoControlador::class, 'insertar'])->name('proyectos.insertar');
    Route::delete('/proyectos/{COD_PROYECTO}', [ProyectoControlador::class, 'destroy'])->name('proyectos.destroy');
    Route::get('/proyectos/{COD_PROYECTO}', [ProyectoControlador::class, 'edit'])->name('proyectos.edit');
    Route::put('/proyectos/{COD_PROYECTO}', [ProyectoControlador::class, 'update'])->name('proyectos.update');
    Route::post('/proyectos/{COD_PROYECTO}/finalizar', [ProyectoControlador::class, 'finalizar'])->name('proyectos.finalizar');
    Route::post('/proyectos/asignarEmpleado', [ProyectoControlador::class, 'asignarEmpleado'])->name('proyectos.asignarEmpleado');
    Route::get('/proyectos/{codProyecto}/empleados_por_proyecto', [EmpleadoProyectoControlador::class, 'index'])->name('proyectos.empleados');
    Route::post('/proyectos/{proyecto}/gestionar-empleados', [ProyectoControlador::class, 'gestionarEmpleados'])->name('proyectos.gestionarEmpleados');

    ////////////////////////////////////////////*SOLICITUDES*///////////////////////////////////////////////////
    Route::get('/solicitudes/generateReport', [SolicitudesControlador::class, 'generateReport'])->name('solicitudes.generateReport');
    Route::get('/solicitudes', [SolicitudesControlador::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/crear', [SolicitudesControlador::class, 'crear'])->name('solicitudes.crear');
    Route::post('/solicitudes', [SolicitudesControlador::class, 'insertar'])->name('solicitudes.insertar');
    Route::delete('/solicitudes/{COD_COMPRA}/destroy', [SolicitudesControlador::class, 'destroy'])->name('solicitudes.destroy');
    Route::get('solicitudes/{COD_COMPRA}/edit', [SolicitudesControlador::class, 'edit'])->name('solicitudes.edit');
    Route::put('/solicitudes/{COD_COMPRA}', [SolicitudesControlador::class, 'update'])->name('solicitudes.update');
    Route::get('/gestionSolicitudes', [GestionSolicitudesControlador::class, 'index'])->name('gestionSolicitudes.index');
    Route::get('/gestionSolicitudes/gestionar/{COD_COMPRA}', [GestionSolicitudesControlador::class, 'gestionar'])->name('gestionSolicitudes.gestionar');
    Route::patch('/gestionSolicitudes/aprobar/{COD_COMPRA}', [GestionSolicitudesControlador::class, 'aprobar'])->name('gestionSolicitudes.aprobar');
    Route::patch('/gestionSolicitudes/rechazar/{COD_COMPRA}', [GestionSolicitudesControlador::class, 'rechazar'])->name('gestionSolicitudes.rechazar');
   
    //////////////////////////////////////////////////*PLANILLAS*///////////////////////////////////////////////////
    Route::get('/planillas/pdf', [PlanillaControlador::class, 'pdf'])->name('planillas.pdf');
    Route::get('/planillas', [PlanillaControlador::class, 'index'])->name('planillas.index');
    Route::get('/planillas/crear', [PlanillaControlador::class, 'crear'])->name('planillas.crear');
    Route::post('/planillas', [PlanillaControlador::class, 'insertar'])->name('planillas.insertar');
    Route::delete('/planillas/{COD_PLANILLA}', [PlanillaControlador::class, 'destroy'])->name('planillas.destroy');
    Route::post('/empleados/eliminar', [PlanillaControlador::class, 'eliminarEmpleados'])->name('empleados.eliminar');
    Route::get('/planillas/{COD_PLANILLA}/edit', [PlanillaControlador::class, 'edit'])->name('planillas.edit');
    Route::get('/planillas/{COD_PLANILLA}/confirmarPlanilla', [PlanillaControlador::class, 'confirmarPlanilla'])->name('planillas.confirmar');
    Route::get('/planillas/{COD_PLANILLA}/cancelarPlanilla', [PlanillaControlador::class, 'cancelarPlanilla'])->name('planillas.cancelar');
    Route::put('/planillas/{COD_PLANILLA}', [PlanillaControlador::class, 'update'])->name('planillas.update');
    Route::post('/planillas/generar', [PlanillaControlador::class, 'generarPlanilla'])->name('planillas.generar');
    Route::get('/planillas/{COD_PLANILLA}', [PlanillaControlador::class, 'show'])->name('planillas.show');
    Route::get('planillas/{id}/pdf', [PlanillaControlador::class, 'pdfIndividual'])->name('reporte.generar');
    Route::post('/planillas/generar_reporte', [PlanillaControlador::class, 'generarReporte'])->name('planillas.generar_reporte');
    Route::get('/planillas/pdf', [PlanillaControlador::class, 'generarReporteGeneral'])->name('planillas.generar_reporte_general');

    //////////////////////////////////////////////////////////*RUTAS EMPLEADO*////////////////////////////////////////    
    Route::get('/empleados/pdf', [EmpleadosControlador::class, 'pdf'])->name('empleados.pdf');
    Route::get('/empleados', [EmpleadosControlador::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadosControlador::class, 'crear'])->name('empleados.crear');
    Route::get('/empleados/crear1/{COD_PROYECTO}', [EmpleadosControlador::class, 'generarPlanillaVista'])->name('empleados.generarPlanillaVista');
    //Route::get('/empleados/crear1/{COD_PROYECTO}', [EmpleadosControlador::class, 'generarPlaniyaVista'])->name('empleados.generarPlaniyaVista');
    Route::post('/empleados/generar-planilla/{COD_PROYECTO}', [EmpleadosControlador::class, 'generarPlanilla'])->name('empleados.generarPlanilla');
    Route::post('/empleados', [EmpleadosControlador::class, 'insertar'])->name('empleados.insertar');
    Route::delete('/empleados/{COD_EMPLEADO}', [EmpleadosControlador::class, 'destroy'])->name('empleados.destroy');
    //Route::delete('/empleados/{COD_EMPLEADO}', [EmpleadosControlador::class, 'destruir'])->name('empleados.destruir');
    Route::get('/empleados/{COD_EMPLEADO}', [EmpleadosControlador::class, 'edit'])->name('empleados.edit');
    //Route::get('/empleados/{COD_EMPLEADO}', [EmpleadosControlador::class, 'editar'])->name('empleados.editar');
    Route::put('/empleados/{COD_EMPLEADO}', [EmpleadosControlador::class, 'update'])->name('empleados.update');
    Route::get('/empleados/create', [EmpleadosControlador::class, 'create'])->name('asignacion.create');
    Route::post('/empleados/guardar', [EmpleadosControlador::class, 'guardar'])->name('asignacion.guardar');
    Route::get('empleados/{dniEmpleado}/proyectos_por_empleado', [EmpleadoProyectoControlador::class, 'proyectosPorEmpleado'])->name('empleados.proyectos');
    Route::post('/empleados/desactivar/{id}', [EmpleadosControlador::class, 'desactivar'])->name('empleados.desactivar');
    Route::post('/empleados/restaurar/{id}', [EmpleadosControlador::class, 'restaurar'])->name('empleados.restaurar');
    //////////////////////////////////////////////////////////*GASTOS*///////////////////////////////////////////////////////////////
    Route::get('/gastos', [GastosControlador::class, 'index'])->name('gastos.index');
    Route::get('/gastos/pdf', [GastosControlador::class, 'pdf'])->name('gastos.pdf');
    Route::get('/gastos/reporte/proyecto', [GastosControlador::class, 'reportePorProyecto'])->name('gastos.reporte.proyecto');
    Route::get('/gastos/reporte/proyecto/pdf', [GastosControlador::class, 'descargarReportePorProyecto'])->name('gastos.descargar.reporte.proyecto');
    Route::get('/gastos/reporte/hoy', [GastosControlador::class, 'reportePorHoy'])->name('gastos.reporte.hoy');
    Route::get('/gastos/reporte/hoy/pdf', [GastosControlador::class, 'descargarReportePorHoy'])->name('gastos.descargar.reporte.hoy');
    Route::get('/gastos/reporte/ano', [GastosControlador::class, 'descargarReportePorAno'])->name('gastos.reporte.ano');
    Route::get('/gastos/reporte/ano/pdf', [GastosControlador::class, 'descargarReportePorAno'])->name('gastos.descargar.reporte.ano');
    Route::get('/gastos/reporte/mes', [GastosControlador::class, 'descargarReportePorMes'])->name('gastos.reporte.mes');
    Route::get('/gastos/descargar/reporte/mes', [GastosControlador::class, 'descargarReportePorMes'])->name('gastos.descargar.reporte.mes');
    Route::get('/gastos/reporte/fecha', [GastosControlador::class, 'reportePorFecha'])->name('gastos.reporte.fecha');
    Route::get('/gastos/reporte/fecha/pdf', [GastosControlador::class, 'descargarReportePorFecha'])->name('gastos.descargar.reporte.fecha');


    //////////////////////////////////////////////////////////*RUTAS ESTADO EMPLEADO*////////////////////////////////////////    
    Route::get('/estado_empleados/pdf', [EstadoEmpleadoControlador::class, 'pdf'])->name('estado_empleados.pdf');
    Route::get('/estado_empleados', [EstadoEmpleadoControlador::class, 'index'])->name('estado_empleados.index');
    Route::get('/estado_empleados/create', [EstadoEmpleadoControlador::class, 'crear'])->name('estado_empleados.create');
    Route::post('/estado_empleados', [EstadoEmpleadoControlador::class, 'insertar'])->name('estado_empleados.insertar');
    Route::delete('/estado_empleados/{COD_ESTADO_EMPLEADO}', [EstadoEmpleadoControlador::class, 'destroy'])->name('estado_empleados.destroy');
    Route::get('/estado_empleados/{COD_ESTADO_EMPLEADO}', [EstadoEmpleadoControlador::class, 'edit'])->name('estado_empleados.edit');
    Route::put('/estado_empleados/{COD_ESTADO_EMPLEADO}', [EstadoEmpleadoControlador::class, 'update'])->name('estado_empleados.update');

    //////////////////////////////////////////////////////////////*EQUIPOS*////////////////////////////////////////////////////
    Route::get('/equipos/pdf', [EquipoControlador::class, 'pdf'])->name('equipos.pdf');
    Route::get('/equipos', [EquipoControlador::class, 'index'])->name('equipos.index');
    Route::get('/equipos/crear', [EquipoControlador::class, 'crear'])->name('equipos.crear');
    Route::post('/equipos', [EquipoControlador::class, 'insertar'])->name('equipos.insertar');
    Route::delete('/equipos/{COD_EQUIPO}', [EquipoControlador::class, 'destroy'])->name('equipos.destroy');
    Route::get('/equipos/{COD_EQUIPO}', [EquipoControlador::class, 'edit'])->name('equipos.edit');
    Route::put('/equipos/{COD_EQUIPO}', [EquipoControlador::class, 'update'])->name('equipos.update');
    Route::put('/equipos/restaurar/{id}', [EquipoControlador::class, 'restaurar'])->name('equipos.restaurar');

    ///////////////////////////////////////////////////////////////*MANTENIMIENTO*////////////////////////////////////////////////////
        Route::middleware(['web'])->group(function () {
        Route::get('/mantenimientos', [MantenimientoControlador::class, 'index'])->name('mantenimientos.index');
        Route::get('/mantenimientos/crear', [MantenimientoControlador::class, 'crear'])->name('mantenimientos.crear');
        Route::post('/mantenimientos', [MantenimientoControlador::class, 'insertar'])->name('mantenimientos.insertar');
        Route::delete('/mantenimientos/{COD_MANTENIMIENTO}', [MantenimientoControlador::class, 'destroy'])->name('mantenimientos.destroy');
        Route::get('/mantenimientos/{COD_MANTENIMIENTO}/edit', [MantenimientoControlador::class, 'edit'])->name('mantenimientos.edit');
        Route::put('/mantenimientos/{COD_MANTENIMIENTO}', [MantenimientoControlador::class, 'update'])->name('mantenimientos.update');
        Route::get('/mantenimientos/{id}/gestionar', [MantenimientoControlador::class, 'gestion'])->name('mantenimientos.gestion');
        Route::patch('/mantenimientos/{id}/actualizarEstado', [MantenimientoControlador::class, 'actualizarEstado'])->name('mantenimientos.actualizarEstado');
        Route::get('/mantenimientos/pdf', [MantenimientoControlador::class, 'pdf'])->name('mantenimientos.pdf');
    
        Route::get('/gestionMantenimiento', [GestionMantenimientoControlador::class, 'index'])->name('gestionMantenimiento.index');
        Route::get('/gestionar/{COD_MANTENIMIENTO}', [GestionMantenimientoControlador::class, 'gestionar'])->name('gestionMantenimiento.gestion');
        Route::patch('/actualizar/{COD_MANTENIMIENTO}', [GestionMantenimientoControlador::class, 'actualizarEstado'])->name('gestionMantenimiento.actualizarEstado');
    });
    ///////////////////////////////////////////////////////////////////*COMPRAS*//////////////////////////////////////////////////
        Route::get('/compras', [ComprasControlador::class, 'index'])->name('compras.index');
        Route::post('compras/generar-gastos', [ComprasControlador::class, 'generarGastos'])->name('compras.generarGastos');
        Route::get('/compras/{COD_COMPRA}/deducciones', [DeduccionControlador::class, 'index'])->name('compras.deduccion');
        Route::post('/compras/{COD_COMPRA}/', [ComprasControlador::class, 'agregarDeduccion'])->name('compras.agregar');
        Route::put('/compras/{COD_COMPRA}/deducciones/{COD_DEDUCCION}', [DeduccionControlador::class, 'update'])->name('compras.deduccion.update');
        Route::delete('/compras/{COD_COMPRA}/deducciones/{COD_DEDUCCION}', [DeduccionControlador::class, 'destroy'])->name('compras.deduccion.destroy');
        Route::post('/compras/liquidar/', [ComprasControlador::class, 'liquidarCompras'])->name('compras.liquidar');
        Route::get('/compras/reporte-buscar', [ComprasControlador::class, 'generarReporteBuscar'])->name('compras.reporte.buscar');


    //////////////////////////////////////////////////////////////////*CARGOS*//////////////////////////////////////////
    Route::get('/cargos/pdf', [CargosControlador::class, 'pdf'])->name('cargos.pdf');
    Route::get('/cargos', [CargosControlador::class, 'index'])->name('cargos.index');
    Route::get('/cargos/crear', [CargosControlador::class, 'crear'])->name('cargos.crear');
    Route::post('/cargos', [CargosControlador::class, 'insertar'])->name('cargos.insertar');
    Route::delete('/cargos/{COD_CARGO}', [CargosControlador::class, 'destroy'])->name('cargos.destroy');
    Route::get('/cargos/{COD_CARGO}', [CargosControlador::class, 'edit'])->name('cargos.edit');
    Route::put('/cargos/{COD_CARGO}', [CargosControlador::class, 'update'])->name('cargos.update');

    //////////////////////////////////////////////////////////////*AREAS*////////////////////////////////////////////////
    Route::get('/areas/pdf', [AreaControlador::class, 'pdf'])->name('areas.pdf');
    Route::get('/areas', [AreaControlador::class, 'index'])->name('areas.index');
    Route::get('/areas/crear', [AreaControlador::class, 'crear'])->name('areas.crear');
    Route::post('/areas', [AreaControlador::class, 'insertar'])->name('areas.insertar');
    Route::delete('/areas/{COD_AREA}', [AreaControlador::class, 'destroy'])->name('areas.destroy');
    Route::get('/areas/{COD_AREA}', [AreaControlador::class, 'edit'])->name('areas.edit');
    Route::put('/areas/{COD_AREA}', [AreaControlador::class, 'update'])->name('areas.update');

   
    /////////////////////////////////////////////////////////////*PERMISOS*/////////////////////////////////////////////////////////
    Route::get('/permisos/pdf', [PermisosControlador::class, 'pdf'])->name('permisos.pdf');
    Route::get('/permisos', [PermisosControlador::class, 'index'])->name('permisos.index');
    Route::get('/permisos/crear', [PermisosControlador::class, 'crear'])->name('permisos.crear');
    Route::post('/permisos', [PermisosControlador::class, 'insertar'])->name('permisos.insertar');
    Route::delete('/permisos/{COD_PERMISOS}', [PermisosControlador::class, 'destroy'])->name('permisos.destroy');
    Route::get('/permisos/{COD_PERMISOS}/edit', [PermisosControlador::class, 'edit'])->name('permisos.edit');
    Route::put('/permisos/{COD_PERMISOS}', [PermisosControlador::class, 'update'])->name('permisos.update');

    ///////////////////////////////////////////////////////////////*EMPLEADO PLANILLAS*////////////////////////////////////////////////////
    Route::get('/empleado_planillas/pdf', [EmpleadosPlanillascontrolador::class, 'pdf'])->name('empleado_planillas.pdf');
    Route::get('/empleado_planillas', [EmpleadosPlanillascontrolador::class, 'index'])->name('empleado_planillas.index');
    Route::get('/empleado_planillas/crear', [EmpleadosPlanillascontrolador::class, 'crear'])->name('empleado_planillas.crear');
    Route::post('/empleado_planillas', [EmpleadosPlanillascontrolador::class, 'insertar'])->name('empleado_planillas.insertar');
    Route::delete('/empleado_planillas/{COD_EMPLEADO_PLANILLA}', [EmpleadosPlanillascontrolador::class, 'destroy'])->name('empleado_planillas.destroy');
    Route::get('/empleado_planillas/{COD_EMPLEADO_PLANILLA}/edit', [EmpleadosPlanillascontrolador::class, 'edit'])->name('empleado_planillas.edit');
    Route::put('/empleado_planillas/{COD_EMPLEADO_PLANILLA}', [EmpleadosPlanillascontrolador::class, 'update'])->name('empleado_planillas.update');

    ////////////////////////////////////////////////////Reportes////////////////////////////////


    Route::get('/Perfil', [PerfilController::class, 'editProfile'])->name('Perfil.edit');
Route::post('/Perfil', [PerfilController::class, 'updateProfile'])->name('Perfil.update');
Route::post('/Perfil/disable-2fa', [PerfilController::class, 'disableTwoFactorAuthentication'])->name('Perfil.disable2fa');
Route::post('/Perfil/enable-2fa', [PerfilController::class, 'enable2fa'])->name('Perfil.enable2fa');
Route::post('/perfil/update-password', [PerfilController::class, 'updatePassword'])->name('Perfil.updatePassword');


Route::get('/Bloqueado', function () {
    return view('auth.bloqueado-por-no-cambiar');
})->name('bloqueado-por-no-cambiar');

Route::get('/idioma', [App\Http\Controllers\LanguageController::class, 'showLanguageOptions'])->name('idioma');
Route::post('/idioma/cambiar', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('cambiar-idioma');

});