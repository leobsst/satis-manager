<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SatisConfigService
{
    /**
     * Generate the satis.json configuration file from database repositories
     */
    public static function generateConfig(): string
    {
        /** @var Collection<int, Repository> $repositories */
        $repositories = Repository::with('credential')->get();

        $satisConfig = [
            'name' => config('app.vendor', 'leobsst') . '/packages',
            'homepage' => config('app.url'),
            'repositories' => [],
            'require' => [],
            'output-dir' => 'storage/app/satis',
            'config' => [
                'github-protocols' => ['https', 'ssh'],
                'secure-http' => true,
            ],
        ];

        if (config('satis.archive')) {
            $satisConfig['archive'] = [
                'directory' => 'dist',
                'format' => 'tar',
                'skip-dev' => true,
                'prefix-url' => config('app.url'),
                'checksum' => true,
            ];
        }

        // Build repositories array from database
        foreach ($repositories as $repository) {
            try {
                $fullUrl = $repository->getFullUrl();
            } catch (\RuntimeException $e) {
                Log::channel('satis')->warning("Skipping repository \"{$repository->url}\": {$e->getMessage()}");

                continue;
            }

            $satisConfig['repositories'][] = [
                'type' => 'vcs',
                'url' => $fullUrl,
            ];

            // Add to require section (with wildcard for all versions)
            $satisConfig['require'][$repository->url] = '*';
        }

        if (empty($satisConfig['require'])) {
            $satisConfig['require'] = new \stdClass; // Ensure it's an object in JSON
            Log::channel('satis')
                ->warning('No repositories found to include in satis.json configuration.');
        }

        // Convert to JSON with pretty print
        $jsonContent = json_encode($satisConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Save to file
        $filePath = base_path('satis.json');
        File::put($filePath, $jsonContent);

        Log::channel('satis')->info('Satis configuration file generated', [
            'repositories_count' => $repositories->count(),
            'file_path' => $filePath,
        ]);

        return $filePath;
    }

    /**
     * Post-process packages.json to remove dist URLs when archive is disabled.
     * This forces Composer to use 'source' (git) instead of 'dist' (zip from GitHub)
     * allowing HTTP basic authentication to work without requiring GitHub tokens.
     */
    public static function postProcessPackages(): void
    {
        if (config('satis.archive')) {
            return; // No need to process if archives are enabled
        }

        $satisDir = storage_path('app/satis');
        $processedCount = 0;

        // Process main packages.json if it has direct package entries
        $packagesPath = $satisDir . '/packages.json';
        if (File::exists($packagesPath)) {
            $packages = json_decode(File::get($packagesPath), true);
            if ($packages && isset($packages['packages']) && ! empty($packages['packages'])) {
                $processedCount += self::removeDistFromPackages($packages['packages']);
                File::put($packagesPath, json_encode($packages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }

        // Process include files (include/all$*.json)
        $includePattern = $satisDir . '/include/all$*.json';
        foreach (File::glob($includePattern) as $includeFile) {
            $data = json_decode(File::get($includeFile), true);
            if ($data && isset($data['packages'])) {
                $count = self::removeDistFromPackages($data['packages']);
                if ($count > 0) {
                    File::put($includeFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    $processedCount += $count;
                }
            }
        }

        // Process p2 metadata files (p2/vendor/package.json)
        $p2Dir = $satisDir . '/p2';
        if (File::isDirectory($p2Dir)) {
            $p2Files = File::allFiles($p2Dir);
            foreach ($p2Files as $p2File) {
                if ($p2File->getExtension() !== 'json') {
                    continue;
                }
                $data = json_decode(File::get($p2File->getPathname()), true);
                if ($data && isset($data['packages'])) {
                    $count = self::removeDistFromPackages($data['packages']);
                    if ($count > 0) {
                        File::put($p2File->getPathname(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        $processedCount += $count;
                    }
                }
            }
        }

        Log::channel('satis')->info("Post-processing complete - removed {$processedCount} dist URLs to force source installation");
    }

    /**
     * Post-process satis output to remove excluded branch versions.
     *
     * For each repository with excluded_branches patterns, scans the satis output
     * files and removes any dev versions (dev-{branch}) whose branch name matches
     * a configured glob pattern (e.g. "dependabot/*").
     *
     * The match is done by extracting the repo path (vendor/name) from the package's
     * source URL, which is provider-agnostic and works with SSH and HTTPS remotes.
     */
    public static function postProcessExcludedBranches(): void
    {
        /** @var Collection<int, Repository> $repositories */
        $repositories = Repository::with('credential')
            ->whereNotNull('excluded_branches')
            ->get()
            ->filter(fn (Repository $r): bool => ! empty($r->excluded_branches));

        if ($repositories->isEmpty()) {
            return;
        }

        // Build a lookup: "vendor/repo_name" => [glob_pattern, ...]
        // This is provider-agnostic because we compare against the repo path,
        // not the full URL (so SSH and HTTPS remotes both match).
        /** @var array<string, list<string>> $exclusionMap */
        $exclusionMap = [];
        foreach ($repositories as $repo) {
            $exclusionMap[$repo->url] = $repo->excluded_branches ?? [];
        }

        $satisDir = storage_path('app/satis');
        $removedCount = 0;

        $processFile = function (string $path) use ($exclusionMap, &$removedCount): void {
            $data = json_decode(File::get($path), true);
            if (! $data || ! isset($data['packages'])) {
                return;
            }

            $removed = self::removeExcludedBranchVersions($data['packages'], $exclusionMap);
            if ($removed > 0) {
                File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $removedCount += $removed;
            }
        };

        // packages.json
        $packagesPath = $satisDir . '/packages.json';
        if (File::exists($packagesPath)) {
            $processFile($packagesPath);
        }

        // include/all$*.json
        foreach (File::glob($satisDir . '/include/all$*.json') as $file) {
            $processFile($file);
        }

        // p2/**/*.json
        $p2Dir = $satisDir . '/p2';
        if (File::isDirectory($p2Dir)) {
            foreach (File::allFiles($p2Dir) as $file) {
                if ($file->getExtension() === 'json') {
                    $processFile($file->getPathname());
                }
            }
        }

        Log::channel('satis')->info("Post-processing complete - removed {$removedCount} excluded branch versions from satis output");
    }

    /**
     * Delete all generated satis output files.
     *
     * This forces a full rebuild on next build run. The output directory is
     * recreated empty so the application can still serve an empty packages.json.
     */
    public static function clearAllBuilds(): void
    {
        $satisDir = storage_path('app/satis');

        if (File::isDirectory($satisDir)) {
            File::deleteDirectory($satisDir);
        }

        File::makeDirectory($satisDir, 0755, true);

        Log::channel('satis')->info('All satis build output cleared.');
    }

    /**
     * Remove all package entries attributed to a given repository from the
     * satis output files.
     *
     * The attribution is done by matching the package version's source URL
     * against the repository path (vendor/name), which is provider-agnostic
     * and works with both SSH and HTTPS remotes.
     */
    public static function clearRepositoryPackages(Repository $repository): void
    {
        $satisDir = storage_path('app/satis');
        $removedCount = 0;

        $processFile = function (string $path) use ($repository, &$removedCount): void {
            $data = json_decode(File::get($path), true);
            if (! $data || ! isset($data['packages'])) {
                return;
            }

            $removed = self::removeRepositoryFromPackages($data['packages'], $repository->url);
            if ($removed > 0) {
                File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $removedCount += $removed;
            }
        };

        $packagesPath = $satisDir . '/packages.json';
        if (File::exists($packagesPath)) {
            $processFile($packagesPath);
        }

        foreach (File::glob($satisDir . '/include/all$*.json') as $file) {
            $processFile($file);
        }

        $p2Dir = $satisDir . '/p2';
        if (File::isDirectory($p2Dir)) {
            foreach (File::allFiles($p2Dir) as $file) {
                if ($file->getExtension() === 'json') {
                    $processFile($file->getPathname());
                }
            }
        }

        Log::channel('satis')->info("Cleared {$removedCount} package versions for repository {$repository->url}.");
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Remove all versions of packages that originate from a given repository.
     * Packages with no remaining versions are removed entirely.
     *
     * @return int Number of versions removed
     */
    private static function removeRepositoryFromPackages(array &$packages, string $repoPath): int
    {
        $count = 0;

        foreach (array_keys($packages) as $packageName) {
            if (! \is_array($packages[$packageName])) {
                continue;
            }

            foreach (array_keys($packages[$packageName]) as $version) {
                $sourceUrl = $packages[$packageName][$version]['source']['url'] ?? null;

                if ($sourceUrl && self::extractRepoPath($sourceUrl) === $repoPath) {
                    unset($packages[$packageName][$version]);
                    $count++;
                }
            }

            if (empty($packages[$packageName])) {
                unset($packages[$packageName]);
            }
        }

        return $count;
    }

    /**
     * Remove dist entries from package data array.
     *
     * @return int Number of dist entries removed
     */
    private static function removeDistFromPackages(array &$packages): int
    {
        $count = 0;

        foreach ($packages as $packageName => &$versions) {
            if (! \is_array($versions)) {
                continue;
            }

            foreach ($versions as $version => &$packageData) {
                if (isset($packageData['dist'])) {
                    unset($packageData['dist']);
                    $count++;
                    Log::channel('satis')->debug("Removed dist URL for {$packageName}:{$version}");
                }
            }
        }

        return $count;
    }

    /**
     * Remove versions whose branch name (extracted from the version string and source URL)
     * matches one of the excluded glob patterns for the corresponding repository.
     *
     * @param  array<string, list<string>>  $exclusionMap  repo_path => [glob_patterns]
     * @return int Number of versions removed
     */
    private static function removeExcludedBranchVersions(array &$packages, array $exclusionMap): int
    {
        $count = 0;

        foreach ($packages as $packageName => &$versions) {
            if (! \is_array($versions)) {
                continue;
            }

            foreach (array_keys($versions) as $version) {
                // Only dev (branch) versions are candidates
                if (! \str_starts_with((string) $version, 'dev-')) {
                    continue;
                }

                $packageData = $versions[$version];
                $sourceUrl = $packageData['source']['url'] ?? null;

                if (! $sourceUrl) {
                    continue;
                }

                $repoPath = self::extractRepoPath($sourceUrl);
                $patterns = $exclusionMap[$repoPath] ?? null;

                if (empty($patterns)) {
                    continue;
                }

                $branch = \substr((string) $version, 4); // strip "dev-" prefix

                foreach ($patterns as $pattern) {
                    if (\fnmatch($pattern, $branch)) {
                        unset($versions[$version]);
                        $count++;
                        Log::channel('satis')->debug("Removed excluded branch version {$packageName}:{$version} (pattern: {$pattern})");

                        break;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Extract the "vendor/repo" path from a git remote URL.
     *
     * Handles both SSH and HTTPS formats:
     *   - git@github.com:vendor/repo.git  → vendor/repo
     *   - https://github.com/vendor/repo.git → vendor/repo
     */
    private static function extractRepoPath(string $url): string
    {
        $url = \rtrim($url, '/');
        $url = \preg_replace('/\.git$/', '', $url) ?? $url;

        if (\str_contains($url, ':') && ! \str_starts_with($url, 'http')) {
            // SSH format: git@host:vendor/repo
            [, $path] = \explode(':', $url, 2);

            return \ltrim($path, '/');
        }

        // HTTPS format: https://host/vendor/repo
        $parsed = \parse_url($url);

        return \ltrim($parsed['path'] ?? '', '/');
    }
}
