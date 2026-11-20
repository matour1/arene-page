@extends('layouts.admin')

@section('titre', 'Articles')

@section('contenu')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                <x-icone nom="file-text" taille="lg" class="text-orange-600" />
                Articles
            </h1>
            <p class="mt-1 text-sm text-slate-500">{{ $articles->total() }} article(s) au total.</p>
        </div>

        <a href="{{ route('admin.articles.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
            <x-icone nom="plus" taille="sm" />
            Nouvel article
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-semibold">Titre</th>
                    <th class="px-5 py-3 font-semibold">Catégorie</th>
                    <th class="px-5 py-3 font-semibold">Statut</th>
                    <th class="px-5 py-3 font-semibold">Publié le</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($articles as $article)
                    <tr class="transition-colors hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $article->titre }}</p>
                            <p class="font-mono text-[11px] text-slate-400">/{{ $article->slug }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $article->categorie ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if ($article->estPublie())
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">
                                    <x-icone nom="check" taille="xs" />
                                    Publié
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">
                                    <x-icone nom="edit" taille="xs" />
                                    Brouillon
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-3">
                                @if ($article->estPublie())
                                    <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                       title="Voir sur le site"
                                       class="text-slate-400 transition hover:text-slate-900">
                                        <x-icone nom="eye" taille="sm" />
                                    </a>
                                @endif
                                <a href="{{ route('admin.articles.edit', $article) }}"
                                   title="Éditer"
                                   class="text-orange-600 transition hover:text-orange-700">
                                    <x-icone nom="edit" taille="sm" />
                                </a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                      onsubmit="return confirm('Supprimer cet article ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Supprimer"
                                            class="text-red-500 transition hover:text-red-700">
                                        <x-icone nom="trash" taille="sm" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <x-icone nom="file-text" taille="xl" class="mx-auto text-slate-300" />
                            <p class="mt-3 text-sm font-semibold text-slate-700">Aucun article.</p>
                            <a href="{{ route('admin.articles.create') }}"
                               class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-orange-700 hover:text-orange-800">
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