@extends('layouts.admin')

@section('titre', 'Tournois')

@section('contenu')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Synchronisation</span>
            <h1 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
                <x-icone nom="trophy" taille="lg" class="text-cy" />
                Tournois
            </h1>
            <p class="chiffre mt-1 text-[0.8rem]">
                Dernière synchro :
                {{ $derniereSync ? \Illuminate\Support\Carbon::parse($derniereSync)->locale('fr')->translatedFormat('d M Y · H:i') : 'jamais' }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.tournois.sync') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <x-icone nom="refresh" taille="xs" />
                Synchroniser
            </button>
        </form>
    </div>

    @if ($nbTotal > 0)
        <div class="alerte mt-4 {{ $nbAvecLien === $nbTotal ? 'alerte-ok' : 'alerte-warn' }}">
            <x-icone :nom="$nbAvecLien === $nbTotal ? 'check-circle' : 'alert'" taille="sm" class="mt-0.5 shrink-0" />
            <span>
                {{ $nbAvecLien }} / {{ $nbTotal }} tournoi(s) avec lien d'affiliation.
                @if ($nbAvecLien < $nbTotal)
                    Les autres utilisent le lien global (<code>GAMEHUB_AFFILIATE_URL</code>).
                @endif
            </span>
        </div>
    @endif

    <div class="mt-6 overflow-x-auto rounded-xl border border-line bg-surface">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-line bg-elevated font-mono text-[0.68rem] uppercase tracking-wider text-dim">
                <tr>
                    <th class="px-5 py-3 font-semibold">Tournoi</th>
                    <th class="px-5 py-3 font-semibold">Jeu</th>
                    <th class="px-5 py-3 font-semibold">Début</th>
                    <th class="px-5 py-3 font-semibold">Places</th>
                    <th class="px-5 py-3 font-semibold">Affiliation</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($tournois as $tournoi)
                    <tr class="transition hover:bg-elevated/50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-txt">{{ $tournoi->titrePropre() }}</p>
                            <p class="font-mono text-[0.7rem] text-dim">{{ $tournoi->id }}</p>
                        </td>
                        <td class="px-5 py-4">{{ $tournoi->jeu }}</td>
                        <td class="chiffre px-5 py-4 text-[0.8rem]">
                            {{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M Y · H:i') ?? '—' }}
                        </td>
                        <td class="chiffre px-5 py-4 text-[0.8rem]">
                            {{ $tournoi->places_inscrits }}/{{ $tournoi->places_maximum }}
                        </td>
                        <td class="px-5 py-4">
                            @if ($tournoi->lien_affiliation)
                                <span class="badge badge-ok">
                                    <x-icone nom="check" taille="xs" />
                                    Dédié
                                </span>
                            @else
                                <span class="badge badge-neutral">Global</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.tournois.edit', $tournoi) }}" class="btn btn-secondary btn-sm">
                                <x-icone nom="edit" taille="xs" />
                                Lien
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <x-icone nom="trophy" taille="xl" class="mx-auto text-dim" />
                            <p class="mt-3 text-sm font-semibold text-txt">Aucun tournoi en base.</p>
                            <p class="mt-1 text-sm">Lance une synchronisation.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tournois->links() }}</div>
@endsection