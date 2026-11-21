<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\Seo\Metadonnees;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private Metadonnees $seo) {}

    public function index(Request $request): View
    {
        $articles = Article::query()
            ->publies()
            ->paginate(9);

        $this->seo
            ->titre('Guides et articles e-sport')
            ->description('Guides pratiques et analyses sur les tournois e-sport mobiles en Afrique : inscriptions, Custom Room Free Fire, retrait des gains et progression.')
            ->canonical(route('articles.index'));

        if ($request->has('page')) {
            $this->seo->noindex();
        }

        return view('articles.index', compact('articles'));
    }

    public function show(string $slug): View
    {
        $article = Article::query()
            ->publies()
            ->where('slug', $slug)
            ->firstOrFail();

        $autres = Article::query()
            ->publies()
            ->whereKeyNot($article->id)
            ->limit(3)
            ->get();

        $this->seo
            ->titre($article->titre)
            ->description($article->chapo ?: str($article->contenu)->stripTags()->limit(155)->toString())
            ->canonical(route('articles.show', $article->slug))
            ->image($article->urlImage())
            ->type('article')
            ->schema($this->schemaArticle($article));

        return view('articles.show', compact('article', 'autres'));
    }

    /**
     * @return array<string, mixed>
     */
    private function schemaArticle(Article $article): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => str($article->titre)->limit(110)->toString(),
            'description' => $article->chapo,
            'datePublished' => $article->publie_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'inLanguage' => 'fr',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('articles.show', $article->slug),
            ],
            'author' => [
                '@type' => 'Organization',
                'name' => $article->auteur ?: config('arene.site.nom'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('arene.site.nom'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('/img/logo.svg'),
                ],
            ],
        ];

        if ($article->image) {
            $schema['image'] = $article->urlImage();
        }

        if ($article->categorie) {
            $schema['articleSection'] = $article->categorie;
        }

        return array_filter($schema, fn ($v) => $v !== null);
    }
}