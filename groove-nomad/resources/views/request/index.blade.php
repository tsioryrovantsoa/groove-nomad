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

                    @if ($request->status === 'no_festival_found')
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Information :</strong> Pour le moment, nous n'avons pas de festival prévu à cette date.
                            Revenez faire votre demande lorsque cette date est proche.
                        </div>
                    @endif

                    <p class="mt-3 text-primary">
                        <strong>Nombre de propositions :</strong> {{ $request->proposals->count() }}
                    </p>
                    @if ($request->proposals->count())
                        <div class="chat-container">
                            @foreach ($request->proposals as $proposal)
                                {{-- Message IA --}}
                                <div class="chat-message">
                                    <div class="chat-bubble-left p-3 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>🤖 Proposition #{{ $proposal->id }}</strong>
                                            <span
                                                class="badge bg-{{ $proposal->status === 'generated' ? 'secondary' : ($proposal->status === 'accepted' ? 'success' : 'danger') }}"
                                                style="color: white;">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        </div>

                                        <div class="mt-2 p-3 rounded markdown-content">
                                            {!! Str::markdown($proposal->response_text) !!}
                                        </div>


                                        <p class="mt-3 mb-0">
                                            <strong>💶 Prix total :</strong>
                                            {{ number_format($proposal->total_price, 2, ',', ' ') }} €
                                        </p>

                                        @if ($proposal->status === 'generated')
                                            <div class="mt-3">
                                                <form action="{{ route('proposals.accept', $proposal) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm me-2">
                                                        ✅ Accepter et payer
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="document.getElementById('refusal-{{ $proposal->id }}').classList.toggle('d-none')">
                                                    ❌ Refuser
                                                </button>

                                                <div id="refusal-{{ $proposal->id }}" class="mt-2 d-none">
                                                    <form action="{{ route('proposals.reject', $proposal) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea name="rejection_reason" class="form-control mb-2" rows="2" placeholder="Motif du refus..."></textarea>
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm">Confirmer le
                                                            refus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                {{-- Message Client (si refus) --}}
                                @if ($proposal->status === 'rejected' && $proposal->rejection_reason)
                                    <div class="chat-message">
                                        <div class="chat-bubble-right p-3 shadow-sm border">
                                            <strong>🙋‍♀️ Refus du client :</strong>
                                            {{ $proposal->rejection_reason }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

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

    <style>
        .markdown-content p {
            margin-bottom: 1rem;
        }

        .markdown-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .markdown-content li {
            margin-bottom: 0.5rem;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .chat-message {
            display: flex;
            align-items: flex-start;
        }

        .chat-bubble-left {
            max-width: 80%;
            border-radius: 1rem;
            margin-right: auto;
        }

        .chat-bubble-right {
            max-width: 60%;
            border-radius: 1rem;
            background-color:#f1f2f4;
            margin-left: auto;
        }

        .chat-bubble-left pre {
            white-space: pre-wrap;
        }

        .chat-bubble-right pre {
            white-space: pre-wrap;
        }

        .chat-bubble-left strong,
        .chat-bubble-right strong {
            display: block;
            margin-bottom: 0.5rem;
        }

        .chat-message form .btn {
            margin-top: 1rem;
        }
    </style>
@endsection
