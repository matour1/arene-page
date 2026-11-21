<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tournoi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_accueil_expose_titre_description_canonical_et_schemas_globaux(): void
    {
        $reponse = $this->get('/')->assertOk();

        $reponse->assertSee('<title>', false);
        $reponse->assertSee('name="description"', false);
        $reponse->assertSee('rel="canonical"', false);
        $reponse->assertSee('og:image', false);

        // Les deux schemas globaux : Organization et WebSite.
        $this->assertStringContainsString('"@type":"Organization"', $reponse->getContent());
        $this->assertStringContainsString('"@type":"WebSite"', $reponse->getContent());
    }

    public function test_les_pages_sont_indexables_par_defaut(): void
    {
        $this->get('/tournois')->assertSee('content="index, follow', false);
        $this->get('/articles')->assertSee('content="index, follow', false);
    }

    public function test_les_resultats_de_filtres_sont_en_noindex(): void
    {
        $this->get('/tournois?jeu=Free+Fire')->assertSee('content="noindex, nofollow"', false);
        $this->get('/lft?pays=CM')->assertSee('content="noindex, nofollow"', false);
        $this->get('/articles?page=2')->assertSee('content="noindex, nofollow"', false);
    }

    public function test_la_page_de_confirmation_est_en_noindex(): void
    {
        $this->get('/lft/merci')->assertSee('content="noindex, nofollow"', false);
    }

    public function test_une_page_non_indexable_n_expose_aucune_donnee_structuree(): void
    {
        $contenu = $this->get('/lft/merci')->getContent();

        $this->assertStringNotContainsString('application/ld+json', $contenu);
    }

    public function test_un_tournoi_expose_un_schema_event_avec_offre(): void
    {
        Tournoi::create([
            'id' => 'T-SEO',
            'titre' => 'Tournoi SEO',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'payant' => true,
            'frais_inscription' => 500,
            'cagnotte_estimee' => 5000,
            'devise' => 'XAF',
            'debut_at' => now()->addDays(3),
            'places_maximum' => 48,
            'places_restantes' => 10,
        ]);

        $contenu = $this->get('/tournois/T-SEO')->assertOk()->getContent();

        $this->assertStringContainsString('"@type":"Event"', $contenu);
        $this->assertStringContainsString('"@type":"Offer"', $contenu);
        $this->assertStringContainsString('"priceCurrency":"XAF"', $contenu);
        $this->assertStringContainsString('InStock', $contenu);
        // La fin d'inscription ne doit pas etre utilisee comme endDate.
        $this->assertStringNotContainsString('"endDate"', $contenu);
    }

    public function test_un_tournoi_complet_est_en_noindex_et_soldout(): void
    {
        Tournoi::create([
            'id' => 'T-FULL',
            'titre' => 'Tournoi complet',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'payant' => true,
            'frais_inscription' => 500,
            'cagnotte_estimee' => 1000,
            'debut_at' => now()->addDays(2),
            'places_maximum' => 48,
            'places_inscrits' => 48,
            'places_restantes' => 0,
        ]);

        $contenu = $this->get('/tournois/T-FULL')->getContent();

        $this->assertStringContainsString('content="noindex, nofollow"', $contenu);
    }

    public function test_un_article_expose_un_schema_blogposting(): void
    {
        Article::create([
            'slug' => 'article-seo',
            'titre' => 'Article SEO',
            'chapo' => 'Un chapô.',
            'contenu' => 'Contenu.',
            'publie_at' => now()->subDay(),
        ]);

        $contenu = $this->get('/articles/article-seo')->getContent();

        $this->assertStringContainsString('"@type":"BlogPosting"', $contenu);
        $this->assertStringContainsString('"datePublished"', $contenu);
    }

    public function test_le_sitemap_contient_les_pages_publiques_et_pas_les_brouillons(): void
    {
        Article::create([
            'slug' => 'publie',
            'titre' => 'Publié',
            'contenu' => 'x',
            'publie_at' => now()->subDay(),
        ]);
        Article::create([
            'slug' => 'brouillon',
            'titre' => 'Brouillon',
            'contenu' => 'x',
            'publie_at' => null,
        ]);

        $reponse = $this->get('/sitemap.xml')->assertOk();
        $reponse->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $reponse->getContent();

        $this->assertStringContainsString(route('tournois.index'), $xml);
        $this->assertStringContainsString('/articles/publie', $xml);
        $this->assertStringNotContainsString('/articles/brouillon', $xml);
        $this->assertStringNotContainsString(config('arene.admin.path'), $xml);
    }

    public function test_le_sitemap_omet_les_tournois_passes(): void
    {
        Tournoi::create([
            'id' => 'T-PASSE',
            'titre' => 'Passé',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'debut_at' => now()->subWeek(),
            'places_maximum' => 48,
            'places_restantes' => 10,
        ]);

        $xml = $this->get('/sitemap.xml')->getContent();

        $this->assertStringNotContainsString('T-PASSE', $xml);
    }

    public function test_le_titre_est_tronque_a_158_caracteres(): void
    {
        Article::create([
            'slug' => 'long',
            'titre' => str_repeat('Titre long ', 30),
            'chapo' => str_repeat('Description très longue ', 20),
            'contenu' => 'x',
            'publie_at' => now()->subDay(),
        ]);

        $contenu = $this->get('/articles/long')->getContent();

        preg_match('/name="description" content="([^"]*)"/', $contenu, $m);

        $this->assertNotEmpty($m);
        $this->assertLessThanOrEqual(158, mb_strlen(html_entity_decode($m[1])));
    }

    public function test_robots_txt_reference_le_sitemap(): void
    {
        $reponse = $this->get('/robots.txt')->assertOk();

        $contenu = $reponse->getContent();

        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $contenu);
        $this->assertStringContainsString('Disallow: /lft/merci', $contenu);
    }
}
