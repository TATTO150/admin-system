@component('mail::message')
# Hola, {{ $user->Nombre_Usuario }}

Gracias por registrarte con nosotros. Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico:

@component('mail::button', ['url' => route('verification.verify', [$user->id, sha1($user->Correo_Electronico)])])
Verificar Email
@endcomponent

Si no te registraste en nuestro sitio, puedes ignorar este correo electrónico.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
