<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La table "questions" (migration 2026_05_13_170313_create_questions_table)
     * n'est utilisée par AUCUN modèle, contrôleur ou route de l'application :
     * - Le modèle Question pointe explicitement vers "exercice_questions"
     *   (protected $table = 'exercice_questions' dans app/Models/Question.php)
     * - La migration 2026_06_23_063051_fix_choix_foreign_key a explicitement
     *   déplacé la clé étrangère choix.question_id de "questions" vers
     *   "exercice_questions", confirmant que exercice_questions est la
     *   table réellement active
     * - Aucune autre référence à "questions" (en tant que table, pas comme
     *   nom de route/URL) n'existe dans le code
     *
     * Cette table est donc un doublon mort, jamais peuplé, qui ne fait que
     * créer de la confusion (deux tables quasi identiques). Supprimée pour
     * clarifier le schéma.
     *
     * IMPORTANT: la table "reponses" (migration
     * 2026_05_13_170450_create_reponses_table), également orpheline —
     * le modèle Reponse pointe vers "exercice_reponses" depuis la
     * migration 2026_06_23_072527_create_exercice_reponses_table — a une
     * contrainte de clé étrangère active sur "questions"
     * (reponses.question_id), jamais corrigée contrairement à celle de
     * choix. Impossible de DROP "questions" tant que cette contrainte
     * existe encore (MySQL refuse de supprimer une table référencée) :
     * "reponses" est donc supprimée en premier, ci-dessous.
     */
    public function up(): void
    {
        Schema::dropIfExists('reponses');
        Schema::dropIfExists('questions');
    }

    public function down(): void
    {
        Schema::create('questions', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('exercice_id')->constrained()->onDelete('cascade');
            $table->text('contenu');
            $table->enum('type', ['qcm', 'ouvert'])->default('qcm');
            $table->integer('points')->default(1);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('reponses', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('exercice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('choix_id')->nullable()->constrained('choix')->onDelete('cascade');
            $table->text('reponse_texte')->nullable();
            $table->integer('score')->nullable();
            $table->enum('statut', ['en_attente', 'corrige'])->default('en_attente');
            $table->text('commentaire_formateur')->nullable();
            $table->unique(['exercice_id', 'user_id', 'question_id']);
            $table->timestamps();
        });
    }
};
