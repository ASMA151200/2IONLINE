<?php
// database/migrations/2026_08_21_130100_create_alertes_table.php
//
// ATTENTION ORDRE: initialement datée 2026_06_29 (avant la création de
// live_sessions le 2026_08_21), cette migration échouait avec "Foreign
// key constraint is incorrectly formed" — MySQL refusait la contrainte
// vers live_session_id car cette table n'existait pas encore au moment
// où alertes tentait de s'exécuter (l'ordre des migrations suit le nom
// du fichier). Renommée pour s'exécuter juste après
// 2026_08_21_130000_create_live_sessions_table.php — sans risque, cette
// migration n'avait jamais réussi à s'exécuter jusqu'ici, aucune ligne
// ne l'enregistrait dans la table de suivi des migrations.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            // CORRIGÉ : la colonne d'origine s'appelait "cours_id" avec une
            // contrainte vers une table "cours" qui n'a jamais existé dans
            // ce schéma (le vrai concept ici est "formations") — cette
            // migration n'a donc jamais pu s'exécuter avec succès
            // (CREATE TABLE échoue si la contrainte cible une table
            // inexistante). Corrigée directement ici plutôt que via une
            // migration corrective, puisque cette table n'a jamais été
            // réellement créée en base.
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
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};