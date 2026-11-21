<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfilLftAdminController;
use App\Http\Controllers\Admin\TournoiAdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LftController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\TournoiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tournois', [TournoiController::class, 'index'])->name('tournois.index');
Route::get('/tournois/{tournoi}', [TournoiController::class, 'show'])->name('tournois.show');

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/lft', [LftController::class, 'index'])->name('lft.index');
Route::get('/lft/rejoindre', [LftController::class, 'create'])->name('lft.create');
Route::get('/lft/merci', [LftController::class, 'merci'])->name('lft.merci');
Route::post('/lft', [LftController::class, 'store'])->name('lft.store');

Route::get('/arene-bot', [PageController::class, 'areneBot'])->name('arene-bot');
Route::get('/a-propos', [PageController::class, 'aPropos'])->name('a-propos');

// SEO : sitemap genere a la demande (routes exclues du back-office).
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
 * Le back-office vit sous un chemin non devinable configure par
 * ARENE_ADMIN_PATH. Si la valeur est vide, on retombe sur /admin.
 */
$cheminAdmin = trim((string) config('arene.admin.path'), '/');

if ($cheminAdmin === '') {
    $cheminAdmin = 'admin';
}

Route::middleware('guest')->group(function () use ($cheminAdmin) {
    Route::get("/{$cheminAdmin}/connexion", [LoginController::class, 'create'])->name('login');
    Route::post("/{$cheminAdmin}/connexion", [LoginController::class, 'store'])->name('login.store');
});

Route::post('/deconnexion', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix($cheminAdmin)
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/tournois', [TournoiAdminController::class, 'index'])->name('tournois.index');
        Route::post('/tournois/sync', [TournoiAdminController::class, 'sync'])->name('tournois.sync');
        Route::get('/tournois/{tournoi}/modifier', [TournoiAdminController::class, 'edit'])->name('tournois.edit');
        Route::put('/tournois/{tournoi}', [TournoiAdminController::class, 'update'])->name('tournois.update');

        Route::resource('articles', ArticleAdminController::class)->except(['show']);

        Route::get('/lft', [ProfilLftAdminController::class, 'index'])->name('lft.index');
        Route::patch('/lft/{profil}/valider', [ProfilLftAdminController::class, 'valider'])->name('lft.valider');
        Route::patch('/lft/{profil}/rejeter', [ProfilLftAdminController::class, 'rejeter'])->name('lft.rejeter');
        Route::delete('/lft/{profil}', [ProfilLftAdminController::class, 'destroy'])->name('lft.destroy');
    });