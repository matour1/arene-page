@extends('layouts.app')

@section('titre', 'Le calendrier des tournois gaming africain')

@section('contenu')
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-slate-200 bg-white">
        {{-- Halo decoratif, purement visuel. --}}
        <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-orange-100/60 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-cyan-100/50 blur-3xl"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-20">
            <div class="max-w-2xl animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-widest text-orange-700 ring-1 ring-orange-100">
                    <x-icone nom="flame" taille="xs" class="animate-pulse-soft" />
                    Tournois · LFT · Contenu
                </span>

                <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-5xl">
                    Trouve ton prochain tournoi,<br class="hidden sm:block">
                    trouve ta team.
                </h1>

                <p class="mt-5 text-base leading-relaxed text-slate-600 sm:text-lg">
                    {{ config('arene.site.baseline') }} Calendrier mis à jour automatiquement,
                    profils LFT filtrables et articles pour progresser.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('tournois.index') }}"
                       class="group inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-base font-semibold text-white transition hover:bg-slate-800">
                        <x-icone nom="calendar" taille="md" />
                        Voir le calendrier
                        <x-icone nom="arrow-right" taille="sm" class="transition-transform duration-200 group-hover:translate-x-1" />
                    </a>

                    <a href="{{ route('lft.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-base font-semibold text-slate-800 transition hover:bg-slate-50">
                        <x-icone nom="user-plus" taille="md" />
                        Créer mon profil LFT
                    </a>
                </div>

                {{-- Bouton communautaire : aucun bloc pub a proximite (regle produit). --}}
                <div class="mt-6">
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
                <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-slate-100 pt-6">
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Tournois</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums">{{ $prochainsTournois->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Profils LFT</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums">{{ $profilLft }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Articles</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums">{{ $derniersArticles->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- Prochains tournois --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                    <x-icone nom="trophy" taille="lg" class="text-orange-600" />
                    Les 3 prochains tournois
                </h2>
                <p class="mt-1 text-sm text-slate-600">Inscriptions ouvertes, données synchronisées depuis GameHub Arena.</p>
            </div>
            <a href="{{ route('tournois.index') }}" class="group hidden items-center gap-1 text-sm font-semibold text-orange-700 hover:text-orange-800 sm:flex">
                Tout voir
                <x-icone nom="arrow-right" taille="sm" class="transition-transform duration-200 group-hover:translate-x-1" />
            </a>
        </div>

        @if ($prochainsTournois->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <x-icone nom="calendar" taille="xl" class="mx-auto text-slate-300" />
                <p class="mt-3 text-sm font-semibold text-slate-700">Aucun tournoi à venir pour le moment.</p>
                <p class="mt-1 text-sm text-slate-500">Le calendrier se remplit chaque jour. Reviens bientôt.</p>
                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-orange-700 hover:text-orange-800">
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
    </section>

    {{-- Derniers articles --}}
    <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h2 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                        <x-icone nom="file-text" taille="lg" class="text-orange-600" />
                        Derniers articles
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">Guides et analyses pour la scène compétitive africaine.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="group hidden items-center gap-1 text-sm font-semibold text-orange-700 hover:text-orange-800 sm:flex">
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
                           class="carte carte-survol group flex flex-col p-5">
                            @if ($article->categorie)
                                <span class="text-[11px] font-bold uppercase tracking-widest text-orange-700">
                                    {{ $article->categorie }}
                                </span>
                            @endif
                            <h3 class="mt-2 text-base font-bold leading-snug transition-colors group-hover:text-orange-700">
                                {{ $article->titre }}
                            </h3>
                            <p class="mt-2 line-clamp-3 text-sm text-slate-600">
                                {{ $article->chapo }}
                            </p>
                            <span class="mt-auto flex items-center gap-1.5 pt-4 text-xs text-slate-400">
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
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="carte p-6 lg:col-span-2">
                <h2 class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
                    <x-icone nom="users" taille="lg" class="text-orange-600" />
                    Matchmaking LFT
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Tu cherches une équipe ou des coéquipiers ? Crée ton profil (jeu, rôle, pays, langue)
                    et laisse les autres joueurs te contacter directement.
                </p>
                <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">
                    <x-icone nom="check-circle" taille="sm" />
                    {{ $profilLft }} profil{{ $profilLft > 1 ? 's' : '' }} disponible{{ $profilLft > 1 ? 's' : '' }}
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('lft.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <x-icone nom="search" taille="sm" />
                        Parcourir les profils
                    </a>
                    <a href="{{ route('lft.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">
                        <x-icone nom="plus" taille="sm" />
                        Créer mon profil
                    </a>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-violet-200 bg-violet-50 p-6">
                <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-violet-200/50 blur-2xl"></div>
                <span class="relative inline-flex items-center gap-2 rounded-full bg-violet-100 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-violet-800">
                    <x-icone nom="sparkles" taille="xs" class="animate-pulse-soft" />
                    Bientôt disponible
                </span>
                <h2 class="relative mt-3 flex items-center gap-2 text-xl font-extrabold tracking-tight text-violet-950">
                    <x-icone nom="bot" taille="lg" class="text-violet-700 animate-float" />
                    Arène Bot
                </h2>
                <p class="relative mt-2 text-sm leading-relaxed text-violet-900/80">
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