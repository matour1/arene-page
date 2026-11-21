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

**B. Si le document root est imposé à la racine**, le [`.htaccess`](.htaccess) racine
redirige tout le trafic vers `public/` :

```
/                    → public/index.php        (Laravel)
/build/app-xxx.css   → public/build/…
/uploads/…           → public/uploads/…
/img/…               → public/img/…
/robots.txt          → Laravel (généré)
/.env, /vendor/…     → 403
```

Prérequis : `mod_rewrite` **et** `AllowOverride All` actifs. Vérification :

```bash
curl -I https://votre-domaine.com/.env
curl -I https://votre-domaine.com/build/manifest.json
```

Attendu : `403` sur `.env`, `200` sur le manifest, `200` sur la page d'accueil.

Si `.env` renvoie `200`, **le `.htaccess` n'est pas lu** : votre document root
pointe probablement ailleurs, ou `AllowOverride` est désactivé. Dans ce cas,
retournez en configuration A (pointeur direct sur `public/`).

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

## 7. Identité visuelle et thèmes

Charte officielle **v1.1 « Sobre & Néon »**.

| Rôle | Police |
| --- | --- |
| Titres | **Orbitron** 500–900 |
| Corps | **Inter** 400–800 |
| Données (prix, dates, IDs) | **JetBrains Mono** 400–600 |

Palette (thème sombre, référence) :

| Token | Sombre | Clair |
| --- | --- | --- |
| `base` (fond) | `#0a0c10` | `#f1f5f9` |
| `surface` (cartes) | `#12151e` | `#ffffff` |
| `elevated` | `#1a1e2b` | `#f8fafc` |
| `cy` (accent) | `#00f0ff` | `#0891b2` |
| `vio` | `#8b5cf6` | `#7c3aed` |
| `yel` | `#facc15` | `#b45309` |
| `txt` | `#f8fafc` | `#0a0c10` |
| `mut` | `#94a3b8` | `#475569` |
| `line` | `#1e2433` | `#e2e8f0` |

### Thème clair / sombre

Un bouton de bascule se trouve dans l'en-tête de toutes les pages (site, back-office,
connexion, erreurs). Comportement :

1. **Choix mémorisé** dans `localStorage` (`arene-theme`).
2. **Sans choix**, suit `prefers-color-scheme` du système.
3. **Aucun flash** : un script inline dans `<head>` applique l'attribut
   `data-theme` sur `<html>` avant le premier rendu.
4. Les tokens vivent dans `@theme` (pour que Tailwind génère les utilitaires)
   et sont surchargés sur `:root[data-theme='light']` ([app.css](resources/css/app.css)).

Pour forcer un thème par défaut : modifier le script inline du `<head>`.

### Icônes et animations

- Icônes : composant [icone.blade.php](resources/views/components/icone.blade.php), SVG inline
  (style Lucide), ~48 icônes. Aucune dépendance ni CDN.
- Animations : `fadeUp`, `blink`, `shimmer`, `shimmer-bg`, `float`, `spin`.
  Barre de progression de navigation + squelette `.skeleton`.
- Toutes désactivées sous `prefers-reduced-motion: reduce`.
- **Aucun emoji** dans l'interface ; `Tournoi::titrePropre()` nettoie ceux de l'API.

### Responsive

- Menu mobile en accordéon (site et back-office) sous 768 px.
- `overflow-x: hidden` en base : aucune barre de défilement horizontale.
- Filtres en 2 colonnes sur mobile, 4 sur desktop ; boutons pleine largeur sous 640 px.
- Le nom de marque est masqué sous 420 px (Orbitron est très large).

---

La charte reprend les tokens de `gamehub-arena.com` :

| Élément | Valeur |
| --- | --- |
| Titres | **Sora** (500–800) |
| Corps | **Inter** (400–700) |
| Données chiffrées, dates, prix | **Space Mono** |
| Or (accent principal) | `#d4a53a` |
| Orange (CTA) | `#f2703a` |
| Cyan | `#5cc8d8` |
| Vert | `#3ddc97` |

