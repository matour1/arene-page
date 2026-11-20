@extends('layouts.app')

@section('titre', $article->titre)
@section('description', $article->chapo ?? config('arene.site.baseline'))

@section('contenu')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-mut transition hover:text-cy">
            <x-icone nom="arrow-left" taille="sm" />
            Guides
        </a>

        <article class="mt-6">
            <header>
                @if ($article->categorie)
                    <span class="badge badge-cyan">{{ $article->categorie }}</span>
                @endif

                <h1 class="mt-3 text-2xl font-bold leading-tight text-txt sm:text-3xl">
                    {{ $article->titre }}
                </h1>

                <p class="chiffre mt-3 text-[0.8rem] text-dim">
                    {{ $article->auteur }}
                    · <time datetime="{{ $article->publie_at?->toIso8601String() }}">
                        {{ $article->publie_at?->locale('fr')->translatedFormat('d M Y') }}
                    </time>
                </p>

                @if ($article->chapo)
                    <p class="mt-5 border-l-2 border-cy pl-4 text-[1.05rem] leading-relaxed text-txt/80">
                        {{ $article->chapo }}
                    </p>
                @endif
            </header>

            @if ($article->image)
                <img src="{{ $article->urlImage() }}"
                     alt=""
                     class="mt-6 w-full rounded-xl border border-line object-cover">
            @endif

            {{-- Contenu saisi par l'admin en Markdown, rendu cote serveur. --}}
            <div class="prose-charte mt-8">
                {!! \Illuminate\Support\Str::markdown($article->contenu) !!}
            </div>
        </article>

        {{-- Publicite uniquement en bas d'article, jamais pres d'un CTA. --}}
        <div class="mt-10">
            @include('partials.ad')
        </div>

        @if ($autres->isNotEmpty())
            <section class="mt-12 border-t border-line pt-8">
                <h2 class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-dim">À lire ensuite</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($autres as $autre)
                        <a href="{{ route('articles.show', $autre->slug) }}"
                           class="carte carte-survol block !p-4">
                            <p class="text-sm font-bold text-txt">{{ $autre->titre }}</p>
                            <p class="mt-1 line-clamp-2 text-xs">{{ $autre->chapo }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection