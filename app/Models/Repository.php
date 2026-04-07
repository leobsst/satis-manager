<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CodespaceProviderEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $vendor
 * @property string $repository_name
 * @property CodespaceProviderEnum $provider
 * @property int|null $repository_credential_id
 * @property list<string>|null $excluded_branches
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read RepositoryCredential|null $credential
 */
class Repository extends Model
{
    protected $fillable = [
        'vendor',
        'repository_name',
        'provider',
        'repository_credential_id',
        'excluded_branches',
    ];

    protected function casts(): array
    {
        return [
            'provider' => CodespaceProviderEnum::class,
            'excluded_branches' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(RepositoryCredential::class, 'repository_credential_id');
    }

    /**
     * Get the full git URL for this repository.
     *
     * For CUSTOM provider, the domain is resolved from the associated credential.
     *
     * @param  bool  $http  Whether to use HTTP(S) prefix instead of SSH
     */
    public function getFullUrl(bool $http = false): string
    {
        if ($this->provider === CodespaceProviderEnum::CUSTOM) {
            $domain = $this->credential?->domain;

            if (! $domain) {
                throw new \RuntimeException("Custom provider repository \"{$this->url}\" requires a credential with a domain.");
            }

            $prefix = $http ? "https://{$domain}/" : "git@{$domain}:";

            return "{$prefix}{$this->url}.git";
        }

        $prefix = $this->provider->prefix($http);

        if (! $prefix) {
            throw new \RuntimeException("Provider {$this->provider->value} does not have a configured prefix.");
        }

        return "{$prefix}{$this->url}.git";
    }
}
