<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permet à une question (table exercice_questions, réellement utilisée
     * par le modèle Question) d'appartenir soit à un exercice, soit à un
     * examen — jusqu'ici seul exercice_id existait (NOT NULL), rendant
     * impossible d'attacher des questions à un examen malgré la relation
     * Examen::questions() qui tentait déjà de le faire (et plantait, car
     * la colonne examen_id n'existait pas du tout).
     *
     * Contrainte applicative (non technique en base, vérifiée côté
     * validation) : exactement un des deux champs (exercice_id OU
     * examen_id) doit être renseigné, jamais les deux, jamais aucun.
     */
    public function up(): void
    {
        Schema::table('exercice_questions', function (Blueprint $table) {
            $table->foreignId('exercice_id')->nullable()->change();
            $table->foreignId('examen_id')->nullable()->after('exercice_id')
                ->constrained('examens')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('exercice_questions', function (Blueprint $table) {
            $table->dropForeign(['examen_id']);
            $table->dropColumn('examen_id');
            $table->foreignId('exercice_id')->nullable(false)->change();
        });
    }
};
