<?php

namespace App\Services;

use App\Models\Tournoi;

class AffiliationLiens
{
    /**
     * Construit l'URL de parrainage d'un tournoi en y ajoutant les UTM.
     *
     * Priorite : lien d'affiliation propre au tournoi (API), puis lien de base
     * configure, puis page publique du tournoi sur GameHub Arena.
     */
    public function url(?Tournoi $tournoi, string $medium = 'calendrier'): string
    {
        $base = $tournoi?->lien_affiliation
            ?: (string) config('arene.affiliation.lien_base')
            ?: $this->urlPublique($tournoi);

        return $this->avecUtm($base, $medium);
    }

    public function avecUtm(string $url, string $medium): string
    {
        $utm = [
            'utm_source' => config('arene.affiliation.utm.source'),
            'utm_medium' => config("arene.affiliation.utm.medium_{$medium}", $medium),
            'utm_campaign' => 'arene-mvp',
        ];

        $separateur = str_contains($url, '?') ? '&' : '?';

        return $url.$separateur.http_build_query($utm);
    }

    /** Page publique du tournoi, sans parametre d'affiliation. */
    private function urlPublique(?Tournoi $tournoi): string
    {
        $racine = rtrim((string) config('arene.affiliation.site_public'), '/');

        return $tournoi
            ? $racine.'/tournois/'.rawurlencode($tournoi->id)
            : $racine;
    }
}