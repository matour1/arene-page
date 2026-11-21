<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ProfilLft;
use App\Models\Tournoi;
use App\Services\Seo\Metadonnees;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private Metadonnees $seo) {}

    public function index(): View
    {
        $prochainsTournois = Tournoi::query()
            ->affiche()
            ->aVenir()
            ->limit(3)
            ->get();

        $derniersArticles = Article::query()
            ->publies()
            ->limit(3)
            ->get();

        $profilLft = (int) Cache::remember('accueil.lft', now()->addMinutes(10), fn () => ProfilLft::query()
            ->valides()
            ->where('disponible', true)
            ->count());

        $this->seo
            ->titre(null)
            ->description("Calendrier des tournois e-sport mobiles d'Afrique centrale et de l'Ouest, matchmaking LFT et guides pour progresser. Synchronisé avec GameHub Arena.")
            ->canonical(route('home'));

        return view('home', compact('prochainsTournois', 'derniersArticles', 'profilLft'));
    }
}