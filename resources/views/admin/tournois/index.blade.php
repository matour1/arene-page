@extends('layouts.admin')

@section('titre', 'Tournois')

@section('contenu')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                <x-icone nom="trophy" taille="lg" class="text-orange-600" />
                Tournois
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Dernière synchronisation :
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'jamais' }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.tournois.sync') }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                <x-icone nom="refresh" taille="sm" />
                Synchroniser maintenant
            </button>
        </form>
    </div>

    @if ($nbTotal > 0)
        <div class="mt-4 inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
            {{ $nbAvecLien === $nbTotal ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
            <x-icone :nom="$nbAvecLien === $nbTotal ? 'check-circle' : 'alert'" taille="sm" />
            {{ $nbAvecLien }} / {{ $nbTotal }} tournoi(s) avec lien d'affiliation.
            @if ($nbAvecLien < $nbTotal)
                Les autres utilisent le lien défini dans GAMEHUB_AFFILIATE_URL.
            @endif
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-semibold">Tournoi</th>
                    <th class="px-5 py-3 font-semibold">Jeu</th>
                    <th class="px-5 py-3 font-semibold">Début</th>
                    <th class="px-5 py-3 font-semibold">Places</th>
                    <th class="px-5 py-3 font-semibold">Affiliation</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tournois as $tournoi)
                    <tr class="transition-colors hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $tournoi->titre }}</p>
                            <p class="font-mono text-[11px] text-slate-400">{{ $tournoi->id }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $tournoi->jeu }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M Y · H:i') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 tabular-nums text-slate-600">
                            {{ $tournoi->places_inscrits }}/{{ $tournoi->places_maximum }}
                        </td>
                        <td class="px-5 py-4">
                            @if ($tournoi->lien_affiliation)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">
                                    <x-icone nom="check" taille="xs" />
                                    Lien dédié
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">
                                    <x-icone nom="info" taille="xs" />
                                    Lien global
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.tournois.edit', $tournoi) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-orange-700 hover:text-orange-800">
                                <x-icone nom="edit" taille="xs" />
                                Lien
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">
                            Aucun tournoi en base. Lance une synchronisation.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tournois->links() }}</div>
@endsection