Polices chargées depuis Google Fonts (`layouts/app.blade.php` et `layouts/admin.blade.php`).
Aucune police n'est compilée : rien à installer côté serveur.

**Icônes** : composant Blade [`resources/views/components/icone.blade.php`](resources/views/components/icone.blade.php),
SVG inline (style Lucide), ~45 icônes. Aucune dépendance externe — pas de Font Awesome, pas de CDN.

**Animations** : `fade-up`, `fade-in`, `scale-in`, `pulse-soft`, `float`, `ticker`, `pulse-ring`,
plus un squelette `.skeleton` et une barre de progression de navigation.
Toutes sont désactivées si `prefers-reduced-motion: reduce`.

**Aucun emoji dans l'interface.** Les titres de tournois venant de l'API en contiennent :
ils sont nettoyés par `Tournoi::titrePropre()` avant affichage.

**Responsive** :
- Menu mobile en accordéon (bouton hamburger) sous 768 px, sur le site **et** le back-office.
- `overflow-x: hidden` en base, pour éliminer les barres de défilement horizontales.
- Filtres en 2 colonnes sur mobile, 4 sur desktop.
- Les boutons passent en pleine largeur sous 640 px.

---

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
## 8. SEO

### Métadonnées par page

Chaque contrôleur injecte un objet [`Metadonnees`](app/Services/Seo/Metadonnees.php)
(instance unique par requête), consommé par [`partials/seo.blade.php`](resources/views/partials/seo.blade.php) :

```php
$this->seo
    ->titre('Calendrier des tournois e-sport')
    ->description('Tous les tournois e-sport mobiles à venir…')
    ->canonical(route('tournois.index'))
    ->noindex();          // si page à exclure
```

Le partial génère : `<title>`, `meta description` (tronquée à 158 caractères),
`meta robots`, `link canonical`, `hreflang`, Open Graph complet, Twitter Card,
balises de vérification Search Console / Bing, et les blocs JSON-LD.

### Données structurées

| Page | Schémas |
| --- | --- |
| Toutes | `Organization`, `WebSite` (avec `SearchAction`) |
| `/tournois` | `ItemList` des tournois à venir |
| `/tournois/{id}` | `Event` + `Offer` (prix, devise, `InStock`/`SoldOut`) |
| `/articles/{slug}` | `BlogPosting` |

Les pages en `noindex` ne reçoivent **aucun** JSON-LD (évite les avertissements
Search Console sur des données incohérentes).

### Pages non indexées

- Résultats de filtres et pagination (`?jeu=`, `?page=`, `?pays=`…) → contenu dupliqué
- `/lft/merci` (confirmation de formulaire)
- Tournois passés ou complets (plus d'action possible pour l'internaute)

### Sitemap et robots

| URL | Contenu |
| --- | --- |
| `/sitemap.xml` | Généré à la demande, cache 1 h. Exclut brouillons, tournois passés/complets, back-office |
| `/robots.txt` | Généré avec l'`APP_URL` réelle — pas de domaine codé en dur |

Les deux utilisent les routes nommées, donc ils restent corrects si vous changez
le chemin du back-office (`ARENE_ADMIN_PATH`).

### Image de partage

[`public/img/og-default.png`](public/img/og-default.png) — 1200×630, générée selon la charte.
Les articles utilisent leur propre image quand elle existe.

### Variables

```dotenv
ARENE_SEO_SUFFIXE="Tournois e-sport, LFT et guides gaming"
ARENE_SEO_DESCRIPTION="Calendrier des tournois e-sport mobiles…"
ARENE_SEO_GOOGLE=        # code de vérification Search Console
ARENE_SEO_BING=          # code de vérification Bing
ARENE_SEO_TWITTER=       # @compte (Twitter Card)
```

### Après la mise en ligne

1. **Search Console** → ajouter la propriété → récupérer le code de vérification
   → `ARENE_SEO_GOOGLE=...` → `php artisan config:cache`
2. Soumettre `https://votre-domaine.com/sitemap.xml`
3. Vérifier une page tournoi dans le **Rich Results Test** de Google
4. Tester le partage sur WhatsApp/Facebook (OG image)

