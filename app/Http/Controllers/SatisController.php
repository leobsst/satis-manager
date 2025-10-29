<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SatisController extends Controller
{
    public function packages($path)
    {
        $basePath = storage_path('app/satis');

        // Construire le chemin complet du fichier
        $filePath = "$basePath/$path";

        // Sécurité : Vérifier que le chemin ne sort pas du dossier satis
        $realPath = realpath($filePath);
        $realBasePath = realpath($basePath);

        if ($realPath === false || strpos($realPath, $realBasePath) !== 0) {
            abort(404);
        }

        // Vérifier que le fichier existe et est un fichier
        if (! file_exists($realPath) || ! is_file($realPath)) {
            abort(404);
        }

        // Vérifier que c'est bien un fichier JSON ou TAR
        $extension = pathinfo($realPath, PATHINFO_EXTENSION);
        if (! in_array($extension, ['json', 'tar'])) {
            abort(404);
        }

        // Vérifier le User-Agent pour tous les fichiers sauf packages.json
        $fileName = basename($realPath);
        if ($fileName !== 'packages.json') {
            $userAgent = request()->userAgent();
            if (! $userAgent || ! str_contains(strtolower($userAgent), 'composer')) {
                abort(403, 'Access denied. Composer User-Agent required.');
            }
        }

        // Déterminer le Content-Type en fonction de l'extension
        $contentType = match ($extension) {
            'json' => 'application/json',
            'tar' => 'application/x-tar',
            default => 'application/octet-stream',
        };

        return new Response(file_get_contents($realPath), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function show()
    {
        return new Response(file_get_contents(storage_path('app/satis/index.html')), 200, [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
