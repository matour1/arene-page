<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'publie_at' => 'datetime',
        ];
    }

    public function scopePublies(Builder $query): Builder
    {
        return $query->whereNotNull('publie_at')
            ->where('publie_at', '<=', now())
            ->latest('publie_at');
    }

    public function estPublie(): bool
    {
        return $this->publie_at !== null && $this->publie_at->isPast();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** URL absolue de l'image, qu'elle vienne d'un upload ou d'une URL distante. */
    public function urlImage(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return str_starts_with($this->image, 'http')
            ? $this->image
            : \Illuminate\Support\Facades\Storage::disk('uploads')->url($this->image);
    }

    public static function slugUnique(string $titre, ?int $ignorerId = null): string
    {
        $base = Str::slug($titre) ?: 'article';
        $slug = $base;
        $suffixe = 2;

        while (static::where('slug', $slug)->when($ignorerId, fn ($q) => $q->whereKeyNot($ignorerId))->exists()) {
            $slug = "{$base}-{$suffixe}";
            $suffixe++;
        }

        return $slug;
    }
}
