@extends('layouts.app')

@section('title', 'Toutes les demandes de devis')

@section('content')
    {{-- 🎵 Préférences utilisateur --}}
    @if($userPreferences)
        <div class="col-lg-10 offset-lg-1 mb-5">
            <div class="preferences-card">
                <div class="preferences-header">
                    <div class="preferences-title">
                        <h3>Mes Préférences Musicales</h3>
                        <p class="text-light">Vos goûts personnalisés pour des aventures sur mesure</p>
                    </div>
                </div>
                
                <div class="preferences-content">
                    <div class="preferences-grid">
                        {{-- Genres musicaux --}}
                        @if($userPreferences->musicGenres->count() > 0)
                            <div class="preference-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                    </div>
                                    <h4>Genres Musicaux</h4>
                                </div>
                                <div class="tags-container">
                                    @foreach($userPreferences->musicGenres as $genre)
                                        <span class="tag tag-music">{{ $genre->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Goûts culturels --}}
                        @if($userPreferences->culturalTastes->count() > 0)
                            <div class="preference-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                    </div>
                                    <h4>Goûts Culturels</h4>
                                </div>
                                <div class="tags-container">
                                    @foreach($userPreferences->culturalTastes as $taste)
                                        <span class="tag tag-culture">{{ $taste->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Phobies --}}
                        @if($userPreferences->phobias->count() > 0)
                            <div class="preference-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                    </div>
                                    <h4>Phobies</h4>
                                </div>
                                <div class="tags-container">
                                    @foreach($userPreferences->phobias as $phobia)
                                        <span class="tag tag-phobia">{{ $phobia->description }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Allergies --}}
                        @if($userPreferences->allergies->count() > 0)
                            <div class="preference-section">
                                <div class="section-header">
                                    <div class="section-icon">
                                        <i class="fas fa-allergies"></i>
                                    </div>
                                    <h4>Allergies</h4>
                                </div>
                                <div class="tags-container">
                                    @foreach($userPreferences->allergies as $allergy)
                                        <span class="tag tag-allergy">{{ $allergy->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Liste des demandes --}}
    <livewire:request-list />
@endsection
