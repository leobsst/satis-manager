<?php

declare(strict_types=1);

namespace App\Http\Requests\Webhooks;

use App\Enums\CodespaceProviderEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class BuildRequest extends WebhookRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::in(array_map(fn ($e) => $e->value, CodespaceProviderEnum::cases()))],
            'source_repo' => ['required', 'string'],
            'tag' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'Field provider is required.',
            'provider.in' => 'Field provider must be one of the following values: ' . implode(', ', array_map(fn ($e) => $e->value, CodespaceProviderEnum::cases())) . '.',
            'source_repo.required' => 'Field source_repo is required.',
            'source_repo.string' => 'Field source_repo must be a string. e.g., vendor/repo',
            'tag.string' => 'Field tag must be a string. e.g., v1.0.0',
        ];
    }
}
