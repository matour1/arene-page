<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleAdminController extends Controller
{
    private const DISQUE = 'uploads';

    public function index(): View
    {
        $articles = Article::query()->latest('created_at')->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('admin.articles.form', ['article' => new Article]);
    }

    public function store(Request $request)
    {
        $donnees = $this->valider($request);
        $donnees['slug'] = Article::slugUnique($donnees['titre']);
        $donnees['image'] = $this->image($request);
        $donnees['publie_at'] = $request->boolean('publier') ? now() : null;

        Article::create($donnees);

        return redirect()->route('admin.articles.index')->with('statut', 'Article cree.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.form', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $donnees = $this->valider($request);
        $donnees['image'] = $this->image($request, $article) ?? $article->image;

        if ($donnees['titre'] !== $article->titre) {
            $donnees['slug'] = Article::slugUnique($donnees['titre'], $article->id);
        }

        // Conserver la date de publication d'origine si l'article etait deja en ligne.
        $donnees['publie_at'] = $request->boolean('publier')
            ? ($article->publie_at ?? now())
            : null;

        $article->update($donnees);

        return redirect()->route('admin.articles.index')->with('statut', 'Article mis a jour.');
    }

    public function destroy(Article $article)
    {
        $this->supprimerImage($article->image);

        $article->delete();

        return redirect()->route('admin.articles.index')->with('statut', 'Article supprime.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request): array
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:150'],
            'chapo' => ['nullable', 'string', 'max:500'],
            'contenu' => ['required', 'string'],
            'categorie' => ['nullable', 'string', 'max:50'],
            'auteur' => ['nullable', 'string', 'max:80'],
            'image_fichier' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($donnees['image_fichier']);

        $donnees['auteur'] = $donnees['auteur'] ?? 'Arene des Gamers';

        return $donnees;
    }

    private function image(Request $request, ?Article $article = null): ?string
    {
        if (! $request->hasFile('image_fichier')) {
            return null;
        }

        $this->supprimerImage($article?->image);

        return $request->file('image_fichier')->store('articles', self::DISQUE);
    }

    private function supprimerImage(?string $chemin): void
    {
        if ($chemin && ! str_starts_with($chemin, 'http')) {
            Storage::disk(self::DISQUE)->delete($chemin);
        }
    }
}