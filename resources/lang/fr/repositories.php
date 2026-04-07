<?php

return [
    'title' => 'Répertoire|Répertoires',

    'create' => [
        'title' => 'Nouveau répertoire',
    ],

    'refresh' => [
        'title' => 'Générer les paquets',
        'success_notification' => 'La génération des paquets a été lancée avec succès.',
    ],

    'rebuild' => [
        'action' => 'Actualiser',
        'success_notification' => 'L\'actualisation du répertoire a été lancée avec succès.',
    ],

    'api_credentials' => [
        'title' => 'Identifiants API',
    ],

    'excluded_branches' => [
        'label' => 'Branches exclues',
        'placeholder' => 'Ajouter un pattern, ex. dependabot/*',
        'helper' => 'Patterns glob de noms de branches à exclure du build. Les branches correspondantes n\'apparaîtront pas comme versions dev-* dans la liste des packages.',
    ],

    'clear_builds' => [
        'title' => 'Effacer tous les builds',
        'confirm_heading' => 'Effacer tous les builds ?',
        'confirm_description' => 'Cela supprimera toutes les métadonnées de packages générées. Un rebuild complet sera nécessaire avant que les packages soient à nouveau disponibles.',
        'success_notification' => 'Tous les builds ont été effacés.',
    ],

];
