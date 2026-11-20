<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">

    {{-- Anti-flash : applique le theme avant le premier rendu. --}}
    <script>
        (function () {
            var choisi = null;
            try { choisi = localStorage.getItem('arene-theme'); } catch (e) {}
            var theme = choisi || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}</title>
    <meta name="description" content="@yield('description', config('arene.site.baseline'))">
    <meta name="theme-color" content="#0a0c10">

    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('titre', config('arene.site.nom')) — {{ config('arene.site.nom') }}">
    <meta property="og:description" content="@yield('description', config('arene.site.baseline'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="fr">

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    {{-- Anti-flash : applique le theme avant le premier rendu. --}}
    <script>
        (function () {
            try {
                var choisi = localStorage.getItem('arene-theme');
            } catch (e) { var choisi = null; }

            var theme = choisi || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    {{-- Charte v1.1 : Orbitron (titres), Inter (corps), JetBrains Mono (donnees). --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base font-sans text-mut antialiased">

    <div id="barre-chargement"
         class="pointer-events-none fixed inset-x-0 top-0 z-[100] h-0.5 origin-left scale-x-0 bg-cy shadow-[0_0_10px_rgba(0,240,255,0.6)] transition-transform duration-300"
         aria-hidden="true"></div>

    <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-cy focus:px-4 focus:py-2 focus:font-semibold focus:text-base">
        Aller au contenu
    </a>

    {{-- Header : 64 px, collant, flou d'arriere-plan. --}}
    <header class="sticky top-0 z-40 border-b border-line bg-base/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">

            <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-3">
                <img src="{{ asset('img/logo.svg') }}"
                     alt="{{ config('arene.site.nom') }}"
                     class="logo-site size-8 shrink-0 transition-transform duration-300 group-hover:scale-110">
                {{-- Le nom complet ne tient pas sous 400 px : masque sur mobile. --}}
                <span class="titre hidden truncate text-[0.82rem] font-black uppercase tracking-[0.08em] text-txt min-[420px]:block sm:text-[0.9rem]">
                    {{ config('arene.site.nom') }}
                </span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Navigation principale">
                <x-nav-link :href="route('tournois.index')" :actif="request()->routeIs('tournois.*')">
                    <span class="inline-flex items-center gap-2 whitespace-nowrap">
                        <x-icone nom="trophy" taille="sm" />
                        Tournois
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('articles.index')" :actif="request()->routeIs('articles.*')">
                    <span class="inline-flex items-center gap-2 whitespace-nowrap">
                        <x-icone nom="file-text" taille="sm" />
                        Guides
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('lft.index')" :actif="request()->routeIs('lft.*')">
                    <span class="inline-flex items-center gap-2 whitespace-nowrap">
                        <x-icone nom="users" taille="sm" />
                        LFT
                    </span>
                </x-nav-link>
                <x-nav-link :href="route('arene-bot')" :actif="request()->routeIs('arene-bot')">
                    <span class="inline-flex items-center gap-2 whitespace-nowrap">
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
                           class="btn btn-secondary btn-sm">
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

                <x-bascule-theme />

                <button type="button"
                        id="bouton-menu"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-line2 bg-elevated text-txt transition hover:border-cy md:hidden"
                        aria-expanded="false"
                        aria-controls="menu-mobile"
                        aria-label="Ouvrir le menu">
                    <x-icone nom="menu" taille="md" />
                </button>
            </div>
        </div>

        <div id="menu-mobile" class="hidden border-t border-line bg-surface md:hidden" aria-label="Menu mobile">
            <nav class="mx-auto max-w-6xl space-y-1 px-4 py-3">
                @php
                    $liens = [
                        ['route' => 'tournois.index', 'motif' => 'tournois.*', 'icone' => 'trophy', 'label' => 'Tournois'],
                        ['route' => 'articles.index', 'motif' => 'articles.*', 'icone' => 'file-text', 'label' => 'Guides & articles'],
                        ['route' => 'lft.index', 'motif' => 'lft.*', 'icone' => 'users', 'label' => 'Matchmaking LFT'],
                        ['route' => 'arene-bot', 'motif' => 'arene-bot', 'icone' => 'bot', 'label' => 'Arène Bot'],
                    ];
                @endphp

                @foreach ($liens as $lien)
                    <a href="{{ route($lien['route']) }}"
                       class="flex items-center gap-3 rounded-lg border-l-2 px-3 py-2.5 text-sm font-semibold transition
                              {{ request()->routeIs($lien['motif'])
                                    ? 'border-cy bg-cy/5 text-cy'
                                    : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}">
                        <x-icone :nom="$lien['icone']" taille="md" />
                        {{ $lien['label'] }}
                    </a>
                @endforeach

                <div class="my-2 border-t border-line"></div>

                <x-lien-affiliation :href="app(\App\Services\AffiliationLiens::class)->url(null, 'accueil')"
                                    taille="lg"
                                    class="w-full">
                    <x-icone nom="zap" taille="sm" />
                    Rejoindre la communauté
                </x-lien-affiliation>

                @if (config('arene.site.communaute_whatsapp'))
                    <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-ok transition hover:bg-ok/5">
                        <x-icone nom="whatsapp" taille="md" />
                        Groupe WhatsApp
                    </a>
                @endif

                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-mut transition hover:bg-elevated hover:text-txt">
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

    <footer class="mt-16 border-t border-line bg-surface">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('img/logo.svg') }}" alt="" class="logo-site size-8">
                        <p class="titre text-sm font-black uppercase tracking-[0.08em] text-txt">
                            {{ config('arene.site.nom') }}
                        </p>
                    </div>
                    <p class="mt-3 text-sm leading-relaxed">
                        {{ config('arene.site.baseline') }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[0.72rem] font-semibold uppercase tracking-[0.15em] text-dim">Explorer</p>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ route('tournois.index') }}" class="transition hover:text-cy">Calendrier des tournois</a></li>
                        <li><a href="{{ route('articles.index') }}" class="transition hover:text-cy">Guides & articles</a></li>
                        <li><a href="{{ route('lft.index') }}" class="transition hover:text-cy">Matchmaking LFT</a></li>
                        <li><a href="{{ route('arene-bot') }}" class="transition hover:text-cy">Arène Bot</a></li>
                    </ul>
                </div>

                <div>
                    <p class="font-mono text-[0.72rem] font-semibold uppercase tracking-[0.15em] text-dim">Communauté</p>
                    <ul class="mt-3 space-y-2 text-sm">
                        @if (config('arene.site.communaute_whatsapp'))
                            <li>
                                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 transition hover:text-cy">
                                    <x-icone nom="whatsapp" taille="sm" />
                                    Groupe WhatsApp
                                </a>
                            </li>
                        @endif
                        @if (config('arene.site.communaute_discord'))
                            <li>
                                <a href="{{ config('arene.site.communaute_discord') }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 transition hover:text-cy">
                                    <x-icone nom="discord" taille="sm" />
                                    Serveur Discord
                                </a>
                            </li>
                        @endif
                        <li><a href="{{ route('a-propos') }}" class="transition hover:text-cy">À propos</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-2 border-t border-line pt-6 font-mono text-[0.72rem] text-dim sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} {{ config('arene.site.nom') }}</p>
                <p class="inline-flex items-start gap-2 sm:items-center">
                    <x-icone nom="shield" taille="xs" class="mt-0.5 shrink-0 sm:mt-0" />
                    Liens tournois affiliés — les inscriptions payantes peuvent générer une commission.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>