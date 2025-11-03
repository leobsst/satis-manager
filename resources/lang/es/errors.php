<?php

return [
    '401' => [
        'title' => 'No autorizado',
        'message' => [
            'p1' => 'Debe estar autenticado para acceder a este recurso.',
            'p2' => 'Por favor, inicie sesión con sus credenciales para continuar.',
        ],
    ],

    '403' => [
        'title' => 'Acceso denegado',
        'message' => [
            'p1' => 'No tiene permiso para acceder a este recurso.',
            'p2' => 'Si cree que esto es un error, póngase en contacto con el administrador del sitio.',
        ],
    ],

    '404' => [
        'title' => 'Página no encontrada',
        'message' => [
            'p1' => 'La página que busca no existe o ha sido movida.',
            'p2' => 'Compruebe la URL o vuelva a la página de inicio.',
        ],
    ],

    '405' => [
        'title' => 'Método no permitido',
        'message' => [
            'p1' => 'El método HTTP utilizado no está permitido para este recurso.',
            'p2' => 'Por favor, verifique su solicitud o vuelva a la página de inicio para continuar navegando.',
        ],
    ],

    '503' => [
        'title' => 'Servicio no disponible',
        'message' => [
            'p1' => 'Los servicios no están disponibles en este momento.',
            'p2' => 'Nuestros servicios están temporalmente en mantenimiento o sobrecargados. Por favor, inténtelo de nuevo en unos momentos.',
        ],
    ],
];
