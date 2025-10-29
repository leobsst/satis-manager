<?php

declare(strict_types=1);

namespace App\Helpers;

class ApiHelper
{
    const ERROR_SYSTEM_MESSAGE = 'An unexpected error occurred. Please try again later.';

    public static function getErrorResponseArray($response)
    {
        return [
            'status' => 'error',
            'data' => $response['error'] ?? null,
            'error' => $response['message'] ?? self::ERROR_SYSTEM_MESSAGE,
        ];
    }
}
