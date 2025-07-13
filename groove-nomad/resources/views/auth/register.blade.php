@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
    <div class="col-lg-8 offset-lg-2">
        <form method="POST" action="{{ route('auth.register') }}">
            @csrf

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}"
                        required>
                </div>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse') }}"
                    required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="ville">Ville de résidence</label>
                    <input type="text" class="form-control" id="ville" name="ville" value="{{ old('ville') }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <label for="passeport">Pays du passeport</label>
                    <input type="text" class="form-control" id="passeport" name="passeport"
                        value="{{ old('passeport') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nationalite">Nationalité</label>
                    <input type="text" class="form-control" id="nationalite" name="nationalite"
                        value="{{ old('nationalite') }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone"
                        value="{{ old('telephone') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="genre">Genre</label>
                    <select class="form-control" id="genre" name="genre" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="situation">Situation matrimoniale</label>
                    <select class="form-control" id="situation" name="situation" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Célibataire">Célibataire</option>
                        <option value="Marié(e)">Marié(e)</option>
                        <option value="Divorcé(e)">Divorcé(e)</option>
                        <option value="Veuf(ve)">Veuf(ve)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                    required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="password_confirmation">Confirmation du mot de passe</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        required>
                </div>
            </div>

            <div class="form-group form-check mt-3">
                <input type="checkbox" class="form-check-input" id="acceptation" name="acceptation" required>
                <label class="form-check-label" for="acceptation">
                    J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de
                        confidentialité</a>.
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">S'inscrire</button>
        </form>
    </div>
@endsection
