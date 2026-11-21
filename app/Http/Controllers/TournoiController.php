<?php

namespace App\Http\Controllers;

use App\Models\Tournoi;
use App\Services\Seo\Metadonnees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TournoiController extends Controller
{
    public function __construct(private Metadonnees $seo) {}

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

        // Liste de chaines : serialisable sans risque, et rarement modifiee.
        // On n'utilise pas le scope `affiche` ici : il ajoute orderBy('debut_at'),
        // interdit par PostgreSQL avec SELECT DISTINCT (la colonne triee doit
        // figurer dans la liste selectionnee). On reproduit le filtre de
        // visibilite et on trie uniquement sur la colonne projetee.
        $jeux = Cache::remember('tournois.jeux', now()->addMinutes(30), fn () => Tournoi::query()
            ->where('visibilite', 'public')
            ->whereNotNull('jeu')
            ->distinct()
            ->orderBy('jeu')
            ->pluck('jeu')
            ->all());

        $this->seo
            ->titre('Calendrier des tournois e-sport')
            ->description('Tous les tournois e-sport mobiles à venir : Free Fire, PUBG Mobile, COD Mobile et Blood Strike. Places restantes, frais et inscriptions.')
            ->canonical(route('tournois.index'));

        // Les pages de resultats filtres/paginees ne doivent pas etre indexees :
        // Google les traite comme du contenu duplique du calendrier principal.
        if ($request->hasAny(['page', 'jeu', 'statut', 'gratuit'])) {
            $this->seo->noindex();
        } else {
            $this->seo->schema([
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Calendrier des tournois e-sport',
                'itemListElement' => $tournois->values()->map(fn (Tournoi $t, int $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => route('tournois.show', $t),
                    'name' => $t->titrePropre(),
                ])->all(),
            ]);
        }

        return view('tournois.index', compact('tournois', 'jeux', 'filtres'));
    }

    public function show(string $tournoi): View
    {
        $tournoi = Tournoi::findOrFail($tournoi);

        abort_unless($tournoi->visibilite === 'public', 404);

        $description = collect([
            $tournoi->jeu,
            $tournoi->debut_at?->locale('fr')->translatedFormat('d F Y à H:i'),
            $tournoi->places_maximum > 0 ? $tournoi->places_restantes.' places restantes' : null,
            $tournoi->payant
                ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise
                : 'Gratuit',
        ])->filter()->implode(' · ');

        $this->seo
            ->titre($tournoi->titrePropre())
            ->description($description.'. Inscriptions sur GameHub Arena.')
            ->canonical(route('tournois.show', $tournoi))
            ->type('website')
            ->schema($this->schemaEvenement($tournoi));

        // Un tournoi passe ou complet n'a plus d'interet pour un moteur :
        // on evite d'indexer une page qui n'offre plus d'action.
        if ($tournoi->debut_at?->isPast() || $tournoi->placesCompletes()) {
            $this->seo->noindex();
        }

        return view('tournois.show', compact('tournoi'));
    }

    /**
     * Evenement schema.org, exploite par Google pour les rich results.
     *
     * @return array<string, mixed>
     */
    private function schemaEvenement(Tournoi $tournoi): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $tournoi->titrePropre(),
            'startDate' => $tournoi->debut_at?->toIso8601String(),
            // Un tournoi en ligne dure quelques heures ; sans duree connue on
            // laisse endDate absent plutot que d'utiliser la fin d'inscription
            // (Google validerait mal la coherence des dates).
            'eventAttendanceMode' => 'https://schema.org/OnlineEventAttendanceMode',
            'eventStatus' => $tournoi->disponible
                ? 'https://schema.org/EventScheduled'
                : 'https://schema.org/EventCancelled',
            'location' => [
                '@type' => 'VirtualLocation',
                'url' => route('tournois.show', $tournoi),
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'GameHub Arena',
                'url' => config('arene.affiliation.site_public'),
            ],
            'inLanguage' => 'fr',
            'isAccessibleForFree' => ! $tournoi->payant,
        ];

        if ($tournoi->cagnotte_estimee > 0) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $tournoi->frais_inscription,
                'priceCurrency' => $tournoi->devise,
                'url' => route('tournois.show', $tournoi),
                'validFrom' => $tournoi->created_at?->toIso8601String(),
                'availability' => $tournoi->placesCompletes()
                    ? 'https://schema.org/SoldOut'
                    : 'https://schema.org/InStock',
            ];
        }

        return array_filter($schema, fn ($v) => $v !== null);
    }
}