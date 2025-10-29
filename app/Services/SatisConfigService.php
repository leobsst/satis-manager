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
}
