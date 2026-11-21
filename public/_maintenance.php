<?php

/**
 * Page de maintenance destinée aux hébergements SANS SSH ni cron.
 *
 * À utiliser depuis un navigateur, protégée par un jeton.
 *
 * ⚠️ SUPPRIMER CE FICHIER après usage.
 *
 * Accès : /_maintenance.php?cle=VOTRE_CLE
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Chargement minimal du .env (sans Laravel, car le cache peut être cassé)
// ---------------------------------------------------------------------------
$racine = dirname(__DIR__);
$envPath = $racine.'/.env';

if (! is_file($envPath)) {
    http_response_code(500);
    exit('Fichier .env introuvable à la racine du projet.');
}

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
    $ligne = trim($ligne);

    if ($ligne === '' || str_starts_with($ligne, '#') || ! str_contains($ligne, '=')) {
        continue;
    }

    [$cle, $valeur] = explode('=', $ligne, 2);
    $valeur = trim($valeur);
    $valeur = trim($valeur, '"\'');
    $env[trim($cle)] = $valeur;
}

// ---------------------------------------------------------------------------
// Authentification : jeton en paramètre ou en en-tête
// ---------------------------------------------------------------------------
$jetonAttendu = $env['ARENE_MAINTENANCE_KEY'] ?? '';
$jetonFourni = $_GET['cle'] ?? $_SERVER['HTTP_X_MAINTENANCE_KEY'] ?? '';

// Si aucun jeton n'est configuré, on refuse tout : pas de page ouverte par défaut.
if ($jetonAttendu === '') {
    http_response_code(403);
    exit('Aucun ARENE_MAINTENANCE_KEY defini dans .env. Ajoutez-en une, puis rechargez.');
}

if (! hash_equals($jetonAttendu, (string) $jetonFourni)) {
    http_response_code(403);
    exit('Cle invalide.');
}

// ---------------------------------------------------------------------------
// Actions
// ---------------------------------------------------------------------------
$action = $_GET['action'] ?? '';
$resultats = [];

/** Exécute une commande artisan et capture sa sortie. */
function artisan(string $racine, string $commande): array
{
    $php = PHP_BINARY;
    $cmd = sprintf('%s %s %s 2>&1', escapeshellarg($php), escapeshellarg($racine.'/artisan'), $commande);

    $sortie = [];
    $code = 0;
    exec($cmd, $sortie, $code);

    return ['code' => $code, 'sortie' => implode("\n", $sortie)];
}

if ($action !== '') {
    $autorisees = [
        'clear' => ['config:clear', 'route:clear', 'view:clear', 'cache:clear'],
        'optimize' => ['config:cache', 'route:cache', 'view:cache'],
        'sync' => ['tournois:sync'],
    ];

    if (! isset($autorisees[$action])) {
        $resultats[] = ['commande' => "(inconnu) $action", 'code' => 1, 'sortie' => 'Action non reconnue.'];
    } else {
        foreach ($autorisees[$action] as $commande) {
            $resultats[] = ['commande' => $commande] + artisan($racine, $commande);
        }
    }
}

// ---------------------------------------------------------------------------
// État courant
// ---------------------------------------------------------------------------
$cheminAdmin = trim((string) ($env['ARENE_ADMIN_PATH'] ?? ''), '/');
$cheminAdmin = $cheminAdmin === '' ? 'admin' : $cheminAdmin;

$cacheConfig = is_file($racine.'/bootstrap/cache/config.php');
$cacheRoutes = is_file($racine.'/bootstrap/cache/routes-v7.php');

// Routes réellement servies : on lit le cache s'il existe, sinon on ne peut
// pas lister sans Laravel — on indique alors la valeur attendue.
$urlAdmin = rtrim((string) ($env['APP_URL'] ?? ''), '/').'/'.$cheminAdmin.'/connexion';

$dbOk = null;
$dbErreur = '';
$pilote = $env['DB_CONNECTION'] ?? 'sqlite';

