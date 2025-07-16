@extends('layouts.app')

@section('title', 'Étape 1 - Tes préférences')

@section('content')
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Étape 1/2 - Tes préférences</h4>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('request.store.step1') }}">
                    @csrf

                    {{-- 🎵 Préférences musicales --}}
                    <h5 class="mt-4 mb-2 border-bottom pb-2">🎶 Genres musicaux préférés</h5>
                    <div class="form-row">
                        @foreach ($genres as $genre)
                            <div class="form-group col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genres[]"
                                        id="genre_{{ Str::slug($genre->name) }}" value="{{ $genre->id }}">
                                    <label class="form-check-label"
                                        for="genre_{{ Str::slug($genre->name) }}">{{ $genre->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 🎨 Goûts culturels --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">🎨 Goûts culturels</h5>
                    <div class="form-row">
                        @foreach ($culturalTastes as $interet)
                            <div class="form-group col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interets[]"
                                        id="{{ Str::slug($interet->name) }}" value="{{ $interet->id }}">
                                    <label class="form-check-label"
                                        for="{{ Str::slug($interet->name) }}">{{ $interet->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- 😱 Phobies (collapse) --}}
                    <h5 class="mt-4 mb-2 border-bottom pb-2">😱 Phobies
                        <a class="btn btn-link btn-sm" data-toggle="collapse" href="#phobiesCollapse" role="button"
                            aria-expanded="false" aria-controls="phobiesCollapse">(voir/masquer)</a>
                    </h5>
                    <div class="collapse" id="phobiesCollapse">
                        <div class="form-row">
                            @foreach ($phobias as $phobie)
                                <div class="form-group col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="phobies[]"
                                            id="{{ Str::slug($phobie->name) }}" value="{{ $phobie->id }}">
                                        <label class="form-check-label"
                                            for="{{ Str::slug($phobie->name) }}">{{ $phobie->description }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 🤧 Allergies (collapse) --}}
                    <h5 class="mt-4 mb-2 border-bottom pb-2">🤧 Allergies
                        <a class="btn btn-link btn-sm" data-toggle="collapse" href="#allergiesCollapse" role="button"
                            aria-expanded="false" aria-controls="allergiesCollapse">(voir/masquer)</a>
                    </h5>
                    <div class="collapse" id="allergiesCollapse">
                        <div class="form-row">
                            @foreach ($allergies as $allergie)
                                <div class="form-group col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allergies[]"
                                            id="{{ Str::slug($allergie->name) }}" value="{{ $allergie->id }}">
                                        <label class="form-check-label"
                                            for="{{ Str::slug($allergie->name) }}">{{ $allergie->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('request.index') }}" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-primary">Continuer vers l'étape 2</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
