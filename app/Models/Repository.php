<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CodespaceProviderEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * Repository class
 *
 * @property string $vendor
 * @property string $repository_name
 * @property CodespaceProviderEnum $provider
 * @property string $url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Repository extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vendor',
        'repository_name',
        'provider',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'provider' => CodespaceProviderEnum::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the full git URL for this repository
     *
     * @param  bool  $http  Whether to use HTTP(S) prefix instead of SSH
     */
    public function getFullUrl(bool $http = false): string
    {
        $prefix = $this->provider->prefix($http);

        if (! $prefix) {
            throw new \RuntimeException("Provider {$this->provider->value} does not have a configured prefix");
        }

        return "{$prefix}{$this->url}.git";
    }
}
