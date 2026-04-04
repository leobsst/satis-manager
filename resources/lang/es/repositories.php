<?php

return [
    'title' => 'Repositorio|Repositorios',

    'create' => [
        'title' => 'Nuevo repositorio',
    ],

    'refresh' => [
        'title' => 'Construir paquetes',
        'success_notification' => 'La construcción de paquetes se ha iniciado correctamente.',
    ],

    'rebuild' => [
        'action' => 'Actualizar',
        'success_notification' => 'La actualización del repositorio se ha iniciado correctamente.',
    ],

    'api_credentials' => [
        'title' => 'Credenciales API',
    ],

    'excluded_branches' => [
        'label' => 'Ramas excluidas',
        'placeholder' => 'Añadir un patrón, p.ej. dependabot/*',
        'helper' => 'Patrones glob de nombres de ramas a excluir del build. Las ramas coincidentes no aparecerán como versiones dev-* en la lista de paquetes.',
    ],

];
