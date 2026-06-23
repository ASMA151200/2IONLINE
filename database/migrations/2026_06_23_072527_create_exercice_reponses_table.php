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
        Schema::create('exercice_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')->references('id')->on('exercice_questions')->onDelete('cascade');
            $table->unsignedBigInteger('choix_id')->nullable();
            $table->foreign('choix_id')->references('id')->on('choix')->onDelete('cascade');
            $table->text('reponse_texte')->nullable();
            $table->integer('score')->nullable();
            $table->enum('statut', ['en_attente', 'corrige'])->default('en_attente');
            $table->text('commentaire_formateur')->nullable();
            $table->unique(['exercice_id', 'user_id', 'question_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercice_reponses');
    }
};
