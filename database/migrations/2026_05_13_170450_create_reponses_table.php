<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('choix_id')->nullable()->constrained('choix')->onDelete('cascade'); // QCM
            $table->text('reponse_texte')->nullable(); // Question ouverte
            $table->integer('score')->nullable(); // Score auto (QCM) ou manuel (ouvert)
            $table->enum('statut', ['en_attente', 'corrige'])->default('en_attente');
            $table->text('commentaire_formateur')->nullable();
            // Un etudiant ne répond qu'une fois par question par exercice
            $table->unique(['exercice_id', 'user_id', 'question_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reponses');
    }
};
