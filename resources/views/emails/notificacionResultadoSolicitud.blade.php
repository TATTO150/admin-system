@component('mail::message')
# Hola {{ $detalles['usuario'] }}

Aquí tienes los detalles de la solicitud realizada para el proyecto {{ $detalles['proyecto'] }}:

@component('mail::panel')
**Resultado:** {{ $detalles['resultado'] }}<br>

@if(!empty($detalles['motivo']))
**Motivo:** {{ $detalles['motivo'] }}
@endif
@endcomponent

@component('mail::button', ['url' => $detalles['url']])
Inicia Sesión y obten más detalles.
@endcomponent

Gracias,<br>
Sistema de Gestión Administrativa.
@endcomponent

@slot('subcopy')
Si tienes problemas para hacer clic en el botón "Iniciar Sesión", copia y pega la siguiente URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endslot
