@extends('layouts.admin')

@section('titre', 'Tableau de bord')

@section('contenu')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                <x-icone nom="layout" taille="lg" class="text-arn" />
                Tableau de bord
            </h1>
            <p class="mt-1 text-sm text-slate-500">Vue d'ensemble du site et de la synchronisation.</p>
        </div>

        <a href="{{ route('home') }}" target="_blank"
           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
            <x-icone nom="eye" taille="xs" />
            Voir le site
        </a>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Tournois --}}
        <div class="carte animate-fade-up p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Tournois</p>
                <x-icone nom="trophy" taille="sm" class="text-arn" />
            </div>
            <p class="mt-2 text-3xl font-extrabold tabular-nums">{{ $nbTournois }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $nbTournoisAVenir }} à venir</p>
        </div>

        {{-- Synchro --}}
        <div class="carte animate-fade-up p-5" style="animation-delay: 60ms">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Dernière synchro</p>
                <x-icone nom="refresh" taille="sm" class="{{ $derniereSync ? 'text-emerald-500' : 'text-slate-300' }}" />
            </div>
            <p class="mt-2 text-base font-extrabold">
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'Jamais' }}
            </p>
            <form method="POST" action="{{ route('admin.tournois.sync') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                    <x-icone nom="refresh" taille="xs" />
                    Synchroniser
                </button>
            </form>
        </div>

        {{-- Articles --}}
        <div class="carte animate-fade-up p-5" style="animation-delay: 120ms">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Articles</p>
                <x-icone nom="file-text" taille="sm" class="text-slate-400" />
            </div>
            <p class="mt-2 text-3xl font-extrabold tabular-nums">{{ $nbArticles }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $nbArticlesPublies }} publiés</p>
        </div>

        {{-- LFT --}}
        <div class="carte animate-fade-up p-5" style="animation-delay: 180ms">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Profils LFT</p>
                <x-icone nom="users" taille="sm" class="text-slate-400" />
            </div>
            <p class="mt-2 text-3xl font-extrabold tabular-nums">{{ $nbProfilsValides }}</p>
            <p class="mt-1 text-xs text-slate-500">
                @if ($nbProfilsEnAttente > 0)
                    <span class="inline-flex items-center gap-1 font-semibold text-amber-700">
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
        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 animate-fade-in">
            <div>
                <p class="flex items-center gap-1.5 text-sm font-bold text-amber-900">
                    <x-icone nom="alert" taille="sm" />
                    Modération en attente
                </p>
                <p class="mt-1 text-sm text-amber-800">
                    {{ $nbProfilsEnAttente }} profil(s) LFT attendent une validation avant publication.
                </p>
            </div>
            <a href="{{ route('admin.lft.index', ['statut' => 'en_attente']) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-amber-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-800">
                <x-icone nom="shield" taille="xs" />
                Modérer maintenant
            </a>
        </div>
    @endif

    {{-- Rappel quota --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
        <p class="flex items-center gap-1.5 text-sm font-bold text-slate-900">
            <x-icone nom="info" taille="sm" class="text-slate-400" />
            Quota API GameHub Arena
        </p>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">
            Plan gratuit : 1 000 requêtes/mois. La synchronisation automatique tourne
            toutes les {{ config('arene.sync.intervalle_minutes') }} minutes et consomme
            au plus {{ config('arene.sync.pages_max') }} requête(s) par exécution.
            Évitez les synchronisations manuelles répétées.
        </p>
    </div>
@endsection