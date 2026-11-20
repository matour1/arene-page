@extends('layouts.admin')

@section('titre', 'Tableau de bord')

@section('contenu')
    <h1 class="text-2xl font-extrabold tracking-tight">Tableau de bord</h1>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Tournois</p>
            <p class="mt-2 text-3xl font-extrabold">{{ $nbTournois }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $nbTournoisAVenir }} à venir</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Dernière synchro</p>
            <p class="mt-2 text-base font-extrabold">
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'Jamais' }}
            </p>
            <form method="POST" action="{{ route('admin.tournois.sync') }}" class="mt-3">
                @csrf
                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                    Synchroniser maintenant
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Articles</p>
            <p class="mt-2 text-3xl font-extrabold">{{ $nbArticles }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $nbArticlesPublies }} publiés</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Profils LFT</p>
            <p class="mt-2 text-3xl font-extrabold">{{ $nbProfilsValides }}</p>
            <p class="mt-1 text-xs text-slate-500">
                @if ($nbProfilsEnAttente > 0)
                    <span class="font-semibold text-amber-700">{{ $nbProfilsEnAttente }} en attente de validation</span>
                @else
                    Aucun en attente
                @endif
            </p>
        </div>
    </div>

    @if ($nbProfilsEnAttente > 0)
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-sm font-bold text-amber-900">Modération en attente</p>
            <p class="mt-1 text-sm text-amber-800">{{ $nbProfilsEnAttente }} profil(s) LFT attendent une validation.</p>
            <a href="{{ route('admin.lft.index', ['statut' => 'en_attente']) }}"
               class="mt-3 inline-block rounded-lg bg-amber-700 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-800">
                Modérer maintenant
            </a>
        </div>
    @endif
@endsection
