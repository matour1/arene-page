<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ProfilLft;
use App\Models\Tournoi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'nbTournois' => Tournoi::count(),
            'nbTournoisAVenir' => Tournoi::aVenir()->count(),
            'derniereSync' => Tournoi::max('synchronise_at'),
            'nbArticles' => Article::count(),
            'nbArticlesPublies' => Article::publies()->count(),
            'nbProfilsEnAttente' => ProfilLft::where('statut', 'en_attente')->count(),
            'nbProfilsValides' => ProfilLft::valides()->count(),
        ]);
    }
}
