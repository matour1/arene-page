<?php

namespace App\Services\GameHub;

use App\Models\Tournoi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TournoiSynchronizer
{
    public function __construct(private GameHubClient $client) {}

    /**
     * Synchronise les tournois publics depuis l'API vers la table locale.
     *
     * @return array{importes: int, pages: int, arrete: bool}
     */
    public function sync(?int $pagesMax = null): array
    {
        $pagesMax ??= (int) config('arene.sync.pages_max', 3);
        $parPage = (int) config('arene.sync.per_page', 20);

        $importes = 0;
        $pages = 0;
        $arrete = false;

        for ($page = 1; $page <= $pagesMax; $page++) {
            $reponse = $this->client->tournois($page, $parPage);
            $pages++;

            // Une réponse nulle signifie token absent, quota atteint ou erreur réseau.
            if ($reponse === null) {
                $arrete = true;
                break;
            }

            foreach ($reponse['data'] ?? [] as $brut) {
                if ($this->enregistrer($brut)) {
                    $importes++;
                }
            }

            $dernierePage = (int) ($reponse['meta']['last_page'] ?? 1);

            if ($page >= $dernierePage) {
                break;
            }
        }

        return ['importes' => $importes, 'pages' => $pages, 'arrete' => $arrete];
    }

    /**
     * @param  array<string, mixed>  $brut
     */
    public function enregistrer(array $brut): bool
    {
        $id = $brut['id'] ?? null;

        if (blank($id)) {
            return false;
        }

        $places = $brut['places'] ?? [];
        $dates = $brut['dates'] ?? [];
        $segmentation = $brut['segmentation'] ?? [];

        Tournoi::updateOrCreate(
            ['id' => (string) $id],
            [
                'titre' => (string) ($brut['titre'] ?? 'Tournoi sans titre'),
                'jeu' => (string) ($brut['jeu']['nom'] ?? 'Inconnu'),
                'format' => $brut['format'] ?? null,
                'statut' => (string) ($brut['statut'] ?? 'inconnu'),
                'disponible' => (bool) ($brut['disponible'] ?? false),
                'raison_indisponibilite' => $this->texte($brut['raison_indisponibilite'] ?? null),
                'visibilite' => (string) ($brut['visibilite'] ?? 'public'),
                'payant' => (bool) ($brut['payant'] ?? false),
                'frais_inscription' => (int) ($brut['frais_inscription'] ?? 0),
                'cagnotte_estimee' => (int) ($brut['cagnotte_estimee'] ?? 0),
                'devise' => (string) ($brut['devise'] ?? 'XAF'),
                'places_inscrits' => (int) ($places['inscrits'] ?? 0),
                'places_maximum' => (int) ($places['maximum'] ?? 0),
                'places_restantes' => (int) ($places['restantes'] ?? 0),
                'debut_at' => $this->date($dates['debut'] ?? null),
                'fin_inscription_at' => $this->date($dates['fin_inscription'] ?? null),
                'rang_min' => isset($segmentation['rang_min']) ? (int) $segmentation['rang_min'] : null,
                'rang_max' => isset($segmentation['rang_max']) ? (int) $segmentation['rang_max'] : null,
                'pro' => (bool) ($brut['pro'] ?? false),
                'synchronise_at' => now(),
            ],
        );

        return true;
    }

    private function date(mixed $valeur): ?Carbon
    {
        if (blank($valeur)) {
            return null;
        }

        try {
            return Carbon::parse((string) $valeur);
        } catch (\Throwable) {
            return null;
        }
    }

    private function texte(mixed $valeur): ?string
    {
        return blank($valeur) ? null : Str::limit((string) $valeur, 250, '');
    }
}
