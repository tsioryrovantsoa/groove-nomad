@extends('layouts.app')

@section('title', 'Chat avec l\'IA')

@section('content')
<div class="col-lg-8 offset-lg-2">
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fa fa-comments mr-2"></i>Créez votre demande de voyage
            </h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('request.store') }}">
                @csrf

                <div class="interactive-form">
                    <p class="form-text">
                        Bonjour ! Je souhaite partir avec un budget de 
                        <input type="number" name="budget" class="form-control inline-input" placeholder="1000€" min="0" required> €
                        du 
                        <input type="date" name="date_start" class="form-control inline-input" required>
                        au 
                        <input type="date" name="date_end" class="form-control inline-input" required>
                        .
                    </p>

                    <p class="form-text">
                        Je veux aller en 
                        <select name="region" class="form-control inline-input" required>
                            <option value="">-- Choisir --</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->region }}">{{ $region->region }}</option>
                            @endforeach
                        </select>
                        pour 
                        <input type="number" name="nombre_personnes" class="form-control inline-input" placeholder="2" min="1" max="20" required>
                        personne(s).
                    </p>
                </div>

                @error('message')
                    <div class="alert alert-danger mt-3">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('request.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left mr-1"></i>Retour aux demandes
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane mr-1"></i>Envoyer ma demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.interactive-form {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
}

.form-text {
    font-size: 16px;
    line-height: 2.5;
    margin-bottom: 15px;
    color: #333;
}

.inline-input {
    display: inline-block;
    width: auto;
    min-width: 120px;
    margin: 0 5px;
    border: 2px solid #e9ecef;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.inline-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

.inline-input::placeholder {
    color: #6c757d;
    font-style: italic;
}

.form-text strong {
    color: #007bff;
}
</style>
@endsection
