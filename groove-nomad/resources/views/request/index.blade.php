@extends('layouts.app')

@section('title', 'Toutes les demandes de devis')

@section('content')
    <div class="col-lg-12">
        @forelse ($requests as $request)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-2">Demande #{{ $request->id }}</h5>

                    <p><strong>Genres préférés :</strong>
                        {{ !empty($request->genres) ? implode(', ', $request->genres) : 'Non précisé' }}
                    </p>

                    <p><strong>Budget :</strong> {{ $request->budget ? $request->budget . ' €' : 'Non précisé' }}</p>

                    <p>
                        <strong>Dates :</strong>
                        {{ $request->date_start ? \Carbon\Carbon::parse($request->date_start)->format('d/m/Y') : '—' }}
                        →
                        {{ $request->date_end ? \Carbon\Carbon::parse($request->date_end)->format('d/m/Y') : '—' }}
                    </p>

                    <p><strong>Région :</strong> {{ $request->region ?? 'Non précisée' }}</p>
                    <p><strong>Type d’aventure :</strong> {{ ucfirst($request->adventure_type) ?? 'Non précisé' }}</p>
                    <p><strong>Nombre de personnes :</strong> {{ $request->people_count ?? 'Non précisé' }}</p>

                    <p><strong>Goûts culturels :</strong>
                        {{ !empty($request->cultural_tastes) ? implode(', ', $request->cultural_tastes) : 'Non précisé' }}
                    </p>

                    <p><strong>Phobies :</strong>
                        {{ !empty($request->phobias) ? implode(', ', $request->phobias) : 'Aucune' }}
                    </p>

                    <p><strong>Allergies :</strong>
                        {{ !empty($request->allergies) ? implode(', ', $request->allergies) : 'Aucune' }}
                    </p>

                    <p><strong>Statut :</strong> {{ ucfirst($request->status) }}</p>

                    <p class="mt-3 text-primary">
                        <strong>Nombre de propositions :</strong> 0
                    </p>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Aucune demande de devis n’a encore été enregistrée.
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
