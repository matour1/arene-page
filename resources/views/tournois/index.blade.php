@extends('layouts.app')

@section('titre', 'Calendrier des tournois')
@section('description', 'Tous les tournois GameHub Arena à venir : jeu, format, places restantes et inscriptions.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Calendrier des tournois</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                Synchronisé automatiquement avec GameHub Arena. Les places restantes et les horaires
                peuvent évoluer — vérifie sur la page du tournoi avant de t'inscrire.
            </p>
        </header>

        {{-- Filtres manuels (MVP : pas de recherche full-text) --}}
        <form method="GET" action="{{ route('tournois.index') }}"
              class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-4">
            <label class="block">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Jeu</span>
                <select name="jeu" class="champ">
                    <option value="">Tous les jeux</option>
                    @foreach ($jeux as $jeu)
                        <option value="{{ $jeu }}" @selected($filtres['jeu'] === $jeu)>{{ $jeu }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold text-slate-600">Statut</span>
                <select name="statut" class="champ">
                    <option value="">Tous</option>
                    <option value="ouvert" @selected($filtres['statut'] === 'ouvert')>Inscriptions ouvertes</option>
                </select>
            </label>

            <label class="flex items-end gap-2 pb-2">
                <input type="checkbox" name="gratuit" value="1" @checked($filtres['gratuit'])
                       class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-semibold text-slate-700">Gratuit uniquement</span>
            </label>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Filtrer
                </button>
                <a href="{{ route('tournois.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        @if ($tournois->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-base font-semibold text-slate-700">Aucun tournoi ne correspond.</p>
                <p class="mt-1 text-sm text-slate-500">Essaie d'élargir les filtres ou reviens plus tard.</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tournois as $tournoi)
                    @include('partials.carte-tournoi', ['tournoi' => $tournoi])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $tournois->links() }}
            </div>
        @endif
    </div>
@endsection
