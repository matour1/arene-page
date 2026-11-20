# Arène des Gamers — MVP

Calendrier des tournois GameHub Arena, liens de parrainage trackés, articles, matchmaking LFT,
et emplacement publicitaire configurable (alternative à AdSense).

**Hors périmètre de ce MVP** : agent IA, paiements mobile money, application mobile native.

- Laravel 13 · PHP 8.3+
- Base : SQLite en local, **Neon (PostgreSQL serverless)** en production
- Front : Blade + Tailwind CSS 4 (assets compilés et versionnés)

---

## 1. Démarrage local

```bash
composer install
cp .env.example .env          # puis renseigner les valeurs
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Le `.env.example` est réglé pour la production. En local, mettre :

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
```

Le premier administrateur est créé par le seed :

| E-mail | Mot de passe |
| --- | --- |
| `admin@arene-des-gamers.test` | `password` |

**À changer immédiatement en production.**

---

## 2. Assets compilés (contrainte LWS)

LWS n'exécute pas `npm`. Les assets sont donc **compilés en local et versionnés** :

- `public/build/manifest.json`
- `public/build/assets/*`
- `public/build/fonts-manifest.json`

`public/build` n'est **pas** dans `.gitignore` (volontairement). Après toute modification
de `resources/css` ou `resources/views` :

```bash
npm install     # une seule fois
npm run build
git add public/build
git commit -m "build: mise à jour des assets compilés"
```

Sans ce commit, les styles ne changent pas en production.

---

## 3. Déploiement sur LWS

### 3.1 Fichiers

1. Vérifier en local : `npm run build`, `php artisan test`, puis **commiter `public/build`**.
2. Sur LWS, soit `git pull`, soit uploader l'archive du projet (sans `node_modules`).
3. `composer install --no-dev --optimize-autoloader`.

### 3.2 Document root

Deux configurations possibles :

**A. Recommandée** — pointer le domaine vers `public/`
(dans cPanel : *Domaines → Document Root* → `.../gaming-arene/public`).

**B. Si le document root est imposé à la racine**, le `.htaccess` fourni à la racine
réécrit tout vers `public/index.php` et bloque l'accès à `.env`, `vendor`, `storage`, etc.
Vérifier dans ce cas que `mod_rewrite` et `AllowOverride All` sont actifs.

### 3.3 Base Neon

Activer **`pdo_pgsql`** côté hébergeur (obligatoire), puis dans `.env` :

```dotenv
DB_CONNECTION=pgsql
DB_HOST=ep-xxxxxxx.eu-central-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=...
DB_PASSWORD=...
DB_SSLMODE=require
```

`DB_SSLMODE=require` est indispensable : Neon refuse les connexions non chiffrées.
`config/database.php` a été modifié pour que `require` soit la valeur par défaut.

Puis :

```bash
php artisan migrate --force
php artisan db:seed --force     # une seule fois, crée l'admin et les articles
```

### 3.4 Permissions

Écriture nécessaire pour le serveur web sur :

- `storage/`
- `bootstrap/cache/`
- `public/uploads/` (images des articles)

```bash
chmod -R 775 storage bootstrap/cache public/uploads
```

Les images sont écrites **directement dans `public/uploads`** (disque `uploads`) :
aucun `php artisan storage:link` n'est nécessaire, ce qui évite une limitation
fréquente des hébergements mutualisés.

### 3.5 Cache de configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> Après **chaque** modification du `.env`, relancer `php artisan config:cache`.

---

## 4. Synchronisation des tournois

### Quota API

Plan gratuit GameHub Arena : **1 000 requêtes/mois**, 30/minute.

Chaque exécution consomme de 1 à `GAMEHUB_SYNC_PAGES_MAX` requêtes.

| Réglage par défaut | Consommation |
| --- | --- |
| `GAMEHUB_SYNC_MINUTES=60` | 24 exécutions/jour ≈ **720 req./mois** |
| `GAMEHUB_SYNC_PAGES_MAX=2` | borne le pire cas à 2 req./exécution |

Il reste ~280 requêtes de marge pour les synchronisations manuelles du back-office.
Un avertissement est journalisé quand `X-RateLimit-Remaining` passe sous 50.

### Cron LWS

Dans cPanel → *Tâches Cron*, une seule entrée :

```
* * * * * /usr/local/bin/php /home/VOTRE_COMPTE/gaming-arene/artisan schedule:run >> /dev/null 2>&1
```

Adapter le chemin de `php` (`which php` en SSH) et le chemin du projet.

### Première synchronisation

```bash
php artisan tournois:sync
```

Ou depuis le back-office : *Tournois → Synchroniser maintenant*.

Sans `GAMEHUB_TOKEN`, la commande s'arrête proprement sans appel réseau.

---

## 5. Régie publicitaire

Le composant [`resources/views/partials/ad.blade.php`](resources/views/partials/ad.blade.php)
n'affiche rien tant que `ARENE_PUB_ACTIF=false`.

Deux modes :

```dotenv
# Script de la régie (Adsterra, PropellerAds, ...)
ARENE_PUB_ACTIF=true
ARENE_PUB_SCRIPT='<script src="..."></script>'
```

```dotenv
# Bannière vendue en direct
ARENE_PUB_ACTIF=true
ARENE_PUB_IMAGE=https://exemple.com/banniere.png
ARENE_PUB_URL=https://annonceur.com
```

**Règle produit respectée** : les blocs n'apparaissent que dans la sidebar et en bas
d'article, jamais à côté des boutons « S'inscrire » ou « Rejoindre ».

