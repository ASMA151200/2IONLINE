<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::table('certificats', function (Blueprint $table) {
            $table->uuid('code_verification')->nullable()->unique()->after('numero_certificat');
        });

        // Backfill des certificats déjà existants
        \DB::table('certificats')->whereNull('code_verification')->cursor()->each(function ($cert) {
            \DB::table('certificats')->where('id', $cert->id)->update(['code_verification' => Str::uuid()]);
        });
    }
    public function down(): void {
        Schema::table('certificats', function (Blueprint $table) { $table->dropColumn('code_verification'); });
    }
};
