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

    public function storeStep1(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:music_genres,id'],
            'interets' => ['nullable', 'array'],
            'interets.*' => ['integer', 'exists:cultural_tastes,id'],
            'phobies' => ['nullable', 'array'],
            'phobies.*' => ['integer', 'exists:phobias,id'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['integer', 'exists:allergies,id'],
        ]);

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

        // Stocker les données en session pour l'étape 2
        $request->session()->put('step1_data', $validated);

        return redirect()->route('request.create.step2');
    }

    public function createStep2()
    {
        // Vérifier que l'utilisateur a complété l'étape 1
        if (!session()->has('step1_data')) {
            return redirect()->route('request.create');
        }

        $regions = Festival::select('region')
            ->groupBy('region')
            ->orderBy('region', 'asc')
            ->get();

        return view('request.create-step2', [
            'regions' => $regions,
        ]);
    }

    public function chat()
    {
        $regions = Festival::select('region')
            ->groupBy('region')
            ->orderBy('region', 'asc')
            ->get();

        return view('chat.index', [
            'regions' => $regions,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'budget' => ['required', 'integer', 'min:0'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'region' => ['required', 'string', 'max:100'],
            'nombre_personnes' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        // Récupérer directement depuis les préférences utilisateur
        $userPreferences = $user->preferences;
        
        $genres = $userPreferences ? $userPreferences->musicGenres->pluck('name')->toArray() : [];
        $culturalTastes = $userPreferences ? $userPreferences->culturalTastes->pluck('name')->toArray() : [];
        $phobias = $userPreferences ? $userPreferences->phobias->pluck('description')->toArray() : [];
        $allergies = $userPreferences ? $userPreferences->allergies->pluck('name')->toArray() : [];

        $data = [
            'user_id' => $user->id,
            'genres' => $genres,
            'budget' => $validated['budget'],
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'region' => $validated['region'],
            'people_count' => $validated['nombre_personnes'],
            'cultural_tastes' => $culturalTastes,
            'phobias' => $phobias,
            'allergies' => $allergies,
            'status' => 'pending',
        ];

        $requestModel = ModelsRequest::create($data);

        // Nettoyer la session si elle existe
        $request->session()->forget('step1_data');

        GenerateProposalJob::dispatch($requestModel);

        return to_route('request.index')->with('success', 'Demande enregistrée avec succès.');
    }
}
