<?php

namespace App\Services;

use App\Models\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SatisConfigService
{
    /**
     * Generate the satis.json configuration file from database repositories
     */
    public static function generateConfig(): string
    {
        $repositories = Repository::all();

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
            $satisConfig['repositories'][] = [
                'type' => 'vcs',
                'url' => $repository->getFullUrl(),
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
     * Post-process packages.json to remove dist URLs when archive is disabled
     * This forces Composer to use 'source' (git) instead of 'dist' (zip from GitHub)
     * allowing HTTP basic authentication to work without requiring GitHub tokens
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
     * Remove dist entries from package data array
     *
     * @return int Number of dist entries removed
     */
    private static function removeDistFromPackages(array &$packages): int
    {
        $count = 0;

        foreach ($packages as $packageName => &$versions) {
            if (! is_array($versions)) {
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
}
