<?php

namespace App\Providers;

use App\Services\Seo\Metadonnees;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Une seule instance par requete : le controleur et la vue partagent
        // les memes metadonnees.
        $this->app->scoped(Metadonnees::class);
    }

    public function boot(): void
    {
        // Le partial SEO recoit toujours un objet Metadonnees exploitable,
        // meme si un controleur n'en a pas fourni.
        View::composer('partials.seo', function ($vue) {
            if (! isset($vue->seo)) {
                $vue->with('seo', app(Metadonnees::class));
            }
        });
    }
}