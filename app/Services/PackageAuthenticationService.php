<?php

namespace App\Services;

use App\Enums\CodespaceProviderEnum;
use Illuminate\Support\Str;

class PackageAuthenticationService
{
    public static function getAvailableAuthentications(): array
    {
        $composerAuth = [];
        $gitConfigs = [];

        foreach (CodespaceProviderEnum::cases() as $provider) {
            $method = Str::camel("get{$provider->value}Authentication");

            if ($providerAuth = self::$method()) {
                // Merge Composer authentication configurations
                if (isset($providerAuth['composer'])) {
                    $composerAuth = array_merge_recursive($composerAuth, $providerAuth['composer']);
                }

                // Collect Git configurations
                if (isset($providerAuth['git'])) {
                    $gitConfigs = array_merge($gitConfigs, $providerAuth['git']);
                }
            }
        }

        $env = [];

        // Set COMPOSER_AUTH with all merged authentications
        if (! empty($composerAuth)) {
            $env['COMPOSER_AUTH'] = json_encode($composerAuth);
        }

        // Set Git configurations
        if (! empty($gitConfigs)) {
            $env['GIT_CONFIG_COUNT'] = (string) count($gitConfigs);
            foreach ($gitConfigs as $index => $config) {
                $env["GIT_CONFIG_KEY_{$index}"] = $config['key'];
                $env["GIT_CONFIG_VALUE_{$index}"] = $config['value'];
            }
        }

        return $env;
    }

    public static function getGithubAuthentication(): ?array
    {
        if ($githubToken = config('services.github.token')) {
            return [
                'composer' => [
                    'github-oauth' => [
                        'github.com' => $githubToken,
                    ],
                ],
                'git' => [
                    [
                        'key' => "url.https://x-access-token:{$githubToken}@github.com/.insteadOf",
                        'value' => 'git@github.com:',
                    ],
                ],
            ];
        }

        return null;
    }

    public static function getGitlabAuthentication(): ?array
    {
        if ($gitlabToken = config('services.gitlab.token')) {
            return [
                'composer' => [
                    'gitlab-token' => [
                        'gitlab.com' => $gitlabToken,
                    ],
                ],
                'git' => [
                    [
                        'key' => "url.https://oauth2:{$gitlabToken}@gitlab.com/.insteadOf",
                        'value' => 'git@gitlab.com:',
                    ],
                ],
            ];
        }

        return null;
    }

    public static function getBitbucketAuthentication(): ?array
    {
        if ($bitbucketToken = config('services.bitbucket.token')) {
            return [
                'composer' => [
                    'bitbucket-oauth' => [
                        'bitbucket.org' => [
                            'consumer-key' => config('services.bitbucket.key', ''),
                            'consumer-secret' => $bitbucketToken,
                        ],
                    ],
                ],
                'git' => [
                    [
                        'key' => "url.https://x-token-auth:{$bitbucketToken}@bitbucket.org/.insteadOf",
                        'value' => 'git@bitbucket.org:',
                    ],
                ],
            ];
        }

        return null;
    }

    public static function getCustomAuthentication(): ?array
    {
        $customToken = config('services.custom.token');
        $customDomain = config('services.custom.domain');
        $customUsername = config('services.custom.username');

        if ($customToken && $customDomain) {
            $gitUrl = $customUsername
                ? "https://{$customUsername}:{$customToken}@{$customDomain}/"
                : "https://x-token-auth:{$customToken}@{$customDomain}/";

            return [
                'composer' => [
                    'http-basic' => [
                        $customDomain => [
                            'username' => $customUsername ?: 'token',
                            'password' => $customToken,
                        ],
                    ],
                ],
                'git' => [
                    [
                        'key' => "url.{$gitUrl}.insteadOf",
                        'value' => "git@{$customDomain}:",
                    ],
                ],
            ];
        }

        return null;
    }
}
