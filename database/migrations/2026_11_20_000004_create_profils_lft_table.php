<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profils_lft', function (Blueprint $table) {
            $table->id();

            $table->string('pseudo')->index();
            $table->string('jeu')->index();
            $table->string('role')->nullable();
            $table->string('rang')->nullable();
            $table->string('plateforme')->nullable();

            $table->string('pays', 2)->nullable()->index();
            $table->string('langue', 5)->default('fr')->index();

            // Contact direct : pas de messagerie dans le MVP.
            $table->string('contact_discord')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->string('email')->nullable();

            $table->text('description')->nullable();

            $table->boolean('disponible')->default(true)->index();

            // Modération : un profil n'est public qu'après validation.
            $table->string('statut')->default('en_attente')->index();
            $table->timestamp('valide_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profils_lft');
    }
};
