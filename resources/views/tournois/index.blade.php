@extends('layouts.app')

@section('titre', 'Calendrier des tournois')
@section('description', 'Tous les tournois GameHub Arena à venir : jeu, format, places restantes et inscriptions.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
        <header class="mb-6">
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Calendrier des tournois</h1>
            <p class="mt-2 max-w-2xl text-[13px] leading-relaxed text-slate-600 sm:text-sm">
                Synchronisé automatiquement avec GameHub Arena. Les places restantes et les horaires
                peuvent évoluer — vérifie sur la page du tournoi avant de t'inscrire.
            </p>
        </header>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('tournois.index') }}"
              class="mb-6 rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
            <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 sm:gap-3">
                <label class="block">
                    <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Jeu</span>
                    <select name="jeu" class="champ !py-1.5 !text-[13px]">
                        <option value="">Tous</option>
                        @foreach ($jeux as $jeu)
                            <option value="{{ $jeu }}" @selected($filtres['jeu'] === $jeu)>{{ $jeu }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Statut</span>
                    <select name="statut" class="champ !py-1.5 !text-[13px]">
                        <option value="">Tous</option>
                        <option value="ouvert" @selected($filtres['statut'] === 'ouvert')>Ouvertes</option>
                    </select>
                </label>

                <label class="col-span-2 flex items-center gap-2 pt-1 sm:col-span-1 sm:pb-2">
                    <input type="checkbox" name="gratuit" value="1" @checked($filtres['gratuit'])
                           class="size-4 rounded border-slate-300 text-arn focus:ring-arn/30">
                    <span class="text-[13px] font-semibold text-slate-700">Gratuit</span>
                </label>

                <div class="col-span-2 flex items-end gap-2 sm:col-span-1">
                    <button type="submit"
                            class="flex-1 rounded-lg bg-slate-900 px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-slate-800">
                        Filtrer
                    </button>
                    <a href="{{ route('tournois.index') }}"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-[13px] font-semibold text-slate-700 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @if ($tournois->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <x-icone nom="calendar" taille="xl" class="mx-auto text-slate-300" />
                <p class="mt-3 text-sm font-semibold text-slate-700">Aucun tournoi ne correspond.</p>
                <p class="mt-1 text-[13px] text-slate-500">Essaie d'élargir les filtres ou reviens plus tard.</p>
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