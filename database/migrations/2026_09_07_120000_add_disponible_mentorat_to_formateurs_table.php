<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formateurs', function (Blueprint $table) {
            // Un formateur doit explicitement se marquer disponible pour
            // être proposé comme mentor — false par défaut, opt-in
            // volontaire plutôt que présomption de disponibilité.
            $table->boolean('disponible_mentorat')->default(false)->after('specialite');
        });
    }

    public function down(): void
    {
        Schema::table('formateurs', function (Blueprint $table) {
            $table->dropColumn('disponible_mentorat');
        });
    }
};