---
## 9. Structure utile

| Chemin | Rôle |
| --- | --- |
| [`config/arene.php`](config/arene.php) | Identité du site, affiliation, quota de sync, régie pub |
| [`app/Services/GameHub/`](app/Services/GameHub) | Client API + synchroniseur |
| [`app/Services/AffiliationLiens.php`](app/Services/AffiliationLiens.php) | Construction des liens trackés |
| [`app/Console/Commands/SyncTournois.php`](app/Console/Commands/SyncTournois.php) | Commande `tournois:sync` |
| [`routes/console.php`](routes/console.php) | Planification |
| [`resources/views/`](resources/views) | Vues Blade publiques et back-office |

---

## 10. Tests

```bash
php artisan test
```

Couverture du MVP : pages publiques, filtres, invisibilité des brouillons et des tournois
passés, UTM des liens d'affiliation, mapping de la synchronisation API (via `Http::fake`),
gestion du quota 429 et du token absent, validation du formulaire LFT, contrôle d'accès admin.

---

## 11. Points de vigilance

- **`pdo_pgsql`** : sans cette extension, Neon est inaccessible. À vérifier avant la mise en ligne.
- **Fuseau horaire** : `APP_TIMEZONE=Africa/Douala`. Les dates API (`+01:00`) sont converties
  puis affichées dans ce fuseau. Changer cette valeur décale tous les horaires affichés.
- **Cache** : Laravel 13 refuse de désérialiser des classes depuis le cache
  (`serializable_classes => false`). Ne jamais mettre un modèle ou une collection Eloquent
  en cache — uniquement des scalaires ou des tableaux.
- **Back-office** : accessible uniquement aux comptes `is_admin = true`.

## 12. Accès au back-office

Le back-office **n'est pas** sur `/admin`. Il vit sous un chemin non devinable défini par
`ARENE_ADMIN_PATH` :

```dotenv
ARENE_ADMIN_PATH=pilotage-3e46jpdcr9
```

L'URL de connexion devient donc :

```
https://votre-domaine.com/pilotage-3e46jpdcr9/connexion
```

`/admin` et `/connexion` renvoient **404** — aucun formulaire de connexion n'est exposé
à un chemin devinable.

**Changer le secret** : modifier `ARENE_ADMIN_PATH`, puis `php artisan config:cache`.
Tous les liens internes du back-office suivent automatiquement (routes nommées).

> Le seed crée `admin@arene-des-gamers.test` / `password`. **Changez ce mot de passe**
> avant la mise en ligne.

---

## 13. Neon — base de production

### Projet

| Élément | Valeur |
| --- | --- |
| Projet | `arene-des-gamers` (`green-cake-22214162`) |
| Région | `aws-eu-central-1` (Francfort) — le plus proche de l'Afrique centrale |
| PostgreSQL | 17.12 |
| Branche | `main` |
| Base | `neondb` |

### Chaîne de connexion

Deux hôtes, **à ne pas confondre** :

| Usage | Hôte |
| --- | --- |
| Application (requêtes courtes) | `ep-snowy-darkness-bhu5b5sd-pooler.c-10.eu-central-1.aws.neon.tech` |
| Migrations / DDL | `ep-snowy-darkness-bhu5b5sd.c-10.eu-central-1.aws.neon.tech` (sans `-pooler`) |

```dotenv
DB_CONNECTION=pgsql
DB_HOST=ep-snowy-darkness-bhu5b5sd-pooler.c-10.eu-central-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=...
DB_SSLMODE=require
```

> Le mot de passe est dans `.env.production`, **ignoré par Git**. Ne le commitez jamais.

### État de la base

| Table | Lignes |
| --- | --- |
| `users` | 1 (admin) |
| `articles` | 4 |
| `tournois` | 1 |
| `profils_lft` | 0 |
| `migrations` | 7 |

Identifiants admin par défaut : `admin@arene-des-gamers.test` / `password`.
**À changer immédiatement** après la première connexion.

### ⚠️ Piège réseau : port 5432 bloqué

