<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Évaluations : "description" est désormais présentée comme la SITUATION
     * (le champ en base garde son nom pour ne rien casser), et on ajoute la
     * CONSIGNE (ce que l'apprenant doit faire).
     */
    public function up(): void
    {
        if (Schema::hasColumn('exercices', 'consigne')) {
            return;
        }

        Schema::table('exercices', function (Blueprint $table) {
            $table->text('consigne')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('exercices', function (Blueprint $table) {
            $table->dropColumn('consigne');
        });
    }
};
