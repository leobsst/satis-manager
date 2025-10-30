<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default configuration for Satis package archive
    |--------------------------------------------------------------------------
    |
    | This option controls whether Satis should create package archives (.tar)
    | when building the repository. You can enable or disable this feature
    | based on your requirements. By default, it is set to true.
    |
    | IMPORTANT:
    | - When ENABLED (true): Packages are downloaded from your Satis server
    |   using HTTP basic authentication (composer config http-basic)
    |
    | - When DISABLED (false): The dist URLs pointing to GitHub/GitLab are
    |   automatically removed from packages.json, forcing Composer to use
    |   'source' (git clone) instead. In this mode, you need to configure
    |   Git credentials on the client machine:
    |   - For GitHub: configure a personal access token in ~/.gitconfig or
    |     use composer config github-oauth.github.com <token>
    |   - For GitLab: similar configuration with gitlab-oauth/gitlab-token
    |
    | Recommendation: Keep this enabled (true) for the best user experience
    | with HTTP basic authentication.
    |
    */

    'archive' => env('SATIS_ARCHIVE', true),
];
