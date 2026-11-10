<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Associe la photo (formation immersive au Sultane, Hammamet) à la formation
     * « Du produit à la signature par le chef David ». L'image est servie par le
     * frontend (public/images) — même principe que les formations d'origine.
     * Sans effet si la formation n'existe pas ou possède déjà une image.
     */
    public function up(): void
    {
        DB::table('formations')
            ->where('titre', 'like', '%Du produit à la signature%')
            ->where(function ($q) {
                $q->whereNull('image')->orWhere('image', '');
            })
            ->update(['image' => '/images/formation-sultane-hammamet.jpg']);
    }

    public function down(): void
    {
        DB::table('formations')
            ->where('image', '/images/formation-sultane-hammamet.jpg')
            ->update(['image' => null]);
    }
};
