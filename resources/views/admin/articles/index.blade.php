@extends('layouts.admin')

@section('titre', 'Articles')

@section('contenu')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Contenu</span>
            <h1 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
                <x-icone nom="file-text" taille="lg" class="text-cy" />
                Articles
            </h1>
            <p class="mt-1 text-sm">{{ $articles->total() }} article(s) au total.</p>
        </div>

        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">
            <x-icone nom="plus" taille="xs" />
            Nouvel article
        </a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-line bg-surface">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-elevated font-mono text-[0.68rem] uppercase tracking-wider text-dim">
                <tr>
                    <th class="px-5 py-3 font-semibold">Titre</th>
                    <th class="px-5 py-3 font-semibold">Catégorie</th>
                    <th class="px-5 py-3 font-semibold">Statut</th>
                    <th class="px-5 py-3 font-semibold">Publié</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($articles as $article)
                    <tr class="transition hover:bg-elevated/50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-txt">{{ $article->titre }}</p>
                            <p class="font-mono text-[0.7rem] text-dim">/{{ $article->slug }}</p>
                        </td>
                        <td class="px-5 py-4">{{ $article->categorie ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if ($article->estPublie())
                                <span class="badge badge-ok">
                                    <x-icone nom="check" taille="xs" />
                                    Publié
                                </span>
                            @else
                                <span class="badge badge-neutral">Brouillon</span>
                            @endif
                        </td>
                        <td class="chiffre px-5 py-4 text-[0.8rem]">
                            {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @if ($article->estPublie())
                                    <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                       title="Voir" class="rounded-lg p-2 text-mut transition hover:bg-cy/10 hover:text-cy">
                                        <x-icone nom="eye" taille="sm" />
                                    </a>
                                @endif
                                <a href="{{ route('admin.articles.edit', $article) }}" title="Éditer"
                                   class="rounded-lg p-2 text-mut transition hover:bg-cy/10 hover:text-cy">
                                    <x-icone nom="edit" taille="sm" />
                                </a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                      onsubmit="return confirm('Supprimer cet article ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer"
                                            class="rounded-lg p-2 text-mut transition hover:bg-err/10 hover:text-err">
                                        <x-icone nom="trash" taille="sm" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <x-icone nom="file-text" taille="xl" class="mx-auto text-dim" />
                            <p class="mt-3 text-sm font-semibold text-txt">Aucun article.</p>
                            <a href="{{ route('admin.articles.create') }}" class="btn btn-neon btn-sm mt-3">
                                <x-icone nom="plus" taille="xs" />
                                Créer le premier article
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
@endsection