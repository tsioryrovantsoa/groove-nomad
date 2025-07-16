<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProposalJob;
use App\Models\Allergy;
use App\Models\CulturalTaste;
use App\Models\Festival;
use App\Models\MusicGenre;
use App\Models\Phobia;
use App\Models\Request as ModelsRequest;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userPreferences = $user->preferences;
        
        return view('request.index', [
            'userPreferences' => $userPreferences
        ]);
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
            'genres.*' => ['integer', 'exists:music_genres,id'],

            'budget' => ['required', 'integer', 'min:0'],

            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],

            'region' => ['required', 'string', 'max:100'],

            'type_aventure' => ['required', 'in:chill,exploratrice,luxe,roots'],

            'nombre_personnes' => ['required', 'integer', 'min:1', 'max:20'],

            'interets' => ['nullable', 'array'],
            'interets.*' => ['integer', 'exists:cultural_tastes,id'],

            'phobies' => ['nullable', 'array'],
            'phobies.*' => ['integer', 'exists:phobias,id'],

            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['integer', 'exists:allergies,id'],
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

        // Créer ou mettre à jour les préférences utilisateur
        $userPreference = $user->preferences()->firstOrCreate();
        
        // Sauvegarder les genres musicaux
        if (!empty($validated['genres'])) {
            $userPreference->addMusicGenres($validated['genres']);
        }
        
        // Sauvegarder les goûts culturels
        if (!empty($validated['interets'])) {
            $userPreference->addCulturalTastes($validated['interets']);
        }
        
        // Sauvegarder les phobies
        if (!empty($validated['phobies'])) {
            $userPreference->addPhobias($validated['phobies']);
        }
        
        // Sauvegarder les allergies
        if (!empty($validated['allergies'])) {
            $userPreference->addAllergies($validated['allergies']);
        }

        $request = ModelsRequest::create($data);

        // GenerateProposalJob::dispatch($request);

        return to_route('request.index')->with('success', 'Demande enregistrée avec succès.');
    }
}
