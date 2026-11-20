@extends('layouts.app')

@section('titre', 'Matchmaking LFT')
@section('description', 'Trouve des coéquipiers : profils LFT filtrables par jeu, pays et langue.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
        <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Communaute</span>
                <h1 class="mt-1 flex items-center gap-2 text-2xl font-bold text-txt sm:text-3xl">
                    <x-icone nom="users" taille="lg" class="text-cy" />
                    Matchmaking LFT
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed">
                    Looking For Team : filtre par jeu, pays ou langue, puis contacte
                    directement le joueur via Discord ou WhatsApp.
                </p>
            </div>

            <a href="{{ route('lft.create') }}" class="btn btn-primary btn-sm">
                <x-icone nom="user-plus" taille="sm" />
                Créer mon profil
            </a>
        </header>

        <form method="GET" action="{{ route('lft.index') }}" class="carte mb-6 !p-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <label class="block">
                    <span class="etiquette !text-[0.78rem]">Jeu</span>
                    <select name="jeu" class="champ !py-2 !text-[0.85rem]">
                        <option value="">Tous</option>
                        @foreach ($jeux as $jeu)
                            <option value="{{ $jeu }}" @selected($filtres['jeu'] === $jeu)>{{ $jeu }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette !text-[0.78rem]">Pays</span>
                    <select name="pays" class="champ !py-2 !text-[0.85rem]">
                        <option value="">Tous</option>
                        @foreach ($pays as $code => $libelle)
                            <option value="{{ $code }}" @selected($filtres['pays'] === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette !text-[0.78rem]">Langue</span>
                    <select name="langue" class="champ !py-2 !text-[0.85rem]">
                        <option value="">Toutes</option>
                        @foreach ($langues as $code => $libelle)
                            <option value="{{ $code }}" @selected($filtres['langue'] === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="flex items-center gap-2.5 pt-1 sm:items-end sm:pb-1">
                    <input type="checkbox" name="disponible" value="1" @checked($filtres['disponible'])
                           class="size-4 rounded border-line2 bg-base text-cy focus:ring-2 focus:ring-cy/30">
                    <span class="text-[0.85rem] font-semibold text-txt">Disponibles</span>
                </label>

                <div class="col-span-2 flex items-end gap-2 sm:col-span-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Filtrer</button>
                    <a href="{{ route('lft.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>

        @if ($profils->isEmpty())
            <div class="carte px-6 py-12 text-center">
                <x-icone nom="users" taille="xl" class="mx-auto text-dim" />
                <p class="mt-3 text-sm font-semibold text-txt">Aucun profil ne correspond à ces filtres.</p>
                <a href="{{ route('lft.create') }}" class="btn btn-neon btn-sm mt-4">
                    Sois le premier à publier ton profil
                </a>
            </div>
        @else
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($profils as $profil)
                    <article class="carte flex gap-4">
                        {{-- Avatar : initiales dans un carre, comme la charte. --}}
                        <div class="relative grid size-12 shrink-0 place-items-center rounded-lg border border-line2 bg-elevated font-bold text-cy">
                            {{ mb_strtoupper(mb_substr($profil->pseudo, 0, 2)) }}
                            @if ($profil->disponible)
                                <span class="absolute -bottom-0.5 -right-0.5 size-3.5 rounded-full border-2 border-surface bg-ok"
                                      title="Disponible"></span>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-[0.95rem] font-bold text-txt">{{ $profil->pseudo }}</h2>
                            <p class="text-[0.8rem]">
                                {{ $profil->role ?? 'Rôle libre' }}
                                · {{ $pays[$profil->pays] ?? $profil->pays }}
                                · {{ strtoupper($profil->langue) }}
                            </p>

                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="badge badge-neutral">{{ $profil->jeu }}</span>
                                @if ($profil->rang)
                                    <span class="badge badge-neutral">{{ $profil->rang }}</span>
                                @endif
                            </div>

                            @if ($profil->description)
                                <p class="mt-2 line-clamp-2 text-[0.8rem] leading-relaxed">
                                    {{ $profil->description }}
                                </p>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if ($url = $profil->contactDiscordUrl())
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-neon btn-sm">
                                        <x-icone nom="discord" taille="xs" />
                                        Discord
                                    </a>
                                @elseif ($profil->contactDiscordTexte())
                                    <span class="badge badge-neutral">
                                        <x-icone nom="discord" taille="xs" />
                                        {{ $profil->contactDiscordTexte() }}
                                    </span>
                                @endif

                                @if ($url = $profil->contactWhatsappUrl())
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                       class="btn btn-neon btn-sm !border-ok !text-ok hover:!bg-ok/10">
                                        <x-icone nom="whatsapp" taille="xs" />
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $profils->links() }}</div>
        @endif
    </div>
@endsection