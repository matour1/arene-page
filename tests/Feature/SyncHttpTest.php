<?php

namespace Tests\Feature;

use App\Models\Tournoi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'arene.sync.jeton' => 'jeton-de-test',
            'services.gamehub.base_url' => 'https://api.test/api/v1',
            'services.gamehub.token' => 'ghk_test_abc',
            'arene.sync.pages_max' => 1,
        ]);
    }

    public function test_sans_jeton_la_route_refuse(): void
    {
        $this->get('/sync/tournois')->assertForbidden();
        $this->get('/sync/tournois?token=faux')->assertForbidden();
    }

    public function test_sans_jeton_configure_la_route_est_indisponible(): void
    {
        config(['arene.sync.jeton' => null]);

        $this->get('/sync/tournois?token=quelconque')->assertStatus(503);
    }

    public function test_avec_le_bon_jeton_la_synchronisation_s_execute(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::response([
                'data' => [[
                    'id' => 'T-SYNC',
                    'titre' => 'Tournoi synchronisé',
                    'jeu' => ['nom' => 'Free Fire'],
                    'statut' => 'ouvert',
                    'visibilite' => 'public',
                    'disponible' => true,
                    'payant' => false,
                    'places' => ['inscrits' => 0, 'maximum' => 48, 'restantes' => 48],
                    'dates' => ['debut' => '2027-01-10T20:00:00+01:00'],
                ]],
                'meta' => ['last_page' => 1],
            ]),
        ]);

        $this->get('/sync/tournois?token=jeton-de-test')
            ->assertOk()
            ->assertJson(['statut' => 'ok', 'importes' => 1]);

        $this->assertDatabaseHas('tournois', ['id' => 'T-SYNC']);
    }

    public function test_deux_appels_rapproches_ne_consomment_qu_une_synchronisation(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::response(['data' => [], 'meta' => ['last_page' => 1]]),
        ]);

        // Le verrou est posé pour 2 minutes après le premier appel.
        $this->get('/sync/tournois?token=jeton-de-test')->assertOk();
        cache()->put('sync.tournois.verrou', true, now()->addMinutes(2));

        $this->get('/sync/tournois?token=jeton-de-test')
            ->assertOk()
            ->assertJson(['statut' => 'ignore']);
    }

    public function test_la_page_tournois_supporte_postgresql_avec_distinct(): void
    {
        // PostgreSQL interdit ORDER BY sur une colonne absente de SELECT DISTINCT.
        // Le contrôleur ne doit pas appliquer le scope `affiche` (qui trie par
        // debut_at) sur la requête de liste des jeux.
        Tournoi::create([
            'id' => 'T-1',
            'titre' => 'Alpha',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'debut_at' => now()->addDays(3),
            'places_maximum' => 48,
            'places_restantes' => 10,
        ]);
        Tournoi::create([
            'id' => 'T-2',
            'titre' => 'Beta',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'debut_at' => now()->addDays(5),
            'places_maximum' => 48,
            'places_restantes' => 10,
        ]);

        $this->get('/tournois')
            ->assertOk()
            ->assertSee('Free Fire');
    }
}
