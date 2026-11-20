@extends('layouts.app')

@section('titre', 'Articles')
@section('description', 'Guides, analyses et actualités de la scène compétitive gaming africaine.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Articles</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                Guides pratiques et analyses pour progresser et comprendre la scène compétitive.
            </p>
        </header>

        @if ($articles->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-base font-semibold text-slate-700">Aucun article publié pour l'instant.</p>
                <p class="mt-1 text-sm text-slate-500">Le premier arrive très bientôt.</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article->slug) }}"
                       class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:border-slate-300 hover:shadow-sm">
                        @if ($article->image)
                            <img src="{{ $article->urlImage() }}"
                                 alt=""
                                 class="h-40 w-full object-cover"
                                 loading="lazy">
                        @endif

                        <div class="flex flex-1 flex-col p-5">
                            @if ($article->categorie)
                                <span class="text-[11px] font-bold uppercase tracking-widest text-arn">
                                    {{ $article->categorie }}
                                </span>
                            @endif
                            <h2 class="mt-2 text-base font-bold leading-snug group-hover:text-arn">
                                {{ $article->titre }}
                            </h2>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">
                                {{ $article->chapo }}
                            </p>
                            <span class="mt-auto pt-4 text-xs text-slate-400">
                                {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $articles->links() }}</div>
        @endif
    </div>
@endsection
