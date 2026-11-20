@extends('layouts.app')

@section('titre', 'Calendrier des tournois')
@section('description', 'Tous les tournois GameHub Arena à venir : jeu, format, places restantes et inscriptions.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
        <header class="mb-6">
            <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Competitions</span>
            <h1 class="mt-1 flex items-center gap-2 text-2xl font-bold text-txt sm:text-3xl">
                <x-icone nom="trophy" taille="lg" class="text-cy" />
                Calendrier
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed">
                Synchronisé automatiquement avec GameHub Arena. Les places et les horaires
                peuvent évoluer — vérifie sur la page du tournoi avant de t'inscrire.
            </p>
        </header>

        <form method="GET" action="{{ route('tournois.index') }}" class="carte mb-6 !p-4">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <label class="block">
                    <span class="etiquette !text-[0.78rem]">Jeu</span>
                    <select name="jeu" class="champ !py-2 !text-[0.85rem]">
                        <option value="">Tous</option>
                        @foreach ($jeux as $jeu)
                            <option value="{{ $jeu }}" @selected($filtres['jeu'] === $jeu)>{{ $jeu }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="etiquette !text-[0.78rem]">Statut</span>
                    <select name="statut" class="champ !py-2 !text-[0.85rem]">
                        <option value="">Tous</option>
                        <option value="ouvert" @selected($filtres['statut'] === 'ouvert')>Ouvertes</option>
                    </select>
                </label>

                <label class="col-span-2 flex items-center gap-2.5 pt-1 sm:col-span-1 sm:pb-1">
                    <input type="checkbox" name="gratuit" value="1" @checked($filtres['gratuit'])
                           class="size-4 rounded border-line2 bg-base text-cy focus:ring-2 focus:ring-cy/30">
                    <span class="text-[0.85rem] font-semibold text-txt">Gratuit</span>
                </label>

                <div class="col-span-2 flex items-end gap-2 sm:col-span-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Filtrer</button>
                    <a href="{{ route('tournois.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>

        @if ($tournois->isEmpty())
            <div class="carte px-6 py-12 text-center">
                <x-icone nom="calendar" taille="xl" class="mx-auto text-dim" />
                <p class="mt-3 text-sm font-semibold text-txt">Aucun tournoi ne correspond.</p>
                <p class="mt-1 text-sm">Essaie d'élargir les filtres.</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tournois as $tournoi)
                    @include('partials.carte-tournoi', ['tournoi' => $tournoi])
                @endforeach
            </div>

            <div class="mt-8">{{ $tournois->links() }}</div>
        @endif
    </div>
@endsection