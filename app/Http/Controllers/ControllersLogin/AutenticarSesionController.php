<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Bitacora;
use App\Models\Parametros;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AutenticarSesionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the login view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request): LoginViewResponse
    {
        $user = $this->guard->user();
        
       
        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
{
    // Buscar el usuario por correo o nombre de usuario
    $user = User::where('Correo_Electronico', $request->Correo_Electronico)
        ->orWhere('Usuario', $request->Correo_Electronico)
        ->first();
    $parametro = Parametros::where('Id_Parametro', 1)->first();

    if ($user) {
        // Comprobar si el usuario ya tiene una sesión activa
        $redirectToUniqueSession = $this->Sesion_Unica($user);
        if ($redirectToUniqueSession) {
            // Detener la ejecución del login y redirigir si ya hay una sesión activa
            return $redirectToUniqueSession;
        }

        // Reiniciar el estado de two_factor_status
        $user->two_factor_status = null;
        $user->save();

        // Verificar si la fecha de vencimiento es hoy
        if ($user->Fecha_Vencimiento && $user->Fecha_Vencimiento->isToday()) {
            $this->bloquearUsuario($user);
            $this->registrarEnBitacora($user, 4, 'Intento de inicio de sesión con cuenta vencida', 'Consulta');
            return redirect()->route('bloqueo');
        }

        // Verificar si el usuario está bloqueado
        if ($user->Estado_Usuario === 'BLOQUEADO'|| $user->Estado_Usuario == 3) {
            $this->registrarEnBitacora($user, 4, 'Intento de inicio de sesión con usuario bloqueado', 'Consulta');
            return redirect()->route('bloqueo');
        }

        // Verificar la contraseña
        if (Hash::check($request->password, $user->Contrasena)) {
            $this->resetearIntentosLogin($user);
            $this->guard->login($user);
            $this->actualizarUltimoLogin($user);
            $this->Primer_Ingreso($user);
            $this->registrarEnBitacora($user, 2, 'Inicio de sesión exitoso', 'Ingreso');
            
            // Guardar el Id_usuario en la sesión
            Session::put('Id_usuario', $user->Id_usuario);

            // Redirigir si el usuario necesita autenticación de dos factores
            if (is_null($user->two_factor_secret) && $user->Verificacion_Usuario == 0) {
                return redirect()->route('two-factor.authenticator');
            }

            // Verificar el rol del usuario
            if ($user->Id_Rol == 3) {
                return redirect()->route('autoregistro-notificacion');
            }

            // Redirigir si el usuario debe restablecer la contraseña
            if ($user->Estado_Usuario === 'RESETEO' || $user->Estado_Usuario == 5 && $user->Id_usuario != 1) {
                return redirect()->route('password.reset.confirmation');
            }

            // Insertar el nuevo registro de sesión en la tabla `usuarios_logueados`
            DB::table('usuarios_logueados')->insert([
                'user_id' => $user->Id_usuario,
                'session_id' => Session::getId(),
            ]);

            // Verificar si la verificación en dos pasos está activa
            if (!is_null($user->two_factor_secret)) {
                return redirect()->route('two-factor.login'); // Redirige a la vista de two-factor-challenge
            }

            return redirect()->route('dashboard');
        } else {
            // Manejo de intentos de inicio de sesión fallidos
            $this->incrementarIntentosLogin($user);
            if ($user->Intentos_Login >= 3 && $user->Id_Rol != 1) {
                $this->bloquearUsuario($user);
                $this->registrarEnBitacora($user, 2, 'Usuario bloqueado por intentos fallidos', 'Update'); 
            }

            $this->registrarEnBitacora($user, 2, 'Intento fallido de inicio de sesión', 'Consulta');
            return back()->withErrors(['email' => 'Usuario o contraseña incorrectos']);
        }
    }

    return back()->withErrors(['email' => 'Usuario o contraseña incorrectos']);
}


    /**
     * Destroy an authenticated session.
     * @param \App\Models\User
     * @param \Illuminate\Support\Facades\Auth
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
{
    $user = $this->guard->user();
    
    $this->registrarEnBitacora($user, 2, 'Cierre de sesión', 'Ingreso'); // ID_objetos 2: 'login'

    // Eliminar el registro de sesión en la tabla usuarios_logueados
    $deleted = DB::table('usuarios_logueados')->where('user_id', $user->Id_usuario)->delete();

    $this->guard->logout();

    if ($request->hasSession()) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Eliminar el Id_usuario de la sesión
        $request->session()->forget('Id_usuario');
    }

    return app(LogoutResponse::class);
}


    /**
     * Actualiza la ultima fecha de conexion.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function actualizarUltimoLogin($user)
    {
        $user->Fecha_Ultima_Conexion = Carbon::now();
        $user->save();
    }

    /**
     * Agrega un nuevo ingreso al sistema.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function Primer_Ingreso($user)
    {
        $user->Primer_Ingreso += 1;
        $user->save();
    }

    /**
     * Agrega un nuevo ingreso al sistema.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function Sesion_Unica($user)
    {
        Log::info('Se llego a la funcion de comprobacion');
        // Comprobar si el usuario ya tiene una sesión activa
        $existingSession = DB::table('usuarios_logueados')->where('user_id', $user->Id_usuario)->first();

        if ($existingSession) {
            Log::info('Se esta redirijiendoJ');
            return redirect()->route('unica.sesion'); // Ruta a la vista de advertencia
        }

        return null;
    }

    /**
     * Incrementa el intento de inicio de sesion para la usuario.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function incrementarIntentosLogin($user)
    {
        $user->Intentos_Login += 1;
        $user->save();
    }

    /**
     * Resetea los intentos de login.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function resetearIntentosLogin($user)
    {
        $user->Intentos_Login = 0;
        $user->save();
    }

    /**
     * Bloquea al usuario.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function bloquearUsuario($user)
    {
        $user->Estado_Usuario = 3;
        $user->save();
    }

    /**
     * Registra un evento en la bitácora.
     *
     * @param  \App\Models\User  $user
     * @param  int  $ID_objetos
     * @param  string  $descripcion
     * @param  string  $accion
     * @return void
     */
    protected function registrarEnBitacora($user, $Id_Objetos, $descripcion, $accion)
    {
        Bitacora::create([
            'Id_usuario' => $user->Id_usuario,
            'Id_Objetos' => $Id_Objetos,
            'Descripcion' => $descripcion,
            'Fecha' => Carbon::now(),
            'Accion' => $accion
        ]);
    }
}
