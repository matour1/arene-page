<?php

namespace App\Console\Commands;

use App\Services\GameHub\TournoiSynchronizer;
use Illuminate\Console\Command;

class SyncTournois extends Command
{
    protected $signature = 'tournois:sync {--pages= : Nombre maximum de pages à récupérer}';

    protected $description = 'Synchronise les tournois GameHub Arena vers la base locale';

    public function handle(TournoiSynchronizer $synchronizer): int
    {
        $pages = $this->option('pages');

        $resultat = $synchronizer->sync($pages !== null ? (int) $pages : null);

        if ($resultat['arrete']) {
            $this->warn("Synchronisation interrompue après {$resultat['pages']} page(s) : API indisponible, token absent ou quota atteint.");

            return self::FAILURE;
        }

        $this->info("{$resultat['importes']} tournoi(s) synchronisé(s) sur {$resultat['pages']} page(s).");

        return self::SUCCESS;
    }
}
