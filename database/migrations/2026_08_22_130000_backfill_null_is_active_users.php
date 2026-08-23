<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige les comptes existants dont "is_active" vaut NULL en base
     * (créés avant l'ajout de cette colonne — le DEFAULT true ne s'est
     * apparemment pas appliqué correctement à toutes les lignes déjà
     * présentes, bloquant leur connexion avec "Ce compte a été désactivé").
     */
    public function up(): void
    {
        DB::table('users')->whereNull('is_active')->update(['is_active' => true]);
    }

    public function down(): void
    {
        // Rien à annuler — correction de données, pas de changement de schéma.
    }
};
