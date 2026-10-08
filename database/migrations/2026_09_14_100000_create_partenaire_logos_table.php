<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Logos "Ils nous font confiance" affichés sur la page d'accueil.
     * Distinct de la table "partenaires" (utilisateurs partenaires qui
     * financent des formations) : ici, simple vitrine géré par l'admin.
     */
    public function up(): void
    {
        if (Schema::hasTable('partenaire_logos')) {
            return;
        }

        Schema::create('partenaire_logos', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('domaine')->nullable();
            $table->string('logo');
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partenaire_logos');
    }
};
