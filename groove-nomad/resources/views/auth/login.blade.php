@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="col-lg-6 offset-lg-3">
        <form method="POST" action="#{{ route('auth.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>

            <div class="text-center mt-3">
                <a href="{{ route('auth.register') }}">Pas de compte ? S'inscrire</a>
            </div>
        </form>
    </div>
@endsection
