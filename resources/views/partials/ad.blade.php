{{-- Emplacement publicitaire : sidebar ou bas d'article uniquement.
     Ne jamais insérer près d'un bouton "S'inscrire" ou "Rejoindre" (règle produit du MVP). --}}
@if (config('arene.pub.actif'))
    <aside class="rounded-2xl border border-slate-200 bg-white p-3" aria-label="Publicité">
        <p class="mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-400">
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
            <div class="flex h-40 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400">
                Emplacement disponible
            </div>
        @endif
    </aside>
@endif
