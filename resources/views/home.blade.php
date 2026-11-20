@extends('layouts.app')

@section('titre', 'Le calendrier des tournois gaming africain')

@section('contenu')
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-slate-200 bg-white">
        <div class="pointer-events-none absolute -right-32 -top-32 h-80 w-80 rounded-full bg-acc/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 left-1/4 h-80 w-80 rounded-full bg-cy/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-20">
            <div class="max-w-2xl animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-arn/20 bg-arn/5 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.15em] text-arn">
                    <x-icone nom="flame" taille="xs" class="animate-pulse-soft" />
                    Tournois · LFT · Contenu
                </span>

                <h1 class="mt-5 text-3xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl">
                    Trouve ton prochain tournoi,<br class="hidden sm:block">
                    trouve ta team.
                </h1>

                <p class="mt-4 text-[15px] leading-relaxed text-slate-600 sm:mt-5 sm:text-lg">
                    {{ config('arene.site.baseline') }} Calendrier mis à jour automatiquement,
                    profils LFT filtrables et articles pour progresser.
                </p>

                <div class="mt-7 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-3">
                    <a href="{{ route('tournois.index') }}"
                       class="group inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:text-base">
                        <x-icone nom="calendar" taille="md" />
                        Voir le calendrier
                        <x-icone nom="arrow-right" taille="sm" class="transition-transform duration-200 group-hover:translate-x-1" />
                    </a>

                    <a href="{{ route('lft.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 sm:text-base">
                        <x-icone nom="user-plus" taille="md" />
                        Créer mon profil LFT
                    </a>
                </div>

                <div class="mt-5">
                    @if (config('arene.site.communaute_whatsapp'))
                        <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                           class="group inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                            <x-icone nom="whatsapp" taille="sm" />
                            Rejoindre la communauté WhatsApp
                            <x-icone nom="arrow-right" taille="xs" class="transition-transform duration-200 group-hover:translate-x-1" />
                        </a>
                    @endif
                </div>

                {{-- Chiffres cles --}}
                <dl class="mt-9 grid max-w-lg grid-cols-3 gap-3 border-t border-slate-100 pt-6">
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Tournois</dt>
                        <dd class="chiffre mt-1 text-xl font-bold sm:text-2xl">{{ $prochainsTournois->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Profils LFT</dt>
                        <dd class="chiffre mt-1 text-xl font-bold sm:text-2xl">{{ $profilLft }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Articles</dt>
                        <dd class="chiffre mt-1 text-xl font-bold sm:text-2xl">{{ $derniersArticles->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Prochains tournois --}}
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-14">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight sm:text-2xl">
                    <x-icone nom="trophy" taille="lg" class="text-acc" />
                    Les 3 prochains tournois
                </h2>
                <p class="mt-1 text-[13px] text-slate-600 sm:text-sm">Inscriptions ouvertes, données synchronisées depuis GameHub Arena.</p>
            </div>
            <a href="{{ route('tournois.index') }}" class="group hidden shrink-0 items-center gap-1 text-sm font-semibold text-arn hover:text-arn/80 sm:flex">
                Tout voir
                <x-icone nom="arrow-right" taille="sm" class="transition-transform duration-200 group-hover:translate-x-1" />
            </a>
        </div>

        @if ($prochainsTournois->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <x-icone nom="calendar" taille="xl" class="mx-auto text-slate-300" />
                <p class="mt-3 text-sm font-semibold text-slate-700">Aucun tournoi à venir pour le moment.</p>
                <p class="mt-1 text-[13px] text-slate-500">Le calendrier se remplit chaque jour. Reviens bientôt.</p>
                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-arn hover:text-arn/80">
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

        <a href="{{ route('tournois.index') }}"
           class="mt-4 flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 py-2.5 text-sm font-semibold text-slate-600 sm:hidden">
            Tout voir
            <x-icone nom="arrow-right" taille="sm" />
        </a>
    </section>

    {{-- Derniers articles --}}
    <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-14">
            <div class="mb-5 flex items-end justify-between gap-4">
                <div>
                    <h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight sm:text-2xl">
                        <x-icone nom="file-text" taille="lg" class="text-acc" />
                        Derniers articles
                    </h2>
                    <p class="mt-1 text-[13px] text-slate-600 sm:text-sm">Guides et analyses pour la scène compétitive africaine.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="group hidden shrink-0 items-center gap-1 text-sm font-semibold text-arn hover:text-arn/80 sm:flex">
                    Tous les articles
                    <x-icone nom="arrow-right" taille="sm" class="transition-transform duration-200 group-hover:translate-x-1" />
                </a>
            </div>

            @if ($derniersArticles->isEmpty())
                <p class="text-sm text-slate-500">Les premiers articles arrivent très bientôt.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($derniersArticles as $article)
                        <a href="{{ route('articles.show', $article->slug) }}"
                           class="group flex flex-col rounded-xl border border-slate-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/60">
                            @if ($article->categorie)
                                <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-arn">
                                    {{ $article->categorie }}
                                </span>
                            @endif
                            <h3 class="titre mt-2 line-clamp-2 text-[15px] font-bold leading-snug transition-colors group-hover:text-arn">
                                {{ $article->titre }}
                            </h3>
                            <p class="mt-2 line-clamp-3 text-[13px] leading-relaxed text-slate-600">
                                {{ $article->chapo }}
                            </p>
                            <span class="chiffre mt-auto flex items-center gap-1.5 pt-4 text-[11px] text-slate-400">
                                <x-icone nom="clock" taille="xs" />
                                {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- LFT + Arene Bot --}}
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-14">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 lg:col-span-2">
                <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight sm:text-xl">
                    <x-icone nom="users" taille="lg" class="text-acc" />
                    Matchmaking LFT
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Tu cherches une équipe ou des coéquipiers ? Crée ton profil (jeu, rôle, pays, langue)
                    et laisse les autres joueurs te contacter directement.
                </p>
                <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-[13px] font-semibold text-emerald-800">
                    <x-icone nom="check-circle" taille="sm" />
                    {{ $profilLft }} profil{{ $profilLft > 1 ? 's' : '' }} disponible{{ $profilLft > 1 ? 's' : '' }}
                </p>
                <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <a href="{{ route('lft.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <x-icone nom="search" taille="sm" />
                        Parcourir les profils
                    </a>
                    <a href="{{ route('lft.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">
                        <x-icone nom="plus" taille="sm" />
                        Créer mon profil
                    </a>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-violet-200 bg-violet-50 p-5 sm:p-6">
                <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-violet-200/50 blur-2xl"></div>
                <span class="relative inline-flex items-center gap-1.5 rounded-full bg-violet-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.15em] text-violet-800">
                    <x-icone nom="sparkles" taille="xs" class="animate-pulse-soft" />
                    Bientôt
                </span>
                <h2 class="relative mt-3 flex items-center gap-2 text-lg font-extrabold tracking-tight text-violet-950 sm:text-xl">
                    <x-icone nom="bot" taille="lg" class="animate-float text-violet-700" />
                    Arène Bot
                </h2>
                <p class="relative mt-2 text-[13px] leading-relaxed text-violet-900/80">
                    Matchmaking intelligent et génération d'articles assistée. En cours de conception.
                </p>
                <a href="{{ route('arene-bot') }}" class="group relative mt-4 inline-flex items-center gap-1 text-sm font-semibold text-violet-800 hover:text-violet-900">
                    En savoir plus
                    <x-icone nom="arrow-right" taille="xs" class="transition-transform duration-200 group-hover:translate-x-1" />
                </a>
            </div>
        </div>
    </section>
@endsection