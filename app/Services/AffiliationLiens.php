<?php

namespace App\Services;

use App\Models\Tournoi;

class AffiliationLiens
{
    /**
     * Construit l'URL de parrainage d'un tournoi en y ajoutant les UTM.
     *
     * Priorite :
     *   1. lien d'affiliation dedie au tournoi (colonne lien_affiliation) ;
     *   2. lien de base configure (GAMEHUB_AFFILIATE_URL) ;
     *   3. page publique du tournoi sur gamehub-arena.com.
     *
     * Les liens de parrainage personnalises ne sont delivres qu'apres
     * connexion au compte GameHub Arena : le lien par tournoi se saisit donc
     * depuis le back-office, tournoi par tournoi.
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

        // Ne pas ecraser des UTM deja presents dans un lien colle depuis la console.
        parse_str((string) parse_url($url, PHP_URL_QUERY), $existants);
        $utm = array_diff_key($utm, $existants);

        if ($utm === []) {
            return $url;
        }

        $separateur = str_contains($url, '?') ? '&' : '?';

        return $url.$separateur.http_build_query($utm);
    }

    /** Page publique du tournoi, sans parametre d'affiliation. */
    public function urlPublique(?Tournoi $tournoi): string
    {
        $racine = rtrim((string) config('arene.affiliation.site_public'), '/');

        return $tournoi
            ? $racine.'/tournois/'.rawurlencode($tournoi->id)
            : $racine;
    }
}