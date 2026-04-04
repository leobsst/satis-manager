<?php

return [
    'title' => 'Repository|Repositories',

    'create' => [
        'title' => 'New repository',
    ],

    'refresh' => [
        'title' => 'Build Packages',
        'success_notification' => 'Package build has been successfully initiated.',
    ],

    'rebuild' => [
        'action' => 'Refresh',
        'success_notification' => 'Repository update has been successfully initiated.',
    ],

    'api_credentials' => [
        'title' => 'API Credentials',
    ],

    'excluded_branches' => [
        'label' => 'Excluded branches',
        'placeholder' => 'Add a pattern, e.g. dependabot/*',
        'helper' => 'Glob patterns of branch names to exclude from the build. Matched branches will not appear as dev-* versions in the package list.',
    ],

];
