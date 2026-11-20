@extends('layouts.app')

@section('titre', $article->titre)
@section('description', $article->chapo ?? config('arene.site.baseline'))

@section('contenu')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">
            ← Articles
        </a>

        <article class="mt-6">
            <header>
                @if ($article->categorie)
                    <span class="text-[11px] font-bold uppercase tracking-widest text-orange-700">
                        {{ $article->categorie }}
                    </span>
                @endif

                <h1 class="mt-2 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">
                    {{ $article->titre }}
                </h1>

                <p class="mt-3 text-sm text-slate-500">
                    {{ $article->auteur }}
                    · <time datetime="{{ $article->publie_at?->toIso8601String() }}">
                        {{ $article->publie_at?->locale('fr')->translatedFormat('d F Y') }}
                    </time>
                </p>

                @if ($article->chapo)
                    <p class="mt-5 border-l-4 border-orange-500 pl-4 text-lg leading-relaxed text-slate-700">
                        {{ $article->chapo }}
                    </p>
                @endif
            </header>

            @if ($article->image)
                <img src="{{ $article->urlImage() }}"
                     alt=""
                     class="mt-6 w-full rounded-2xl object-cover">
            @endif

            {{-- Le contenu est saisi par l'admin en Markdown simple, rendu côté serveur. --}}
            <div class="prose mt-8 max-w-none text-slate-800">
                {!! \Illuminate\Support\Str::markdown($article->contenu) !!}
            </div>
        </article>

        {{-- Publicité uniquement en bas d'article, jamais près d'un CTA. --}}
        <div class="mt-10">
            @include('partials.ad')
        </div>

        @if ($autres->isNotEmpty())
            <section class="mt-12 border-t border-slate-200 pt-8">
                <h2 class="text-sm font-extrabold uppercase tracking-widest text-slate-500">À lire ensuite</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($autres as $autre)
                        <a href="{{ route('articles.show', $autre->slug) }}"
                           class="block rounded-xl border border-slate-200 bg-white p-4 transition hover:border-slate-300">
                            <p class="text-sm font-bold text-slate-900">{{ $autre->titre }}</p>
                            <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $autre->chapo }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
