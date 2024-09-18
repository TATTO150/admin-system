<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;
use Laravel\Fortify\Fortify;
use App\Models\Rol;
use App\Actions\Fortify\CreateNewUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Rules\Validaciones;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\FechaController;
use App\Models\histcontrasena;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Permisos;
use Illuminate\Support\Facades\Auth;
use App\Mail\EnviarContraseñaTemporal; // Importar el Mailable
use Illuminate\Support\Facades\Mail; // Importar Mail
use App\Models\EstadoUsuario;
use App\Http\Requests\UsuarioRequest;

class RegistrarUsuarioController extends Controller
{
    protected $guard;
    protected $createNewUser;
    protected $roles;

    public function __construct(StatefulGuard $guard, CreateNewUser $createNewUser)
    {
        $this->guard = $guard;
        $this->createNewUser = $createNewUser;
        $this->roles = Rol::all();
    }

    public function create(Request $request): RegisterViewResponse
    {
        return app(RegisterViewResponse::class);
        Log::info('Request data: ', $request->all());

    }

    public function store(Request $request, CreatesNewUsers $creator)
    {
        $request->validate([
            'Usuario' =>  [(new Validaciones)->requerirSinEspacios()->requerirTodoMayusculas()],
            'Nombre_Usuario' => [(new Validaciones)->requerirUnEspacio()->requerirTodoMayusculas()->prohibirNumerosSimbolos()],
            'Contrasena' => [(new Validaciones)->requerirSinEspacios()->requerirSimbolo()->requerirMinuscula()->requerirMayuscula()->requerirNumero()->requerirlongitudMinima(8)->requerirlongitudMaxima(12)->requerirCampo()],
            'Correo_Electronico' => [(new Validaciones)->requerirSinEspacios()->requerirArroba()->requerirCampo()->requerirCorreoUnico('users', 'email')],
        ]);

        if (config('fortify.lowercase_usernames')) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }

        $user = $creator->create($request->all());

        event(new Registered($user));

        $this->guard->login($user);

        return redirect()->route('two-factor.authenticator');
    }
    public function show($Id_usuario)
    {
        // Buscar el usuario por ID
        $usuario = User::find($Id_usuario);

        // Verificar si el usuario fue encontrado
        if (!$usuario) {
            return redirect()->route('usuarios.index')->with('error', 'Usuario no encontrado');
        }

        // Pasar el usuario a la vista
        return view('usuarios.show', compact('usuario'));
    }

    public function index()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar si el rol del usuario tiene el permiso de consultar en la pantalla USUARIO
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                      ->from('tbl_objeto')
                      ->where('Objeto', 'USUARIO')
                      ->limit(1);
            })
            ->where('Permiso_Consultar', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
            return redirect()->route('dashboard')->withErrors('No tiene permiso de ingresar a la ventana de usuarios');
        }
    
        $response = Http::get('http://127.0.0.1:3000/Usuarios');
        $usuarios = User::with('rol')->get();
           // Obtener todos los usuarios y cargar la relación con el estado de usuario
    $usuarios = User::with('estado')->get();
    
        return view('usuarios.index', compact('usuarios'));
    }

    public function pdf(){
        $usuarios=User::all();
        //fecha
        $fechaHora = \Carbon\Carbon::now()->format('d-m-Y H:i:s');

        //cambio de img a formato pdf
        $path = public_path('images/CTraterra.jpeg');
        $logoBase64 = 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    
        //paginacion
        $pdf = Pdf::loadView('usuarios.pdf', compact('usuarios','fechaHora','logoBase64'))
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
    ]);
        
        return $pdf->stream();
    }

    public function crear()
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;
    
        // Verificar permisos
        $permisoConsultar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'USUARIO')
                    ->limit(1);
            })
            ->where('Permiso_Insercion', 'PERMITIDO')
            ->exists();
    
        if (!$permisoConsultar) {
            return redirect()->route('usuarios.index')->withErrors('No tiene permiso para crear usuarios');
        }
    
        // Obtener los roles y los estados de usuario
        $roles = Rol::all();
        $estados = EstadoUsuario::all();
    
        // Retornar la vista con los roles y estados
        return view('usuarios.crear', compact('roles', 'estados'));
    }
    

    

