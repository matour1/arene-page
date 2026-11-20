@extends('layouts.app')

@section('titre', 'Matchmaking LFT')
@section('description', 'Trouve des coéquipiers : profils LFT filtrables par jeu, pays et langue.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Matchmaking LFT</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    Looking For Team : parcours les profils, filtre par jeu, pays ou langue,
                    puis contacte directement le joueur via Discord ou WhatsApp.
                </p>
            </div>

            <a href="{{ route('lft.create') }}"
               class="rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-700">
                Créer mon profil
            </a>
        </header>

        <form method="GET" action="{{ route('lft.index') }}"
              class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-5">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Jeu</span>
                <select name="jeu" class="champ">
                    <option value="">Tous</option>
                    @foreach ($jeux as $jeu)
                        <option value="{{ $jeu }}" @selected($filtres['jeu'] === $jeu)>{{ $jeu }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Pays</span>
                <select name="pays" class="champ">
                    <option value="">Tous</option>
                    @foreach ($pays as $code => $libelle)
                        <option value="{{ $code }}" @selected($filtres['pays'] === $code)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Langue</span>
                <select name="langue" class="champ">
                    <option value="">Toutes</option>
                    @foreach ($langues as $code => $libelle)
                        <option value="{{ $code }}" @selected($filtres['langue'] === $code)>{{ $libelle }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex items-end gap-2 pb-2">
                <input type="checkbox" name="disponible" value="1" @checked($filtres['disponible'])
                       class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-semibold text-slate-700">Disponibles</span>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Filtrer
                </button>
                <a href="{{ route('lft.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        @if ($profils->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-base font-semibold text-slate-700">Aucun profil ne correspond à ces filtres.</p>
                <a href="{{ route('lft.create') }}" class="mt-3 inline-block text-sm font-semibold text-orange-700 hover:text-orange-800">
                    Sois le premier à publier ton profil →
                </a>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($profils as $profil)
                    <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">{{ $profil->pseudo }}</h2>
                            @if ($profil->disponible)
                                <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-800">
                                    Dispo
                                </span>
                            @endif
                        </div>

                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                {{ $profil->jeu }}
                            </span>
                            @if ($profil->role)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                    {{ $profil->role }}
                                </span>
                            @endif
                            @if ($profil->rang)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                    {{ $profil->rang }}
                                </span>
                            @endif
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            {{ $pays[$profil->pays] ?? $profil->pays }}
                            · {{ $langues[$profil->langue] ?? strtoupper($profil->langue) }}
                            @if ($profil->plateforme)
                                · {{ $profil->plateforme }}
                            @endif
                        </p>

                        @if ($profil->description)
                            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600">
                                {{ $profil->description }}
                            </p>
                        @endif

                        <div class="mt-auto flex flex-wrap gap-2 pt-4">
                            @if ($url = $profil->contactDiscordUrl())
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">
                                    Discord
                                </a>
                            @elseif ($profil->contactDiscordTexte())
                                {{-- Pseudo seul : on l'affiche en clair, sans fabriquer d'URL invalide. --}}
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-800">
                                    <span class="font-normal opacity-70">Discord</span>
                                    {{ $profil->contactDiscordTexte() }}
                                </span>
                            @endif

                            @if ($url = $profil->contactWhatsappUrl())
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $profils->links() }}</div>
        @endif
    </div>
@endsection
