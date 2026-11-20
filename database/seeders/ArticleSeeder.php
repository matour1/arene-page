<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->articles() as $index => $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                $article + ['publie_at' => now()->subDays($index)],
            );
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function articles(): array
    {
        return [
            [
                'slug' => 'rejoindre-son-premier-tournoi-free-fire',
                'titre' => 'Comment rejoindre ton premier tournoi Free Fire sans te planter',
                'categorie' => 'Guide',
                'chapo' => 'Inscription, vérification du rang, horaires : la checklist complète avant ton premier tournoi.',
                'contenu' => <<<'MD'
                Tu as atteint Heroic, tu joues en squad depuis six mois, et tu penses être prêt. Bonne nouvelle : tu l'es probablement. Mauvaise nouvelle : la majorité des premières participations échouent pour des raisons administratives, pas techniques.

                ## 1. Vérifie les conditions d'éligibilité

                Avant de cliquer sur « S'inscrire », lis trois informations sur la page du tournoi :

                - **Le format** : `br_classement` signifie que les points se cumulent sur plusieurs parties. Un format en élimination directe ne se prépare pas de la même façon.
                - **Les frais d'inscription** : la plupart des tournois de la plateforme coûtent entre 500 et 2 000 XAF. Les tournois gratuits existent et sont un excellent terrain d'essai.
                - **La segmentation par rang** : si un `rang_min` est indiqué, un compte trop bas sera refusé à la vérification.

                ## 2. Inscris-toi tôt, mais pas trop

                Les places partent vite sur les formats 48 joueurs. Inscris-toi dès l'ouverture des inscriptions, mais garde une marge : il faut généralement confirmer sa présence 15 à 30 minutes avant le début.

                ## 3. Prépare ton identité de jeu

                Ton pseudo doit correspondre à celui utilisé dans le jeu. Un écart entre le pseudo déclaré et le pseudo en partie est le motif de disqualification le plus fréquent sur les tournois amateurs.

                ## 4. Teste ta connexion avant le jour J

                Un tournoi perdu sur une déconnexion, ça arrive toutes les semaines. Teste ta connexion à l'heure exacte du tournoi au moins une fois dans la semaine qui précède, sur le même réseau et au même endroit.

                ## 5. Prévois la logistique du paiement

                Pour les tournois payants, la confirmation d'inscription passe par un paiement mobile. Vérifie que ton solde est suffisant **avant** l'ouverture, pas pendant.

                ## Le vrai conseil

                Ton premier tournoi n'a pas besoin d'être le plus gros. Choisis un format gratuit ou à faible frais d'entrée, avec moins de 50 joueurs. L'objectif du premier tournoi est d'apprendre le rythme : les horaires réels, la communication avec l'organisateur, la gestion du stress entre deux parties.
                MD,
            ],
            [
                'slug' => 'lft-bien-rediger-son-profil',
                'titre' => 'LFT : les 6 informations qui font qu\'on te contacte (ou pas)',
                'categorie' => 'Guide',
                'chapo' => 'Un profil LFT rempli correctement reçoit jusqu\'à cinq fois plus de contacts. Voici quoi écrire.',
                'contenu' => <<<'MD'
                Un profil LFT vide ne sert à rien. Un profil LFT trop rempli ne sert à rien non plus. Ce qui fonctionne, c'est un profil court, structuré, avec un contact cliquable.

                ## 1. Le pseudo exact

                Utilise le pseudo que tu portes en jeu, à la casse près. Les gens qui te cherchent dans le jeu doivent te trouver du premier coup.

                ## 2. Le rôle, pas le style

                « Rusher » est un rôle. « J'aime l'agressivité » est un style. Les équipes recrutent des rôles : rusher, sniper, support, IGL. Choisis-en un.

                ## 3. Le rang réel

                Annonce ton rang actuel, pas ton pic de la saison dernière. Une équipe qui te recrute sur un rang surestimé te remplacera après deux parties.

                ## 4. Le pays et la langue

                C'est le filtre le plus utilisé par les recruteurs, avant même le jeu. Un profil sans pays ni langue est invisible dans les recherches.

                ## 5. Un contact qui marche

                Discord ou WhatsApp, au moins un des deux. Un identifiant Discord mal orthographié est pire que pas de contact du tout : la personne croit t'avoir écrit.

                ## 6. Deux phrases de description

                Pas un roman. Deux phrases qui répondent à : quand joues-tu, et que cherches-tu ?

                > « Dispo en soirée semaine et week-end, cherche une squad stable pour tournois hebdomadaires. »

                C'est suffisant. Tout le reste se discutera en message privé.
                MD,
            ],
            [
                'slug' => 'scene-competitive-afrique-centrale-2026',
                'titre' => 'La scène compétitive en Afrique centrale : ce qui a changé en 2026',
                'categorie' => 'Analyse',
                'chapo' => 'Plus de tournois, des cagnottes modestes mais réelles, et une professionnalisation lente mais visible.',
                'contenu' => <<<'MD'
                La scène compétitive mobile d'Afrique centrale a longtemps vécu sur des tournois ponctuels, organisés par des communautés Discord, sans calendrier ni prize pool formalisé. En 2026, la structure change.

                ## Des calendriers réguliers

                Le fait marquant n'est pas la taille des cagnottes, mais leur **régularité**. Un tournoi hebdomadaire à 5 000 XAF de cagnotte crée plus de vocation qu'un tournoi annuel à 500 000 XAF, parce qu'il donne un rythme d'entraînement.

                ## Le passage au paiement mobile

                Les frais d'inscription entre 500 et 2 000 XAF, réglés par mobile money, ont résolu un problème pratique : l'engagement. Un joueur qui a payé 1 000 XAF se présente à l'heure. Un joueur inscrit gratuitement, beaucoup moins.

                ## Ce qui manque encore

                Trois choses freinent la professionnalisation :

                1. **La vérification des identités de jeu**, encore largement déclarative.
                2. **La couverture médiatique** : peu de résumés de tournois, peu de portraits de joueurs.
                3. **Les structures d'équipe** : la majorité des squads restent informelles, sans capitaine désigné ni planning.

                ## Ce que ça change pour les joueurs

                Le niveau moyen monte. Un joueur qui atteint Heroic en solo ne suffit plus : les équipes cherchent des joueurs capables de tenir un rôle précis sur plusieurs tournois consécutifs. La régularité devient la compétence la plus recherchée.
                MD,
            ],
            [
                'slug' => 'preparer-un-bo3-mobile',
                'titre' => 'Préparer un BO3 sur mobile : la méthode en 4 étapes',
                'categorie' => 'Guide',
                'chapo' => 'Entre deux parties d\'un même tournoi, la récupération compte autant que la stratégie.',
                'contenu' => <<<'MD'
                Un BO3 sur mobile se gagne autant entre les parties que pendant. Voici une méthode simple, testée sur des formats de tournoi à élimination.

                ## 1. Avant la première partie : figer la composition

                Ne changez pas de rôle entre la partie 1 et la partie 2 sur la base d'une seule défaite. Une composition qui a perdu de peu est généralement meilleure qu'une composition improvisée.

                ## 2. Entre les parties : 3 minutes, pas 10

                La fenêtre entre deux parties est courte. Utilisez-la pour :

                - noter **une** erreur collective, pas cinq ;
                - vérifier la charge de l'appareil et la température ;
                - boire de l'eau, réellement.

                Les longues discussions stratégiques entre deux parties sont contre-productives : elles installent la tension.

                ## 3. Adapter une seule chose par partie

                Si vous perdez la partie 1 sur les rotations, corrigez les rotations. Ne changez pas simultanément le point de chute, le rôle du sniper et la stratégie de fin de zone. Vous ne saurez pas ce qui a fonctionné.

                ## 4. Gérer la partie 3 à froid

                La partie décisive se joue souvent après une pause plus longue. Reprenez l'échauffement mécanique (deux ou trois minutes de tapotement, pas une partie complète) et rappelez le plan initial. La partie 3 doit ressembler à la partie 1, pas à une réinvention.

                ## Ce qu'il faut retenir

                La constance bat l'improvisation. Sur un BO3 mobile, l'équipe qui change le moins de choses entre les parties gagne le plus souvent.
                MD,
            ],
        ];
    }
}