try {
    if ($pilote === 'sqlite') {
        $fichier = $env['DB_DATABASE'] ?: $racine.'/database/database.sqlite';
        $pdo = new PDO('sqlite:'.$fichier, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10,
        ]);
    } else {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
            $env['DB_HOST'] ?? 'localhost',
            $env['DB_PORT'] ?? '5432',
            $env['DB_DATABASE'] ?? '',
            $env['DB_SSLMODE'] ?? 'prefer',
        );
        $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? '', $env['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10,
        ]);
    }

    $dbOk = (bool) $pdo->query('select 1')->fetchColumn();
} catch (Throwable $e) {
    $dbOk = false;
    $dbErreur = $e->getMessage();
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance — Arène des Gamers</title>
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body {
            font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace;
            background: #0a0c10; color: #f8fafc;
            margin: 0; padding: 24px; line-height: 1.6;
        }
        .wrap { max-width: 900px; margin: 0 auto; }
        h1 { font-size: 1.1rem; letter-spacing: .1em; text-transform: uppercase; color: #00f0ff; }
        h2 { font-size: .8rem; letter-spacing: .12em; text-transform: uppercase; color: #94a3b8; margin-top: 28px; }
        .card { background: #12151e; border: 1px solid #1e2433; border-radius: 10px; padding: 16px; margin: 10px 0; }
        .ok { color: #3ddc97; }
        .ko { color: #ff5c5c; }
        .mut { color: #94a3b8; }
        code, .val { color: #facc15; word-break: break-all; }
        a.btn {
            display: inline-block; margin: 4px 6px 4px 0; padding: 8px 14px;
            border: 1px solid #00f0ff; border-radius: 6px;
            color: #00f0ff; text-decoration: none; font-size: .85rem;
        }
        a.btn:hover { background: rgb(0 240 255 / .1); }
        a.danger { border-color: #ff5c5c; color: #ff5c5c; }
        a.danger:hover { background: rgb(255 92 92 / .1); }
        pre {
            background: #0a0c10; border: 1px solid #1e2433; border-radius: 6px;
            padding: 12px; overflow-x: auto; font-size: .78rem; white-space: pre-wrap;
        }
        .alerte {
            background: rgb(245 158 11 / .08); border: 1px solid rgb(245 158 11 / .35);
            border-radius: 8px; padding: 12px 14px; color: #fcd34d; margin: 16px 0;
        }
    </style>
</head>
<body>
<div class="wrap">

    <h1>Maintenance — Arène des Gamers</h1>

    <div class="alerte">
        Supprimez ce fichier (<code>_maintenance.php</code>) une fois le problème résolu.
    </div>

    <h2>État</h2>
    <div class="card">
        <p>
            Connexion base :
            <?php if ($dbOk === true): ?>
                <span class="ok">OK</span>
            <?php else: ?>
                <span class="ko">ÉCHEC</span>
                <?php if ($dbErreur !== ''): ?>
                    <br><span class="mut" style="font-size:.78rem"><?= htmlspecialchars($dbErreur) ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </p>
        <p>
            Cache de configuration :
            <?= $cacheConfig ? '<span class="ok">actif</span>' : '<span class="mut">absent</span>' ?>
        </p>
        <p>
            Cache de routes :
            <?= $cacheRoutes ? '<span class="ok">actif</span>' : '<span class="mut">absent</span>' ?>
        </p>
    </div>

    <h2>Accès au back-office</h2>
    <div class="card">
        <p>
            <span class="mut">ARENE_ADMIN_PATH dans .env :</span>
            <span class="val"><?= htmlspecialchars($env['ARENE_ADMIN_PATH'] ?? '(absent)') ?></span>
        </p>
        <p>
            <span class="mut">Chemin effectif :</span>
            <code>/<?= htmlspecialchars($cheminAdmin) ?></code>
        </p>
        <p>
            <a class="btn" href="/<?= htmlspecialchars($cheminAdmin) ?>/connexion" target="_blank">
                Ouvrir /<?= htmlspecialchars($cheminAdmin) ?>/connexion
            </a>
            <a class="btn" href="/admin/connexion" target="_blank">Tester /admin/connexion</a>
        </p>
        <?php if ($cacheRoutes): ?>
            <p class="mut" style="font-size:.8rem">
                Le cache de routes est actif : si vous avez changé ARENE_ADMIN_PATH après
                l'avoir généré, les anciennes routes sont encore servies. Utilisez
                « Vider les caches » ci-dessous.
            </p>
        <?php endif; ?>
    </div>

    <h2>Actions</h2>
    <div class="card">
        <a class="btn" href="?cle=<?= urlencode($jetonAttendu) ?>&action=clear">Vider les caches</a>
        <a class="btn" href="?cle=<?= urlencode($jetonAttendu) ?>&action=optimize">Régénérer les caches</a>
        <a class="btn" href="?cle=<?= urlencode($jetonAttendu) ?>&action=sync">Synchroniser les tournois</a>
    </div>

    <?php if ($resultats !== []): ?>
        <h2>Résultat</h2>
        <?php foreach ($resultats as $r): ?>
            <div class="card">
                <p>
                    <code>$ php artisan <?= htmlspecialchars($r['commande']) ?></code>
                    <span class="<?= $r['code'] === 0 ? 'ok' : 'ko' ?>">
                        [<?= $r['code'] === 0 ? 'OK' : 'code '.$r['code'] ?>]
                    </span>
                </p>
                <pre><?= htmlspecialchars($r['sortie'] ?: '(aucune sortie)') ?></pre>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
</html>
