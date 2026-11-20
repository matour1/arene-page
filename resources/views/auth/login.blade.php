<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — {{ config('arene.site.nom') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 font-sans antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white">
                AG
            </span>
            <h1 class="mt-4 text-xl font-extrabold tracking-tight">Back-office</h1>
            <p class="mt-1 text-sm text-slate-500">{{ config('arene.site.nom') }}</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <label class="block">
                <span class="etiquette">E-mail</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="champ">
            </label>

            <label class="block">
                <span class="etiquette">Mot de passe</span>
                <input type="password" name="password" required
                       class="champ">
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1"
                       class="rounded border-slate-300 text-arn focus:ring-arn/20">
                <span class="text-sm text-slate-600">Rester connecté</span>
            </label>

            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Se connecter
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-900">← Retour au site</a>
        </p>
    </div>
</body>
</html>
