<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mailer Predeterminado
    |--------------------------------------------------------------------------
    |
    | Esta opción controla el mailer predeterminado que se usa para enviar
    | todos los mensajes de correo electrónico, a menos que se especifique
    | explícitamente otro mailer al enviar el mensaje. Todos los mailers
    | adicionales se pueden configurar dentro de la matriz "mailers".
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'), // Mailer predeterminado, puede ser sobrescrito por la variable de entorno MAIL_MAILER.

    /*
    |--------------------------------------------------------------------------
    | Configuraciones de Mailers
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar todos los mailers utilizados por tu aplicación y
    | sus respectivos ajustes. Se han configurado varios ejemplos y puedes
    | añadir los tuyos según lo requiera tu aplicación.
    |
    | Laravel soporta una variedad de controladores de transporte de correo
    | que se pueden utilizar al enviar un correo electrónico. Puedes
    | especificar cuál estás utilizando para tus mailers a continuación.
    |
    | Soportados: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "resend", "log", "array",
    |            "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp', // Tipo de transporte usado, en este caso SMTP.
            'host' => env('MAIL_HOST', 'smtp.gmail.com'), // Servidor SMTP, puede ser sobrescrito por la variable de entorno MAIL_HOST.
            'port' => env('MAIL_PORT', 587), // Puerto usado para la conexión SMTP, puede ser sobrescrito por la variable de entorno MAIL_PORT.
            'encryption' => env('MAIL_ENCRYPTION', 'tls'), // Tipo de cifrado usado (tls), puede ser sobrescrito por la variable de entorno MAIL_ENCRYPTION.
            'username' => env('MAIL_USERNAME'), // Nombre de usuario para la autenticación SMTP, definido en la variable de entorno MAIL_USERNAME.
            'password' => env('MAIL_PASSWORD'), // Contraseña para la autenticación SMTP, definida en la variable de entorno MAIL_PASSWORD.
            'timeout' => null, // Tiempo de espera para la conexión SMTP.
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)), // Dominio local para el saludo EHLO/HELO.
        ],

        'ses' => [
            'transport' => 'ses', // Tipo de transporte usado, en este caso SES (Amazon Simple Email Service).
        ],

        'postmark' => [
            'transport' => 'postmark', // Tipo de transporte usado, en este caso Postmark.
        ],

        'resend' => [
            'transport' => 'resend', // Tipo de transporte usado, en este caso Resend.
        ],

        'sendmail' => [
            'transport' => 'sendmail', // Tipo de transporte usado, en este caso Sendmail.
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'), // Ruta al ejecutable de Sendmail.
        ],

        'log' => [
            'transport' => 'log', // Tipo de transporte usado, en este caso Log (registra correos en los archivos de log).
            'channel' => env('MAIL_LOG_CHANNEL'), // Canal de log usado para registrar correos, definido en la variable de entorno MAIL_LOG_CHANNEL.
        ],

        'array' => [
            'transport' => 'array', // Tipo de transporte usado, en este caso Array (almacena correos en un array para pruebas).
        ],

        'failover' => [
            'transport' => 'failover', // Tipo de transporte usado, en este caso Failover (conmutación por error).
            'mailers' => [
                'smtp', // Primer mailer para usar en caso de fallo.
                'log',  // Segundo mailer para usar en caso de fallo.
            ],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin', // Tipo de transporte usado, en este caso Roundrobin (distribuye correos entre varios mailers).
            'mailers' => [
                'ses', // Primer mailer usado en la distribución.
                'postmark', // Segundo mailer usado en la distribución.
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Dirección "From" Global
    |--------------------------------------------------------------------------
    |
    | Puedes desear que todos los correos electrónicos enviados por tu aplicación
    | se envíen desde la misma dirección. Aquí puedes especificar un nombre y
    | dirección que se utilizarán globalmente para todos los correos enviados
    | por tu aplicación.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'onesolutiongrupo@gmail.com'), // Dirección de correo "From" predeterminada, definida en la variable de entorno MAIL_FROM_ADDRESS.
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')), // Nombre "From" predeterminado, definido en la variable de entorno MAIL_FROM_NAME.
    ],

];
