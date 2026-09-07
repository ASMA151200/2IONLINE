<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * VRAIE cause enfin trouvée via les logs Laravel (storage/logs/
     * laravel.log, accédé via la nouvelle commande "php artisan
     * logs:tail" faute d'accès shell classique) :
     *
     *   SQLSTATE[HY000]: General error: 1364 Field 'date_inscription'
     *   doesn't have a default value
     *
     * La vraie table "inscriptions" sur ce serveur a une colonne
     * "date_inscription" (PAS juste "date" comme je le pensais lors du
     * précédent correctif) — obligatoire, sans valeur par défaut. Ni le
     * modèle Inscription, ni EtudiantService, ni le webhook PayDunya, ne
     * remplissent jamais cette colonne (ils ne connaissent que "date"),
     * donc CHAQUE insertion échouait avec cette erreur — masquée en
     * production par le message générique "Une erreur est survenue sur
     * le serveur" (voir bootstrap/app.php).
     *
     * Corrigé au niveau base plutôt que dans le code : rend la colonne
     * nullable si elle existe encore et est actuellement NOT NULL sans
     * défaut, sans jamais la supprimer (au cas où d'anciennes données ou
     * requêtes en dépendraient encore ailleurs).
     */
    public function up(): void
    {
        if (!Schema::hasTable('inscriptions') || !Schema::hasColumn('inscriptions', 'date_inscription')) {
            return;
        }

        $column = collect(DB::select("
            SELECT IS_NULLABLE FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'inscriptions' AND COLUMN_NAME = 'date_inscription'
        "))->first();

        if ($column && $column->IS_NULLABLE === 'NO') {
            DB::statement('ALTER TABLE inscriptions MODIFY date_inscription DATE NULL');

            // Comble les lignes déjà existantes avec la date de création,
            // par cohérence avec le correctif équivalent déjà appliqué
            // sur la colonne "date".
            DB::table('inscriptions')->whereNull('date_inscription')->update(['date_inscription' => DB::raw('DATE(created_at)')]);
        }
    }

    public function down(): void
    {
        // Pas de rollback automatique — remettre NOT NULL sans défaut
        // recréerait exactement le bug corrigé ici.
    }
};
