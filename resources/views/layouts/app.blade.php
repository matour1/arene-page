<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}</title>
    <meta name="description" content="@yield('description', config('arene.site.baseline'))">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}">
    <meta property="og:description" content="@yield('description', config('arene.site.baseline'))">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <meta name="theme-color" content="#0f172a">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-white">
        Aller au contenu
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5">
                <img src="{{ asset('img/logo-clair.svg') }}"
                     alt=""
                     class="h-10 w-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                <span class="hidden whitespace-nowrap text-base font-extrabold tracking-tight md:block">
                    {{ config('arene.site.nom') }}
                </span>
            </a>

            <nav class="flex items-center gap-1" aria-label="Navigation principale">
                <x-nav-link :href="route('tournois.index')" :actif="request()->routeIs('tournois.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="trophy" taille="sm" />
                        <span class="hidden md:inline">Tournois</span>
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('articles.index')" :actif="request()->routeIs('articles.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="file-text" taille="sm" />
                        <span class="hidden md:inline">Articles</span>
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('lft.index')" :actif="request()->routeIs('lft.*')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="users" taille="sm" />
                        <span class="hidden md:inline">LFT</span>
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('arene-bot')" :actif="request()->routeIs('arene-bot')">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <x-icone nom="bot" taille="sm" />
                        <span class="hidden md:inline">Arène Bot</span>
                    </span>
                </x-nav-link>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       title="Back-office"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                        <x-icone nom="settings" taille="sm" />
                        <span class="hidden md:inline">Admin</span>
                    </a>
                @endauth

                <x-lien-affiliation :href="app(\App\Services\AffiliationLiens::class)->url(null, 'accueil')" taille="sm" class="hidden sm:inline-flex">
                    <x-icone nom="zap" taille="xs" />
                    Rejoindre
                </x-lien-affiliation>
            </div>
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
                        <p class="text-sm font-extrabold">{{ config('arene.site.nom') }}</p>
                    </div>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        {{ config('arene.site.baseline') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Explorer</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li><a href="{{ route('tournois.index') }}" class="hover:text-orange-700">Calendrier des tournois</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-orange-700">Articles</a></li>
                        <li><a href="{{ route('lft.index') }}" class="hover:text-orange-700">Matchmaking LFT</a></li>
                        <li><a href="{{ route('arene-bot') }}" class="hover:text-orange-700">Arène Bot</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Communauté</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        @if (config('arene.site.communaute_whatsapp'))
                            <li>
                                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 hover:text-orange-700">
                                    <x-icone nom="whatsapp" taille="sm" />
                                    Groupe WhatsApp
                                </a>
                            </li>
                        @endif
                        @if (config('arene.site.communaute_discord'))
                            <li>
                                <a href="{{ config('arene.site.communaute_discord') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 hover:text-orange-700">
                                    <x-icone nom="discord" taille="sm" />
                                    Serveur Discord
                                </a>
                            </li>
                        @endif
                        <li><a href="{{ route('a-propos') }}" class="hover:text-orange-700">À propos</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-2 border-t border-slate-100 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} {{ config('arene.site.nom') }}.</p>
                <p class="inline-flex items-center gap-1.5">
                    <x-icone nom="shield" taille="xs" />
                    Liens tournois affiliés — les inscriptions payantes peuvent générer une commission.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>