---


Chaque bouton « S'inscrire » pointe vers le lien de parrainage avec les UTM :

```
.../tournois/15?dev=<token>&utm_source=arene-des-gamers&utm_medium=calendrier&utm_campaign=arene-mvp
```

`utm_medium` varie selon le point d'entrée : `accueil`, `calendrier`, `article`.

Ordre de résolution du lien ([`App\Services\AffiliationLiens`](app/Services/AffiliationLiens.php)) :

1. `lien_affiliation` du tournoi (colonne prévue pour l'API d'affiliation) ;
2. `GAMEHUB_AFFILIATE_URL` ;
3. page publique du tournoi (`GAMEHUB_SITE_URL`).

Les liens sortants portent `rel="sponsored noopener noreferrer"`.

---

## 6. Affiliation et tracking

GameHub Arena est hébergé sur **`gamehub-arena.com`** (le sous-domaine `api.` ne sert que
l'API REST — ses pages publiques renvoient 404).

Deux dispositifs de commission, d'après les CGU (§6) :

| Dispositif | Taux | Déclencheur |
| --- | --- | --- |
| Parrainage global (code `GH-XXXXXX`) | **5 %** | Première inscription payante d'un filleul, une seule fois par filleul |
| Lien de tournoi dédié | **2 %** | Chaque inscription payante finalisée sur ce tournoi |

Les deux ne se cumulent pas sur la même inscription. Les taux affichés dans le
back-office se règlent avec `GAMEHUB_COMMISSION_GLOBALE` et `GAMEHUB_COMMISSION_TOURNOI`.

### Saisir un lien de parrainage

Le lien personnalisé n'est généré qu'**après connexion** au compte GameHub Arena.
Il se colle donc tournoi par tournoi :

1. Back-office → *Tournois*
2. Icône crayon sur la ligne du tournoi
3. Coller l'URL, enregistrer

Le bandeau indique le nombre de tournois couverts par un lien dédié.

### UTM

Chaque bouton « S'inscrire » reçoit les UTM :

```
?utm_source=arene-des-gamers&utm_medium=calendrier&utm_campaign=arene-mvp
```

`utm_medium` varie selon le point d'entrée : `accueil`, `calendrier`, `article`.
Les UTM déjà présents dans un lien collé **ne sont jamais écrasés**.

### Ordre de résolution

1. `lien_affiliation` du tournoi ;
2. `GAMEHUB_AFFILIATE_URL` ;
3. page publique du tournoi (`GAMEHUB_SITE_URL`) — **sans commission**.

Les liens sortants portent `rel="sponsored noopener noreferrer"`.

---

## 7. Identité visuelle

- Logo : [`public/img/logo.svg`](public/img/logo.svg) (fond sombre) et
  [`public/img/logo-clair.svg`](public/img/logo-clair.svg) (fond clair). Hexagone,
  « A » stylisé, palette `#e2e8f5` / `#00d4ff`. Fourni en 16 px (favicon) à 80 px.
- Icônes : composant Blade [`resources/views/components/icone.blade.php`](resources/views/components/icone.blade.php),
  SVG inline style Lucide. **Aucune dépendance externe** (Font Awesome, CDN) : rien à
  installer ni à charger côté serveur. Ajouter une icône = ajouter une entrée dans le tableau.
- Animations : `fade-up`, `fade-in`, `scale-in`, `pulse-soft`, `float`, `glow`
  (déclarées dans [`resources/css/app.css`](resources/css/app.css)). Toutes désactivées
  automatiquement si `prefers-reduced-motion: reduce`.
- Pages d'erreur : `404`, `403`, `419`, `429`, `500`, `503` dans
  [`resources/views/errors/`](resources/views/errors), avec la charte du site.

---
## 8. Structure utile

| Chemin | Rôle |
| --- | --- |
| [`config/arene.php`](config/arene.php) | Identité du site, affiliation, quota de sync, régie pub |
| [`app/Services/GameHub/`](app/Services/GameHub) | Client API + synchroniseur |
| [`app/Services/AffiliationLiens.php`](app/Services/AffiliationLiens.php) | Construction des liens trackés |
| [`app/Console/Commands/SyncTournois.php`](app/Console/Commands/SyncTournois.php) | Commande `tournois:sync` |
| [`routes/console.php`](routes/console.php) | Planification |
| [`resources/views/`](resources/views) | Vues Blade publiques et back-office |

---

## 9. Tests

```bash
php artisan test
```

Couverture du MVP : pages publiques, filtres, invisibilité des brouillons et des tournois
passés, UTM des liens d'affiliation, mapping de la synchronisation API (via `Http::fake`),
gestion du quota 429 et du token absent, validation du formulaire LFT, contrôle d'accès admin.

---

## 10. Points de vigilance

- **`pdo_pgsql`** : sans cette extension, Neon est inaccessible. À vérifier avant la mise en ligne.
- **Fuseau horaire** : `APP_TIMEZONE=Africa/Douala`. Les dates API (`+01:00`) sont converties
  puis affichées dans ce fuseau. Changer cette valeur décale tous les horaires affichés.
- **Cache** : Laravel 13 refuse de désérialiser des classes depuis le cache
  (`serializable_classes => false`). Ne jamais mettre un modèle ou une collection Eloquent
  en cache — uniquement des scalaires ou des tableaux.
- **Back-office** : accessible uniquement aux comptes `is_admin = true`.
