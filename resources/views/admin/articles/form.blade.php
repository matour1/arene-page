@extends('layouts.admin')

@section('titre', $article->exists ? 'Éditer un article' : 'Nouvel article')

@section('contenu')
    <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-mut transition hover:text-cy">
        <x-icone nom="arrow-left" taille="sm" />
        Articles
    </a>

    <h1 class="mt-4 text-xl font-bold text-txt sm:text-2xl">
        {{ $article->exists ? 'Éditer un article' : 'Nouvel article' }}
    </h1>

    @if ($errors->any())
        <div class="alerte alerte-err mt-5">
            <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
            <ul class="list-inside list-disc space-y-0.5">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          enctype="multipart/form-data"
          class="carte mt-6 space-y-5">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <label class="block">
            <span class="etiquette">Titre <span class="text-err">*</span></span>
            <input type="text" name="titre" value="{{ old('titre', $article->titre) }}" required maxlength="150"
                   class="champ">
        </label>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="etiquette">Catégorie</span>
                <input type="text" name="categorie" value="{{ old('categorie', $article->categorie) }}" maxlength="50"
                       placeholder="Guide, Analyse, Actu…" class="champ">
            </label>

            <label class="block">
                <span class="etiquette">Auteur</span>
                <input type="text" name="auteur" value="{{ old('auteur', $article->auteur ?? 'Arène des Gamers') }}" maxlength="80"
                       class="champ">
            </label>
        </div>

        <label class="block">
            <span class="etiquette">Chapô</span>
            <textarea name="chapo" rows="2" maxlength="500" class="champ resize-y">{{ old('chapo', $article->chapo) }}</textarea>
        </label>

        <label class="block">
            <span class="etiquette">
                Contenu <span class="text-err">*</span>
                <span class="font-normal text-dim">— Markdown accepté</span>
            </span>
            <textarea name="contenu" rows="18" required class="champ resize-y font-mono !text-[0.82rem]">{{ old('contenu', $article->contenu) }}</textarea>
        </label>

        <label class="block">
            <span class="etiquette">Image</span>
            @if ($article->image)
                <img src="{{ $article->urlImage() }}" alt="" class="mb-2 h-28 rounded-lg border border-line object-cover">
            @endif
            <input type="file" name="image_fichier" accept="image/*"
                   class="w-full rounded-lg border border-line2 bg-base p-2 text-sm text-mut file:mr-3 file:rounded-md file:border-0 file:bg-elevated file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-txt">
        </label>

        <label class="flex cursor-pointer items-center gap-2.5 border-t border-line pt-5">
            <input type="checkbox" name="publier" value="1" @checked(old('publier', $article->estPublie()))
                   class="size-4 rounded border-line2 bg-base text-cy focus:ring-2 focus:ring-cy/30">
            <span class="text-sm font-semibold text-txt">Publier (visible sur le site)</span>
        </label>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">Annuler</a>
            <button type="submit" class="btn btn-primary btn-sm">
                <x-icone nom="check" taille="xs" />
                Enregistrer
            </button>
        </div>
    </form>
@endsection