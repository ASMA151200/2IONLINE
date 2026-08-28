<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table pivot représentant le lien "financé par" entre une formation
     * et un partenaire — un partenaire peut financer plusieurs formations,
     * une formation peut avoir plusieurs partenaires financeurs.
     */
    public function up(): void
    {
        Schema::create('formation_partenaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->foreignId('partenaire_id')->constrained('partenaires')->onDelete('cascade');
            $table->decimal('montant_finance', 12, 2)->default(0);
            $table->date('date_financement');
            $table->timestamps();

            $table->unique(['formation_id', 'partenaire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_partenaire');
    }
};
