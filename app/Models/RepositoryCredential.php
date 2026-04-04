<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CodespaceProviderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property CodespaceProviderEnum $provider
 * @property string $token
 * @property string|null $username
 * @property string|null $domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class RepositoryCredential extends Model
{
    protected $fillable = [
        'name',
        'provider',
        'token',
        'username',
        'domain',
    ];

    protected function casts(): array
    {
        return [
            'provider' => CodespaceProviderEnum::class,
            'token' => 'encrypted',
            'username' => 'encrypted',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
    }
}
