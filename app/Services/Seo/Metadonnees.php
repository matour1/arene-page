<?php

namespace App\Services\Seo;

use Illuminate\Support\Str;

/**
 * Construit les metadonnees d'une page : titre, description, canonical,
 * Open Graph, Twitter Card et donnees structurees schema.org.
 *
 * Les valeurs sont resolues a l'affichage, donc chaque controleur peut
 * surcharger uniquement ce qui le concerne.
 */
class Metadonnees
{
    private ?string $titre = null;

    private ?string $description = null;

    private ?string $canonical = null;

    private ?string $image = null;

    private string $type = 'website';

    private bool $noindex = false;

    /** @var array<int, array<string, mixed>> */
    private array $donneesStructurees = [];

    public function titre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function canonical(?string $url): static
    {
        $this->canonical = $url;

        return $this;
    }

    public function image(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /** Empeche l'indexation (pages de confirmation, resultats de filtres...). */
    public function noindex(bool $actif = true): static
    {
        $this->noindex = $actif;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $donnees
     */
    public function schema(array $donnees): static
    {
        $this->donneesStructurees[] = $donnees;

        return $this;
    }

    public function titreComplet(): string
    {
        $nom = config('arene.site.nom');
        $suffixe = config('arene.seo.titre_suffixe');

        if (blank($this->titre)) {
            return "{$nom} — {$suffixe}";
        }

        return "{$this->titre} | {$nom}";
    }

    public function descriptionFinale(): string
    {
        $texte = $this->description ?: config('arene.seo.description');

        // Les moteurs tronquent au-dela de ~160 caracteres.
        return Str::limit(strip_tags((string) $texte), 158, '');
    }

    public function urlCanonical(): string
    {
        return $this->canonical ?: url()->current();
    }

    public function urlImage(): string
    {
        $image = $this->image ?: config('arene.seo.image_defaut');

        return Str::startsWith($image, ['http://', 'https://'])
            ? $image
            : url($image);
    }

    public function typePage(): string
    {
        return $this->type;
    }

    public function estNoindex(): bool
    {
        return $this->noindex;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function schemas(): array
    {
        return $this->donneesStructurees;
    }

    /**
     * Donnees structurees communes, injectees sur chaque page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function schemasGlobaux(): array
    {
        $nom = config('arene.site.nom');

        $organisation = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $nom,
            'url' => url('/'),
            'logo' => url('/img/logo.svg'),
            'description' => config('arene.seo.description'),
        ];

        $whatsapp = config('arene.site.communaute_whatsapp');

        if (filled($whatsapp)) {
            $organisation['sameAs'] = [$whatsapp];
        }

        $site = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $nom,
            'url' => url('/'),
            'inLanguage' => 'fr',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/tournois').'?jeu={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return [$organisation, $site];
    }
}
