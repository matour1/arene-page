<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournoi;
use App\Services\GameHub\TournoiSynchronizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournoiAdminController extends Controller
{
    public function index(): View
    {
        $tournois = Tournoi::query()
            ->orderByRaw('debut_at IS NULL')
            ->orderBy('debut_at')
            ->paginate(25);

        return view('admin.tournois.index', [
            'tournois' => $tournois,
            'derniereSync' => Tournoi::max('synchronise_at'),
            'nbAvecLien' => Tournoi::whereNotNull('lien_affiliation')->count(),
            'nbTotal' => Tournoi::count(),
        ]);
    }

    public function edit(Tournoi $tournoi): View
    {
        return view('admin.tournois.edit', compact('tournoi'));
    }

    public function update(Request $request, Tournoi $tournoi)
    {
        $donnees = $request->validate([
            'lien_affiliation' => ['nullable', 'url', 'max:500'],
        ], [
            'lien_affiliation.url' => 'Le lien doit être une URL complète (https://...).',
        ]);

        $tournoi->update($donnees);

        return redirect()
            ->route('admin.tournois.index')
            ->with('statut', "Lien d'affiliation enregistré pour « {$tournoi->titre} ».");
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