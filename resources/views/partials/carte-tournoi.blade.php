@props(['tournoi'])

@php
    $debut = $tournoi->debut_at;
    $dateLabel = $debut ? $debut->locale('fr')->translatedFormat('D d M · H:i') : 'À venir';
    $remplissage = $tournoi->places_maximum > 0
        ? min(100, round($tournoi->places_inscrits / $tournoi->places_maximum * 100))
        : 0;
    $complet = $tournoi->placesCompletes();
    $ouvert = $tournoi->disponible && ! $complet;
@endphp

<article class="carte-tournoi">

    {{-- En-tete : titre + statut --}}
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="tournament-title line-clamp-2 text-[0.98rem] font-bold leading-snug text-txt sm:text-[1.05rem]">
                <a href="{{ route('tournois.show', $tournoi) }}" class="transition hover:text-cy">
                    {{ $tournoi->titrePropre() }}
                </a>
            </h3>
            <p class="chiffre mt-1 text-[0.85rem] text-mut">{{ $dateLabel }}</p>
        </div>

        @if ($complet)
            <span class="badge badge-danger shrink-0 whitespace-nowrap">Complet</span>
        @elseif ($ouvert)
            <span class="badge badge-ok badge-dot shrink-0 whitespace-nowrap">Ouvert</span>
        @else
            <span class="badge badge-neutral shrink-0">Fermé</span>
        @endif
    </div>

    {{-- Badges jeu / format --}}
    <div class="flex flex-wrap gap-1.5">
        <span class="badge badge-neutral">{{ $tournoi->jeu }}</span>
        @if ($tournoi->format)
            <span class="badge badge-neutral">{{ str_replace('_', ' ', $tournoi->format) }}</span>
        @endif
        @if ($tournoi->pro)
            <span class="badge badge-violet">Pro</span>
        @endif
    </div>

    {{-- Metadonnees : grille 2 colonnes --}}
    <dl class="grid grid-cols-2 gap-x-3 gap-y-2.5 text-[0.82rem]">
        <div class="flex min-w-0 flex-col gap-0.5">
            <dt class="font-mono text-[0.66rem] uppercase tracking-wider text-dim">Cagnotte</dt>
            <dd class="titre truncate font-extrabold {{ $tournoi->cagnotte_estimee > 0 ? 'text-yel' : 'text-mut' }}">
                {{ $tournoi->cagnotte_estimee > 0
                    ? number_format($tournoi->cagnotte_estimee, 0, ',', ' ').' '.$tournoi->devise
                    : '—' }}
            </dd>
        </div>

        <div class="flex min-w-0 flex-col gap-0.5">
            <dt class="font-mono text-[0.66rem] uppercase tracking-wider text-dim">Inscription</dt>
            <dd class="chiffre truncate font-semibold text-txt">
                {{ $tournoi->payant ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise : 'Gratuit' }}
            </dd>
        </div>

        <div class="flex flex-col gap-0.5">
            <dt class="font-mono text-[0.66rem] uppercase tracking-wider text-dim">Places</dt>
            <dd class="chiffre font-semibold text-txt">
                {{ $tournoi->places_inscrits }} / {{ $tournoi->places_maximum }}
            </dd>
        </div>

        <div class="flex flex-col gap-0.5">
            <dt class="font-mono text-[0.66rem] uppercase tracking-wider text-dim">Restantes</dt>
            <dd class="chiffre font-semibold {{ $tournoi->places_restantes <= 5 ? 'text-warn' : 'text-txt' }}">
                {{ $tournoi->places_restantes }}
            </dd>
        </div>
    </dl>

    {{-- Jauge de remplissage --}}
    <div class="progress" role="progressbar"
         aria-valuenow="{{ $remplissage }}" aria-valuemin="0" aria-valuemax="100"
         aria-label="Remplissage">
        @php
            $couleurJauge = $remplissage >= 90 ? '!bg-err' : ($remplissage >= 60 ? '!bg-yel' : '!bg-cy');
        @endphp
        <div class="progress-fill progress-fill-anime {{ $couleurJauge }}"
             style="width: {{ max(2, $remplissage) }}%"></div>
    </div>

    <div class="mt-auto flex items-center gap-2">
        <a href="{{ route('tournois.show', $tournoi) }}" class="btn btn-secondary btn-sm flex-1">
            Détails
        </a>

        @if ($ouvert)
            <x-lien-affiliation :href="$tournoi->urlInscription('calendrier')" taille="sm" class="flex-1">
                <x-icone nom="crosshair" taille="xs" />
                S'inscrire
            </x-lien-affiliation>
        @else
            <span class="btn btn-secondary btn-sm flex-1 cursor-not-allowed opacity-50">
                {{ $complet ? 'Complet' : 'Fermé' }}
            </span>
        @endif
    </div>
</article>