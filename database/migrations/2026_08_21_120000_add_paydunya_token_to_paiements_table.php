<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la colonne nécessaire pour relier un paiement à son invoice
     * PayDunya. Le "token" est renvoyé par PayDunya à la création de
     * l'invoice (POST /checkout-invoice/create) et permet de retrouver le
     * bon Paiement quand l'IPN (webhook) de confirmation arrive.
     */
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('paydunya_token')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('paydunya_token');
        });
    }
};
