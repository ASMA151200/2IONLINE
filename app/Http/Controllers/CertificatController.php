<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Formation;
use App\Models\User;
use App\Http\Requests\StoreCertificatRequest;
use App\Http\Requests\UpdateCertificatRequest;
use App\Services\CertificatGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CertificatController extends Controller
{
    public function __construct(protected CertificatGeneratorService $generator)
    {
    }

    /**
     * Génère automatiquement un certificat PDF côté serveur (admin/
     * formateur uniquement) — alternative à l'upload manuel d'un PDF déjà
     * préparé. NÉCESSITE `composer require barryvdh/laravel-dompdf` (voir
     * app/Services/CertificatGeneratorService.php).
     */
    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'formation_id' => 'required|exists:formations,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $formation = Formation::findOrFail($data['formation_id']);

        $certificat = $this->generator->generate($user, $formation);

        return response()->json([
            'success' => true,
            'message' => 'Certificat généré avec succès',
            'data' => $certificat->load(['user', 'formation']),
        ], 201);
    }
    /**
     * Liste des certificats
     */
    public function index(): JsonResponse
    {
        $certificats = Certificat::with(['user', 'formation'])
            ->latest()
            ->get();

        return response()->json($certificats);
    }

    /**
     * Ajouter un certificat
     */
    public function store(StoreCertificatRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('fichier_pdf')) {
            $data['fichier_pdf'] = $request
                ->file('fichier_pdf')
                ->store('certificats', 'public');
        }

        $certificat = Certificat::create($data);

        return response()->json([
            'message' => 'Certificat créé avec succès',
            'data' => $certificat->load(['user', 'formation'])
        ], 201);
    }

    /**
     * Afficher un certificat
     */
    public function show(Certificat $certificat): JsonResponse
    {
        return response()->json(
            $certificat->load(['user', 'formation'])
        );
    }

    /**
     * Modifier un certificat
     */
    public function update(
        UpdateCertificatRequest $request,
        Certificat $certificat
    ): JsonResponse {

        $data = $request->validated();

        if ($request->hasFile('fichier_pdf')) {

            if (
                $certificat->fichier_pdf &&
                Storage::disk('public')->exists($certificat->fichier_pdf)
            ) {
                Storage::disk('public')
                    ->delete($certificat->fichier_pdf);
            }

            $data['fichier_pdf'] = $request
                ->file('fichier_pdf')
                ->store('certificats', 'public');
        }

        $certificat->update($data);

        return response()->json([
            'message' => 'Certificat modifié avec succès',
            'data' => $certificat->fresh()
        ]);
    }

    /**
     * Supprimer un certificat
     */
    public function destroy(Certificat $certificat): JsonResponse
    {
        if (
            $certificat->fichier_pdf &&
            Storage::disk('public')->exists($certificat->fichier_pdf)
        ) {
            Storage::disk('public')
                ->delete($certificat->fichier_pdf);
        }

        $certificat->delete();

        return response()->json([
            'message' => 'Certificat supprimé avec succès'
        ]);
    }
   public function download(Certificat $certificat): BinaryFileResponse
{
    if ($certificat->user_id !== auth()->id()) {
        abort(403, 'Accès interdit');
    }

    if (!$certificat->fichier_pdf || !Storage::disk('public')->exists($certificat->fichier_pdf)) {
        abort(404, 'Fichier introuvable');
    }

    return Storage::disk('public')->download(
        $certificat->fichier_pdf,
        'certificat-' . $certificat->numero_certificat . '.pdf'
    );
}
}