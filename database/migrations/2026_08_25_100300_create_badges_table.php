<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: 'premiere_lecon', 'examen_reussi'
            $table->string('titre');
            $table->string('description');
            $table->string('icone')->nullable(); // nom d'icône lucide-react
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('obtenu_le')->useCurrent();
            $table->unique(['user_id', 'badge_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
