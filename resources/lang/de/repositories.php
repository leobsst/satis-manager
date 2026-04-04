<?php

return [
    'title' => 'Repository|Repositories',

    'create' => [
        'title' => 'Neues Repository',
    ],

    'refresh' => [
        'title' => 'Pakete erstellen',
        'success_notification' => 'Die Paketerstellung wurde erfolgreich gestartet.',
    ],

    'rebuild' => [
        'action' => 'Aktualisieren',
        'success_notification' => 'Die Repository-Aktualisierung wurde erfolgreich gestartet.',
    ],

    'api_credentials' => [
        'title' => 'API-Anmeldedaten',
    ],

    'excluded_branches' => [
        'label' => 'Ausgeschlossene Branches',
        'placeholder' => 'Muster hinzufügen, z.B. dependabot/*',
        'helper' => 'Glob-Muster für Branch-Namen, die vom Build ausgeschlossen werden sollen. Übereinstimmende Branches erscheinen nicht als dev-*-Versionen in der Paketliste.',
    ],

];
