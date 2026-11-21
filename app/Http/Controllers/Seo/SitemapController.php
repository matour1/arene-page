<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tournoi;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Sitemap XML genere a la demande et mis en cache une heure.
     *
     * On n'inclut que les pages reellement indexables : hors filtres,
     * hors tournois passes ou complets, hors brouillons.
     */
    public function index(): Response
    {
        $xml = cache()->remember('seo.sitemap', now()->addHour(), function () {
            $urls = [];

            $ajouter = function (string $loc, ?string $lastmod = null, string $freq = 'weekly', string $prio = '0.5') use (&$urls) {
                $urls[] = array_filter([
                    'loc' => $loc,
                    'lastmod' => $lastmod,
                    'changefreq' => $freq,
                    'priority' => $prio,
                ]);
            };

            // Pages fixes
            $ajouter(url('/'), now()->toAtomString(), 'daily', '1.0');
            $ajouter(route('tournois.index'), now()->toAtomString(), 'daily', '0.9');
            $ajouter(route('articles.index'), now()->toAtomString(), 'weekly', '0.8');
            $ajouter(route('lft.index'), now()->toAtomString(), 'daily', '0.7');
            $ajouter(route('lft.create'), null, 'monthly', '0.4');
            $ajouter(route('arene-bot'), null, 'monthly', '0.4');
            $ajouter(route('a-propos'), null, 'yearly', '0.3');

            // Tournois a venir et encore ouverts
            Tournoi::query()
                ->affiche()
                ->aVenir()
                ->where('disponible', true)
                ->where('places_restantes', '>', 0)
                ->chunk(200, function ($lot) use ($ajouter) {
                    foreach ($lot as $tournoi) {
                        $ajouter(
                            route('tournois.show', $tournoi),
                            $tournoi->updated_at?->toAtomString(),
                            'daily',
                            '0.8',
                        );
                    }
                });

            // Articles publies
            Article::query()
                ->publies()
                ->chunk(200, function ($lot) use ($ajouter) {
                    foreach ($lot as $article) {
                        $ajouter(
                            route('articles.show', $article->slug),
                            $article->updated_at?->toAtomString(),
                            'monthly',
                            '0.7',
                        );
                    }
                });

            return $this->versXml($urls);
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * robots.txt genere avec l'URL reelle du site (APP_URL).
     * Evite de coder en dur le domaine dans un fichier statique.
     */
    public function robots(): Response
    {
        $lignes = [
            '# '.config('arene.site.nom').' — robots.txt',
            '',
            'User-agent: *',
            'Allow: /',
            '',
            '# Pages sans valeur pour les moteurs.',
            'Disallow: /lft/merci',
            '',
            '# Resultats de filtres et pagination : evite le contenu duplique.',
            'Disallow: /*?page=',
            'Disallow: /*?jeu=',
            'Disallow: /*?statut=',
            'Disallow: /*?gratuit=',
            'Disallow: /*?pays=',
            'Disallow: /*?langue=',
            'Disallow: /*?disponible=',
            '',
            '# Robots d\'audit SEO : cadence reduite.',
            'User-agent: AhrefsBot',
            'Crawl-delay: 10',
            '',
            'User-agent: SemrushBot',
            'Crawl-delay: 10',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lignes)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<int, array<string, string|null>>  $urls
     */
    private function versXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($url['loc'])."</loc>\n";

            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod'])."</lastmod>\n";
            }

            $xml .= '    <changefreq>'.e($url['changefreq'])."</changefreq>\n";
            $xml .= '    <priority>'.e($url['priority'])."</priority>\n";
            $xml .= "  </url>\n";
        }

        return $xml.'</urlset>'."\n";
    }
}