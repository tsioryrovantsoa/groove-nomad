@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
    <div class="col-lg-8 offset-lg-2">
        <form method="POST" action="{{ route('auth.register') }}">
            @csrf

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="last_name">Nom</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <label for="first_name">Prénom</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                        value="{{ old('first_name') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}"
                    required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="city">Ville de résidence</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}"
                        required>
                </div>
                <div class="form-group col-md-6">
                    <label for="passport_country">Pays du passeport</label>
                    <select class="form-control" id="passport_country" name="passport_country" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->iso2 }}"
                                {{ old('passport_country') == $country->iso2 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nationality">Nationalité</label>
                    <select class="form-control" id="nationality" name="nationality" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->iso2 }}"
                                {{ old('nationality') == $country->iso2 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="phone_number">Numéro de téléphone</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number"
                        value="{{ old('phone_number') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="birth_date">Date de naissance</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                        value="{{ old('birth_date') }}" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="gender">Genre</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Homme</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Femme</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="marital_status">Situation matrimoniale</label>
                    <select class="form-control" id="marital_status" name="marital_status" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Célibataire
                        </option>
                        <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Marié(e)
                        </option>
                        <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorcé(e)
                        </option>
                        <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Veuf(ve)
                        </option>
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
                <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" required>
                <label class="form-check-label" for="terms_accepted">
                    J'accepte les <a href="#">conditions d'utilisation</a> et la <a href="#">politique de
                        confidentialité</a>.
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">S'inscrire</button>
        </form>
    </div>
@endsection
