<!-- resources/views/emails/reset_password.blade.php -->
@component('mail::message')
# Restablecer Contraseña

Hola {{ $usuario }},

Has solicitado restablecer tu contraseña. Para continuar, haz clic en el botón a continuación:

@component('mail::button', ['url' => $url])
Restablecer Contraseña
@endcomponent

Si no solicitaste este cambio, no se requiere ninguna acción adicional.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
