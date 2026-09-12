<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jusqu'ici, une question ouverte dans un EXAMEN (certification)
     * n'était jamais enregistrée nulle part : ExamenService::soumettre()
     * calculait un score partiel (uniquement les QCM) et jetait le texte
     * réellement écrit par l'étudiant pour toute question ouverte —
     * aucune trace, donc aucune possibilité de correction manuelle
     * ultérieure par le formateur. Contrairement aux exercices, où
     * chaque réponse est déjà stockée individuellement dans
     * exercice_reponses.
     *
     * Plutôt que de dupliquer toute cette table pour les examens, on
     * l'étend — exactement le même principe déjà utilisé par
     * exercice_questions (colonne exercice_id ET examen_id, l'une des
     * deux étant toujours nulle selon le parent réel de la question).
     */
    public function up(): void
    {
        Schema::table('exercice_reponses', function (Blueprint $table) {
            if (!Schema::hasColumn('exercice_reponses', 'examen_id')) {
                $table->foreignId('examen_id')->nullable()->after('exercice_id')->constrained('examens')->onDelete('cascade');
            }
        });

        // exercice_id doit devenir nullable : une réponse à une question
        // d'EXAMEN n'a pas d'exercice_id (SQL brut, doctrine/dbal non
        // installé sur ce projet — voir la migration équivalente sur
        // formations.user_id plus tôt cette session pour le même choix).
        DB::statement('ALTER TABLE exercice_reponses MODIFY exercice_id BIGINT UNSIGNED NULL');

        // L'ancienne contrainte unique (exercice_id, user_id, question_id)
        // ne protège pas correctement les réponses d'examen (exercice_id
        // NULL n'est jamais considéré égal à lui-même par MySQL, donc
        // aucune déduplication réelle pour ces lignes-là) — remplacée
        // par une contrainte couvrant aussi examen_id.
        if ($this->indexExists('exercice_reponses', 'exercice_reponses_exercice_id_user_id_question_id_unique')) {
            DB::statement('ALTER TABLE exercice_reponses DROP INDEX exercice_reponses_exercice_id_user_id_question_id_unique');
        }
        if (!$this->indexExists('exercice_reponses', 'exercice_reponses_exercice_examen_user_question_unique')) {
            DB::statement('ALTER TABLE exercice_reponses ADD UNIQUE exercice_reponses_exercice_examen_user_question_unique (exercice_id, examen_id, user_id, question_id)');
        }
    }

    public function down(): void
    {
        if ($this->indexExists('exercice_reponses', 'exercice_reponses_exercice_examen_user_question_unique')) {
            DB::statement('ALTER TABLE exercice_reponses DROP INDEX exercice_reponses_exercice_examen_user_question_unique');
        }

        Schema::table('exercice_reponses', function (Blueprint $table) {
            $table->dropForeign(['examen_id']);
            $table->dropColumn('examen_id');
        });

        DB::statement('ALTER TABLE exercice_reponses MODIFY exercice_id BIGINT UNSIGNED NOT NULL');
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]))->isNotEmpty();
    }
};
