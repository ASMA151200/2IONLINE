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
            if ($actus->image) {
                Storage::disk('public')->delete($actus->image);
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
        if ($actus->image) {
            Storage::disk('public')->delete($actus->image);
        }

        $actus->delete();

        return response()->json([
            'message' => 'Actu supprimée'
        ]);
    }
}