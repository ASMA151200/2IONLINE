<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sondages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->string('titre');
            $table->json('questions'); // [{id, texte, type: 'note'|'texte'}]
            $table->timestamps();
        });

        Schema::create('sondage_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sondage_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('reponses'); // [{question_id, valeur}]
            $table->timestamps();
            $table->unique(['sondage_id', 'user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('sondage_reponses');
        Schema::dropIfExists('sondages');
    }
};
