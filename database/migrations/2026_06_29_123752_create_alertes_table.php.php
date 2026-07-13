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
            $table->foreignId('cours_id')->constrained()->onDelete('cascade');
            $table->foreignId('formateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('live_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['rappel_live', 'annulation', 'deadline', 'annonce']);
            $table->string('titre', 100);
            $table->string('message', 300);
            $table->timestamp('envoye_le')->nullable();
            $table->unsignedInteger('nb_push_envoyes')->default(0);
            $table->timestamps();

            $table->index(['cours_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};