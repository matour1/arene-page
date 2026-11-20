@extends('layouts.app')

@section('titre', 'Le calendrier des tournois gaming africain')

@section('contenu')
    {{-- Hero --}}
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-20">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-widest text-orange-700">
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
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-3 text-base font-semibold text-white transition hover:bg-slate-800">
                        Voir le calendrier
                    </a>

                    <a href="{{ route('lft.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-base font-semibold text-slate-800 transition hover:bg-slate-50">
                        Créer mon profil LFT
                    </a>
                </div>

                {{-- Bouton communautaire : aucun bloc pub à proximité (règle produit). --}}
                <div class="mt-6">
                    @if (config('arene.site.communaute_whatsapp'))
                        <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                            Rejoindre la communauté WhatsApp →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Prochains tournois --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">Les 3 prochains tournois</h2>
                <p class="mt-1 text-sm text-slate-600">Inscriptions ouvertes, données synchronisées depuis GameHub Arena.</p>
            </div>
            <a href="{{ route('tournois.index') }}" class="hidden text-sm font-semibold text-orange-700 hover:text-orange-800 sm:block">
                Tout voir →
            </a>
        </div>

        @if ($prochainsTournois->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="text-sm font-semibold text-slate-700">Aucun tournoi à venir pour le moment.</p>
                <p class="mt-1 text-sm text-slate-500">Le calendrier se remplit chaque jour. Reviens bientôt.</p>
                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="mt-4 inline-block text-sm font-semibold text-orange-700 hover:text-orange-800">
                        Être prévenu sur WhatsApp →
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
                    <h2 class="text-2xl font-extrabold tracking-tight">Derniers articles</h2>
                    <p class="mt-1 text-sm text-slate-600">Guides et analyses pour la scène compétitive africaine.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="hidden text-sm font-semibold text-orange-700 hover:text-orange-800 sm:block">
                    Tous les articles →
                </a>
            </div>

            @if ($derniersArticles->isEmpty())
                <p class="text-sm text-slate-500">Les premiers articles arrivent très bientôt.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($derniersArticles as $article)
                        <a href="{{ route('articles.show', $article->slug) }}"
                           class="group flex flex-col rounded-2xl border border-slate-200 p-5 transition hover:border-slate-300 hover:shadow-sm">
                            @if ($article->categorie)
                                <span class="text-[11px] font-bold uppercase tracking-widest text-orange-700">
                                    {{ $article->categorie }}
                                </span>
                            @endif
                            <h3 class="mt-2 text-base font-bold leading-snug group-hover:text-orange-700">
                                {{ $article->titre }}
                            </h3>
                            <p class="mt-2 line-clamp-3 text-sm text-slate-600">
                                {{ $article->chapo }}
                            </p>
                            <span class="mt-auto pt-4 text-xs text-slate-400">
                                {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- LFT + Arène Bot --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
                <h2 class="text-xl font-extrabold tracking-tight">Matchmaking LFT</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Tu cherches une équipe ou des coéquipiers ? Crée ton profil (jeu, rôle, pays, langue)
                    et laisse les autres joueurs te contacter directement.
                </p>
                <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800">
                    {{ $profilLft }} profil{{ $profilLft > 1 ? 's' : '' }} disponible{{ $profilLft > 1 ? 's' : '' }}
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('lft.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Parcourir les profils
                    </a>
                    <a href="{{ route('lft.create') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        Créer mon profil
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-6">
                <span class="inline-flex items-center gap-2 rounded-full bg-violet-100 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-violet-800">
                    🤖 Bientôt disponible
                </span>
                <h2 class="mt-3 text-xl font-extrabold tracking-tight text-violet-950">Arène Bot</h2>
                <p class="mt-2 text-sm leading-relaxed text-violet-900/80">
                    Matchmaking intelligent et génération d'articles assistée. En cours de conception.
                </p>
                <a href="{{ route('arene-bot') }}" class="mt-4 inline-block text-sm font-semibold text-violet-800 hover:text-violet-900">
                    En savoir plus →
                </a>
            </div>
        </div>
    </section>
@endsection
