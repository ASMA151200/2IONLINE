<?php
// app/Http/Controllers/Api/AlerteController.php

namespace App\Http\Controllers\Api;

use App\Models\Alerte;
use App\Services\AlertService;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function __construct(private AlertService $alertService) {}

    public function store(Request $request)
    {
        $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'type'     => 'required|in:annulation,deadline,annonce',
            'titre'    => 'required|string|max:100',
            'message'  => 'required|string|max:300',
        ]);

        $alerte = Alerte::create([
            ...$request->only(['cours_id', 'type', 'titre', 'message']),
            'formateur_id' => auth()->id(),
        ]);

        $this->alertService->envoyerAuxInscrits($alerte);

        return response()->json($alerte, 201);
    }

    public function index()
    {
        return Alerte::where('formateur_id', auth()->id())
            ->with('cours:id,titre')
            ->latest()
            ->paginate(20);
    }
}