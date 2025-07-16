<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProposalJob;
use App\Models\Allergy;
use App\Models\CulturalTaste;
use App\Models\Festival;
use App\Models\MusicGenre;
use App\Models\Phobia;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $requests = ModelsRequest::with('proposals')->where('user_id', $user->id)->latest()->paginate(5); // 5 par page (modifie à ton besoin)

        return view('request.index', compact('requests'));
    }
    public function create()
    {
        $genres = MusicGenre::all();
        $regions = Festival::select('region')
            ->groupBy('region')
            ->orderBy('region', 'asc')
            ->get();
        $culturalTastes = CulturalTaste::all();
        $phobias = Phobia::all();
        $allergies = Allergy::all();

        return view('request.create', [
            'genres' => $genres,
            'regions' => $regions,
            'culturalTastes' => $culturalTastes,
            'phobias' => $phobias,
            'allergies' => $allergies,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'genres' => ['nullable', 'array'],
            'genres.*' => ['string', 'max:100'],

            'budget' => ['required', 'integer', 'min:0'],

            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],

            'region' => ['required', 'string', 'max:100'],

            'type_aventure' => ['nullable', 'in:chill,exploratrice,luxe,roots'],

            'nombre_personnes' => ['required', 'integer', 'min:1', 'max:20'],

            'interets' => ['nullable', 'array'],
            'interets.*' => ['string', 'max:100'],

            'phobies' => ['nullable', 'array'],
            'phobies.*' => ['string', 'max:255'],

            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['string', 'max:100'],
        ]);

        $data = [
            'user_id' => $user->id,
            'genres' => $validated['genres'] ?? [],
            'budget' => $validated['budget'] ?? null,
            'date_start' => $validated['date_start'] ?? null,
            'date_end' => $validated['date_end'] ?? null,
            'region' => $validated['region'] ?? null,
            'adventure_type' => $validated['type_aventure'] ?? null,
            'people_count' => $validated['nombre_personnes'] ?? null,
            'cultural_tastes' => $validated['interets'] ?? [],
            'phobias' => $validated['phobies'] ?? [],
            'allergies' => $validated['allergies'] ?? [],
            'status' => 'pending',
        ];

        $request = ModelsRequest::create($data);

        GenerateProposalJob::dispatch($request);

        return to_route('request.index')->with('success', 'Demande enregistrée avec succès.');
    }
}
