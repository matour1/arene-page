<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Tournoi extends Model
{
    protected $table = 'tournois';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
            'payant' => 'boolean',
            'pro' => 'boolean',
            'frais_inscription' => 'integer',
            'cagnotte_estimee' => 'integer',
            'places_inscrits' => 'integer',
            'places_maximum' => 'integer',
            'places_restantes' => 'integer',
            'debut_at' => 'datetime',
            'fin_inscription_at' => 'datetime',
            'synchronise_at' => 'datetime',
        ];
    }

    /** Tournois encore ouverts aux inscriptions et à venir. */
    public function scopeAVenir(Builder $query): Builder
    {
        return $query->where('statut', 'ouvert')
            ->whereNotNull('debut_at')
            ->where('debut_at', '>=', now());
    }

    public function scopeAffiche(Builder $query): Builder
    {
        return $query->where('visibilite', 'public')->orderBy('debut_at');
    }

    /**
     * Titre sans emoji : les annonces GameHub en contiennent, ce qui casse
     * l'alignement typographique et l'accessibilite (lecteurs d'ecran).
     */
    public function titrePropre(): string
    {
        $nettoye = preg_replace('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE0F}\x{200D}]/u', '', $this->titre);

        return trim(preg_replace('/\s+/u', ' ', (string) $nettoye));
    }

    public function placesCompletes(): bool
    {
        return $this->places_maximum > 0 && $this->places_restantes === 0;
    }

    public function urlInscription(?string $medium = 'calendrier'): string
    {
        return app(\App\Services\AffiliationLiens::class)->url($this, $medium);
    }
}
