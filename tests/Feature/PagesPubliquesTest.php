<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ProfilLft;
use App\Models\Tournoi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesPubliquesTest extends TestCase
{
    use RefreshDatabase;

    public function test_accueil_affiche_les_trois_prochains_tournois_et_les_articles(): void
    {
        Tournoi::create($this->tournoiAttr(['id' => 'T-1', 'titre' => 'Alpha', 'debut_at' => now()->addDays(2)]));
        Tournoi::create($this->tournoiAttr(['id' => 'T-2', 'titre' => 'Beta', 'debut_at' => now()->addDays(3)]));
        Tournoi::create($this->tournoiAttr(['id' => 'T-3', 'titre' => 'Gamma', 'debut_at' => now()->addDays(4)]));
        // Tournoi passé : ne doit pas apparaître.
        Tournoi::create($this->tournoiAttr(['id' => 'T-4', 'titre' => 'Passe', 'debut_at' => now()->subDays(2)]));

        Article::create([
            'slug' => 'mon-article',
            'titre' => 'Mon article de test',
            'contenu' => 'Contenu',
            'publie_at' => now()->subDay(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Alpha')
            ->assertSee('Beta')
            ->assertSee('Gamma')
            ->assertDontSee('Passe')
            ->assertSee('Mon article de test');
    }

    public function test_un_article_non_publie_est_invisible(): void
    {
        Article::create([
            'slug' => 'brouillon',
            'titre' => 'Brouillon secret',
            'contenu' => 'Contenu',
            'publie_at' => null,
        ]);

        $this->get('/articles')->assertOk()->assertDontSee('Brouillon secret');
        $this->get('/articles/brouillon')->assertNotFound();
    }

    public function test_calendrier_filtre_par_jeu_et_masque_les_tournois_passes(): void
    {
        Tournoi::create($this->tournoiAttr(['id' => 'T-FF', 'titre' => 'Cup Free Fire', 'jeu' => 'Free Fire']));
        Tournoi::create($this->tournoiAttr(['id' => 'T-CODM', 'titre' => 'Cup COD', 'jeu' => 'Call of Duty']));
        Tournoi::create($this->tournoiAttr([
            'id' => 'T-PASSE',
            'titre' => 'Vieux tournoi',
            'jeu' => 'Free Fire',
            'debut_at' => now()->subWeek(),
        ]));

        $this->get('/tournois?jeu=Free+Fire')
            ->assertOk()
            ->assertSee('Cup Free Fire')
            ->assertDontSee('Cup COD')
            ->assertDontSee('Vieux tournoi');
    }

    public function test_le_lien_dinscription_est_un_lien_sponsorise_avec_utm(): void
    {
        Tournoi::create($this->tournoiAttr(['id' => 'T-UTM', 'debut_at' => now()->addDays(1)]));

        $this->get('/tournois/T-UTM')
            ->assertOk()
            ->assertSee('rel="sponsored noopener noreferrer"', false)
            ->assertSee('utm_source=arene-des-gamers', false)
            ->assertSee('utm_medium=calendrier', false);
    }

    public function test_un_tournoi_non_public_renvoie_404(): void
    {
        Tournoi::create($this->tournoiAttr(['id' => 'T-PRIVE', 'visibilite' => 'prive']));

        $this->get('/tournois/T-PRIVE')->assertNotFound();
    }

    public function test_le_formulaire_lft_valide_les_donnees_et_cree_un_profil_en_attente(): void
    {
        $this->post('/lft', [
            'pseudo' => 'Testeur',
            'jeu' => 'Free Fire',
            'pays' => 'CM',
            'langue' => 'fr',
            'contact_discord' => 'testeur#1234',
        ])->assertRedirect(route('lft.merci'));

        $this->assertDatabaseHas('profils_lft', [
            'pseudo' => 'Testeur',
            'statut' => 'en_attente',
            'pays' => 'CM',
        ]);
    }

    public function test_le_formulaire_lft_exige_un_moyen_de_contact(): void
    {
        $this->post('/lft', [
            'pseudo' => 'SansContact',
            'jeu' => 'Free Fire',
            'pays' => 'CM',
            'langue' => 'fr',
        ])->assertSessionHasErrors(['contact_discord']);

        $this->assertDatabaseCount('profils_lft', 0);
    }

    public function test_seuls_les_profils_valides_apparaissent_publiquement(): void
    {
        ProfilLft::create([
            'pseudo' => 'Valide',
            'jeu' => 'Free Fire',
            'pays' => 'CM',
            'langue' => 'fr',
            'statut' => 'valide',
        ]);
        ProfilLft::create([
            'pseudo' => 'EnAttente',
            'jeu' => 'Free Fire',
            'pays' => 'CM',
            'langue' => 'fr',
            'statut' => 'en_attente',
        ]);

        $this->get('/lft')
            ->assertOk()
            ->assertSee('Valide')
            ->assertDontSee('EnAttente');
    }

    public function test_le_back_office_est_interdit_aux_visiteurs_et_non_admins(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));

        $utilisateur = User::factory()->create(['is_admin' => false]);
        $this->actingAs($utilisateur)->get('/admin')->assertForbidden();
    }

    public function test_un_admin_peut_acceder_au_back_office(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Tableau de bord');
    }

    /**
     * @param  array<string, mixed>  $surcharges
     * @return array<string, mixed>
     */
    private function tournoiAttr(array $surcharges = []): array
    {
        return array_merge([
            'id' => 'T-DEFAUT',
            'titre' => 'Tournoi de test',
            'jeu' => 'Free Fire',
            'statut' => 'ouvert',
            'visibilite' => 'public',
            'disponible' => true,
            'debut_at' => now()->addDays(5),
            'places_maximum' => 48,
            'places_restantes' => 10,
        ], $surcharges);
    }
}
