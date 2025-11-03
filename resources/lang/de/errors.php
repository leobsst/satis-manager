<?php

return [
    '401' => [
        'title' => 'Nicht autorisiert',
        'message' => [
            'p1' => 'Sie müssen authentifiziert sein, um auf diese Ressource zuzugreifen.',
            'p2' => 'Bitte melden Sie sich mit Ihren Anmeldedaten an, um fortzufahren.',
        ],
    ],

    '403' => [
        'title' => 'Zugriff verweigert',
        'message' => [
            'p1' => 'Sie haben keine Berechtigung, auf diese Ressource zuzugreifen.',
            'p2' => 'Wenn Sie glauben, dass dies ein Fehler ist, wenden Sie sich bitte an den Site-Administrator.',
        ],
    ],

    '404' => [
        'title' => 'Seite nicht gefunden',
        'message' => [
            'p1' => 'Die gesuchte Seite existiert nicht oder wurde verschoben.',
            'p2' => 'Überprüfen Sie die URL oder kehren Sie zur Startseite zurück.',
        ],
    ],

    '405' => [
        'title' => 'Methode nicht erlaubt',
        'message' => [
            'p1' => 'Die verwendete HTTP-Methode ist für diese Ressource nicht zulässig.',
            'p2' => 'Bitte überprüfen Sie Ihre Anfrage oder kehren Sie zur Startseite zurück, um fortzufahren.',
        ],
    ],

    '503' => [
        'title' => 'Dienst nicht verfügbar',
        'message' => [
            'p1' => 'Dienste sind derzeit nicht verfügbar.',
            'p2' => 'Unsere Dienste sind vorübergehend wegen Wartungsarbeiten oder Überlastung ausgefallen. Bitte versuchen Sie es in wenigen Augenblicken erneut.',
        ],
    ],
];
