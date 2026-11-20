<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Administration') — Admin · {{ config('arene.site.nom') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <div class="flex items-center gap-5">
                <a href="{{ route('admin.dashboard') }}" class="group flex shrink-0 items-center gap-2.5">
                    <img src="{{ asset('img/logo-clair.svg') }}" alt=""
                         class="h-9 w-9 transition-transform duration-300 group-hover:scale-110">
                    <span class="hidden whitespace-nowrap text-sm font-extrabold sm:block">Back-office</span>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Navigation back-office">
                    <x-nav-link :href="route('admin.dashboard')" :actif="request()->routeIs('admin.dashboard')">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icone nom="layout" taille="sm" />
                            <span class="hidden lg:inline">Tableau de bord</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.articles.index')" :actif="request()->routeIs('admin.articles.*')">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icone nom="file-text" taille="sm" />
                            <span class="hidden lg:inline">Articles</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.tournois.index')" :actif="request()->routeIs('admin.tournois.*')">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icone nom="trophy" taille="sm" />
                            <span class="hidden lg:inline">Tournois</span>
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.lft.index')" :actif="request()->routeIs('admin.lft.*')">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icone nom="users" taille="sm" />
                            <span class="hidden lg:inline">LFT</span>
                        </span>
                    </x-nav-link>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-slate-900">
                    <x-icone nom="external-link" taille="xs" />
                    <span class="hidden sm:inline">Voir le site</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                        <x-icone nom="log-out" taille="xs" />
                        <span class="hidden sm:inline">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
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