public function insertar(UsuarioRequest $request)
{
    // Generar una nueva contraseña aleatoria
    $contraseña = Str::random(8); // Ajusta la longitud según sea necesario

    // Encriptar la contraseña
    $contraseñaHasheada = Hash::make($contraseña);
    Log::info('Contraseña Generada: ' . $contraseña); // Depuración
    Log::info('Contraseña Hasheada: ' . $contraseñaHasheada); // Depuración

    // Formatear las fechas para que sean compatibles con MySQL
    $fechaUltimaConexion = Carbon::now()->format('Y-m-d H:i:s');
    $primerIngreso = Carbon::now()->format('Y-m-d H:i:s');
    $fechaVencimiento = Carbon::now()->addMonth(3)->format('Y-m-d H:i:s');

    // Crear el usuario en la base de datos
    $response = Http::post('http://146.190.208.117:3000/INS_USUARIOS', [
        'Usuario' => $request->Usuario,
        'Nombre_Usuario' => $request->Nombre_Usuario,
        'Estado_Usuario' => $request->Estado_Usuario,
        'Contrasena' => $contraseñaHasheada,
        'Id_Rol' => $request->Id_Rol,
        'Fecha_Ultima_Conexion' => $fechaUltimaConexion,
        'Primer_Ingreso' => $primerIngreso,
        'Fecha_Vencimiento' => $fechaVencimiento,
        'Correo_Electronico' => $request->Correo_Electronico,
    ]);

    if ($response->successful()) {
        $data = $response->json(); // Captura la respuesta JSON

        if (isset($data['Id_usuario'])) {
            $id_usuario = $data['Id_usuario'];

            // Almacenar la contraseña en la tabla de historial de contraseñas
            Histcontrasena::create([
                'Id_usuario' => $id_usuario,
                'Contrasena' => $contraseña, // Usa la contraseña en texto plano aquí
            ]);

            // Enviar el correo con la contraseña temporal
            $detalles = [
                'nombre' => $request->Nombre_Usuario,
                'usuario' => $request->Usuario,
                'contraseña_temporal' => $contraseña,
            ];

            Mail::to($request->Correo_Electronico)->send(new EnviarContraseñaTemporal($detalles));

            return redirect()->route('usuarios.index')->with('success', 'Usuario creado y correo enviado correctamente.');
        } else {
            Log::error('La respuesta de la API no contiene el ID del usuario.', ['response' => $data]);
            return redirect()->back()->withErrors(['error' => 'Error al crear el usuario: No se pudo obtener el ID del usuario']);
        }
    } else {
        Log::error('Fallo en la solicitud de la API: ' . $response->body());
        return redirect()->back()->withErrors(['error' => 'Error al crear el usuario: Fallo en la solicitud de la API']);
    }
}
 





    public function destroy($Id_usuario)
    {
        Log::info('Iniciando proceso de eliminación de usuario', ['Id_usuario' => $Id_usuario]);

    $user = Auth::user();
    $roleId = $user->Id_Rol;

    Log::info('Usuario autenticado', ['user' => $user, 'roleId' => $roleId]);

    // Verificar si el rol del usuario tiene el permiso de eliminación en el objeto USUARIO
    $permisoEliminar = Permisos::where('Id_Rol', $roleId)
        ->where('Id_Objeto', function ($query) {
            $query->select('Id_Objetos')
                ->from('tbl_objeto')
                ->where('Objeto', 'USUARIO')
                ->limit(1);
        })
        ->where('Permiso_Eliminacion', 'PERMITIDO')
        ->exists();

    Log::info('Permiso de eliminación', ['permisoEliminar' => $permisoEliminar]);

    if (!$permisoEliminar) {
        Log::warning('Permiso de eliminación denegado');
        return redirect()->route('usuarios.index')->withErrors('No tiene permiso para eliminar usuarios');
    }

    try {
        // Encuentra el usuario por su ID
        $usuario = User::findOrFail($Id_usuario);
        Log::info('Usuario encontrado', ['usuario' => $usuario]);

        // Elimina el usuario
        $usuario->delete();
        Log::info('Usuario eliminado correctamente');

        // Redirige con un mensaje de éxito
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    } catch (\Exception $e) {
        Log::error('Error al eliminar el usuario: ' . $e->getMessage());
        // Redirige con un mensaje de error en caso de fallo
        return redirect()->route('usuarios.index')->with('error', 'Error al eliminar el usuario');
    }
    }


    public function edit($Id_usuario)
    {
        $user = Auth::user();
        $roleId = $user->Id_Rol;

        // Verificar si el rol del usuario tiene el permiso de actualización en el objeto USUARIO
        $permisoActualizar = Permisos::where('Id_Rol', $roleId)
            ->where('Id_Objeto', function ($query) {
                $query->select('Id_Objetos')
                    ->from('tbl_objeto')
                    ->where('Objeto', 'USUARIO')
                    ->limit(1);
            })
            ->where('Permiso_Actualizacion', 'PERMITIDO')
            ->exists();

        if (!$permisoActualizar) {
            return redirect()->route('usuarios.index')->withErrors('No tiene permiso para editar usuarios');
        }

        // Obtener la información del usuario desde la API
        $response = Http::get("http://localhost:3000/usuarios/{$Id_usuario}");
        $usuario = $response->json();

        // Verifica el contenido de la respuesta
        if (!isset($usuario['Id_usuario'])) {
            dd('Id_usuario no está definido en la respuesta de la API', $usuario);
        }
        if (isset($usuario['Fecha_Ultima_Conexion'])) {
            $usuario['Fecha_Ultima_Conexion'] = \Carbon\Carbon::parse($usuario['Fecha_Ultima_Conexion'])->format('Y-m-d H:i:s');
        }
        // Modificar el formato de Fecha_Vencimiento si existe en la respuesta
    if (isset($usuario['Fecha_Vencimiento'])) {
        $usuario['Fecha_Vencimiento'] = \Carbon\Carbon::parse($usuario['Fecha_Vencimiento'])->format('d-m-Y');
    }
         // Cargar los estados de usuario desde la base de datos
    $estados = EstadoUsuario::all();
        // Cargar la vista de edición con los datos del usuario y los roles
        return view('usuarios.edit', compact('usuario', 'estados'))->with('roles', $this->roles);
    }

