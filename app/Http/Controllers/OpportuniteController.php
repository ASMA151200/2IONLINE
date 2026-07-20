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

        if ($request->hasFile('documents')) {
            if ($opportunite->documents) {
                Storage::disk('public')->delete($opportunite->documents);
            }

            $data['documents'] = $request->file('documents')
                ->store('opportunites/docs', 'public');
        }

        $opportunite->update($data);

        return response()->json([
            'message' => 'Opportunité mise à jour',
            'data' => $opportunite
        ]);
    }

    public function destroy(Opportunite $opportunite)
    {
        if ($opportunite->documents) {
            Storage::disk('public')->delete($opportunite->documents);
        }

        $opportunite->delete();

        return response()->json([
            'message' => 'Opportunité supprimée'
        ]);
    }
}