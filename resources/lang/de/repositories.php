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

    'clear_builds' => [
        'title' => 'Alle Builds löschen',
        'confirm_heading' => 'Alle Builds löschen?',
        'confirm_description' => 'Dadurch werden alle generierten Paket-Metadaten gelöscht. Ein vollständiger Rebuild ist erforderlich, bevor die Pakete wieder verfügbar sind.',
        'success_notification' => 'Alle Build-Ausgaben wurden gelöscht.',
    ],

];
