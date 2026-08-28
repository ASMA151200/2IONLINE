<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // null = don anonyme
            $table->string('nom_donateur')->nullable(); // si don anonyme mais nom fourni
            $table->string('email_donateur')->nullable();
            $table->decimal('montant', 12, 2);
            $table->text('message')->nullable();
            $table->string('paydunya_token')->nullable()->unique();
            $table->enum('statut', ['en attente', 'confirme', 'echec'])->default('en attente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('dons'); }
};
