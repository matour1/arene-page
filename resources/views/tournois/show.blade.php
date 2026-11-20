@extends('layouts.app')

@section('titre', $tournoi->titrePropre())
@section('description', 'Détails du tournoi '.$tournoi->titrePropre().' — '.$tournoi->jeu.'.')

@section('contenu')
    @php
        $complet = $tournoi->placesCompletes();
        $ouvert = $tournoi->disponible && ! $complet;
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-10">
        <a href="{{ route('tournois.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-mut transition hover:text-cy">
            <x-icone nom="arrow-left" taille="sm" />
            Calendrier
        </a>

        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="carte !p-6 sm:!p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge badge-neutral">{{ $tournoi->jeu }}</span>
                        @if ($tournoi->format)
                            <span class="badge badge-neutral">{{ str_replace('_', ' ', $tournoi->format) }}</span>
                        @endif
                        @if ($tournoi->pro)
                            <span class="badge badge-violet">Pro</span>
                        @endif
                        @if ($complet)
                            <span class="badge badge-danger">Complet</span>
                        @elseif ($ouvert)
                            <span class="badge badge-ok badge-dot">Inscriptions ouvertes</span>
                        @else
                            <span class="badge badge-neutral">Fermé</span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-xl font-bold leading-tight text-txt sm:text-2xl">
                        {{ $tournoi->titrePropre() }}
                    </h1>

                    <dl class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-line bg-elevated p-4">
                            <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Début</dt>
                            <dd class="chiffre mt-1 text-sm font-bold text-txt">
                                {{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M Y · H:i') ?? 'À confirmer' }}
                            </dd>
                        </div>

                        <div class="rounded-lg border border-line bg-elevated p-4">
                            <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Fin inscriptions</dt>
                            <dd class="chiffre mt-1 text-sm font-bold text-txt">
                                {{ $tournoi->fin_inscription_at?->locale('fr')->translatedFormat('d M Y · H:i') ?? 'À confirmer' }}
                            </dd>
                        </div>

                        <div class="rounded-lg border border-line bg-elevated p-4">
                            <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Places</dt>
                            <dd class="chiffre mt-1 text-sm font-bold text-txt">
                                {{ $tournoi->places_inscrits }} / {{ $tournoi->places_maximum }}
                                <span class="font-normal text-mut">({{ $tournoi->places_restantes }} restantes)</span>
                            </dd>
                        </div>

                        <div class="rounded-lg border border-line bg-elevated p-4">
                            <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Inscription</dt>
                            <dd class="chiffre mt-1 text-sm font-bold text-txt">
                                {{ $tournoi->payant ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise : 'Gratuit' }}
                            </dd>
                        </div>

                        @if ($tournoi->cagnotte_estimee > 0)
                            <div class="rounded-lg border border-line bg-elevated p-4">
                                <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Cagnotte</dt>
                                <dd class="titre mt-1 text-sm font-extrabold text-yel">
                                    {{ number_format($tournoi->cagnotte_estimee, 0, ',', ' ') }} {{ $tournoi->devise }}
                                </dd>
                            </div>
                        @endif

                        @if ($tournoi->rang_min || $tournoi->rang_max)
                            <div class="rounded-lg border border-line bg-elevated p-4">
                                <dt class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Rang requis</dt>
                                <dd class="chiffre mt-1 text-sm font-bold text-txt">
                                    {{ $tournoi->rang_min ?? '—' }} → {{ $tournoi->rang_max ?? '—' }}
                                </dd>
                            </div>
                        @endif
                    </dl>

                    @if (! $tournoi->disponible && $tournoi->raison_indisponibilite)
                        <div class="alerte alerte-warn mt-6">
                            <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
                            <span>{{ $tournoi->raison_indisponibilite }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-4">
                <div class="carte !p-6">
                    <p class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Participer</p>

                    <div class="mt-4">
                        @if ($ouvert)
                            <x-lien-affiliation :href="$tournoi->urlInscription('calendrier')" taille="lg" class="w-full">
                                <x-icone nom="crosshair" taille="sm" />
                                S'inscrire
                            </x-lien-affiliation>
                            <p class="mt-3 text-xs leading-relaxed text-dim">
                                L'inscription se finalise sur GameHub Arena. Nous percevons une commission
                                sur les inscriptions payantes, sans surcoût pour toi.
                            </p>
                        @else
                            <div class="alerte alerte-err">
                                <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
                                <span>{{ $complet ? 'Ce tournoi est complet.' : 'Les inscriptions sont fermées.' }}</span>
                            </div>
                            <a href="{{ route('tournois.index') }}" class="btn btn-neon btn-sm mt-3 w-full">
                                Voir les autres tournois
                            </a>
                        @endif
                    </div>
                </div>

                @include('partials.ad')
            </aside>
        </div>
    </div>
@endsection