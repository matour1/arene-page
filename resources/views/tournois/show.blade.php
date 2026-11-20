@extends('layouts.app')

@section('titre', $tournoi->titre)
@section('description', 'Détails du tournoi '.$tournoi->titre.' — '.$tournoi->jeu.'.')

@section('contenu')
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        <a href="{{ route('tournois.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">
            ← Calendrier
        </a>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                            {{ $tournoi->jeu }}
                        </span>
                        @if ($tournoi->format)
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                                {{ str_replace('_', ' ', $tournoi->format) }}
                            </span>
                        @endif
                        @if ($tournoi->pro)
                            <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-semibold text-violet-700">Pro</span>
                        @endif
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold
                            {{ $tournoi->disponible ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $tournoi->disponible ? 'Inscriptions ouvertes' : 'Inscriptions fermées' }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">
                        {{ $tournoi->titre }}
                    </h1>

                    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Début</dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                {{ $tournoi->debut_at?->locale('fr')->translatedFormat('l d F Y · H:i') ?? 'À confirmer' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Fin des inscriptions</dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                {{ $tournoi->fin_inscription_at?->locale('fr')->translatedFormat('d F Y · H:i') ?? 'À confirmer' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Places</dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                {{ $tournoi->places_inscrits }} / {{ $tournoi->places_maximum }}
                                <span class="text-sm font-normal text-slate-500">
                                    ({{ $tournoi->places_restantes }} restantes)
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Frais d'inscription</dt>
                            <dd class="mt-1 font-bold text-slate-900">
                                {{ $tournoi->payant ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise : 'Gratuit' }}
                            </dd>
                        </div>

                        @if ($tournoi->cagnotte_estimee > 0)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cagnotte estimée</dt>
                                <dd class="mt-1 font-bold text-slate-900">
                                    {{ number_format($tournoi->cagnotte_estimee, 0, ',', ' ') }} {{ $tournoi->devise }}
                                </dd>
                            </div>
                        @endif

                        @if ($tournoi->rang_min || $tournoi->rang_max)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <dt class="text-xs font-semibold uppercase tracking-widest text-slate-500">Rang requis</dt>
                                <dd class="mt-1 font-bold text-slate-900">
                                    {{ $tournoi->rang_min ?? '—' }} → {{ $tournoi->rang_max ?? '—' }}
                                </dd>
                            </div>
                        @endif
                    </dl>

                    @if (! $tournoi->disponible && $tournoi->raison_indisponibilite)
                        <p class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                            {{ $tournoi->raison_indisponibilite }}
                        </p>
                    @endif
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-sm font-extrabold uppercase tracking-widest text-slate-500">Participer</h2>

                    <div class="mt-4">
                        @if ($tournoi->disponible && ! $tournoi->placesCompletes())
                            <x-lien-affiliation :href="$tournoi->urlInscription('calendrier')" taille="lg" class="w-full">
                                S'inscrire sur GameHub Arena
                            </x-lien-affiliation>
                            <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                L'inscription se finalise sur GameHub Arena. Nous percevons une commission
                                sur les inscriptions payantes, sans surcoût pour toi.
                            </p>
                        @else
                            <p class="rounded-xl bg-slate-100 p-4 text-sm font-semibold text-slate-600">
                                {{ $tournoi->placesCompletes() ? 'Ce tournoi est complet.' : 'Les inscriptions sont fermées.' }}
                            </p>
                            <a href="{{ route('tournois.index') }}" class="mt-3 block text-center text-sm font-semibold text-orange-700 hover:text-orange-800">
                                Voir les autres tournois →
                            </a>
                        @endif
                    </div>
                </div>

                @include('partials.ad')
            </aside>
        </div>
    </div>
@endsection
