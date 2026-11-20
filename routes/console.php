<?php

use App\Console\Commands\SyncTournois;
use Illuminate\Support\Facades\Schedule;

/*
 * Quota GameHub Arena (plan gratuit) : 1 000 requetes par mois.
 * Une execution consomme de 1 a 3 requetes (voir arene.sync.pages_max).
 * Toutes les heures = 24 executions/jour = environ 720 requetes/mois,
 * ce qui laisse une marge pour les synchronisations manuelles.
 *
 * Sur LWS, le cron systeme doit appeler `php artisan schedule:run` chaque minute.
 */
$intervalle = max(5, (int) config('arene.sync.intervalle_minutes', 60));

$planification = in_array($intervalle, [10, 15, 20, 30], true)
    ? Schedule::command(SyncTournois::class)->cron("*/{$intervalle} * * * *")
    : Schedule::command(SyncTournois::class)->hourly();

$planification->withoutOverlapping()->onOneServer();