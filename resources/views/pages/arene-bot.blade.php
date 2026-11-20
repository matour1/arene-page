@extends('layouts.app')

@section('titre', 'Arène Bot — bientôt disponible')
@section('description', 'L\'IA de l\'Arène des Gamers : matchmaking intelligent et génération de contenu. Bientôt disponible.')

@section('contenu')
    <div class="mx-auto max-w-3xl px-4 py-14 sm:px-6">
        <span class="inline-flex items-center gap-2 rounded-full bg-violet-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-violet-800">
            <x-icone nom="bot" taille="xs" />
            Bientôt disponible
        </span>

        <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">
            Arène Bot
        </h1>

        <p class="mt-4 text-lg leading-relaxed text-slate-700">
            Une intelligence artificielle pensée pour la communauté gaming africaine.
            Pas encore en ligne — voici ce qu'elle fera.
        </p>

        <div class="mt-10 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-base font-extrabold">Matchmaking intelligent</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Au lieu de filtrer manuellement, tu décris ce que tu cherches en langage naturel
                    (« un rusher Heroic disponible le soir, francophone ») et le bot te propose
                    les profils LFT les plus pertinents.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-base font-extrabold">Génération d'articles</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Résumés de tournois, portraits de joueurs et guides de progression générés
                    à partir des données de la plateforme, puis relus par un humain avant publication.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-base font-extrabold">Alertes personnalisées</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                    Notification quand un tournoi correspond à ton jeu, ton rang et tes horaires —
                    par WhatsApp ou Discord.
                </p>
            </div>
        </div>

        <div class="mt-10 rounded-2xl border border-violet-200 bg-violet-50 p-6">
            <p class="text-sm font-semibold text-violet-950">En attendant, tout le reste fonctionne déjà.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('tournois.index') }}" class="rounded-xl bg-violet-700 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-800">
                    Voir les tournois
                </a>
                <a href="{{ route('lft.index') }}" class="rounded-xl border border-violet-300 bg-white px-4 py-2 text-sm font-semibold text-violet-800 hover:bg-violet-100">
                    Parcourir les profils LFT
                </a>
            </div>
        </div>
    </div>
@endsection
