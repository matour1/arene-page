<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité du site
    |--------------------------------------------------------------------------
    */

    'site' => [
        'nom' => env('ARENE_NOM', 'Arène des Gamers'),
        'baseline' => env('ARENE_BASELINE', 'Le calendrier des tournois et le matchmaking LFT de la communauté gaming africaine.'),
        'communaute_discord' => env('ARENE_DISCORD_URL'),
        'communaute_whatsapp' => env('ARENE_WHATSAPP_URL', 'https://chat.whatsapp.com/Ij9AdHcAGyqC1SoBOhXpTe'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    |
    | Valeurs de repli quand une page ne fournit pas ses propres metadonnees.
    | `image_defaut` sert aux partages sociaux (1200x630, format Open Graph).
    |
    */

    'seo' => [
        'titre_suffixe' => env('ARENE_SEO_SUFFIXE', 'Tournois e-sport, LFT et guides gaming'),
        'description' => env('ARENE_SEO_DESCRIPTION', "Calendrier des tournois e-sport mobiles d'Afrique centrale et de l'Ouest, matchmaking LFT et guides pour progresser. Synchronisé avec GameHub Arena."),

        'image_defaut' => env('ARENE_SEO_IMAGE', '/img/og-default.png'),
        'twitter' => env('ARENE_SEO_TWITTER'),

        // Codes de verification des outils de webmaster.
        'google_verification' => env('ARENE_SEO_GOOGLE'),
        'bing_verification' => env('ARENE_SEO_BING'),

        'langue' => 'fr_FR',
        'locale' => 'fr',
    ],

    /*
    |--------------------------------------------------------------------------
    | Back-office
    |--------------------------------------------------------------------------
    |
    | Le back-office vit sous un chemin non devinable plutot que /admin.
    | Renseigner ARENE_ADMIN_PATH dans .env (ex. pilotage-9f2c).
    | Laisser vide retombe sur /admin, ce qui n'est acceptable qu'en local.
    |
    */

    'admin' => [
        'path' => env('ARENE_ADMIN_PATH', 'admin'),

        // Nombre de tentatives de connexion avant blocage temporaire.
        'tentatives_max' => (int) env('ARENE_ADMIN_TENTATIVES', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Affiliation GameHub Arena
    |--------------------------------------------------------------------------
    |
    | Le lien de parrainage reçu de la console développeur. Les paramètres UTM
    | sont ajoutés à la volée pour chaque point d'entrée (calendrier, accueil).
    |
    */

    'affiliation' => [
        'lien_base' => env('GAMEHUB_AFFILIATE_URL'),

        // Le site public GameHub Arena (pas le sous-domaine api.*).
        'site_public' => env('GAMEHUB_SITE_URL', 'https://gamehub-arena.com'),

        /*
         * Taux de commission, d'après les CGU GameHub Arena (§6).
         *  - 5 % : parrainage global, une seule fois par filleul, sur sa
         *          première inscription payante (code GH-XXXXXX du wallet).
         *  - 2 % : affiliation par tournoi, via un lien de tournoi dédié.
         * Servent uniquement aux estimations affichées dans le back-office.
         */
        'commission_globale' => (float) env('GAMEHUB_COMMISSION_GLOBALE', 5),
        'commission_tournoi' => (float) env('GAMEHUB_COMMISSION_TOURNOI', 2),

        'utm' => [
            'source' => 'arene-des-gamers',
            'medium_accueil' => 'accueil',
            'medium_calendrier' => 'calendrier',
            'medium_article' => 'article',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Synchronisation des tournois
    |--------------------------------------------------------------------------
    |
    | Quota du plan gratuit GameHub Arena : 1 000 requêtes par mois.
    |
    | Chaque exécution consomme autant de requêtes que de pages parcourues.
    | Avec `per_page = 20` et un calendrier de moins de 20 tournois, une seule
    | requête suffit : 24 exécutions/jour = ~720 requêtes/mois en horaire.
    |
    | Fréquence par défaut : toutes les heures. Ne descendre en dessous de
    | 30 minutes que si le nombre de tournois reste faible, et surveiller
    | l'en-tête X-RateLimit-Remaining (un avertissement est journalisé à 50).
    |
    */

    'sync' => [
        // Doit diviser 60 (15, 20, 30 ou 60) : la planification l'exige.
        'intervalle_minutes' => (int) env('GAMEHUB_SYNC_MINUTES', 60),

        // Plafond de pages par exécution : borne le pire cas de consommation.
        'pages_max' => (int) env('GAMEHUB_SYNC_PAGES_MAX', 2),
        'per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Régie publicitaire (alternative à AdSense)
    |--------------------------------------------------------------------------
    |
    | Le MVP n'impose pas de régie précise. Le composant Blade `partials.ad`
    | injecte le code fourni par la régie choisie.
    |
    | RÈGLE PRODUIT : ne jamais placer un bloc publicitaire à côté d'un bouton
    | "S'inscrire" ou "Rejoindre". Emplacements autorisés uniquement :
    | sidebar et bas d'article.
    |
    */

    'pub' => [
        'actif' => (bool) env('ARENE_PUB_ACTIF', false),

        // Script de la régie, injecté tel quel dans la page (ex. Adsterra, PropellerAds).
        'script' => env('ARENE_PUB_SCRIPT'),

        // Ou une bannière image vendue en direct.
        'image' => env('ARENE_PUB_IMAGE'),
        'url' => env('ARENE_PUB_URL'),
        'alt' => env('ARENE_PUB_ALT', 'Publicité'),

        'mention' => 'Publicité',
    ],

];
