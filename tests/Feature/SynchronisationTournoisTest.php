<?php

namespace Tests\Feature;

use App\Models\Tournoi;
use App\Services\GameHub\GameHubClient;
use App\Services\GameHub\TournoiSynchronizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SynchronisationTournoisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gamehub.base_url' => 'https://api.test/api/v1',
            'services.gamehub.token' => 'ghk_test_abc',
            'arene.sync.pages_max' => 3,
            'arene.sync.per_page' => 20,
        ]);
    }

    public function test_importe_un_tournoi_avec_le_mapping_complet(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::response([
                'data' => [$this->payload()],
                'meta' => ['last_page' => 1],
            ]),
        ]);

        $resultat = $this->synchroniseur()->sync();

        $this->assertSame(1, $resultat['importes']);
        $this->assertFalse($resultat['arrete']);

        $tournoi = Tournoi::find('T-12FBE0C3E04B2288');

        $this->assertNotNull($tournoi);
        $this->assertSame('Free Fire', $tournoi->jeu);
        $this->assertTrue($tournoi->payant);
        $this->assertSame(500, $tournoi->frais_inscription);
        $this->assertSame(47, $tournoi->places_restantes);
        $this->assertSame('2026-11-28 22:00:00', $tournoi->debut_at->format('Y-m-d H:i:s'));
        $this->assertTrue($tournoi->disponible);
    }

    public function test_envoie_le_token_en_entete_authorization(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::response(['data' => [], 'meta' => ['last_page' => 1]]),
        ]);

        $this->synchroniseur()->sync();

        Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer ghk_test_abc'));
    }

    public function test_une_deuxieme_synchronisation_met_a_jour_sans_dupliquer(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::sequence()
                ->push(['data' => [$this->payload()], 'meta' => ['last_page' => 1]])
                ->push(['data' => [$this->payload(['places' => ['inscrits' => 10, 'maximum' => 48, 'restantes' => 38]])], 'meta' => ['last_page' => 1]]),
        ]);

        $this->synchroniseur()->sync();
        $this->synchroniseur()->sync();

        $this->assertDatabaseCount('tournois', 1);
        $this->assertSame(38, Tournoi::find('T-12FBE0C3E04B2288')->places_restantes);
    }

    public function test_un_quota_depasse_ne_casse_rien_et_arrete_la_synchronisation(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::response(['message' => 'Too Many Requests'], 429),
        ]);

        $resultat = $this->synchroniseur()->sync();

        $this->assertTrue($resultat['arrete']);
        $this->assertSame(0, $resultat['importes']);
    }

    public function test_sans_token_aucun_appel_reseau_nest_effectue(): void
    {
        config(['services.gamehub.token' => null]);
        Http::fake();

        $resultat = $this->synchroniseur()->sync();

        $this->assertTrue($resultat['arrete']);
        Http::assertNothingSent();
    }

    public function test_pagine_jusqua_la_derniere_page_indiquee_par_lapi(): void
    {
        Http::fake([
            'api.test/api/v1/tournois*' => Http::sequence()
                ->push(['data' => [$this->payload(['id' => 'T-A'])], 'meta' => ['last_page' => 2]])
                ->push(['data' => [$this->payload(['id' => 'T-B'])], 'meta' => ['last_page' => 2]]),
        ]);

        $resultat = $this->synchroniseur()->sync();

        $this->assertSame(2, $resultat['importes']);
        $this->assertSame(2, $resultat['pages']);
        $this->assertDatabaseHas('tournois', ['id' => 'T-A']);
        $this->assertDatabaseHas('tournois', ['id' => 'T-B']);
    }

    public function test_ne_depasse_pas_le_nombre_de_pages_configure(): void
    {
        config(['arene.sync.pages_max' => 1]);

        Http::fake([
            'api.test/api/v1/tournois*' => Http::response([
                'data' => [$this->payload(['id' => 'T-A'])],
                'meta' => ['last_page' => 5],
            ]),
        ]);

        $resultat = $this->synchroniseur()->sync();

        $this->assertSame(1, $resultat['pages']);
        Http::assertSentCount(1);
    }

    private function synchroniseur(): TournoiSynchronizer
    {
        return new TournoiSynchronizer(app(GameHubClient::class));
    }

    /**
     * @param  array<string, mixed>  $surcharges
     * @return array<string, mixed>
     */
    private function payload(array $surcharges = []): array
    {
        return array_replace_recursive([
            'id' => 'T-12FBE0C3E04B2288',
            'titre' => '🏆 BR QUAD CLASH — GAMEHUB ARENA',
            'jeu' => ['id' => 1, 'nom' => 'Free Fire'],
            'format' => 'br_classement',
            'statut' => 'ouvert',
            'disponible' => true,
            'raison_indisponibilite' => null,
            'visibilite' => 'public',
            'payant' => true,
            'frais_inscription' => 500,
            'cagnotte_estimee' => 5500,
            'devise' => 'XAF',
            'places' => ['inscrits' => 1, 'maximum' => 48, 'restantes' => 47],
            'dates' => [
                'debut' => '2026-11-28T22:00:00+01:00',
                'fin_inscription' => '2026-11-28T20:22:00+01:00',
            ],
            'segmentation' => ['rang_min' => null, 'rang_max' => null],
            'pro' => true,
        ], $surcharges);
    }
}
