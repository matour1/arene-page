@extends('layouts.app')

@section('titre', 'Arène Bot — bientôt disponible')
@section('description', "L'IA de l'Arène des Gamers : matchmaking intelligent et génération de contenu.")

@section('contenu')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
        <span class="badge badge-violet">
            <x-icone nom="bot" taille="xs" />
            Bientôt disponible
        </span>

        <h1 class="mt-4 text-2xl font-bold text-txt sm:text-3xl">Arène Bot</h1>

        <p class="mt-3 text-[1.05rem] leading-relaxed">
            Une intelligence artificielle pensée pour la communauté gaming africaine.
            Pas encore en ligne — voici ce qu'elle fera.
        </p>

        <div class="mt-10 space-y-4">
            <div class="carte">
                <h2 class="flex items-center gap-2 text-[1.05rem] font-bold text-txt">
                    <x-icone nom="crosshair" taille="sm" class="text-cy" />
                    Matchmaking intelligent
                </h2>
                <p class="mt-2 text-sm leading-relaxed">
                    Au lieu de filtrer manuellement, tu décris ce que tu cherches en langage naturel
                    (« un rusher Heroic disponible le soir, francophone ») et le bot te propose
                    les profils LFT les plus pertinents.
                </p>
            </div>

            <div class="carte">
                <h2 class="flex items-center gap-2 text-[1.05rem] font-bold text-txt">
                    <x-icone nom="file-text" taille="sm" class="text-cy" />
                    Génération de guides
                </h2>
                <p class="mt-2 text-sm leading-relaxed">
                    Résumés de tournois, portraits de joueurs et guides de progression générés
                    à partir des données de la plateforme, puis relus par un humain avant publication.
                </p>
            </div>

            <div class="carte">
                <h2 class="flex items-center gap-2 text-[1.05rem] font-bold text-txt">
                    <x-icone nom="zap" taille="sm" class="text-cy" />
                    Alertes personnalisées
                </h2>
                <p class="mt-2 text-sm leading-relaxed">
                    Notification quand un tournoi correspond à ton jeu, ton rang et tes horaires —
                    par WhatsApp ou Discord.
                </p>
            </div>
        </div>

        <div class="carte mt-10 border-vio/30 bg-vio/5">
            <p class="text-sm font-semibold text-txt">En attendant, tout le reste fonctionne déjà.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('tournois.index') }}" class="btn btn-primary btn-sm">
                    <x-icone nom="trophy" taille="xs" />
                    Voir les tournois
                </a>
                <a href="{{ route('lft.index') }}" class="btn btn-neon btn-sm">
                    <x-icone nom="users" taille="xs" />
                    Profils LFT
                </a>
            </div>
        </div>
    </div>
@endsection