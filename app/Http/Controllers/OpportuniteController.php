<?php

namespace App\Http\Controllers;

use App\Models\Opportunite;
use App\Http\Requests\StoreOpportuniteRequest;
use App\Http\Requests\UpdateOpportuniteRequest;
use Illuminate\Support\Facades\Storage;

class OpportuniteController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Opportunite::latest()->get()
        ]);
    }

    public function store(StoreOpportuniteRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('documents')) {
            $data['documents'] = $request->file('documents')
                ->store('opportunites/docs', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('opportunites/images', 'public');
        }

        $opp = Opportunite::create($data);

        return response()->json([
            'message' => 'Opportunité créée',
            'data' => $opp
        ], 201);
    }

    public function show(Opportunite $opportunite)
    {
        return response()->json([
            'data' => $opportunite
        ]);
    }

    public function update(UpdateOpportuniteRequest $request, Opportunite $opportunite)
    {
        $data = $request->validated();

        // getRawOriginal() : ->documents/->image passent maintenant par
        // un accesseur qui renvoie toujours une URL complète — Storage::
        // delete() a besoin du chemin RELATIF brut, sinon la suppression
        // échoue silencieusement.
        if ($request->hasFile('documents')) {
            if ($opportunite->getRawOriginal('documents')) {
                Storage::disk('public')->delete($opportunite->getRawOriginal('documents'));
            }

            $data['documents'] = $request->file('documents')
                ->store('opportunites/docs', 'public');
        }

        if ($request->hasFile('image')) {
            if ($opportunite->getRawOriginal('image')) {
                Storage::disk('public')->delete($opportunite->getRawOriginal('image'));
            }

            $data['image'] = $request->file('image')
                ->store('opportunites/images', 'public');
        }

        $opportunite->update($data);

        return response()->json([
            'message' => 'Opportunité mise à jour',
            'data' => $opportunite
        ]);
    }

    public function destroy(Opportunite $opportunite)
    {
        if ($opportunite->getRawOriginal('documents')) {
            Storage::disk('public')->delete($opportunite->getRawOriginal('documents'));
        }

        if ($opportunite->getRawOriginal('image')) {
            Storage::disk('public')->delete($opportunite->getRawOriginal('image'));
        }

        $opportunite->delete();

        return response()->json([
            'message' => 'Opportunité supprimée'
        ]);
    }
}