@extends('layouts.admin')

@section('titre', $article->exists ? 'Éditer un article' : 'Nouvel article')

@section('contenu')
    <a href="{{ route('admin.articles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">
        ← Articles
    </a>

    <h1 class="mt-4 text-2xl font-extrabold tracking-tight">
        {{ $article->exists ? 'Éditer un article' : 'Nouvel article' }}
    </h1>

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          enctype="multipart/form-data"
          class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <label class="block">
            <span class="etiquette">Titre *</span>
            <input type="text" name="titre" value="{{ old('titre', $article->titre) }}" required maxlength="150"
                   class="champ">
        </label>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="etiquette">Catégorie</span>
                <input type="text" name="categorie" value="{{ old('categorie', $article->categorie) }}" maxlength="50"
                       placeholder="Guide, Analyse, Actu…"
                       class="champ">
            </label>

            <label class="block">
                <span class="etiquette">Auteur</span>
                <input type="text" name="auteur" value="{{ old('auteur', $article->auteur ?? 'Arène des Gamers') }}" maxlength="80"
                       class="champ">
            </label>
        </div>

        <label class="block">
            <span class="etiquette">Chapô</span>
            <textarea name="chapo" rows="2" maxlength="500"
                      class="champ">{{ old('chapo', $article->chapo) }}</textarea>
        </label>

        <label class="block">
            <span class="etiquette">Contenu * <span class="font-normal text-slate-400">(Markdown accepté)</span></span>
            <textarea name="contenu" rows="18" required
                      class="champ font-mono">{{ old('contenu', $article->contenu) }}</textarea>
        </label>

        <label class="block">
            <span class="etiquette">Image</span>
            @if ($article->image)
                <img src="{{ $article->urlImage() }}" alt="" class="mb-2 h-28 rounded-lg object-cover">
            @endif
            <input type="file" name="image_fichier" accept="image/*"
                   class="champ">
        </label>

        <label class="flex items-center gap-2 border-t border-slate-100 pt-5">
            <input type="checkbox" name="publier" value="1" @checked(old('publier', $article->estPublie()))
                   class="rounded border-slate-300 text-arn focus:ring-arn/20">
            <span class="text-sm font-semibold text-slate-700">Publier (visible sur le site)</span>
        </label>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.articles.index') }}"
               class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Annuler
            </a>
            <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                Enregistrer
            </button>
        </div>
    </form>
@endsection
