<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use App\Models\CulturalTaste;
use App\Models\Festival;
use App\Models\MusicGenre;
use App\Models\Phobia;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $genres = MusicGenre::all();
        $regions = Festival::select('region')
            ->groupBy('region')
            ->orderBy('region', 'asc')
            ->get();
        $culturalTastes = CulturalTaste::all();
        $phobias = Phobia::all();
        $allergies = Allergy::all();

        return view('request.index', [
            'genres' => $genres,
            'regions' => $regions,
            'culturalTastes' => $culturalTastes,
            'phobias' => $phobias,
            'allergies' => $allergies,
        ]);
    }
}
