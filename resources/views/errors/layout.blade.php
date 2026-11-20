<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — {{ config('arene.site.nom') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-4xl items-center px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('img/logo-clair.svg') }}" alt="" class="h-10 w-10">
                <span class="text-base font-extrabold tracking-tight">{{ config('arene.site.nom') }}</span>
            </a>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-16">
        <div class="w-full max-w-xl text-center">
            <p class="font-mono text-7xl font-black tracking-tighter text-slate-200 sm:text-8xl">
                @yield('code')
            </p>

            <div class="mx-auto mt-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-900 text-white">
                <x-icone :nom="$icone ?? 'alert'" taille="xl" />
            </div>

            <h1 class="mt-6 text-2xl font-extrabold tracking-tight sm:text-3xl">
                @yield('titre')
            </h1>

            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-600">
                @yield('message')
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">
                    <x-icone nom="arrow-left" taille="sm" />
                    Revenir en arrière
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    <x-icone nom="home" taille="sm" />
                    Accueil
                </a>
            </div>

            <nav class="mt-10 border-t border-slate-200 pt-6" aria-label="Raccourcis">
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                    <li>
                        <a href="{{ route('tournois.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-slate-600 hover:text-orange-700">
                            <x-icone nom="trophy" taille="xs" />
                            Tournois
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-slate-600 hover:text-orange-700">
                            <x-icone nom="file-text" taille="xs" />
                            Articles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lft.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-slate-600 hover:text-orange-700">
                            <x-icone nom="users" taille="xs" />
                            LFT
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white py-6">
        <p class="text-center text-xs text-slate-500">
            &copy; {{ now()->year }} {{ config('arene.site.nom') }}
        </p>
    </footer>
</body>
</html>