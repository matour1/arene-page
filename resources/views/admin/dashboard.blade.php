@extends('layouts.admin')

@section('titre', 'Tableau de bord')

@section('contenu')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Vue d'ensemble</span>
            <h1 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
                <x-icone nom="layout" taille="lg" class="text-cy" />
                Tableau de bord
            </h1>
        </div>

        <a href="{{ route('home') }}" target="_blank" class="btn btn-neon btn-sm">
            <x-icone nom="eye" taille="xs" />
            Voir le site
        </a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="carte animate-fade-up">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Tournois</p>
                <x-icone nom="trophy" taille="sm" class="text-cy" />
            </div>
            <p class="chiffre mt-2 text-3xl font-bold text-cy">{{ $nbTournois }}</p>
            <p class="mt-1 text-xs">{{ $nbTournoisAVenir }} à venir</p>
        </div>

        <div class="carte animate-fade-up" style="animation-delay:60ms">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Dernière synchro</p>
                <x-icone nom="refresh" taille="sm" class="{{ $derniereSync ? 'text-ok' : 'text-dim' }}" />
            </div>
            <p class="chiffre mt-2 text-[0.9rem] font-bold text-txt">
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'Jamais' }}
            </p>
            <form method="POST" action="{{ route('admin.tournois.sync') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <x-icone nom="refresh" taille="xs" />
                    Synchroniser
                </button>
            </form>
        </div>

        <div class="carte animate-fade-up" style="animation-delay:120ms">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Articles</p>
                <x-icone nom="file-text" taille="sm" class="text-vio" />
            </div>
            <p class="chiffre mt-2 text-3xl font-bold text-cy">{{ $nbArticles }}</p>
            <p class="mt-1 text-xs">{{ $nbArticlesPublies }} publiés</p>
        </div>

        <div class="carte animate-fade-up" style="animation-delay:180ms">
            <div class="flex items-center justify-between">
                <p class="font-mono text-[0.7rem] uppercase tracking-[0.15em] text-dim">Profils LFT</p>
                <x-icone nom="users" taille="sm" class="text-vio" />
            </div>
            <p class="chiffre mt-2 text-3xl font-bold text-cy">{{ $nbProfilsValides }}</p>
            <p class="mt-1 text-xs">
                @if ($nbProfilsEnAttente > 0)
                    <span class="inline-flex items-center gap-1 font-semibold text-warn">
                        <x-icone nom="alert" taille="xs" />
                        {{ $nbProfilsEnAttente }} en attente
                    </span>
                @else
                    Aucun en attente
                @endif
            </p>
        </div>
    </div>

    @if ($nbProfilsEnAttente > 0)
        <div class="alerte alerte-warn mt-6 animate-fade-in flex-wrap items-center justify-between gap-4">
            <div>
                <p class="flex items-center gap-2 text-sm font-bold">
                    <x-icone nom="alert" taille="sm" />
                    Modération en attente
                </p>
                <p class="mt-1 text-sm">
                    {{ $nbProfilsEnAttente }} profil(s) LFT attendent une validation avant publication.
                </p>
            </div>
            <a href="{{ route('admin.lft.index', ['statut' => 'en_attente']) }}" class="btn btn-primary btn-sm">
                <x-icone nom="shield" taille="xs" />
                Modérer
            </a>
        </div>
    @endif

    <div class="carte mt-6">
        <p class="flex items-center gap-2 text-sm font-bold text-txt">
            <x-icone nom="info" taille="sm" class="text-cy" />
            Quota API GameHub Arena
        </p>
        <p class="mt-2 text-sm leading-relaxed">
            Plan gratuit : 1 000 requêtes/mois. La synchronisation automatique tourne
            toutes les {{ config('arene.sync.intervalle_minutes') }} minutes et consomme
            au plus {{ config('arene.sync.pages_max') }} requête(s) par exécution.
        </p>
    </div>
@endsection