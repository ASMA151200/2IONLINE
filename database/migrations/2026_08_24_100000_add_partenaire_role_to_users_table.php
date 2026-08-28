<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute 'partenaire' à l'enum MySQL de la colonne role — Laravel n'a
     * pas de méthode Schema native pour modifier les valeurs d'un enum
     * existant, un ALTER TABLE brut est nécessaire.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'formateur', 'etudiant', 'partenaire') DEFAULT 'etudiant'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'formateur', 'etudiant') DEFAULT 'etudiant'");
    }
};
