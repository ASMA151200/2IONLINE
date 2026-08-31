<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important :
        // 1. UserSeeder — compte admin de test (propriétaire par défaut
        //    des formations créées par FormationsSeeder)
        // 2. FormationsSeeder — les 11 formations du catalogue
        // 3. FormateurSeeder / EtudiantSeeder — comptes de démonstration
        //    (mot de passe "password123"), avec inscriptions RÉELLEMENT
        //    actives pour les étudiants (pas juste le lien informatif)
        // 4. Forum/Sondage/Mentorat/Message — contenu réaliste qui
        //    s'appuie sur les comptes créés à l'étape 3
        //
        // ModuleSeeder, LeconSeeder, ExercicesSeeder, ExamenSeeder,
        // CertificatSeeder, InscriptionSeeder, PaiementSeeder,
        // ProgressionSeeder, QuestionSeeder, ReponseSeeder, ResultatSeeder
        // restent des coquilles vides — à compléter plus tard si besoin.
        $this->call([
            UserSeeder::class,
            FormationsSeeder::class,
            FormateurSeeder::class,
            EtudiantSeeder::class,
            ActusSeeder::class,
            OpportuniteSeeder::class,
            ForumSeeder::class,
            SondageSeeder::class,
            MentoratSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
