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

    //Creer une lecon
    public function create(array $data): Lecon
    {
        //Upload video (fichier prioritaire si les deux sont fournis)
        if (isset($data['video'])) {
            $data['video'] = $data['video']->store('lecons/videos', 'public');
        } elseif (!empty($data['video_url'])) {
            $data['video'] = $data['video_url'];
        }
        unset($data['video_url']);

        //Upload document
        if(isset($data['document']))
            {
                $data['document'] = $data['document']->store('lecons/documents', 'public');
            }

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
        if (isset($data['video'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
            $data['video'] = $data['video']->store('lecons/videos', 'public');
        } elseif (!empty($data['video_url'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('video'));
            $data['video'] = $data['video_url'];
        }
        unset($data['video_url']);

        //Remplacer un document
        if (isset($data['document'])) {
            $this->deleteIfLocalFile($lecon->getRawOriginal('document'));
            $data['document'] = $data['document']->store('lecons/documents', 'public');
        }

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
