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

        // Vérifier que le fichier existe et est un fichier JSON
        if (! file_exists($realPath) || ! is_file($realPath)) {
            abort(404);
        }

        // Vérifier que c'est bien un fichier JSON
        $extension = pathinfo($realPath, PATHINFO_EXTENSION);
        if ($extension !== 'json') {
            abort(404);
        }

        return new Response(file_get_contents($realPath), 200, [
            'Content-Type' => 'application/json',
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
