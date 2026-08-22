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
     */
    public function up(): void
    {
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
    }
};
