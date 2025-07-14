@extends('layouts.app')

@section('title', 'Planifie ton aventure musicale')

@section('content')
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <form method="POST" action="#">
                    @csrf

                    {{-- 🎵 Préférences musicales --}}
                    {{-- Genres musicaux préférés --}}
                    <h5 class="mt-4 mb-2 border-bottom pb-2">🎶 Genres musicaux préférés</h5>
                    <div class="form-row">
                        @foreach (['Rock', 'Électro', 'Hip-hop', 'Jazz', 'Pop', 'World', 'Autre'] as $genre)
                            <div class="form-group col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="genres[]"
                                        id="genre_{{ Str::slug($genre) }}" value="{{ $genre }}">
                                    <label class="form-check-label"
                                        for="genre_{{ Str::slug($genre) }}">{{ $genre }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    {{-- 💸 Budget & 📅 Dates --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">💸 Budget & 📅 Dates</h5>

                    <div class="form-group">
                        <label for="budget">Budget total (€)</label>
                        <input type="number" name="budget" id="budget" class="form-control" placeholder="Ex: 1000"
                            min="0">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="date_start">Date de début</label>
                            <input type="date" name="date_start" id="date_start" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date_end">Date de fin</label>
                            <input type="date" name="date_end" id="date_end" class="form-control">
                        </div>
                    </div>

                    {{-- 🌍 Destination & Aventure --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">🌍 Destination & Type d’aventure</h5>

                    <div class="form-group">
                        <label for="region">Région du monde souhaitée</label>
                        <select name="region" id="region" class="form-control">
                            <option value="">-- Choisir --</option>
                            <option>Europe</option>
                            <option>Amérique du Nord</option>
                            <option>Amérique du Sud</option>
                            <option>Asie</option>
                            <option>Afrique</option>
                            <option>Océanie</option>
                        </select>
                    </div>

                    <fieldset class="form-group">
                        <legend>Type d’aventure</legend>
                        @foreach (['chill', 'exploratrice', 'luxe', 'roots'] as $type)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_aventure" id="{{ $type }}"
                                    value="{{ $type }}">
                                <label class="form-check-label" for="{{ $type }}">{{ ucfirst($type) }}</label>
                            </div>
                        @endforeach
                    </fieldset>

                    <fieldset class="form-group">
                        {{-- Nombre de personnes --}}
                        <div class="form-group">
                            <label for="nombre_personnes">Nombre de personnes</label>
                            <input type="number" name="nombre_personnes" id="nombre_personnes" class="form-control"
                                min="1" max="20" placeholder="Ex: 1">
                        </div>

                    </fieldset>

                    {{-- 🎨 Goûts culturels --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">🎨 Goûts culturels</h5>
                    <div class="form-row">
                        @foreach (['Cosmopolitisme esthético-culturel', 'Road trips et aventure', 'Arts visuels et street art', 'Lifestyle côtier et beach culture', 'Arts et artisanat contemporains', 'Gaming et esports', 'Technologie et innovation', 'Culture urbaine et street art', 'Écotourisme et conservation', 'Multiculturalisme urbain'] as $interet)
                            <div class="form-group col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interets[]"
                                        id="{{ Str::slug($interet) }}" value="{{ $interet }}">
                                    <label class="form-check-label"
                                        for="{{ Str::slug($interet) }}">{{ $interet }}</label>
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
                            @foreach (['Agoraphobie', 'Ochlophobie', 'Aviophobie', 'Amaxophobie', 'Diderodromophobie / Sidérodromophobie', 'Nyctophobie / Achluophobie', 'Sociophobie / Anthropophobie', 'Acrophobie / Altophobie', 'Ombrophobie', 'Domophobie / Ecophobie'] as $phobie)
                                <div class="form-group col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="phobies[]"
                                            id="{{ Str::slug($phobie) }}" value="{{ $phobie }}">
                                        <label class="form-check-label"
                                            for="{{ Str::slug($phobie) }}">{{ $phobie }}</label>
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
                            @foreach (['Allergies respiratoires', 'Allergies alimentaires', 'Allergies médicamenteuses', 'Allergies de contact (cutanées)', 'Allergies aux venins et piqûres', 'Réactions physiques et photoallergies', 'Allergies professionnelles (occupationales)', 'Allergies aux poils et squames d’animaux'] as $allergie)
                                <div class="form-group col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="allergies[]"
                                            id="{{ Str::slug($allergie) }}" value="{{ $allergie }}">
                                        <label class="form-check-label"
                                            for="{{ Str::slug($allergie) }}">{{ $allergie }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success mt-4">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
@endsection
