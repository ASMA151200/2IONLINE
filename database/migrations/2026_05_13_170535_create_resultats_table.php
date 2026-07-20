<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resultats', function (Blueprint $table) {
            $table->id();
            $table->decimal('score', 5, 2);
            $table->date('date_passage');
            $table->enum('statut', ['reussi', 'echoue', 'en cours']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('examen_id')->constrained('examens')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultats');
    }
};
