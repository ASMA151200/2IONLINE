<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actuses', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('description')->nullable();
            $table->longText('contenu_html');
            $table->string('image')->nullable();
            $table->enum('type', ['actualite', 'evenement', 'communique', 'blog']);
            $table->dateTime('date_publication');
            $table->dateTime('date_expiration')->nullable();
            $table->enum('statut', ['brouillon', 'publie', 'archive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actuses');
    }
};
