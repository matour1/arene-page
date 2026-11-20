<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Administration') — Admin · {{ config('arene.site.nom') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    <meta name="theme-color" content="#0b0e13">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;600;700&family=Space+Mono:wght@500;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">

    <div id="barre-chargement"
         class="pointer-events-none fixed inset-x-0 top-0 z-[100] h-0.5 origin-left scale-x-0 bg-gradient-to-r from-arn via-acc to-cy transition-transform duration-300"
         aria-hidden="true"></div>

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="group flex shrink-0 items-center gap-2.5">
                    <img src="{{ asset('img/logo-clair.svg') }}" alt=""
                         class="h-9 w-9 transition-transform duration-300 group-hover:scale-110">
                    <span class="titre truncate text-sm font-extrabold">Back-office</span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigation back-office">
                    <x-nav-link :href="route('admin.dashboard')" :actif="request()->routeIs('admin.dashboard')">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                            <x-icone nom="layout" taille="sm" />
                            <span class="hidden lg:inline">Tableau</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.articles.index')" :actif="request()->routeIs('admin.articles.*')">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                            <x-icone nom="file-text" taille="sm" />
                            Articles
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.tournois.index')" :actif="request()->routeIs('admin.tournois.*')">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                            <x-icone nom="trophy" taille="sm" />
                            Tournois
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.lft.index')" :actif="request()->routeIs('admin.lft.*')">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                            <x-icone nom="users" taille="sm" />
                            LFT
                        </span>
                    </x-nav-link>
                </nav>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('home') }}"
                   title="Voir le site"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-2.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    <x-icone nom="external-link" taille="sm" />
                    <span class="hidden lg:inline">Voir le site</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            title="Déconnexion"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-2.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                        <x-icone nom="log-out" taille="sm" />
                        <span class="hidden lg:inline">Déconnexion</span>
                    </button>
                </form>

                <button type="button"
                        id="bouton-menu"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 transition hover:bg-slate-50 md:hidden"
                        aria-expanded="false"
                        aria-controls="menu-mobile"
                        aria-label="Ouvrir le menu">
                    <x-icone nom="menu" taille="md" />
                </button>
            </div>
        </div>

        <div id="menu-mobile" class="hidden border-t border-slate-200 bg-white md:hidden" aria-label="Menu mobile">
            <nav class="mx-auto max-w-6xl space-y-1 px-4 py-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="layout" taille="md" />
                    Tableau de bord
                </a>
                <a href="{{ route('admin.articles.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.articles.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="file-text" taille="md" />
                    Articles
                </a>
                <a href="{{ route('admin.tournois.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.tournois.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="trophy" taille="md" />
                    Tournois
                </a>
                <a href="{{ route('admin.lft.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('admin.lft.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <x-icone nom="users" taille="md" />
                    Profils LFT
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8">
        @if (session('statut'))
            <div class="mb-5 flex items-start gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 animate-fade-in">
                <x-icone nom="check-circle" taille="sm" class="mt-0.5" />
                <span>{{ session('statut') }}</span>
            </div>
        @endif

        @if (session('erreur'))
            <div class="mb-5 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 animate-fade-in">
                <x-icone nom="alert" taille="sm" class="mt-0.5" />
                <span>{{ session('erreur') }}</span>
            </div>
        @endif

        @yield('contenu')
    </main>
</body>
</html>