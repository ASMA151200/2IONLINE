<?php

namespace App\Services;

use App\Models\Categorie;

class CategorieService
{
    // Liste des catégories
    public function getAll()
    {
        return Categorie::with('formations')->latest()->get();
    }

    // Créer une catégorie
    public function create(array $data): Categorie
    {
        return Categorie::create($data);
    }

    // Afficher une catégorie
    public function getById(int $id): Categorie
    {
        return Categorie::with('formations')->findOrFail($id);
    }

    // Modifier une catégorie
    public function update(Categorie $categorie, array $data): Categorie
    {
        $categorie->update($data);

        return $categorie;
    }

    // Supprimer une catégorie
    public function delete(Categorie $categorie): void
    {
        $categorie->deleteOrFail();
    }
}
