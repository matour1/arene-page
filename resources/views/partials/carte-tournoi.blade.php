@props(['tournoi'])

@php
    $debut = $tournoi->debut_at;
    $dateLabel = $debut ? $debut->locale('fr')->translatedFormat('D d M · H:i') : 'Date à venir';
    $places = $tournoi->places_maximum > 0
        ? $tournoi->places_restantes.'/'.$tournoi->places_maximum.' places'
        : null;
@endphp

<article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:shadow-sm">
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
            {{ $tournoi->jeu }}
        </span>

        @if ($tournoi->pro)
            <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-semibold text-violet-700">Pro</span>
        @endif

        @if ($tournoi->payant)
            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">
                {{ number_format($tournoi->frais_inscription, 0, ',', ' ') }} {{ $tournoi->devise }}
            </span>
        @else
            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">Gratuit</span>
        @endif
    </div>

    <h3 class="text-base font-bold leading-snug text-slate-900">
        <a href="{{ route('tournois.show', $tournoi) }}" class="hover:text-orange-700">
            {{ $tournoi->titre }}
        </a>
    </h3>

    <dl class="mt-3 space-y-1.5 text-sm text-slate-600">
        <div class="flex items-center gap-2">
            <dt class="sr-only">Date</dt>
            <span aria-hidden="true">🗓️</span>
            <dd>{{ $dateLabel }}</dd>
        </div>

        @if ($places)
            <div class="flex items-center gap-2">
                <dt class="sr-only">Places</dt>
                <span aria-hidden="true">👥</span>
                <dd>{{ $places }}</dd>
            </div>
        @endif

        @if ($tournoi->cagnotte_estimee > 0)
            <div class="flex items-center gap-2">
                <dt class="sr-only">Cagnotte</dt>
                <span aria-hidden="true">🏆</span>
                <dd>{{ number_format($tournoi->cagnotte_estimee, 0, ',', ' ') }} {{ $tournoi->devise }}</dd>
            </div>
        @endif
    </dl>

    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
        <a href="{{ route('tournois.show', $tournoi) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">
            Détails
        </a>

        @if ($tournoi->disponible && ! $tournoi->placesCompletes())
            <x-lien-affiliation :href="$tournoi->urlInscription('calendrier')" taille="sm">
                S'inscrire
            </x-lien-affiliation>
        @else
            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-500">
                {{ $tournoi->placesCompletes() ? 'Complet' : 'Inscriptions fermées' }}
            </span>
        @endif
    </div>
</article>
