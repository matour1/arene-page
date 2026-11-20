<?php

namespace Tests\Unit;

use App\Models\Tournoi;
use App\Services\AffiliationLiens;
use Tests\TestCase;

class AffiliationLiensTest extends TestCase
{
    public function test_ajoute_les_utm_avec_le_bon_separateur(): void
    {
        $liens = new AffiliationLiens();

        $url = $liens->avecUtm('https://exemple.test/tournois/1', 'calendrier');

        $this->assertStringStartsWith('https://exemple.test/tournois/1?', $url);
        $this->assertStringContainsString('utm_source=arene-des-gamers', $url);
        $this->assertStringContainsString('utm_medium=calendrier', $url);
        $this->assertStringContainsString('utm_campaign=arene-mvp', $url);
    }

    public function test_conserve_le_parametre_de_parrainage_et_ajoute_les_utm(): void
    {
        $liens = new AffiliationLiens();

        $url = $liens->avecUtm('https://exemple.test/tournois/15?dev=abc123', 'accueil');

        // Le paramètre d'affiliation doit survivre et les UTM s'ajouter avec '&'.
        $this->assertStringContainsString('dev=abc123&utm_source=arene-des-gamers', $url);
        $this->assertStringContainsString('utm_medium=accueil', $url);
    }

    public function test_utilise_le_lien_du_tournoi_quand_il_existe(): void
    {
        $tournoi = new Tournoi(['lien_affiliation' => 'https://exemple.test/t/42?dev=perso']);

        $url = (new AffiliationLiens())->url($tournoi, 'article');

        $this->assertStringContainsString('https://exemple.test/t/42?dev=perso', $url);
        $this->assertStringContainsString('utm_medium=article', $url);
    }

    public function test_retombe_sur_le_lien_de_configuration_sans_tournoi(): void
    {
        config(['arene.affiliation.lien_base' => 'https://exemple.test/defaut']);

        $url = (new AffiliationLiens())->url(null, 'calendrier');

        $this->assertStringStartsWith('https://exemple.test/defaut?', $url);
    }

    public function test_retombe_sur_le_site_public_quand_aucun_lien_nest_configure(): void
    {
        config([
            'arene.affiliation.lien_base' => null,
            'arene.affiliation.site_public' => 'https://exemple.test',
        ]);

        $tournoi = new Tournoi(['id' => 'T-ABC']);

        $url = (new AffiliationLiens())->url($tournoi, 'calendrier');

        $this->assertStringStartsWith('https://exemple.test/tournois/T-ABC?', $url);
    }
}
