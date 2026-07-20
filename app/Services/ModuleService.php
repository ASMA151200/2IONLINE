<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Storage;

class ModuleService
{
    //Liste des modules
    public function getAll()
    {
        return Module::with('lecons')->latest()->get();
    }

    //Creer un module
    public function create(array $data): Module
    {
        return Module::create($data);
    }

    //Afficher un module
    public function getById(int $id): Module
    {
        return Module::with('lecons')->findOrFail($id);
    }

    //Modifier un module
    public function update(Module $module, array $data): Module
    {
        $module->update($data);
        return $module;
    }

    //Supprimer un module
    public function delete(Module $module): void
    {
        $module->deleteOrFail();
    }

}


?>
