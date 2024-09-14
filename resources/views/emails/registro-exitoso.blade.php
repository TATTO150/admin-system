@component('mail::message')
# ¡Registro Exitoso!

Hola {{ $usuario }},

Tu cuenta ha sido creada exitosamente en nuestro sistema. A continuación, encontrarás los detalles de tu cuenta:

- **Usuario:** {{ $usuario }}
- **Contraseña:** {{ $contrasena }}

Puedes iniciar sesión utilizando estos datos. Te recomendamos cambiar tu contraseña después de iniciar sesión por primera vez.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
