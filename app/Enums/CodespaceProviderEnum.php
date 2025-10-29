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

    public function prefix(): ?string
    {
        $customDomain = config('services.custom.domain');

        return match ($this) {
            self::GITHUB => 'git@github.com:',
            self::GITLAB => 'git@gitlab.com:',
            self::BITBUCKET => 'git@bitbucket.org:',
            default => $customDomain ? "git@{$customDomain}:" : null
        };
    }
}
