<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournois', function (Blueprint $table) {
            // Identifiant public fourni par l'API GameHub Arena (ex. T-12FBE0C3E04B2288).
            $table->string('id')->primary();

            $table->string('titre');
            $table->string('jeu')->index();
            $table->string('format')->nullable();
            $table->string('statut')->index();

            $table->boolean('disponible')->default(false);
            $table->string('raison_indisponibilite')->nullable();
            $table->string('visibilite')->default('public');

            $table->boolean('payant')->default(false);
            $table->unsignedInteger('frais_inscription')->default(0);
            $table->unsignedInteger('cagnotte_estimee')->default(0);
            $table->string('devise', 8)->default('XAF');

            $table->unsignedInteger('places_inscrits')->default(0);
            $table->unsignedInteger('places_maximum')->default(0);
            $table->unsignedInteger('places_restantes')->default(0);

            $table->timestamp('debut_at')->nullable()->index();
            $table->timestamp('fin_inscription_at')->nullable();

            $table->unsignedInteger('rang_min')->nullable();
            $table->unsignedInteger('rang_max')->nullable();

            $table->boolean('pro')->default(false);

            // Lien de parrainage personnel résolu depuis l'API d'affiliation.
            $table->string('lien_affiliation')->nullable();

            $table->timestamp('synchronise_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournois');
    }
};
