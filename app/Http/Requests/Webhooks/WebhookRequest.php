<?php

declare(strict_types=1);

namespace App\Http\Requests\Webhooks;

use App\Helpers\ApiHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class WebhookRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json(ApiHelper::getErrorResponseArray([
            'error' => $validator->errors()->first(),
        ]), 400);

        throw new ValidationException($validator, $response);
    }
}
