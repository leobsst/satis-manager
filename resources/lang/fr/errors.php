<?php

return [
    '401' => [
        'title' => 'Non autorisé',
        'message' => [
            'p1' => 'Vous devez être authentifié pour accéder à cette ressource.',
            'p2' => 'Veuillez vous connecter avec vos identifiants pour continuer.',
        ],
    ],

    '403' => [
        'title' => 'Accès refusé',
        'message' => [
            'p1' => "Vous n'avez pas l'autorisation d'accéder é cette ressource.",
            'p2' => "Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur du site.",
        ],
    ],

    '404' => [
        'title' => 'Page Non Trouvée',
        'message' => [
            'p1' => "La page que vous recherchez n'existe pas ou a été déplacée.",
            'p2' => "Vérifiez l'URL ou retournez à la page d'accueil.",
        ],
    ],

    '405' => [
        'title' => 'Méthode Non Autorisée',
        'message' => [
            'p1' => "La méthode HTTP utilisée n'est pas autorisée pour cette ressource.",
            'p2' => "Veuillez vérifier votre requête ou retourner à la page d'accueil pour continuer votre navigation.",
        ],
    ],

    '503' => [
        'title' => 'Service Indisponible',
        'message' => [
            'p1' => 'Services indisponibles pour le moment.',
            'p2' => 'Nos services sont temporairement en maintenance ou surchargés. Veuillez réessayer dans quelques instants.',
        ],
    ],
];
