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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-white">
        Aller au contenu
    </a>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/85 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-sm font-black text-white">
                    AG
                </span>
                <span class="hidden text-base font-extrabold tracking-tight sm:block">
                    {{ config('arene.site.nom') }}
                </span>
            </a>

            <nav class="flex items-center gap-1" aria-label="Navigation principale">
                <x-nav-link :href="route('tournois.index')" :actif="request()->routeIs('tournois.*')">Tournois</x-nav-link>
                <x-nav-link :href="route('articles.index')" :actif="request()->routeIs('articles.*')">Articles</x-nav-link>
                <x-nav-link :href="route('lft.index')" :actif="request()->routeIs('lft.*')">LFT</x-nav-link>
                <x-nav-link :href="route('arene-bot')" :actif="request()->routeIs('arene-bot')">
                    Arène Bot
                </x-nav-link>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Admin
                    </a>
                @endauth

                <x-lien-affiliation :href="app(\App\Services\AffiliationLiens::class)->url(null, 'accueil')" taille="sm" class="hidden sm:inline-flex">
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
                    <p class="text-sm font-extrabold">{{ config('arene.site.nom') }}</p>
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
                                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener" class="hover:text-orange-700">
                                    Groupe WhatsApp
                                </a>
                            </li>
                        @endif
                        @if (config('arene.site.communaute_discord'))
                            <li>
                                <a href="{{ config('arene.site.communaute_discord') }}" target="_blank" rel="noopener" class="hover:text-orange-700">
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
                <p>
                    Liens tournois affiliés — les inscriptions payantes peuvent générer une commission.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
