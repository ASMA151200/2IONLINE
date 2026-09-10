<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Deux problèmes réels trouvés en vérifiant le flux "modifier la
     * formation enseignée par un formateur" (réassignation) :
     *
     * 1. formations.user_id n'était PAS nullable
     *    (FormateurService::update() met pourtant explicitement
     *    user_id à null pour "libérer" la formation qu'un formateur
     *    possédait avant de lui en assigner une nouvelle — cette
     *    requête échouait donc avec une erreur SQL "column cannot be
     *    null" dès qu'un admin réassignait un formateur qui possédait
     *    déjà une formation, ou tentait de retirer son assignation).
     *
     * 2. onDelete('cascade') sur cette clé étrangère : supprimer un
     *    compte formateur supprimait en cascade TOUTE sa formation —
     *    modules, leçons, inscriptions des apprenants, tout. Remplacé
     *    par onDelete('set null') : supprimer un formateur libère
     *    simplement sa formation (qui redevient assignable à un autre
     *    professeur), sans jamais détruire le contenu pédagogique ni
     *    l'historique des apprenants inscrits.
     */
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // ->change() nécessite le package doctrine/dbal, non installé
        // sur ce projet — SQL brut pour éviter d'ajouter une dépendance
        // Composer supplémentaire (et l'étape manuelle "composer
        // install" que ça impliquerait sur le serveur).
        DB::statement('ALTER TABLE formations MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('formations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE formations MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('formations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
