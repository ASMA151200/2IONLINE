<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table pour les cours en direct (YouTube Live). N'existait pas du
     * tout avant, malgré la contrainte de clé étrangère "live_session_id"
     * déjà présente (mais orpheline) sur la table alertes.
     */
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('youtube_video_id');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['formation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
