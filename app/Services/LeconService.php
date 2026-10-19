<?php

namespace App\Services;

use App\Models\Lecon;
use Illuminate\Support\Facades\Storage;

class LeconService
{
    //Liste des lecons
    public function getAll()
    {
        return Lecon::with('module')->latest()->get();
    }

    /**
     * Valide puis déplace un fichier déposé via UploadController (envoi
     * direct) vers son emplacement définitif. Le chemin doit se trouver dans
     * le dossier temporaire de L'UTILISATEUR CONNECTÉ : impossible de
     * référencer le fichier d'un autre ou de sortir du dossier (.. refusé).
     */
    private function promoteTemp(string $ref, string $targetDir): string
    {
        $userId = auth()->id();
        $valid = $userId
            && preg_match('#^uploads/tmp/' . preg_quote((string) $userId, '#') . '/[A-Za-z0-9._-]+$#', $ref)
            && !str_contains($ref, '..')
            && Storage::disk('public')->exists($ref);

        if (!$valid) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fichier' => 'Le fichier envoyé est introuvable ou a expiré. Veuillez le renvoyer.',
            ]);
        }

        $final = $targetDir . '/' . basename($ref);
        Storage::disk('public')->move($ref, $final);

        return $final;
    }

    //Creer une lecon
    public function create(array $data): Lecon
    {
        //Upload video (fichier prioritaire si les deux sont fournis)
        if (!empty($data['video_ref'])) {
            $data['video'] = $this->promoteTemp($data['video_ref'], 'lecons/videos');
        } elseif (isset($data['video'])) {
            $data['video'] = $data['video']->store('lecons/videos', 'public');
        } elseif (!empty($data['video_url'])) {
            $data['video'] = $data['video_url'];
        }
        unset($data['video_url'], $data['video_ref']);

        //Upload document
        if (!empty($data['document_ref'])) {
            $data['document'] = $this->promoteTemp($data['document_ref'], 'lecons/documents');
        } elseif (isset($data['document'])) {
            $data['document'] = $data['document']->store('lecons/documents', 'public');
        }
        unset($data['document_ref']);

        return Lecon::create($data);

    }

    //Afficher une lecon
    public function getById(int $id): Lecon
    {
        return Lecon::with('module')->findOrFail($id);
    }

    // Supprime le fichier physique d'un ancien chemin stocké — UNIQUEMENT
    // si ce n'est pas un lien externe (URL). Doit toujours recevoir la
    // valeur BRUTE (getRawOriginal), jamais celle passée par l'accesseur
    // Lecon::getVideoAttribute()/getDocumentAttribute() (qui renvoie
    // toujours une URL complète, y compris pour un fichier local) —
    // sinon cette vérification serait toujours vraie et ne supprimerait
    // plus jamais rien.
    private function deleteIfLocalFile(?string $rawValue): void
    {
        if ($rawValue && !str_starts_with($rawValue, 'http://') && !str_starts_with($rawValue, 'https://')) {
            Storage::disk('public')->delete($rawValue);
        }
    }

    //Modifier une lecon
    public function update(Lecon $lecon, array $data): Lecon
    {
        //Remplacer une video
        if (!empty($data['video_ref'])) {
            $newVideo = $this->promoteTemp($data['video_ref'], 'lecons/videos');
            $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
            $data['video'] = $newVideo;
        } elseif (isset($data['video'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
            $data['video'] = $data['video']->store('lecons/videos', 'public');
        } elseif (!empty($data['video_url'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
            $data['video'] = $data['video_url'];
        }
        unset($data['video_url'], $data['video_ref']);

        //Remplacer un document
        if (!empty($data['document_ref'])) {
            $newDocument = $this->promoteTemp($data['document_ref'], 'lecons/documents');
            $this->deleteIfLocalFile($lecon->getRawOriginal('document'));
            $data['document'] = $newDocument;
        } elseif (isset($data['document'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('document'));
            $data['document'] = $data['document']->store('lecons/documents', 'public');
        }
        unset($data['document_ref']);

        $lecon->update($data);

        return $lecon;

    }

    //Supprimer une lecon
    public function delete(Lecon $lecon): void
    {
        $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
        $this->deleteIfLocalFile($lecon->getRawOriginal('document'));

        $lecon->deleteOrFail();
    }

}
