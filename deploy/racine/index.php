<?php

/**
 * Contrôleur frontal pour hébergement mutualisé (LWS).
 *
 * À placer à la RACINE du domaine, aux côtés de index.php et de l'ancien
 * .htaccess LWS, quand le document root ne peut pas être changé.
 *
 * Rôle : servir les fichiers de arene-page/public/ s'ils existent, puis
 * déléguer à Laravel. Aucune directive mod_rewrite n'est nécessaire.
 *
 * Supprimez ce commentaire d'installation une fois en place.
 */

declare(strict_types=1);

$public = __DIR__.'/arene-page/public';

// --- 1. Servir un fichier statique existant (css, js, images, fonts) -------
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);

// Interdire toute remontée de répertoire.
if (str_contains($uri, '..')) {
    http_response_code(400);
    exit('Requête invalide.');
}

$chemin = realpath($public.$uri);

if ($chemin !== false && is_file($chemin) && str_starts_with($chemin, realpath($public))) {
    $ext = strtolower(pathinfo($chemin, PATHINFO_EXTENSION));

    // Scripts de diagnostic (préfixe _) : exécutés, pour pouvoir analyser
    // une erreur sans accès SSH. À supprimer après résolution.
    if ($ext === 'php' && str_starts_with(basename($chemin), '_')) {
        chdir(dirname($chemin));
        require $chemin;
        exit;
    }

    if ($ext !== 'php') {
        $types = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'text/javascript; charset=UTF-8',
            'mjs' => 'text/javascript; charset=UTF-8',
            'json' => 'application/json',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'txt' => 'text/plain; charset=UTF-8',
            'xml' => 'application/xml; charset=UTF-8',
        ];

        $ext = strtolower(pathinfo($chemin, PATHINFO_EXTENSION));
        header('Content-Type: '.($types[$ext] ?? 'application/octet-stream'));
        header('Content-Length: '.filesize($chemin));
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($chemin);
        exit;
    }
}

// --- 2. Déléguer toute autre requête à Laravel ----------------------------
$index = $public.'/index.php';

if (! is_file($index)) {
    http_response_code(500);
    exit('index.php introuvable dans arene-page/public.');
}

// Laravel résout les chemins depuis public/, pas depuis la racine du domaine.
chdir($public);

require $index;
