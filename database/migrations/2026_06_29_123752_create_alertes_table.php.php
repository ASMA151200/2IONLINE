<?php
// database/migrations/2024_01_01_000011_create_alertes_table.php

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