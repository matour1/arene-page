<?php

namespace App\Http\Controllers;

use App\Services\Seo\Metadonnees;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private Metadonnees $seo) {}

    public function areneBot(): View
    {
        $this->seo
            ->titre('Arène Bot — matchmaking intelligent')
            ->description('Le futur assistant de l\'Arène des Gamers : matchmaking intelligent, génération de guides et alertes personnalisées. Bientôt disponible.')
            ->canonical(route('arene-bot'));

        return view('pages.arene-bot');
    }

    public function aPropos(): View
    {
        $this->seo
            ->titre('À propos')
            ->description('Qui nous sommes : le calendrier des tournois GameHub Arena, un espace LFT et des guides pour la scène e-sport d\'Afrique centrale et de l\'Ouest.')
            ->canonical(route('a-propos'));

        return view('pages.a-propos');
    }
}