<?php

return [
    '401' => [
        'title' => 'Non autorizzato',
        'message' => [
            'p1' => 'Devi essere autenticato per accedere a questa risorsa.',
            'p2' => 'Per favore, accedi con le tue credenziali per continuare.',
        ],
    ],

    '403' => [
        'title' => 'Accesso negato',
        'message' => [
            'p1' => 'Non hai il permesso di accedere a questa risorsa.',
            'p2' => 'Se ritieni che si tratti di un errore, contatta l\'amministratore del sito.',
        ],
    ],

    '404' => [
        'title' => 'Pagina non trovata',
        'message' => [
            'p1' => 'La pagina che stai cercando non esiste o è stata spostata.',
            'p2' => 'Controlla l\'URL o torna alla homepage.',
        ],
    ],

    '405' => [
        'title' => 'Metodo non consentito',
        'message' => [
            'p1' => 'Il metodo HTTP utilizzato non è consentito per questa risorsa.',
            'p2' => 'Per favore, verifica la tua richiesta o torna alla homepage per continuare la navigazione.',
        ],
    ],

    '503' => [
        'title' => 'Servizio non disponibile',
        'message' => [
            'p1' => 'I servizi non sono attualmente disponibili.',
            'p2' => 'I nostri servizi sono temporaneamente in manutenzione o sovraccarichi. Per favore, riprova tra qualche istante.',
        ],
    ],
];
