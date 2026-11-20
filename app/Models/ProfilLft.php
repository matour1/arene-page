<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProfilLft extends Model
{
    protected $table = 'profils_lft';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'disponible' => 'boolean',
            'valide_at' => 'datetime',
        ];
    }

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', 'valide');
    }

    public function scopeFiltre(Builder $query, array $filtres): Builder
    {
        return $query
            ->when($filtres['jeu'] ?? null, fn ($q, $v) => $q->where('jeu', $v))
            ->when($filtres['pays'] ?? null, fn ($q, $v) => $q->where('pays', $v))
            ->when($filtres['langue'] ?? null, fn ($q, $v) => $q->where('langue', $v))
            ->when($filtres['role'] ?? null, fn ($q, $v) => $q->where('role', $v))
            ->when($filtres['disponible'] ?? null, fn ($q) => $q->where('disponible', true))
            ->latest();
    }

    /**
     * Le champ Discord accepte un pseudo simple ou une URL complète.
     * Un pseudo seul n'a pas d'URL canonique : on ne fabrique pas de lien mort.
     */
    public function contactDiscordUrl(): ?string
    {
        if (! $this->contact_discord) {
            return null;
        }

        if (str_starts_with($this->contact_discord, 'https://')) {
            return $this->contact_discord;
        }

        return null;
    }

    public function contactDiscordTexte(): ?string
    {
        return $this->contact_discord;
    }

    public function contactWhatsappUrl(): ?string
    {
        if (! $this->contact_whatsapp) {
            return null;
        }

        $numero = preg_replace('/\D+/', '', $this->contact_whatsapp);

        return $numero ? 'https://wa.me/'.$numero : null;
    }
}
