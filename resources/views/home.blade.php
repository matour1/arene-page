@extends('layouts.app')

@section('titre', 'Calendrier des tournois e-sport africains')

@section('contenu')
    {{-- Hero --}}
    <section class="border-b border-line">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-20">
            <div class="max-w-2xl animate-fade-up">
                <span class="overline inline-flex items-center gap-2 font-mono text-[0.72rem] font-semibold uppercase tracking-[0.15em] text-cy">
                    <x-icone nom="flame" taille="xs" class="animate-blink" />
                    Tournois · LFT · Guides
                </span>

                <h1 class="mt-4 text-[2rem] font-black uppercase leading-[1.05] tracking-tight text-txt sm:text-[2.75rem]">
                    Entre dans<br>
                    <span class="text-cy">l'arène</span>
                </h1>

                <p class="mt-5 max-w-xl text-[1.05rem] leading-relaxed">
                    Le calendrier des tournois e-sport mobiles de la communauté africaine francophone.
                    Trouve ta prochaine compétition, ta team, et les guides pour progresser.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('tournois.index') }}" class="btn btn-primary btn-lg">
                        <x-icone nom="trophy" taille="md" />
                        Voir les tournois
                    </a>
                    <a href="{{ route('lft.create') }}" class="btn btn-neon btn-lg">
                        <x-icone nom="user-plus" taille="md" />
                        Créer mon profil LFT
                    </a>
                </div>

                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-ok transition hover:text-txt">
                        <x-icone nom="whatsapp" taille="sm" />
                        Rejoindre la communauté WhatsApp
                        <x-icone nom="arrow-right" taille="xs" />
                    </a>
                @endif

                {{-- Statistiques --}}
                <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-line pt-6">
                    <div>
                        <dt class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Tournois</dt>
                        <dd class="chiffre mt-1 text-2xl font-bold text-cy">{{ $prochainsTournois->count() }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Profils LFT</dt>
                        <dd class="chiffre mt-1 text-2xl font-bold text-cy">{{ $profilLft }}</dd>
                    </div>
                    <div>
                        <dt class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Guides</dt>
                        <dd class="chiffre mt-1 text-2xl font-bold text-cy">{{ $derniersArticles->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Tournois --}}
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">01 · Competitions</span>
                <h2 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
                    <x-icone nom="trophy" taille="lg" class="text-cy" />
                    Prochains tournois
                </h2>
                <p class="mt-1 text-sm">Synchronisé automatiquement avec GameHub Arena.</p>
            </div>
            <a href="{{ route('tournois.index') }}" class="btn btn-neon btn-sm hidden shrink-0 sm:inline-flex">
                Tout voir
                <x-icone nom="arrow-right" taille="xs" />
            </a>
        </div>

        @if ($prochainsTournois->isEmpty())
            <div class="carte px-6 py-12 text-center">
                <x-icone nom="calendar" taille="xl" class="mx-auto text-dim" />
                <p class="mt-3 text-sm font-semibold text-txt">Aucun tournoi à venir pour le moment.</p>
                <p class="mt-1 text-sm">Le calendrier se remplit chaque jour.</p>
                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-cy hover:text-txt">
                        <x-icone nom="whatsapp" taille="sm" />
                        Être prévenu sur WhatsApp
                    </a>
                @endif
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($prochainsTournois as $tournoi)
                    @include('partials.carte-tournoi', ['tournoi' => $tournoi])
                @endforeach
            </div>
        @endif

        <a href="{{ route('tournois.index') }}" class="btn btn-neon mt-4 w-full sm:hidden">
            Tout voir
            <x-icone nom="arrow-right" taille="xs" />
        </a>
    </section>

    {{-- Guides --}}
    <section class="border-y border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">02 · Ressources</span>
                    <h2 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
                        <x-icone nom="file-text" taille="lg" class="text-cy" />
                        Guides & analyses
                    </h2>
                    <p class="mt-1 text-sm">Pour progresser et comprendre la scène compétitive.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="btn btn-neon btn-sm hidden shrink-0 sm:inline-flex">
                    Tous les guides
                    <x-icone nom="arrow-right" taille="xs" />
                </a>
            </div>

            @if ($derniersArticles->isEmpty())
                <p class="text-sm">Les premiers guides arrivent très bientôt.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($derniersArticles as $article)
                        <a href="{{ route('articles.show', $article->slug) }}"
                           class="carte carte-survol group flex flex-col">
                            @if ($article->categorie)
                                <span class="badge badge-cyan self-start">{{ $article->categorie }}</span>
                            @endif
                            <h3 class="mt-3 line-clamp-2 text-[1.05rem] font-bold leading-snug text-txt transition group-hover:text-cy">
                                {{ $article->titre }}
                            </h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed">
                                {{ $article->chapo }}
                            </p>
                            <span class="chiffre mt-auto flex items-center gap-2 pt-4 text-[0.78rem] text-dim">
                                <x-icone nom="clock" taille="xs" />
                                {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- LFT + Bot --}}
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="carte lg:col-span-2">
                <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">03 · Communaute</span>
                <h2 class="mt-1 flex items-center gap-2 text-lg font-bold text-txt sm:text-xl">
                    <x-icone nom="users" taille="lg" class="text-cy" />
                    Matchmaking LFT
                </h2>
                <p class="mt-2 text-sm leading-relaxed">
                    Tu cherches une équipe ? Crée ton profil (jeu, rôle, pays, langue)
                    et laisse les autres joueurs te contacter directement par Discord ou WhatsApp.
                </p>

                <div class="mt-4">
                    <span class="badge badge-ok">
                        <x-icone nom="check-circle" taille="xs" />
                        {{ $profilLft }} profil{{ $profilLft > 1 ? 's' : '' }} disponible{{ $profilLft > 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <a href="{{ route('lft.index') }}" class="btn btn-primary btn-sm">
                        <x-icone nom="search" taille="sm" />
                        Parcourir les profils
                    </a>
                    <a href="{{ route('lft.create') }}" class="btn btn-secondary btn-sm">
                        <x-icone nom="plus" taille="sm" />
                        Créer mon profil
                    </a>
                </div>
            </div>

            <div class="carte border-vio/30 bg-vio/5">
                <span class="badge badge-violet">
                    <x-icone nom="sparkles" taille="xs" class="animate-blink" />
                    Bientôt
                </span>
                <h2 class="mt-3 flex items-center gap-2 text-lg font-bold text-txt sm:text-xl">
                    <x-icone nom="bot" taille="lg" class="animate-float text-vio" />
                    Arène Bot
                </h2>
                <p class="mt-2 text-sm leading-relaxed">
                    Matchmaking intelligent et génération de guides assistée. En cours de conception.
                </p>
                <a href="{{ route('arene-bot') }}" class="btn btn-neon btn-sm mt-4">
                    En savoir plus
                    <x-icone nom="arrow-right" taille="xs" />
                </a>
            </div>
        </div>
    </section>
@endsection