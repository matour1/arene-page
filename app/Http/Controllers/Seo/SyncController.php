<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Services\GameHub\TournoiSynchronizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Point d'entrée HTTP pour un déclencheur externe (cron-job.org, UptimeRobot…).
 *
 * Utilité : les hébergements mutualisés sans accès `crontab` ne peuvent pas
 * exécuter `schedule:run`. Un service externe appelle cette URL périodiquement.
 *
 * Sécurité : jeton en paramètre, comparé avec hash_equals.
 * Le quota de l'API GameHub est préservé par un verrou anti-chevauchement.
 */
class SyncController extends Controller
{
    public function __invoke(Request $request, TournoiSynchronizer $synchronizer): JsonResponse
    {
        $attendu = (string) config('arene.sync.jeton');

        if ($attendu === '') {
            return response()->json([
                'erreur' => 'ARENE_SYNC_TOKEN non defini dans .env',
            ], 503);
        }

        if (! hash_equals($attendu, (string) $request->query('token', ''))) {
            return response()->json(['erreur' => 'Jeton invalide'], 403);
        }

        // Verrou : deux appels rapprochés ne doivent pas consommer deux fois
        // le quota mensuel de l'API GameHub.
        $verrou = cache()->add('sync.tournois.verrou', true, now()->addMinutes(2));

        if (! $verrou) {
            return response()->json([
                'statut' => 'ignore',
                'raison' => 'Synchronisation deja en cours ou recente',
            ], 200);
        }

        try {
            $resultat = $synchronizer->sync();

            return response()->json([
                'statut' => $resultat['arrete'] ? 'interrompu' : 'ok',
                'importes' => $resultat['importes'],
                'pages' => $resultat['pages'],
                'horodatage' => now()->toIso8601String(),
            ], $resultat['arrete'] ? 502 : 200);
        } finally {
            cache()->forget('sync.tournois.verrou');
        }
    }
}
