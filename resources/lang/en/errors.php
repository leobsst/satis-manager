<?php

return [
    '401' => [
        'title' => 'Not Authorized',
        'message' => [
            'p1' => 'You must be authenticated to access this resource.',
            'p2' => 'Please log in with your credentials to continue.',
        ],
    ],

    '403' => [
        'title' => 'Access Denied',
        'message' => [
            'p1' => 'You do not have permission to access this resource.',
            'p2' => 'If you believe this is an error, please contact the site administrator.',
        ],
    ],

    '404' => [
        'title' => 'Page Not Found',
        'message' => [
            'p1' => 'The page you are looking for does not exist or has been moved.',
            'p2' => 'Check the URL or return to the homepage.',
        ],
    ],

    '405' => [
        'title' => 'Method Not Allowed',
        'message' => [
            'p1' => 'The HTTP method used is not allowed for this resource.',
            'p2' => 'Please check your request or return to the homepage to continue browsing.',
        ],
    ],

    '503' => [
        'title' => 'Service Unavailable',
        'message' => [
            'p1' => 'Services are currently unavailable.',
            'p2' => 'Our services are temporarily down for maintenance or overloaded. Please try again in a few moments.',
        ],
    ],
];
