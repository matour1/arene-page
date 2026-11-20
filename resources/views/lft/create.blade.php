@extends('layouts.app')

@section('titre', 'Créer mon profil LFT')
@section('description', 'Publie ton profil Looking For Team et laisse les joueurs te contacter.')

@section('contenu')
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <a href="{{ route('lft.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">
            ← Profils LFT
        </a>

        <header class="mt-6">
            <h1 class="text-3xl font-extrabold tracking-tight">Créer mon profil LFT</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                Ton profil sera visible après validation par notre équipe (sous 24 h).
                Indique au moins un moyen de contact — sans contact, personne ne peut te joindre.
            </p>
        </header>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="text-sm font-bold text-red-800">Corrige les points suivants :</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('lft.store') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="etiquette">Pseudo *</span>
                    <input type="text" name="pseudo" value="{{ old('pseudo') }}" required maxlength="50"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Jeu *</span>
                    <input type="text" name="jeu" value="{{ old('jeu', 'Free Fire') }}" required maxlength="60"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Rôle recherché</span>
                    <input type="text" name="role" value="{{ old('role') }}" maxlength="40"
                           placeholder="rusher, sniper, IGL…"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Rang</span>
                    <input type="text" name="rang" value="{{ old('rang') }}" maxlength="40"
                           placeholder="Heroic, Master…"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Pays *</span>
                    <select name="pays" required class="champ">
                        @foreach ($pays as $code => $libelle)
                            <option value="{{ $code }}" @selected(old('pays', 'CM') === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette">Langue *</span>
                    <select name="langue" required class="champ">
                        @foreach ($langues as $code => $libelle)
                            <option value="{{ $code }}" @selected(old('langue', 'fr') === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette">Plateforme</span>
                    <input type="text" name="plateforme" value="{{ old('plateforme', 'mobile') }}" maxlength="40"
                           class="champ">
                </label>
            </div>

            <hr class="border-slate-100">

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="etiquette">Discord</span>
                    <input type="text" name="contact_discord" value="{{ old('contact_discord') }}" maxlength="80"
                           placeholder="pseudo#0000 ou identifiant"
                           class="champ">
                    <span class="mt-1 block text-xs text-slate-500">Discord ou WhatsApp : au moins un des deux.</span>
                </label>

                <label class="block">
                    <span class="etiquette">WhatsApp</span>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp') }}" maxlength="30"
                           placeholder="+237 6XX XX XX XX"
                           class="champ">
                    <span class="mt-1 block text-xs text-slate-500">Format international recommandé.</span>
                </label>
            </div>

            <label class="block">
                <span class="etiquette">Description</span>
                <textarea name="description" rows="4" maxlength="600"
                          placeholder="Ton style de jeu, tes horaires, ce que tu cherches…"
                          class="champ">{{ old('description') }}</textarea>
            </label>

            <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-5">
                <p class="text-xs text-slate-500">Pas de messagerie intégrée : les joueurs te contactent directement.</p>
                <button type="submit" class="rounded-xl bg-arn px-6 py-3 text-sm font-bold text-white hover:bg-arn/90">
                    Publier mon profil
                </button>
            </div>
        </form>
    </div>
@endsection
