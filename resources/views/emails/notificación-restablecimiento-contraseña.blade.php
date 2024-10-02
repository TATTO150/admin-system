@component('mail::message')
# Notificación de Restablecimiento de Contraseña

El usuario **{{ $nombre }}** ha restablecido su contraseña. Aquí tienes la información para revisar su estado:

@component('mail::panel')
**Correo del Usuario:** {{ $usuario }}  
**Usuario:** {{ $nombre}}
@endcomponent

Si deseas darle acceso al sistema, haz clic en el siguiente botón:

@component('mail::button', ['url' => url('/login')])
Aprobar Acceso del Usuario
@endcomponent

Gracias,  
Constructora Traterra S. de RL

@slot('subcopy')
Si tienes problemas para hacer clic en el botón, copia y pega la siguiente URL en tu navegador: [{{ url('/login') }}]({{ url('/login') }})..
@endslot
@endcomponent
