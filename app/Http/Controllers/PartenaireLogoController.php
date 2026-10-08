<?php

namespace App\Http\Controllers;

use App\Models\PartenaireLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Gestion des logos partenaires de la page d'accueil.
 * Lecture publique (actifs uniquement) ; écriture réservée à l'admin.
 */
class PartenaireLogoController extends Controller
{
    /** Public : logos actifs, dans l'ordre d'affichage. */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => PartenaireLogo::where('actif', true)->orderBy('ordre')->orderBy('id')->get(),
        ]);
    }

    /** Admin : tous les logos, y compris masqués. */
    public function indexAdmin()
    {
        return response()->json([
            'success' => true,
            'data' => PartenaireLogo::orderBy('ordre')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'domaine' => 'nullable|string|max:255',
            'logo' => 'required|image|max:5120',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $data['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        $data['ordre'] = $data['ordre'] ?? ((int) PartenaireLogo::max('ordre') + 1);

        $logo = PartenaireLogo::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Partenaire ajouté',
            'data' => $logo,
        ], 201);
    }

    public function update(Request $request, PartenaireLogo $partenaireLogo)
    {
        $data = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'domaine' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:5120',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($partenaireLogo->getRawOriginal('logo')) {
                Storage::disk('public')->delete($partenaireLogo->getRawOriginal('logo'));
            }
            $data['logo'] = $request->file('logo')->store('partenaires/logos', 'public');
        } else {
            unset($data['logo']);
        }

        $partenaireLogo->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Partenaire modifié',
            'data' => $partenaireLogo->fresh(),
        ]);
    }

    public function destroy(PartenaireLogo $partenaireLogo)
    {
        if ($partenaireLogo->getRawOriginal('logo')) {
            Storage::disk('public')->delete($partenaireLogo->getRawOriginal('logo'));
        }

        $partenaireLogo->delete();

        return response()->json(['success' => true, 'message' => 'Partenaire supprimé']);
    }
}
