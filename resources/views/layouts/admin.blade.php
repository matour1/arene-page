<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Administration') — Admin · {{ config('arene.site.nom') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-black text-white">
                        AG
                    </span>
                    <span class="text-sm font-extrabold">Back-office</span>
                </a>

                <nav class="hidden items-center gap-1 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :actif="request()->routeIs('admin.dashboard')">Tableau de bord</x-nav-link>
                    <x-nav-link :href="route('admin.articles.index')" :actif="request()->routeIs('admin.articles.*')">Articles</x-nav-link>
                    <x-nav-link :href="route('admin.tournois.index')" :actif="request()->routeIs('admin.tournois.*')">Tournois</x-nav-link>
                    <x-nav-link :href="route('admin.lft.index')" :actif="request()->routeIs('admin.lft.*')">LFT</x-nav-link>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">Voir le site</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        @if (session('statut'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('statut') }}
            </div>
        @endif

        @if (session('erreur'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                {{ session('erreur') }}
            </div>
        @endif

        @yield('contenu')
    </main>
</body>
</html>
