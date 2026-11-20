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
    <title>@yield('titre', 'Administration') — Admin · {{ config('arene.site.nom') }}</title>
    <meta name="theme-color" content="#0a0c10">
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

    <header class="sticky top-0 z-40 border-b border-line bg-base/90 backdrop-blur-md">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="group flex shrink-0 items-center gap-2.5">
                    <img src="{{ asset('img/logo.svg') }}" alt=""
                         class="logo-site size-8 transition-transform duration-300 group-hover:scale-110">
                    <span class="titre hidden truncate font-mono text-[0.75rem] font-bold uppercase tracking-[0.1em] text-txt sm:block">
                        Back-office
                    </span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigation back-office">
                    <x-nav-link :href="route('admin.dashboard')" :actif="request()->routeIs('admin.dashboard')">
                        <span class="inline-flex items-center gap-2 whitespace-nowrap">
                            <x-icone nom="layout" taille="sm" />
                            <span class="hidden lg:inline">Tableau</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.articles.index')" :actif="request()->routeIs('admin.articles.*')">
                        <span class="inline-flex items-center gap-2 whitespace-nowrap">
                            <x-icone nom="file-text" taille="sm" />
                            Articles
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.tournois.index')" :actif="request()->routeIs('admin.tournois.*')">
                        <span class="inline-flex items-center gap-2 whitespace-nowrap">
                            <x-icone nom="trophy" taille="sm" />
                            Tournois
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.lft.index')" :actif="request()->routeIs('admin.lft.*')">
                        <span class="inline-flex items-center gap-2 whitespace-nowrap">
                            <x-icone nom="users" taille="sm" />
                            LFT
                        </span>
                    </x-nav-link>
                </nav>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('home') }}" title="Voir le site" class="btn btn-secondary btn-sm">
                    <x-icone nom="external-link" taille="sm" />
                    <span class="hidden lg:inline">Site</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion" class="btn btn-danger btn-sm">
                        <x-icone nom="log-out" taille="sm" />
                        <span class="hidden lg:inline">Quitter</span>
                    </button>
                </form>

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
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-lg border-l-2 px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'border-cy bg-cy/5 text-cy' : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}">
                    <x-icone nom="layout" taille="md" />
                    Tableau de bord
                </a>
                <a href="{{ route('admin.articles.index') }}"
                   class="flex items-center gap-3 rounded-lg border-l-2 px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.articles.*') ? 'border-cy bg-cy/5 text-cy' : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}">
                    <x-icone nom="file-text" taille="md" />
                    Articles
                </a>
                <a href="{{ route('admin.tournois.index') }}"
                   class="flex items-center gap-3 rounded-lg border-l-2 px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.tournois.*') ? 'border-cy bg-cy/5 text-cy' : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}">
                    <x-icone nom="trophy" taille="md" />
                    Tournois
                </a>
                <a href="{{ route('admin.lft.index') }}"
                   class="flex items-center gap-3 rounded-lg border-l-2 px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.lft.*') ? 'border-cy bg-cy/5 text-cy' : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}">
                    <x-icone nom="users" taille="md" />
                    Profils LFT
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        @if (session('statut'))
            <div class="alerte alerte-ok mb-5 animate-fade-in">
                <x-icone nom="check-circle" taille="sm" class="mt-0.5 shrink-0" />
                <span>{{ session('statut') }}</span>
            </div>
        @endif

        @if (session('erreur'))
            <div class="alerte alerte-err mb-5 animate-fade-in">
                <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
                <span>{{ session('erreur') }}</span>
            </div>
        @endif

        @yield('contenu')
    </main>
</body>
</html>