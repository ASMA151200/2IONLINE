<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('image')->nullable();
            $table->foreignId('formateur_id')->constrained('users')->onDelete('cascade');
            $table->string('niveau'); // ou enum suivant ton choix
            $table->string('duree');
            $table->decimal('prix', 10, 2);
            $table->enum('statut', ['en ligne', 'presentiel', 'hybride']);
            $table->integer('nb_inscrit')->default(0);
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
