<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mentorats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade'); // alumni ou formateur
            $table->foreignId('mentore_id')->constrained('users')->onDelete('cascade'); // étudiant
            $table->enum('statut', ['en_attente', 'actif', 'termine', 'refuse'])->default('en_attente');
            $table->text('message_demande')->nullable();
            $table->timestamps();
            $table->unique(['mentor_id', 'mentore_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('mentorats'); }
};
