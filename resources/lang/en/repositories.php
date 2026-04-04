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

    'clear_builds' => [
        'title' => 'Clear all builds',
        'confirm_heading' => 'Clear all builds?',
        'confirm_description' => 'This will delete all generated package metadata. A full rebuild will be required before packages are available again.',
        'success_notification' => 'All build output has been cleared.',
    ],

    'clear_repo_build' => [
        'action' => 'Clear build',
        'confirm_heading' => 'Clear build',
        'success_notification' => 'Repository build output has been cleared.',
    ],

];
