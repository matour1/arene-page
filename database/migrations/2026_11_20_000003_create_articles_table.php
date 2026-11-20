<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('titre');
            $table->string('chapo', 500)->nullable();
            $table->longText('contenu');
            $table->string('image')->nullable();
            $table->string('auteur')->default('Arène des Gamers');
            $table->string('categorie')->nullable()->index();

            // Un article n'est visible publiquement qu'une fois publié.
            $table->timestamp('publie_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
