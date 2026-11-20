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
    <title>Connexion — {{ config('arene.site.nom') }}</title>
    <meta name="theme-color" content="#0a0c10">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-base px-4 font-sans text-mut antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="logo-site mx-auto size-14">
            <h1 class="titre mt-4 font-mono text-[0.8rem] font-bold uppercase tracking-[0.15em] text-txt">
                Back-office
            </h1>
            <p class="mt-1 text-xs text-dim">{{ config('arene.site.nom') }}</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="carte space-y-4">
            @csrf

            @if ($errors->any())
                <div class="alerte alerte-err">
                    <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <label class="block">
                <span class="etiquette">E-mail</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" class="champ">
            </label>

            <label class="block">
                <span class="etiquette">Mot de passe</span>
                <input type="password" name="password" required autocomplete="current-password" class="champ">
            </label>

            <label class="flex cursor-pointer items-center gap-2.5">
                <input type="checkbox" name="remember" value="1"
                       class="size-4 rounded border-line2 bg-base text-cy focus:ring-2 focus:ring-cy/30">
                <span class="text-[0.85rem]">Rester connecté</span>
            </label>

            <button type="submit" class="btn btn-primary w-full">
                <x-icone nom="log-out" taille="sm" class="rotate-180" />
                Se connecter
            </button>
        </form>

        <div class="mt-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-mut transition hover:text-cy">
                <x-icone nom="arrow-left" taille="xs" />
                Retour au site
            </a>
            <x-bascule-theme />
        </div>
    </div>
</body>
</html>