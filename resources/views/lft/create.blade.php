@extends('layouts.app')

@section('titre', 'Créer mon profil LFT')
@section('description', 'Publie ton profil Looking For Team et laisse les joueurs te contacter.')

@section('contenu')
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 sm:py-12">
        <a href="{{ route('lft.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-mut transition hover:text-cy">
            <x-icone nom="arrow-left" taille="sm" />
            Profils LFT
        </a>

        <header class="mt-5">
            <h1 class="text-2xl font-bold text-txt sm:text-3xl">Créer mon profil</h1>
            <p class="mt-2 text-sm leading-relaxed">
                Ton profil sera visible après validation par notre équipe (sous 24 h).
                Indique au moins un moyen de contact — sans contact, personne ne peut te joindre.
            </p>
        </header>

        @if ($errors->any())
            <div class="alerte alerte-err mt-6">
                <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
                <div>
                    <p class="font-bold">Corrige les points suivants :</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('lft.store') }}" class="carte mt-6 space-y-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="etiquette">Pseudo <span class="text-err">*</span></span>
                    <input type="text" name="pseudo" value="{{ old('pseudo') }}" required maxlength="50"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Jeu <span class="text-err">*</span></span>
                    <input type="text" name="jeu" value="{{ old('jeu', 'Free Fire') }}" required maxlength="60"
                           class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Rôle recherché</span>
                    <input type="text" name="role" value="{{ old('role') }}" maxlength="40"
                           placeholder="rusher, sniper, IGL…" class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Rang</span>
                    <input type="text" name="rang" value="{{ old('rang') }}" maxlength="40"
                           placeholder="Heroic, Master…" class="champ">
                </label>

                <label class="block">
                    <span class="etiquette">Pays <span class="text-err">*</span></span>
                    <select name="pays" required class="champ">
                        @foreach ($pays as $code => $libelle)
                            <option value="{{ $code }}" @selected(old('pays', 'CM') === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette">Langue <span class="text-err">*</span></span>
                    <select name="langue" required class="champ">
                        @foreach ($langues as $code => $libelle)
                            <option value="{{ $code }}" @selected(old('langue', 'fr') === $code)>{{ $libelle }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block sm:col-span-2">
                    <span class="etiquette">Plateforme</span>
                    <input type="text" name="plateforme" value="{{ old('plateforme', 'mobile') }}" maxlength="40"
                           class="champ">
                </label>
            </div>

            <hr class="border-line">

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="etiquette">Discord</span>
                    <input type="text" name="contact_discord" value="{{ old('contact_discord') }}" maxlength="80"
                           placeholder="pseudo#0000" class="champ">
                    <span class="astuce mt-1">Discord ou WhatsApp : au moins un des deux.</span>
                </label>

                <label class="block">
                    <span class="etiquette">WhatsApp</span>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp') }}" maxlength="30"
                           placeholder="+237 6XX XX XX XX" class="champ">
                    <span class="astuce mt-1">Format international recommandé.</span>
                </label>
            </div>

            <label class="block">
                <span class="etiquette">Description</span>
                <textarea name="description" rows="4" maxlength="600"
                          placeholder="Ton style de jeu, tes horaires, ce que tu cherches…"
                          class="champ resize-y">{{ old('description') }}</textarea>
            </label>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-line pt-5">
                <p class="text-xs text-dim">Pas de messagerie intégrée : les joueurs te contactent directement.</p>
                <button type="submit" class="btn btn-primary">
                    <x-icone nom="check" taille="sm" />
                    Publier mon profil
                </button>
            </div>
        </form>
    </div>
@endsection