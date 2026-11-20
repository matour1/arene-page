<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameHub\TournoiSynchronizer;
use Illuminate\View\View;

class TournoiAdminController extends Controller
{
    public function index(): View
    {
        $tournois = \App\Models\Tournoi::query()
            ->orderByRaw('debut_at IS NULL')
            ->orderBy('debut_at')
            ->paginate(25);

        return view('admin.tournois.index', [
            'tournois' => $tournois,
            'derniereSync' => \App\Models\Tournoi::max('synchronise_at'),
        ]);
    }

    public function sync(TournoiSynchronizer $synchronizer)
    {
        $resultat = $synchronizer->sync();

        return back()->with(
            $resultat['arrete'] ? 'erreur' : 'statut',
            $resultat['arrete']
                ? 'Synchronisation interrompue : API indisponible, token absent ou quota atteint.'
                : "{$resultat['importes']} tournoi(s) synchronisé(s)."
        );
    }
}