Sur certains réseaux (FAI, proxy d'entreprise, Wi-Fi public), **le port 5432 est
intercepté par un proxy transparent**. Symptômes :

```
TCP connecté en 0,1 s
... puis timeout au handshake PostgreSQL
```

Le piège : `Test-NetConnection` renvoie `True` pour *n'importe quelle* adresse,
y compris `192.0.2.1` (non routable). Le TCP aboutit localement, mais rien
ne parvient au serveur.

**Diagnostic** — envoyer un `SSLRequest` PostgreSQL brut (8 octets) :

```js
const b = Buffer.alloc(8);
b.writeInt32BE(8, 0);
b.writeInt32BE(80877103, 4);  // code SSLRequest
socket.write(b);               // réponse attendue : 'S' ou 'N'
```

Aucune réponse = port bloqué.

**Contournement** : le driver HTTP de Neon passe par HTTPS (443), qui n'est
jamais bloqué.

```bash
npm install @neondatabase/serverless
```

```js
import { neon } from '@neondatabase/serverless';
const sql = neon(process.env.DATABASE_URL);
const rows = await sql`select * from tournois`;
```

Ce driver sert à l'administration ponctuelle, **pas** à l'application :
Laravel utilise `pdo_pgsql` en TCP. Sur LWS (datacenter), le port 5432 n'est
pas filtré — le problème est propre à certains réseaux clients.

### Extension PHP requise

`pdo_pgsql` doit être actif. En local sous WAMP :

```ini
; C:\wamp64\bin\php\php8.4.15\php.ini
extension=pdo_pgsql
extension=pgsql
```

Vérification : `php -m | findstr pgsql`

---

## 14. Neon — accès agent (CLI, skills, MCP)

L'environnement agent est configuré selon
[neon.com/prompts/agent-setup.md](https://neon.com/prompts/agent-setup.md).

| Élément | État |
| --- | --- |
| CLI Neon | `9.9.2` |
| Connexion | OAuth, profil `DEFAULT` |
| Skills | `neon`, `neon-auth`, `neon-postgres`, `neon-functions`, `neon-ai-gateway`, `neon-object-storage` |
| MCP | `https://mcp.neon.tech/mcp` (VS Code, user-level) |

### Commandes utiles

```bash
neon projects list                    # lister les projets
neon connection-string --project-id <id> --no-pooled
neon psql --project-id <id>           # console SQL
neon skills status --global           # vérifier les skills
```

> Le MCP ne se charge qu'après **redémarrage de l'agent**.

---

## 15. Maintenance sans SSH (terminal limité ou cPanel seul)

Si le terminal du serveur est absent ou limité, une page de maintenance
accessible par navigateur remplace les commandes Artisan.

### Mise en service

1. Générer une clé :

```bash
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"
```

2. Renseigner `.env` sur le serveur :

```dotenv
ARENE_MAINTENANCE_KEY=la_cle_generee
```

3. Ouvrir :

```
https://votre-domaine.com/_maintenance.php?cle=la_cle_generee
```

### Ce qu'elle affiche

- Connexion base (PostgreSQL ou SQLite selon `DB_CONNECTION`)
- Présence des caches de configuration et de routes
- Valeur effective de `ARENE_ADMIN_PATH` et lien direct vers la connexion
- Liens de test `/admin/connexion` vs chemin secret

### Ce qu'elle permet

| Bouton | Effet |
| --- | --- |
| Vider les caches | `config:clear`, `route:clear`, `view:clear`, `cache:clear` |
| Régénérer les caches | `config:cache`, `route:cache`, `view:cache` |
| Synchroniser les tournois | `tournois:sync` |

Chaque commande affiche son code de sortie et sa sortie complète.

### ⚠️ Sécurité

- Sans `ARENE_MAINTENANCE_KEY`, la page répond **403** (aucun accès ouvert).
- La clé est comparée avec `hash_equals` (résistant au timing).
- La page émet `noindex, nofollow`.

**Supprimez `public/_maintenance.php` une fois le déploiement terminé :**

```bash
rm public/_maintenance.php
```

---
