<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormationResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array
    {
        return [

            'id'=>$this->id,

            'titre'=>$this->titre,

            'description'=>
            $this->description,

            'image'=>
            $this->image,

            'niveau'=>
            $this->niveau,

            'duree'=>
            $this->duree,

            'prix'=>
            $this->prix,

            'statut'=>
            $this->statut,

            'nb_inscrit'=>
            $this->nb_inscrit,

            'formateur'=>
            $this->formateur,

            'categorie'=>
            $this->categorie,

            'created_at'=>
            $this->created_at

        ];
    }
}