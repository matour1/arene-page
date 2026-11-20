@extends('layouts.admin')

@section('titre', 'Tournois')

@section('contenu')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">Tournois</h1>
            <p class="mt-1 text-sm text-slate-500">
                Dernière synchronisation :
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'jamais' }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.tournois.sync') }}">
            @csrf
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Synchroniser maintenant
            </button>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-semibold">Tournoi</th>
                    <th class="px-5 py-3 font-semibold">Jeu</th>
                    <th class="px-5 py-3 font-semibold">Début</th>
                    <th class="px-5 py-3 font-semibold">Places</th>
                    <th class="px-5 py-3 font-semibold">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tournois as $tournoi)
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $tournoi->titre }}</p>
                            <p class="text-xs text-slate-400">{{ $tournoi->id }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $tournoi->jeu }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M Y · H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $tournoi->places_inscrits }}/{{ $tournoi->places_maximum }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold
                                {{ $tournoi->disponible ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $tournoi->statut }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                            Aucun tournoi en base. Lance une synchronisation.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tournois->links() }}</div>
@endsection
