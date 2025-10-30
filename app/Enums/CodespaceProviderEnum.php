<?php

namespace App\Enums;

enum CodespaceProviderEnum: string
{
    case GITHUB = 'github';
    case GITLAB = 'gitlab';
    case BITBUCKET = 'bitbucket';
    case CUSTOM = 'custom'; // e.g self-hosted git server (gitea, etc.)

    public static function asSelectableArray(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[$case->value] = ucfirst($case->value);
        }

        return $cases;
    }

    public function icon(): string
    {
        return match ($this) {
            self::GITHUB => 'icon-github',
            self::GITLAB => 'icon-gitlab',
            self::BITBUCKET => 'icon-bitbucket',
            default => 'icon-git',
        };
    }

    /**
     * Get the git URL prefix for this provider
     *
     * @param  bool  $http  Whether to use HTTP(S) prefix instead of SSH
     */
    public function prefix(bool $http = false): ?string
    {
        $customDomain = config('services.custom.domain');
        $customPrefix = $customDomain ? ($http ? "https://{$customDomain}/" : "git@{$customDomain}:") : null;

        return match ($this) {
            self::GITHUB => $http ? 'https://github.com/' : 'git@github.com:',
            self::GITLAB => $http ? 'https://gitlab.com/' : 'git@gitlab.com:',
            self::BITBUCKET => $http ? 'https://bitbucket.org/' : 'git@bitbucket.org:',
            default => $customPrefix
        };
    }
}
