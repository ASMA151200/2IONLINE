<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunite_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('statut', ['envoyee', 'vue', 'acceptee', 'refusee'])->default('envoyee');
            $table->timestamps();
            $table->unique(['opportunite_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('candidatures'); }
};
