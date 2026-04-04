# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

---

## [1.2.0] - 2026-04-04

### Added
- **Per-repository credentials** — a new "Credentials" section in the admin panel lets you store named, encrypted credentials (token, username, domain) for any git provider. Credentials can be created and edited inline from the repository form.
- **Credential assignment per repository** — each repository can now have its own credential (or fall back to the global env-based config). The build process automatically merges per-repo credentials on top of global ones.
- **Excluded branch patterns** — each repository can define a list of glob patterns (e.g. `dependabot/*`, `renovate/**`) to exclude from the satis build. Matched `dev-*` versions are removed from the generated package metadata after each build.

### Changed
- `PackageAuthenticationService` refactored: global and per-credential auth are now built through typed private methods per provider, then merged. Added `getAllAuthentications()` (global + all DB credentials) and `getAuthForRepository(Repository)` (global + single repo credential).
- `BuildPackages` job now uses `getAuthForRepository()` when building a single repo and `getAllAuthentications()` when building all repos.
- `SatisConfigService::generateConfig()` now eager-loads the `credential` relationship on repositories to avoid N+1 queries.
- `Repository::getFullUrl()` resolves the domain for custom-provider repos from the associated credential first, then falls back to `CUSTOM_PROVIDER_DOMAIN` env variable.

### Security
- Credential tokens and usernames are stored encrypted at rest using Laravel's `encrypted` cast (AES-256-CBC via `APP_KEY`). They are never stored or logged in plaintext.

---

## [1.1.0] - 2026-02-23

### Added
- PHP 8.5 added to the CI test matrix.

### Changed
- `declare(strict_types=1)` added to all PHP files.
- Filament public assets updated.

---

## [1.0.2] - 2025-11-03

### Added
- i18n: Spanish (`es`) and German (`de`) translations added for all admin panel strings.
- i18n: Italian (`it`) and French (`fr`) translations expanded.

### Changed
- API credentials creation flow improved: the generated `client_id` and `client_secret` are now displayed immediately in a modal after clicking "Generate API Credentials" on a repository row.
- Repositories table: "Rebuild" and "Generate API Credentials" actions grouped under a single action button per row.

### Fixed
- PHPStan type errors resolved.

---

## [1.0.1] - 2025-10-30

### Added
- **Satis archive mode** — controlled by the `SATIS_ARCHIVE` env variable. When disabled, a post-processing step strips `dist` URLs from the generated package metadata to force Composer to use the git source, enabling HTTP basic authentication without requiring provider tokens on the client.
- **Custom git provider support** — self-hosted servers (Gitea, etc.) configurable via `CUSTOM_PROVIDER_DOMAIN`, `CUSTOM_PROVIDER_TOKEN`, and `CUSTOM_PROVIDER_USERNAME`.
- **Rebuild individual repository** action in the repositories table.
- **Local repository** support added as a valid provider type.
- Package icon (`icon-package`) added for build actions.
- MIT License added.

### Changed
- `SatisConfigService` extracted and refactored to handle both config generation and post-processing of built packages.
- Repository form now shows the provider's URL prefix dynamically as the user types.

---

## [1.0.0] - 2025-10-29

### Added
- Initial release.
- Filament-powered admin panel with role-based access (admin / user via Spatie Permissions).
- Repository CRUD: manage GitHub, GitLab, and Bitbucket repositories.
- Global git/composer authentication via environment variables (`GITHUB_TOKEN`, `GITLAB_TOKEN`, `BITBUCKET_KEY` / `BITBUCKET_TOKEN`).
- Queue-based `BuildPackages` job with per-repository and full-build support.
- Artisan command `packages:build` for CLI-driven builds.
- Webhook endpoint (`POST /api/packages/build`) for push-triggered rebuilds.
- OAuth client credential generation per repository for HTTP basic authentication.
- Package serving via `SatisController` with path-traversal protection and Composer user-agent enforcement.
- Multi-factor authentication (TOTP + email) for admin users.
- Job monitoring UI: Jobs, Job Batches, Failed Jobs.
- Log viewer via `leobsst/filament-log-manager`.
- i18n: English and French translations.
- GitHub Actions CI: tests, PHPStan, code style.

[Unreleased]: https://github.com/leobsst/satis-manager/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/leobsst/satis-manager/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/leobsst/satis-manager/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/leobsst/satis-manager/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/leobsst/satis-manager/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/leobsst/satis-manager/releases/tag/v1.0.0
