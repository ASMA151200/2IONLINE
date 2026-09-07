<?php

namespace App\Http\Controllers;

use App\Models\Actus;
use App\Http\Requests\StoreActusRequest;
use App\Http\Requests\UpdateActusRequest;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Facades\Storage;

class ActusController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Actus::latest()->get()
        ]);
    }

    public function store(StoreActusRequest $request)
    {
        $data = $request->validated();

        if (isset($data['contenu_html'])) {
            $data['contenu_html'] = HtmlSanitizer::clean($data['contenu_html']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('actus/images', 'public');
        }

        $actus = Actus::create($data);

        return response()->json([
            'message' => 'Actu créée',
            'data' => $actus
        ], 201);
    }

    public function show(Actus $actus)
    {
        return response()->json([
            'data' => $actus
        ]);
    }

    public function update(UpdateActusRequest $request, Actus $actus)
    {
        $data = $request->validated();

        if (isset($data['contenu_html'])) {
            $data['contenu_html'] = HtmlSanitizer::clean($data['contenu_html']);
        }

        if ($request->hasFile('image')) {
            // getRawOriginal() : $actus->image passe maintenant par
            // l'accesseur qui renvoie toujours une URL complète — Storage::
            // delete() a besoin du chemin RELATIF brut, jamais de l'URL,
            // sinon la suppression échoue silencieusement (aucun fichier
            // n'est réellement supprimé, juste jamais d'erreur visible).
            if ($actus->getRawOriginal('image')) {
                Storage::disk('public')->delete($actus->getRawOriginal('image'));
            }

            $data['image'] = $request->file('image')
                ->store('actus/images', 'public');
        }

        $actus->update($data);

        return response()->json([
            'message' => 'Actu mise à jour',
            'data' => $actus
        ]);
    }

    public function destroy(Actus $actus)
    {
        if ($actus->getRawOriginal('image')) {
            Storage::disk('public')->delete($actus->getRawOriginal('image'));
        }

        $actus->delete();

        return response()->json([
            'message' => 'Actu supprimée'
        ]);
    }
}