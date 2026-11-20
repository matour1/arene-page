@extends('layouts.admin')

@section('titre', 'Articles')

@section('contenu')
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-extrabold tracking-tight">Articles</h1>
        <a href="{{ route('admin.articles.create') }}"
           class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
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
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $article->titre }}</p>
                            <p class="text-xs text-slate-400">/{{ $article->slug }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $article->categorie ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @if ($article->estPublie())
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">Publié</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">Brouillon</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if ($article->estPublie())
                                    <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                       class="text-xs font-semibold text-slate-500 hover:text-slate-900">Voir</a>
                                @endif
                                <a href="{{ route('admin.articles.edit', $article) }}"
                                   class="text-xs font-semibold text-orange-700 hover:text-orange-800">Éditer</a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                      onsubmit="return confirm('Supprimer cet article ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                            Aucun article. Crée le premier.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
@endsection
