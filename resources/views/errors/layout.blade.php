<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">

    <script>
        (function () {
            var choisi = null;
            try { choisi = localStorage.getItem('arene-theme'); } catch (e) {}
            var theme = choisi || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.dataset.theme = theme;
        })();
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('code') — {{ config('arene.site.nom') }}</title>
    <meta name="theme-color" content="#0a0c10">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap">

    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-base font-sans text-mut antialiased">
    <header class="border-b border-line bg-surface">
        <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('img/logo.svg') }}" alt="" class="logo-site size-8">
                <span class="titre font-mono text-[0.78rem] font-bold uppercase tracking-[0.1em] text-txt">
                    {{ config('arene.site.nom') }}
                </span>
            </a>
            <x-bascule-theme />
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-16">
        <div class="w-full max-w-xl text-center">
            <p class="font-mono text-6xl font-bold tracking-tighter text-line2 sm:text-7xl">
                @yield('code')
            </p>

            <div class="mx-auto mt-4 grid size-16 place-items-center rounded-xl border border-line2 bg-elevated text-cy">
                <x-icone :nom="$icone ?? 'alert'" taille="xl" />
            </div>

            <h1 class="mt-6 text-xl font-bold text-txt sm:text-2xl">@yield('titre')</h1>

            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed">@yield('message')</p>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
                   class="btn btn-secondary btn-sm">
                    <x-icone nom="arrow-left" taille="sm" />
                    Revenir en arrière
                </a>
                <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                    <x-icone nom="home" taille="sm" />
                    Accueil
                </a>
            </div>

            <nav class="mt-10 border-t border-line pt-6" aria-label="Raccourcis">
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                    <li>
                        <a href="{{ route('tournois.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-mut transition hover:text-cy">
                            <x-icone nom="trophy" taille="xs" />
                            Tournois
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-mut transition hover:text-cy">
                            <x-icone nom="file-text" taille="xs" />
                            Guides
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lft.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-mut transition hover:text-cy">
                            <x-icone nom="users" taille="xs" />
                            LFT
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>

    <footer class="border-t border-line bg-surface py-5">
        <p class="text-center font-mono text-[0.72rem] text-dim">
            &copy; {{ now()->year }} {{ config('arene.site.nom') }}
        </p>
    </footer>
</body>
</html>