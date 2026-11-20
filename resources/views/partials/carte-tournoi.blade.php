@props(['tournoi'])

@php
    $debut = $tournoi->debut_at;
    $dateLabel = $debut ? $debut->locale('fr')->translatedFormat('D d M · H:i') : 'Date à venir';
    $places = $tournoi->places_maximum > 0
        ? $tournoi->places_restantes.'/'.$tournoi->places_maximum
        : null;
    $remplissage = $tournoi->places_maximum > 0
        ? min(100, round($tournoi->places_inscrits / $tournoi->places_maximum * 100))
        : 0;
@endphp

<article class="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/60">

    {{-- Bandeau superieur : jeu + statut --}}
    <div class="flex items-center justify-between gap-2 border-b border-slate-100 bg-slate-50/70 px-4 py-2.5">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-600">
            <x-icone nom="gamepad" taille="xs" />
            {{ $tournoi->jeu }}
        </span>

        <div class="flex items-center gap-1.5">
            @if ($tournoi->pro)
                <span class="rounded bg-violet-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-violet-700">
                    Pro
                </span>
            @endif

            @if ($tournoi->payant)
                <span class="chiffre rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800">
                    {{ number_format($tournoi->frais_inscription, 0, ',', ' ') }} {{ $tournoi->devise }}
                </span>
            @else
                <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-800">
                    Gratuit
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="titre line-clamp-2 text-[15px] font-bold leading-snug text-slate-900">
            <a href="{{ route('tournois.show', $tournoi) }}" class="transition-colors hover:text-arn">
                {{ $tournoi->titrePropre() }}
            </a>
        </h3>

        <dl class="mt-3 space-y-1.5 text-[13px] text-slate-600">
            <div class="flex items-center gap-2">
                <dt class="sr-only">Date</dt>
                <x-icone nom="calendar" taille="xs" class="text-slate-400" />
                <dd class="chiffre">{{ $dateLabel }}</dd>
            </div>

            @if ($tournoi->cagnotte_estimee > 0)
                <div class="flex items-center gap-2">
                    <dt class="sr-only">Cagnotte</dt>
                    <x-icone nom="trophy" taille="xs" class="text-acc" />
                    <dd class="chiffre font-semibold text-slate-800">
                        {{ number_format($tournoi->cagnotte_estimee, 0, ',', ' ') }} {{ $tournoi->devise }}
                    </dd>
                </div>
            @endif
        </dl>

        {{-- Jauge de remplissage --}}
        @if ($places)
            <div class="mt-3">
                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span class="inline-flex items-center gap-1">
                        <x-icone nom="users" taille="xs" />
                        Places
                    </span>
                    <span class="chiffre font-semibold">{{ $places }}</span>
                </div>
                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $remplissage >= 90 ? 'bg-red-400' : ($remplissage >= 60 ? 'bg-acc' : 'bg-emerald-400') }}"
                         style="width: {{ max(2, $remplissage) }}%"></div>
                </div>
            </div>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2 border-t border-slate-100 pt-3.5 mt-4">
            <a href="{{ route('tournois.show', $tournoi) }}"
               class="text-xs font-semibold text-slate-500 transition hover:text-slate-900">
                Détails
            </a>

            @if ($tournoi->disponible && ! $tournoi->placesCompletes())
                <x-lien-affiliation :href="$tournoi->urlInscription('calendrier')" taille="sm">
                    S'inscrire
                    <x-icone nom="arrow-right" taille="xs" />
                </x-lien-affiliation>
            @else
                <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-500">
                    {{ $tournoi->placesCompletes() ? 'Complet' : 'Fermé' }}
                </span>
            @endif
        </div>
    </div>
</article>