<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('present')->default(true);
            $table->timestamp('marked_at')->useCurrent();
            $table->timestamps();
            $table->unique(['live_session_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('presences'); }
};
