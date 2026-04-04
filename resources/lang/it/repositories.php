<?php

return [
    'title' => 'Repository|Repositories',

    'create' => [
        'title' => 'Nuovo repository',
    ],

    'refresh' => [
        'title' => 'Costruisci pacchetti',
        'success_notification' => 'La costruzione dei pacchetti è stata avviata con successo.',
    ],

    'rebuild' => [
        'action' => 'Aggiorna',
        'success_notification' => 'L\'aggiornamento del repository è stato avviato con successo.',
    ],

    'api_credentials' => [
        'title' => 'Credenziali API',
    ],

    'excluded_branches' => [
        'label' => 'Branch esclusi',
        'placeholder' => 'Aggiungi un pattern, es. dependabot/*',
        'helper' => 'Pattern glob di nomi di branch da escludere dal build. I branch corrispondenti non appariranno come versioni dev-* nella lista dei pacchetti.',
    ],

];
