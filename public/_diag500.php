<?php

/**
 * Diagnostic profond : démarre Laravel et capture l'exception exacte
 * qui provoque le 500, avec fichier et ligne.
 *
 * ⚠️ SUPPRIMER CE FICHIER dès le problème résolu.
 *
 * Accès : /_diag500.php?cle=VOTRE_CLE
 */

declare(strict_types=1);

$racine = dirname(__DIR__);
$envPath = $racine.'/.env';

if (! is_file($envPath)) {
    http_response_code(500);
    exit('.env introuvable.');
}

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
    $ligne = trim($ligne);
    if ($ligne === '' || str_starts_with($ligne, '#') || ! str_contains($ligne, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $ligne, 2);
    $env[trim($k)] = trim(trim($v), '"\'');
}

$attendu = $env['ARENE_MAINTENANCE_KEY'] ?? '';
$fourni = $_GET['cle'] ?? '';

if ($attendu === '' || ! hash_equals($attendu, (string) $fourni)) {
    http_response_code(403);
    exit('Cle invalide.');
}

$chemin = $_GET['url'] ?? '/';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTIC 500 ===\n";
echo "URL testee : {$chemin}\n\n";

try {
    echo "[1] Chargement de l'autoloader...\n";
    require $racine.'/vendor/autoload.php';

    echo "[2] Bootstrap de l'application...\n";
    $app = require $racine.'/bootstrap/app.php';

    echo "[3] Demarrage du kernel HTTP...\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "[4] Construction de la requete...\n";
    $request = Illuminate\Http\Request::create($chemin, 'GET');

    echo "[5] Traitement de la requete...\n\n";
    $response = $kernel->handle($request);

    echo "\n=== RESULTAT ===\n";
    echo 'Statut HTTP : '.$response->getStatusCode()."\n";
    echo 'Taille      : '.strlen($response->getContent())." octets\n";

    if ($response->getStatusCode() >= 500) {
        echo "\nUn 500 a ete renvoye (gere par le Handler, donc non leve).\n\n";

        // Extraire les dernieres entrees du log
        $logPath = $racine.'/storage/logs/laravel.log';
        if (is_file($logPath)) {
            $lignes = @file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            $erreurs = array_values(array_filter($lignes, fn ($l) => str_contains($l, '.ERROR')));

            echo "=== DERNIERES ERREURS DU LOG ===\n";
            echo implode("\n", array_slice($erreurs, -5))."\n\n";

            // Le fichier et la ligne sont dans la premiere ligne d'erreur
            $derniere = end($erreurs);
            if ($derniere && preg_match('#at (/[^:]+):(\d+)#', $derniere, $m)) {
                echo "Emplacement : {$m[1]}:{$m[2]}\n";
            }
        } else {
            echo "Pas de laravel.log.\n";
        }
    } else {
        echo "\nLe rendu a reussi. Aucun 500 sur cette URL.\n";
    }
} catch (Throwable $e) {
    echo "\n=== EXCEPTION CAPTUREE ===\n\n";
    echo 'Classe  : '.get_class($e)."\n";
    echo 'Message : '.$e->getMessage()."\n";
    echo 'Fichier : '.$e->getFile().':'.$e->getLine()."\n\n";

    // Cause racine si encapsulee
    $prec = $e->getPrevious();
    $n = 1;
    while ($prec !== null && $n <= 4) {
        echo "--- Cause anterieure #{$n} ---\n";
        echo 'Classe  : '.get_class($prec)."\n";
        echo 'Message : '.$prec->getMessage()."\n";
        echo 'Fichier : '.$prec->getFile().':'.$prec->getLine()."\n\n";
        $prec = $prec->getPrevious();
        $n++;
    }

    echo "=== TRACE (10 premieres lignes) ===\n";
    $lignes = explode("\n", $e->getTraceAsString());
    echo implode("\n", array_slice($lignes, 0, 10))."\n";
}
