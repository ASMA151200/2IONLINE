<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('etudiants', function (Blueprint $table) {
            $table->boolean('alumni_visible')->default(false)->after('niveau');
            $table->string('poste_actuel')->nullable()->after('alumni_visible');
            $table->string('entreprise_actuelle')->nullable()->after('poste_actuel');
        });
    }
    public function down(): void {
        Schema::table('etudiants', function (Blueprint $table) {
            $table->dropColumn(['alumni_visible', 'poste_actuel', 'entreprise_actuelle']);
        });
    }
};
