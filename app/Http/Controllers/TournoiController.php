<?php

namespace App\Http\Controllers;

use App\Models\Tournoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TournoiController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = [
            'jeu' => $request->string('jeu')->toString() ?: null,
            'statut' => $request->string('statut')->toString() ?: null,
            'gratuit' => $request->boolean('gratuit'),
        ];

        $tournois = Tournoi::query()
            ->affiche()
            ->aVenir()
            ->when($filtres['jeu'], fn ($q, $v) => $q->where('jeu', $v))
            ->when($filtres['statut'], fn ($q, $v) => $q->where('statut', $v))
            ->when($filtres['gratuit'], fn ($q) => $q->where('payant', false))
            ->paginate(12)
            ->withQueryString();

        // Liste de chaînes : sérialisable sans risque, et rarement modifiée.
        $jeux = Cache::remember('tournois.jeux', now()->addMinutes(30), fn () => Tournoi::query()
            ->affiche()
            ->whereNotNull('jeu')
            ->distinct()
            ->orderBy('jeu')
            ->pluck('jeu')
            ->all());

        return view('tournois.index', compact('tournois', 'jeux', 'filtres'));
    }

    public function show(string $tournoi): View
    {
        $tournoi = Tournoi::findOrFail($tournoi);

        abort_unless($tournoi->visibilite === 'public', 404);

        return view('tournois.show', compact('tournoi'));
    }
}
