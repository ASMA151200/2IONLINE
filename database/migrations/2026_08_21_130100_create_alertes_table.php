<?php
// database/migrations/2026_08_21_130100_create_alertes_table.php
//
// ATTENTION ORDRE: initialement datée 2026_06_29 (avant la création de
// live_sessions le 2026_08_21), cette migration échouait avec "Foreign
// key constraint is incorrectly formed" — MySQL refusait la contrainte
// vers live_session_id car cette table n'existait pas encore au moment
// où alertes tentait de s'exécuter (l'ordre des migrations suit le nom
// du fichier). Renommée pour s'exécuter juste après
// 2026_08_21_130000_create_live_sessions_table.php.
//
// ATTENTION 2: il s'avère que la table "alertes" existe déjà
// physiquement en base (créée à un moment antérieur non enregistré
// dans la table de suivi des migrations — vu la version originale de
// ce fichier référençant "cours_id"/"cours", elle a probablement été
// créée avec CETTE ancienne structure, avant la correction vers
// "formation_id"). Migration rendue idempotente : ne recrée rien si la
// table existe déjà, et corrige la colonne si elle est encore sous
// l'ancien nom "cours_id".

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('alertes')) {
            Schema::create('alertes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('formation_id')->constrained()->onDelete('cascade');
                $table->foreignId('formateur_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('live_session_id')->nullable()->constrained()->onDelete('cascade');
                $table->enum('type', ['rappel_live', 'annulation', 'deadline', 'annonce']);
                $table->string('titre', 100);
                $table->string('message', 300);
                $table->timestamp('envoye_le')->nullable();
                $table->unsignedInteger('nb_push_envoyes')->default(0);
                $table->timestamps();

                $table->index(['formation_id', 'type']);
            });
            return;
        }

        // La table existe déjà — vérifie si elle a encore l'ancienne
        // colonne "cours_id" (structure d'avant la correction) et la
        // corrige le cas échéant, sans jamais recréer la table.
        if (Schema::hasColumn('alertes', 'cours_id') && !Schema::hasColumn('alertes', 'formation_id')) {
            $constraints = collect(DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'alertes' AND COLUMN_NAME = 'cours_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "));

            foreach ($constraints as $c) {
                DB::statement("ALTER TABLE alertes DROP FOREIGN KEY `{$c->CONSTRAINT_NAME}`");
            }

            DB::statement('ALTER TABLE alertes CHANGE cours_id formation_id BIGINT UNSIGNED NOT NULL');

            Schema::table('alertes', function (Blueprint $table) {
                $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};