<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes formateurs de démonstration — mot de passe "password123" pour
 * tous, à changer en conditions réelles. Nécessaires pour peupler
 * forum/sondages/mentorat/messages avec du contenu réaliste.
 */
class FormateurSeeder extends Seeder
{
    public function run(): void
    {
        $formateurs = [
            ['prenom' => 'Aminata', 'nom' => 'Diallo', 'email' => 'aminata.diallo@2i-online.com', 'telephone' => '770001111', 'specialite' => 'Cuisine gastronomique'],
            ['prenom' => 'Moussa', 'nom' => 'Ndiaye', 'email' => 'moussa.ndiaye@2i-online.com', 'telephone' => '770002222', 'specialite' => 'Pâtisserie et boulangerie'],
            ['prenom' => 'Fatou', 'nom' => 'Sarr', 'email' => 'fatou.sarr@2i-online.com', 'telephone' => '770003333', 'specialite' => 'Service en salle et œnologie'],
        ];

        foreach ($formateurs as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'prenom' => $data['prenom'],
                    'nom' => $data['nom'],
                    'telephone' => $data['telephone'],
                    'password' => Hash::make('password123'),
                    'role' => UserRole::formateur->value,
                    'is_active' => true,
                ],
            );

            $user->formateur()->firstOrCreate([], ['specialite' => $data['specialite']]);
        }

        $this->command->info(count($formateurs) . ' formateurs de démonstration créés avec succès.');
    }
}
