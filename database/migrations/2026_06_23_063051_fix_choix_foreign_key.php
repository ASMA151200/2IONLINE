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
        Schema::table('choix', function (Blueprint $table) {
        // Supprimer l'ancienne clé étrangère
        $table->dropForeign(['question_id']);

        // Ajouter la nouvelle clé étrangère vers exercice_questions
        $table->foreign('question_id')
              ->references('id')
              ->on('exercice_questions')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('choix', function (Blueprint $table) {
        $table->dropForeign(['question_id']);
        $table->foreign('question_id')
              ->references('id')
              ->on('questions')
              ->onDelete('cascade');
        });
        
    }

};

