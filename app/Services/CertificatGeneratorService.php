<?php

namespace App\Services;

use App\Models\Certificat;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Génère automatiquement un certificat PDF côté serveur, en alternative à
 * l'upload manuel (le seul chemin possible auparavant).
 *
 * NÉCESSITE la librairie barryvdh/laravel-dompdf, PAS ENCORE installée
 * dans ce projet — impossible pour moi d'exécuter Composer depuis mon
 * environnement. À faire manuellement avant que cette fonctionnalité
 * marche :
 *
 *   composer require barryvdh/laravel-dompdf
 *
 * Le reste (vue Blade, service, route, contrôleur) est prêt et
 * n'attend que cette dépendance pour fonctionner.
 */
class CertificatGeneratorService
{
    public function generate(User $user, Formation $formation): Certificat
    {
        $numero = 'CERT-' . now()->format('Y') . '-' . strtoupper(Str::random(8));

        $pdf = app('dompdf.wrapper')->loadView('certificats.template', [
            'user' => $user,
            'formation' => $formation,
            'numero' => $numero,
            'date' => now()->format('d/m/Y'),
        ])->setPaper('a4', 'landscape');

        $path = 'certificats/' . $numero . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return Certificat::create([
            'numero_certificat' => $numero,
            'code_verification' => (string) Str::uuid(),
            'fichier_pdf' => $path,
            'date_obtention' => now()->toDateString(),
            'user_id' => $user->id,
            'formation_id' => $formation->id,
        ]);
    }
}
