<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ProfilLft;
use App\Models\Tournoi;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Requêtes directes : 3 lignes indexées, moins coûteux qu'un aller-retour
        // de cache. Le cache ne sert qu'aux valeurs scalaires (compteur).
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

        return view('home', compact('prochainsTournois', 'derniersArticles', 'profilLft'));
    }
}
