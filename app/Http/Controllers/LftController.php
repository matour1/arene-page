<?php

namespace App\Http\Controllers;

use App\Models\ProfilLft;
use App\Services\Seo\Metadonnees;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LftController extends Controller
{
    public function __construct(private Metadonnees $seo) {}

    /** Liste des pays proposés dans les filtres. */
    public const PAYS = [
        'CM' => 'Cameroun',
        'NG' => 'Nigeria',
        'CI' => 'Côte d\'Ivoire',
        'SN' => 'Sénégal',
        'GA' => 'Gabon',
        'TD' => 'Tchad',
        'CF' => 'Centrafrique',
        'CG' => 'Congo',
        'CD' => 'RD Congo',
        'BJ' => 'Bénin',
        'TG' => 'Togo',
        'ML' => 'Mali',
        'BF' => 'Burkina Faso',
        'GN' => 'Guinée',
        'GH' => 'Ghana',
    ];

    public const LANGUES = [
        'fr' => 'Français',
        'en' => 'Anglais',
    ];

    public function index(Request $request): View
    {
        $filtres = [
            'jeu' => $request->string('jeu')->toString() ?: null,
            'pays' => $request->string('pays')->toString() ?: null,
            'langue' => $request->string('langue')->toString() ?: null,
            'disponible' => $request->boolean('disponible'),
        ];

        $profils = ProfilLft::query()
            ->valides()
            ->filtre($filtres)
            ->paginate(15)
            ->withQueryString();

        // PostgreSQL exige que la colonne de tri figure dans un SELECT DISTINCT.
        // On projette puis trie en PHP pour rester portable.
        $jeux = ProfilLft::query()
            ->valides()
            ->distinct()
            ->orderBy('jeu')
            ->pluck('jeu');

        $this->seo
            ->titre('Matchmaking LFT — trouver une équipe')
            ->description('Profils de joueurs en recherche d\'équipe : jeu, rôle, rang, pays et langue. Contact direct par Discord ou WhatsApp, sans messagerie interne.')
            ->canonical(route('lft.index'));

        // Resultats filtres : non indexes pour eviter le contenu duplique.
        if ($request->hasAny(['page', 'jeu', 'pays', 'langue', 'disponible'])) {
            $this->seo->noindex();
        }

        return view('lft.index', [
            'profils' => $profils,
            'jeux' => $jeux,
            'filtres' => $filtres,
            'pays' => self::PAYS,
            'langues' => self::LANGUES,
        ]);
    }

    public function create(): View
    {
        $this->seo
            ->titre('Créer mon profil LFT')
            ->description('Publie ton profil de joueur en recherche d\'équipe : jeu, rôle, rang, pays et langue. Visible après validation, sous 24 h.')
            ->canonical(route('lft.create'));

        return view('lft.create', [
            'pays' => self::PAYS,
            'langues' => self::LANGUES,
        ]);
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'pseudo' => ['required', 'string', 'max:50'],
            'jeu' => ['required', 'string', 'max:60'],
            'role' => ['nullable', 'string', 'max:40'],
            'rang' => ['nullable', 'string', 'max:40'],
            'plateforme' => ['nullable', 'string', 'max:40'],
            'pays' => ['required', 'string', 'size:2'],
            'langue' => ['required', 'string', 'in:fr,en'],
            'contact_discord' => ['nullable', 'string', 'max:80', 'required_without:contact_whatsapp'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30', 'required_without:contact_discord'],
            'description' => ['nullable', 'string', 'max:600'],
        ], [
            'contact_discord.required_without' => 'Indiquez au moins un contact : Discord ou WhatsApp.',
            'contact_whatsapp.required_without' => 'Indiquez au moins un contact : Discord ou WhatsApp.',
        ]);

        ProfilLft::create($donnees + [
            'disponible' => true,
            'statut' => 'en_attente',
        ]);

        return redirect()
            ->route('lft.merci')
            ->with('pseudo', $donnees['pseudo']);
    }

    public function merci(): View
    {
        // Page de confirmation : aucune valeur pour les moteurs.
        $this->seo
            ->titre('Profil envoyé')
            ->noindex();

        return view('lft.merci');
    }
}
