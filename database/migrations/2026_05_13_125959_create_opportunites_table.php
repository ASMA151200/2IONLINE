<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunites', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type', ['stage', 'emploi', 'formation', 'bourse', 'partenariat']);
            $table->text('description');
            $table->string('documents')->nullable(); // URL PDF
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('ville');
            $table->string('pays');
            $table->string('entreprise')->nullable();
            $table->string('lien_inscription')->nullable();
            $table->enum('statut', ['ouvert', 'ferme', 'en cours']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunites');
    }
};
