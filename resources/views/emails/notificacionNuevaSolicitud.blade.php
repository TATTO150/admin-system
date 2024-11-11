@component('mail::message')
# Una nueva solicitud esta pendiente de revision.

Aquí tienes los detalles de la solicitud:

@component('mail::panel')
**Usuario Solicitante:** {{ $detalles['usuario'] }}<br>
**Descripcion de la Solicitud:** {{ $detalles['descripcion'] }}<br>
**Proyecto Asociado:** {{ $detalles['proyecto'] }}
@endcomponent

@component('mail::button', ['url' => $detalles['url']])
Inicia Sesión y Revisa la Solicitud
@endcomponent

Gracias,<br>
Sistema de Gestion Administrativa.
@endcomponent

@slot('subcopy')
Si tienes problemas para hacer clic en el botón "Iniciar Sesión", copia y pega la siguiente URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endslot
