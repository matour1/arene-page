@extends('layouts.app')

@section('titre', 'Guides & articles')
@section('description', 'Guides, analyses et actualités de la scène compétitive gaming africaine.')

@section('contenu')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
        <header class="mb-8">
            <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Ressources</span>
            <h1 class="mt-1 flex items-center gap-2 text-2xl font-bold text-txt sm:text-3xl">
                <x-icone nom="file-text" taille="lg" class="text-cy" />
                Guides & articles
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed">
                Guides pratiques et analyses pour progresser et comprendre la scène compétitive.
            </p>
        </header>

        @if ($articles->isEmpty())
            <div class="carte px-6 py-12 text-center">
                <x-icone nom="file-text" taille="xl" class="mx-auto text-dim" />
                <p class="mt-3 text-sm font-semibold text-txt">Aucun article publié pour l'instant.</p>
                <p class="mt-1 text-sm">Le premier arrive très bientôt.</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article->slug) }}"
                       class="carte carte-survol group flex flex-col !p-0">
                        @if ($article->image)
                            <img src="{{ $article->urlImage() }}"
                                 alt=""
                                 class="h-40 w-full object-cover"
                                 loading="lazy">
                        @endif

                        <div class="flex flex-1 flex-col p-5">
                            @if ($article->categorie)
                                <span class="badge badge-cyan self-start">{{ $article->categorie }}</span>
                            @endif
                            <h2 class="mt-3 line-clamp-2 text-[1.05rem] font-bold leading-snug text-txt transition group-hover:text-cy">
                                {{ $article->titre }}
                            </h2>
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed">
                                {{ $article->chapo }}
                            </p>
                            <span class="chiffre mt-auto flex items-center gap-2 pt-4 text-[0.78rem] text-dim">
                                <x-icone nom="clock" taille="xs" />
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