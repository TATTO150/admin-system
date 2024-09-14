@component('mail::message')
# Hola {{ $detalles['nombre'] }}

Tu registro ha sido exitoso. Aquí tienes tu contraseña temporal de acceso:

@component('mail::panel')
**Usuario:** {{ $detalles['usuario'] }}  
**Contraseña:** {{ $detalles['contraseña_temporal'] }}
@endcomponent

@component('mail::button', ['url' => url('/login')])
Iniciar Sesión
@endcomponent

Por favor, cambia esta contraseña después de iniciar sesión.

Gracias,<br>
{{ config('app.name') }}
@endcomponent

@slot('subcopy')
Si tienes problemas para hacer clic en el botón "Iniciar Sesión", copia y pega la siguiente URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endslot
