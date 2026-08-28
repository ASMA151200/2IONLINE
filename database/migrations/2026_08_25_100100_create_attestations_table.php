<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->string('numero_attestation')->unique();
            $table->string('fichier_pdf');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('live_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('date_delivrance');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('attestations'); }
};
