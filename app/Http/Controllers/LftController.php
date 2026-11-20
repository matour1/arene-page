<?php

namespace App\Http\Controllers;

use App\Models\ProfilLft;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LftController extends Controller
{
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

        $jeux = ProfilLft::query()
            ->valides()
            ->distinct()
            ->orderBy('jeu')
            ->pluck('jeu');

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
        return view('lft.merci');
    }
}