public function update(UsuarioRequest $request, $Id_usuario)
{
    try {
        // Formatear la fecha de la última conexión
        $fechaUltimaConexion = \Carbon\Carbon::parse($request->Fecha_Ultima_Conexion)->format('Y-m-d H:i:s');

        // Realizar la solicitud HTTP PUT para actualizar el usuario
        $response = Http::put("http://127.0.0.1:3000/Usuarios/{$Id_usuario}", [
            'Usuario' => $request->Usuario,
            'Nombre_Usuario' => $request->Nombre_Usuario,
            'Estado_Usuario' => $request->Estado_Usuario,
            'Id_Rol' => $request->Id_Rol,
            'Fecha_Vencimiento' => Carbon::now()->addMonth(3)->format('Y-m-d H:i:s'), // Formatear la fecha de vencimiento
            'Correo_Electronico' => $request->Correo_Electronico,
            'Fecha_Ultima_Conexion' => $fechaUltimaConexion,
        ]);

        // Verificar si la respuesta es exitosa
        if ($response->successful()) {
            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
        } else {
            // Manejo de errores cuando la API no responde con éxito
            return redirect()->route('usuarios.index')->with('error', 'Error al actualizar el usuario. Por favor, intente nuevamente.');
        }
    } catch (\Exception $e) {
        // Manejo de cualquier excepción que ocurra durante el proceso
        return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
    }
}
    

    
}
