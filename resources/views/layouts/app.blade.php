<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}</title>
    <meta name="description" content="@yield('description', config('arene.site.baseline'))">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}">
    <meta property="og:description" content="@yield('description', config('arene.site.baseline'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="fr">

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <meta name="theme-color" content="#0b0e13">

    {{-- Meme trio typographique que gamehub-arena.com : Sora, Inter, Space Mono. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;600;700&family=Space+Mono:wght@500;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    {{-- Barre de progression de navigation --}}
    <div id="barre-chargement"
         class="pointer-events-none fixed inset-x-0 top-0 z-[100] h-0.5 origin-left scale-x-0 bg-gradient-to-r from-arn via-acc to-cy transition-transform duration-300"
         aria-hidden="true"></div>

    <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-white">
        Aller au contenu
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="group flex min-w-0 shrink-0 items-center gap-2.5">
                <img src="{{ asset('img/logo-clair.svg') }}"
                     alt=""
                     class="h-9 w-9 shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                <span class="titre truncate text-[15px] font-extrabold tracking-tight">
                    {{ config('arene.site.nom') }}
                </span>
            </a>

            {{-- Navigation bureau --}}
            <nav class="hidden items-center gap-1 md:flex" aria-label="Navigation principale">
                <x-nav-link :href="route('tournois.index')" :actif="request()->routeIs('tournois.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="trophy" taille="sm" />
                        Tournois
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('articles.index')" :actif="request()->routeIs('articles.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="file-text" taille="sm" />
                        Articles
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('lft.index')" :actif="request()->routeIs('lft.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="users" taille="sm" />
                        LFT
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('arene-bot')" :actif="request()->routeIs('arene-bot')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="bot" taille="sm" />
                        Arène Bot
                    </span>
                </x-nav-link>
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                @auth
                    <span class="hidden sm:block">
                        <a href="{{ route('admin.dashboard') }}"
                           title="Back-office"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                            <x-icone nom="settings" taille="sm" />
                            <span class="hidden lg:inline">Admin</span>
                        </a>
                    </span>
                @endauth

                <span class="hidden sm:block">
                    <x-lien-affiliation :href="app(\App\Services\AffiliationLiens::class)->url(null, 'accueil')" taille="sm">
                        <x-icone nom="zap" taille="xs" />
                        <span class="hidden lg:inline">Rejoindre</span>
                    </x-lien-affiliation>
                </span>

                {{-- Toggle mobile : remplace la navigation sous 768 px. --}}
                <button type="button"
                        id="bouton-menu"
                        class="inline-flex size-10 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition hover:bg-slate-50 md:hidden"
                        aria-expanded="false"
                        aria-controls="menu-mobile"
                        aria-label="Ouvrir le menu">
                    <x-icone nom="menu" taille="md" />
                </button>
            </div>
        </div>

        {{-- Panneau mobile --}}
        <div id="menu-mobile"
             class="hidden border-t border-slate-200 bg-white md:hidden"
             aria-label="Menu mobile">
            <nav class="mx-auto max-w-6xl space-y-1 px-4 py-3">
                <a href="{{ route('tournois.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('tournois.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="trophy" taille="md" />
                    Tournois
                </a>
                <a href="{{ route('articles.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('articles.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="file-text" taille="md" />
                    Articles
                </a>
                <a href="{{ route('lft.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('lft.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="users" taille="md" />
                    Matchmaking LFT
                </a>
                <a href="{{ route('arene-bot') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('arene-bot') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="bot" taille="md" />
                    Arène Bot
                </a>

                <div class="my-2 border-t border-slate-100"></div>

                <x-lien-affiliation :href="app(\App\Services\AffiliationLiens::class)->url(null, 'accueil')"
                                    taille="lg"
                                    class="w-full">
                    <x-icone nom="zap" taille="sm" />
                    Rejoindre la communauté
                </x-lien-affiliation>

                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        <x-icone nom="whatsapp" taille="md" />
                        Groupe WhatsApp
                    </a>
                @endif

                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        <x-icone nom="settings" taille="md" />
                        Back-office
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="contenu">
        @yield('contenu')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('img/logo-clair.svg') }}" alt="" class="h-8 w-8">
                        <p class="titre text-sm font-extrabold">{{ config('arene.site.nom') }}</p>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        {{ config('arene.site.baseline') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Explorer</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li><a href="{{ route('tournois.index') }}" class="hover:text-arn">Calendrier des tournois</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-arn">Articles</a></li>
                        <li><a href="{{ route('lft.index') }}" class="hover:text-arn">Matchmaking LFT</a></li>
                        <li><a href="{{ route('arene-bot') }}" class="hover:text-arn">Arène Bot</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Communauté</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        @if (config('arene.site.communaute_whatsapp'))
                            <li>
                                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 hover:text-arn">
                                    <x-icone nom="whatsapp" taille="sm" />
                                    Groupe WhatsApp
                                </a>
                            </li>
                        @endif
                        @if (config('arene.site.communaute_discord'))
                            <li>
                                <a href="{{ config('arene.site.communaute_discord') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 hover:text-arn">
                                    <x-icone nom="discord" taille="sm" />
                                    Serveur Discord
                                </a>
                            </li>
                        @endif
                        <li><a href="{{ route('a-propos') }}" class="hover:text-arn">À propos</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-2 border-t border-slate-100 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} {{ config('arene.site.nom') }}.</p>
                <p class="inline-flex items-start gap-1.5 sm:items-center">
                    <x-icone nom="shield" taille="xs" class="mt-0.5 sm:mt-0" />
                    Liens tournois affiliés — les inscriptions payantes peuvent générer une commission.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>