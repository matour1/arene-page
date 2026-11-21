<?php

/**
 * Diagnostic étendu pour hébergement sans SSH confortable.
 *
 * ⚠️ SUPPRIMER CE FICHIER après résolution.
 *
 * Accès : /_diag.php?cle=VOTRE_CLE
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

// ---------------------------------------------------------------------------
// Collecte
// ---------------------------------------------------------------------------
$rapport = [];

// 1. Serveur
$rapport['PHP CLI'] = PHP_VERSION;
$rapport['SAPI'] = PHP_SAPI;
$rapport['pdo_pgsql'] = extension_loaded('pdo_pgsql') ? 'chargee' : 'ABSENTE';
$rapport['pgsql'] = extension_loaded('pgsql') ? 'chargee' : 'absente';
$rapport['opcache'] = function_exists('opcache_get_status') && @opcache_get_status() !== false ? 'actif' : 'inactif';
$rapport['mod_rewrite (Apache)'] = function_exists('apache_get_modules')
    ? (in_array('mod_rewrite', apache_get_modules(), true) ? 'actif' : 'INACTIF')
    : 'inconnu (PHP-FPM)';

// 2. Fichiers
$rapport['public/index.php'] = is_file($racine.'/public/index.php') ? 'present' : 'ABSENT';
$rapport['public/.htaccess'] = is_file($racine.'/public/.htaccess') ? 'present' : 'ABSENT';
$rapport['racine .htaccess'] = is_file($racine.'/.htaccess') ? 'present' : 'absent';
$rapport['bootstrap/cache/'] = is_writable($racine.'/bootstrap/cache') ? 'inscriptible' : 'NON INSCRIPTIBLE';
$rapport['storage/'] = is_writable($racine.'/storage') ? 'inscriptible' : 'NON INSCRIPTIBLE';

// 3. Environnement
$rapport['APP_ENV'] = $env['APP_ENV'] ?? '(absent)';
$rapport['APP_DEBUG'] = $env['APP_DEBUG'] ?? '(absent)';
$rapport['APP_URL'] = $env['APP_URL'] ?? '(absent)';
$rapport['APP_KEY'] = ($env['APP_KEY'] ?? '') !== '' ? 'definie' : 'VIDE';
$rapport['DB_CONNECTION'] = $env['DB_CONNECTION'] ?? '(absent)';
$rapport['ARENE_ADMIN_PATH'] = $env['ARENE_ADMIN_PATH'] ?? '(absent)';

// 4. Routes connues de la base (si connexion possible)
$routes = [];
$dbErreur = '';
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
        $env['DB_HOST'] ?? 'localhost',
        $env['DB_PORT'] ?? '5432',
        $env['DB_DATABASE'] ?? '',
        $env['DB_SSLMODE'] ?? 'prefer',
    );
    $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? '', $env['DB_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 15,
    ]);
    $routes['connexion base'] = 'OK';
    $routes['articles'] = $pdo->query('select count(*) from articles')->fetchColumn();
    $routes['tournois'] = $pdo->query('select count(*) from tournois')->fetchColumn();
    $routes['users'] = $pdo->query('select count(*) from users')->fetchColumn();
} catch (Throwable $e) {
    $routes['connexion base'] = 'ECHEC';
    $dbErreur = $e->getMessage();
}

// 5. Sessions : test reel d'ecriture
$sessionOk = 'inconnu';
try {
    $pdoSess = $pdo ?? null;
    if ($pdoSess) {
        $pdoSess->exec("insert into sessions (id, payload, last_activity) values ('diag-probe', 'x', 0) on conflict (id) do nothing");
        $pdoSess->exec("delete from sessions where id = 'diag-probe'");
        $sessionOk = 'table sessions accessible';
    }
} catch (Throwable $e) {
    $sessionOk = 'ECHEC : '.substr($e->getMessage(), 0, 100);
}

// 6. Log Laravel
$logPath = $racine.'/storage/logs/laravel.log';
$logLignes = [];
if (is_file($logPath)) {
    $lignes = @file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $utiles = array_values(array_filter($lignes, function ($l) {
        return preg_match('/^\d{4}-\d{2}-\d{2}|SQLSTATE|Exception|Undefined|not found|Class .* not found|No application/i', $l);
    }));
    $logLignes = array_slice($utiles, -30);
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Diagnostic — Arène des Gamers</title>
<style>
    :root{color-scheme:dark}
    *{box-sizing:border-box}
    body{font-family:ui-monospace,Menlo,Consolas,monospace;background:#0a0c10;color:#f8fafc;margin:0;padding:20px;line-height:1.55}
    .w{max-width:920px;margin:0 auto}
    h1{font-size:1rem;letter-spacing:.1em;text-transform:uppercase;color:#00f0ff;margin:0 0 4px}
    h2{font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:#94a3b8;margin:26px 0 8px}
    .c{background:#12151e;border:1px solid #1e2433;border-radius:8px;padding:14px;margin:8px 0}
    table{width:100%;border-collapse:collapse;font-size:.82rem}
    td{padding:5px 0;vertical-align:top;border-bottom:1px solid #1a1e2b}
    td:first-child{color:#8b93a3;width:44%}
    .ok{color:#3ddc97}.ko{color:#ff5c5c}.warn{color:#facc15}.mut{color:#5d6675}
    pre{background:#0a0c10;border:1px solid #1e2433;border-radius:6px;padding:12px;overflow-x:auto;font-size:.74rem;white-space:pre-wrap;margin:0}
    .alerte{background:rgb(245 158 11/.08);border:1px solid rgb(245 158 11/.35);border-radius:8px;padding:11px 13px;color:#fcd34d;margin:14px 0;font-size:.82rem}
</style>
</head>
<body><div class="w">
<h1>Diagnostic — Arène des Gamers</h1>
<div class="alerte">Supprimez <code>_diag.php</code> et <code>_maintenance.php</code> une fois le déploiement terminé.</div>

<h2>Serveur</h2>
<div class="c"><table>
<?php foreach ($rapport as $k => $v):
    $classe = 'mut';
    if (in_array($v, ['ABSENTE','ABSENT','INACTIF','NON INSCRIPTIBLE','VIDE'], true)) $classe = 'ko';
    elseif (in_array($v, ['chargee','present','inscriptible','actif','definie','OK'], true)) $classe = 'ok';
?>
<tr><td><?= htmlspecialchars((string) $k) ?></td><td class="<?= $classe ?>"><?= htmlspecialchars((string) $v) ?></td></tr>
<?php endforeach; ?>
</table></div>

<h2>Base de données</h2>
<div class="c"><table>
<?php foreach ($routes as $k => $v): ?>
<tr><td><?= htmlspecialchars((string) $k) ?></td>
    <td class="<?= $v === 'OK' || is_numeric($v) ? 'ok' : 'ko' ?>"><?= htmlspecialchars((string) $v) ?></td></tr>
<?php endforeach; ?>
<tr><td>table sessions</td><td class="<?= str_contains($sessionOk, 'accessible') ? 'ok' : 'ko' ?>"><?= htmlspecialchars($sessionOk) ?></td></tr>
</table>
<?php if ($dbErreur !== ''): ?>
<pre><?= htmlspecialchars($dbErreur) ?></pre>
<?php endif; ?>
</div>

<h2>URLs à tester</h2>
<div class="c" style="font-size:.82rem">
    <?php $base = rtrim($env['APP_URL'] ?? '', '/'); $admin = $env['ARENE_ADMIN_PATH'] ?: 'admin'; ?>
    <p><a style="color:#00f0ff" href="<?= htmlspecialchars($base) ?>/" target="_blank">Accueil</a></p>
    <p><a style="color:#00f0ff" href="<?= htmlspecialchars($base) ?>/tournois" target="_blank">/tournois</a></p>
    <p><a style="color:#00f0ff" href="<?= htmlspecialchars($base.'/'.$admin) ?>/connexion" target="_blank">/<?= htmlspecialchars($admin) ?>/connexion</a></p>
</div>

<h2>Dernières erreurs Laravel</h2>
<div class="c">
<?php if ($logLignes === []): ?>
    <p class="mut"><?= is_file($logPath) ? 'Log sans erreur exploitable.' : 'Aucun laravel.log.' ?></p>
<?php else: ?>
    <pre><?= htmlspecialchars(implode("\n", $logLignes)) ?></pre>
<?php endif; ?>
</div>

</div></body>
</html>
