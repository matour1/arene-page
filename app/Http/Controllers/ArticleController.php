<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->publies()
            ->paginate(9);

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

        return view('articles.show', compact('article', 'autres'));
    }
}
