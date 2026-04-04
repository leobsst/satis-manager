<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CodespaceProviderEnum;
use App\Models\Repository;
use App\Models\RepositoryCredential;

class PackageAuthenticationService
{
    /**
     * Get merged authentication for a build-all run.
     *
     * Merges global provider credentials (from env) with all per-repo
     * credentials stored in the database. Per-repo credentials take precedence
     * over global ones for the same domain.
     */
    public static function getAllAuthentications(): array
    {
        $composed = self::buildGlobalComposed();

        $credentials = RepositoryCredential::all();
        foreach ($credentials as $credential) {
            $credComposed = self::buildComposedFromCredential($credential);
            $composed = self::mergeComposed($composed, $credComposed);
        }

        return self::composedToEnv($composed);
    }

    /**
     * Get merged authentication for a single-repository build.
     *
     * Uses the repository's own credential (if set) merged on top of the
     * global authentication. The per-repo credential takes precedence.
     */
    public static function getAuthForRepository(Repository $repository): array
    {
        $composed = self::buildGlobalComposed();

        if ($repository->credential !== null) {
            $credComposed = self::buildComposedFromCredential($repository->credential);
            $composed = self::mergeComposed($composed, $credComposed);
        }

        return self::composedToEnv($composed);
    }

    /**
     * @deprecated Use getAllAuthentications() or getAuthForRepository() instead.
     */
    public static function getAvailableAuthentications(): array
    {
        return self::getAllAuthentications();
    }

    // -------------------------------------------------------------------------
    // Internal: build composed auth structures
    // -------------------------------------------------------------------------

    /**
     * Build a composed auth structure from all global env-based credentials.
     *
     * @return array{composer: array, git: list<array{key: string, value: string}>}
     */
    private static function buildGlobalComposed(): array
    {
        $composed = ['composer' => [], 'git' => []];

        foreach (CodespaceProviderEnum::cases() as $provider) {
            $providerComposed = match ($provider) {
                CodespaceProviderEnum::GITHUB => self::buildGithubComposed(
                    config('services.github.token')
                ),
                CodespaceProviderEnum::GITLAB => self::buildGitlabComposed(
                    config('services.gitlab.token')
                ),
                CodespaceProviderEnum::BITBUCKET => self::buildBitbucketComposed(
                    config('services.bitbucket.key'),
                    config('services.bitbucket.token')
                ),
                CodespaceProviderEnum::CUSTOM => self::buildCustomComposed(
                    config('services.custom.token'),
                    config('services.custom.domain'),
                    config('services.custom.username')
                ),
            };

            if ($providerComposed !== null) {
                $composed = self::mergeComposed($composed, $providerComposed);
            }
        }

        return $composed;
    }

    /**
     * Build a composed auth structure from a RepositoryCredential model.
     *
     * @return array{composer: array, git: list<array{key: string, value: string}>}
     */
    private static function buildComposedFromCredential(RepositoryCredential $credential): array
    {
        $composed = match ($credential->provider) {
            CodespaceProviderEnum::GITHUB => self::buildGithubComposed($credential->token),
            CodespaceProviderEnum::GITLAB => self::buildGitlabComposed($credential->token),
            CodespaceProviderEnum::BITBUCKET => self::buildBitbucketComposed($credential->username, $credential->token),
            CodespaceProviderEnum::CUSTOM => self::buildCustomComposed($credential->token, $credential->domain, $credential->username),
        };

        return $composed ?? ['composer' => [], 'git' => []];
    }

    // -------------------------------------------------------------------------
    // Internal: per-provider composed builders
    // -------------------------------------------------------------------------

    /**
     * @return array{composer: array, git: list<array{key: string, value: string}>}|null
     */
    private static function buildGithubComposed(?string $token): ?array
    {
        if (! $token) {
            return null;
        }

        return [
            'composer' => [
                'github-oauth' => ['github.com' => $token],
            ],
            'git' => [
                [
                    'key' => "url.https://x-access-token:{$token}@github.com/.insteadOf",
                    'value' => 'git@github.com:',
                ],
            ],
        ];
    }

    /**
     * @return array{composer: array, git: list<array{key: string, value: string}>}|null
     */
    private static function buildGitlabComposed(?string $token): ?array
    {
        if (! $token) {
            return null;
        }

        return [
            'composer' => [
                'gitlab-token' => ['gitlab.com' => $token],
            ],
            'git' => [
                [
                    'key' => "url.https://oauth2:{$token}@gitlab.com/.insteadOf",
                    'value' => 'git@gitlab.com:',
                ],
            ],
        ];
    }

    /**
     * @return array{composer: array, git: list<array{key: string, value: string}>}|null
     */
    private static function buildBitbucketComposed(?string $consumerKey, ?string $consumerSecret): ?array
    {
        if (! $consumerSecret) {
            return null;
        }

        return [
            'composer' => [
                'bitbucket-oauth' => [
                    'bitbucket.org' => [
                        'consumer-key' => $consumerKey ?? '',
                        'consumer-secret' => $consumerSecret,
                    ],
                ],
            ],
            'git' => [
                [
                    'key' => "url.https://x-token-auth:{$consumerSecret}@bitbucket.org/.insteadOf",
                    'value' => 'git@bitbucket.org:',
                ],
            ],
        ];
    }

    /**
     * @return array{composer: array, git: list<array{key: string, value: string}>}|null
     */
    private static function buildCustomComposed(?string $token, ?string $domain, ?string $username): ?array
    {
        if (! $token || ! $domain) {
            return null;
        }

        $gitUrl = $username
            ? "https://{$username}:{$token}@{$domain}/"
            : "https://x-token-auth:{$token}@{$domain}/";

        return [
            'composer' => [
                'http-basic' => [
                    $domain => [
                        'username' => $username ?: 'token',
                        'password' => $token,
                    ],
                ],
            ],
            'git' => [
                [
                    'key' => "url.{$gitUrl}.insteadOf",
                    'value' => "git@{$domain}:",
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Internal: merge + serialize
    // -------------------------------------------------------------------------

    /**
     * Merge two composed auth structures. The second takes precedence for the
     * same domain keys (git entries are appended, not deduplicated).
     *
     * @param  array{composer: array, git: list<array{key: string, value: string}>}  $base
     * @param  array{composer: array, git: list<array{key: string, value: string}>}  $override
     * @return array{composer: array, git: list<array{key: string, value: string}>}
     */
    private static function mergeComposed(array $base, array $override): array
    {
        return [
            'composer' => array_merge_recursive($base['composer'], $override['composer']),
            'git' => array_merge($base['git'], $override['git']),
        ];
    }

    /**
     * Convert a composed auth structure to process environment variables.
     *
     * @param  array{composer: array, git: list<array{key: string, value: string}>}  $composed
     * @return array<string, string>
     */
    private static function composedToEnv(array $composed): array
    {
        $env = [];

        if (! empty($composed['composer'])) {
            $env['COMPOSER_AUTH'] = json_encode($composed['composer']);
        }

        if (! empty($composed['git'])) {
            $env['GIT_CONFIG_COUNT'] = (string) \count($composed['git']);
            foreach ($composed['git'] as $index => $config) {
                $env["GIT_CONFIG_KEY_{$index}"] = $config['key'];
                $env["GIT_CONFIG_VALUE_{$index}"] = $config['value'];
            }
        }

        return $env;
    }
}
