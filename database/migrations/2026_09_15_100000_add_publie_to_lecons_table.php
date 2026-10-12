<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publication d'une leçon : seul le formateur/admin la voit tant qu'elle
     * n'est pas publiée. Défaut = true pour que les leçons déjà existantes
     * restent visibles des apprenants actuels après la mise à jour.
     */
    public function up(): void
    {
        if (Schema::hasColumn('lecons', 'publie')) {
            return;
        }

        Schema::table('lecons', function (Blueprint $table) {
            $table->boolean('publie')->default(true)->after('ordre');
        });
    }

    public function down(): void
    {
        Schema::table('lecons', function (Blueprint $table) {
            $table->dropColumn('publie');
        });
    }
};
