{{-- Emplacement publicitaire : sidebar ou bas d'article uniquement.
     Ne jamais inserer pres d'un bouton "S'inscrire" ou "Rejoindre". --}}
@if (config('arene.pub.actif'))
    <aside class="carte !p-3" aria-label="Publicité">
        <p class="mb-2 font-mono text-[0.62rem] uppercase tracking-[0.15em] text-dim">
            {{ config('arene.pub.mention') }}
        </p>

        @if (config('arene.pub.script'))
            <div>{!! config('arene.pub.script') !!}</div>
        @elseif (config('arene.pub.image'))
            <a href="{{ config('arene.pub.url') }}" target="_blank" rel="sponsored noopener noreferrer">
                <img src="{{ config('arene.pub.image') }}"
                     alt="{{ config('arene.pub.alt') }}"
                     class="w-full rounded-lg"
                     loading="lazy">
            </a>
        @else
            <div class="grid h-40 place-items-center rounded-lg border border-dashed border-line2 text-xs text-dim">
                Emplacement disponible
            </div>
        @endif
    </aside>
@endif