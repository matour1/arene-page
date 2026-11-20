@extends('layouts.app')

@section('titre', 'À propos')

@section('contenu')
    <div class="mx-auto max-w-2xl px-4 py-14 sm:px-6">
        <h1 class="text-3xl font-extrabold tracking-tight">À propos</h1>

        <div class="prose mt-6 max-w-none text-slate-700">
            <p>
                {{ config('arene.site.nom') }} est un point de rendez-vous pour les joueurs compétitifs
                d'Afrique centrale et de l'Ouest. Notre objectif : rendre visible tout ce qui se passe
                dans l'arène — les tournois, les joueurs qui cherchent une équipe, et les conseils
                qui font progresser.
            </p>

            <h2>Ce que nous faisons</h2>
            <ul>
                <li>Un calendrier des tournois GameHub Arena, synchronisé automatiquement.</li>
                <li>Un espace LFT (Looking For Team) pour trouver des coéquipiers.</li>
                <li>Des articles originaux sur la scène compétitive.</li>
            </ul>

            <h2>Transparence</h2>
            <p>
                Certains liens vers les tournois sont des liens d'affiliation : si tu t'inscris à un
                tournoi payant via notre site, nous percevons une commission de la plateforme.
                Le prix que tu paies reste identique.
            </p>
        </div>
    </div>
@endsection
