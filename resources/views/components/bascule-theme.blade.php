@props([
    'classe' => '',
])

{{-- Bascule clair / sombre. L'icone affichee est pilotee par CSS selon
     l'attribut data-theme de <html>, sans dependre du JavaScript. --}}
<button type="button"
        data-bascule-theme
        title="Changer de thème"
        aria-label="Changer de thème clair ou sombre"
        class="inline-flex size-9 items-center justify-center rounded-lg border border-line2 bg-elevated text-mut transition hover:border-cy hover:text-cy {{ $classe }}">
    <span class="icone-en-sombre">
        <x-icone nom="sun" taille="sm" />
    </span>
    <span class="icone-en-clair">
        <x-icone nom="moon" taille="sm" />
    </span>
</button>