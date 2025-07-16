@extends('layouts.app')

@section('title', 'Étape 2 - Démarrer ton trip')

@section('content')
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Étape 2/2 - Démarrer ton trip</h4>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('request.store') }}">
                    @csrf

                    {{-- 💸 Budget & 📅 Dates --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">💸 Budget & 📅 Dates</h5>

                    <div class="form-group">
                        <label for="budget">Budget total (€)</label>
                        <input type="number" name="budget" id="budget" class="form-control" placeholder="Ex: 1000"
                            min="0" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="date_start">Date de début</label>
                            <input type="date" name="date_start" id="date_start" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="date_end">Date de fin</label>
                            <input type="date" name="date_end" id="date_end" class="form-control" required>
                        </div>
                    </div>

                    {{-- 🌍 Destination & Aventure --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">🌍 Destination & Type d'aventure</h5>

                    <div class="form-group">
                        <label for="region">Région du monde souhaitée</label>
                        <select name="region" id="region" class="form-control" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->region }}">{{ $region->region }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="form-group">
                        <label for="nombre_personnes">Nombre de personnes</label>
                        <input type="number" name="nombre_personnes" id="nombre_personnes" class="form-control"
                            min="1" max="20" placeholder="Ex: 1" required>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('request.create') }}" class="btn btn-secondary">Retour à l'étape 1</a>
                        <button type="submit" class="btn btn-success">Envoyer ma demande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 