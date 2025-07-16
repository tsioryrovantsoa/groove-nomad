<div wire:poll.5s wire:ignore.self>
    <div class="col-lg-12">
        @forelse ($requests as $request)
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-plane mr-2"></i>
                            Demande #{{ $request->id }}
                        </h5>
                        <span class="badge badge-light badge-pill">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fa fa-euro mr-1"></i>Budget
                                </h6>
                                <p class="h5 text-success mb-0">
                                    {{ $request->budget ? number_format($request->budget, 0, ',', ' ') . ' €' : 'Non précisé' }}
                                </p>
                            </div>

                            <div class="info-section mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fa fa-calendar mr-1"></i>Dates
                                </h6>
                                <p class="mb-0">
                                    <span class="badge badge-info mr-1">
                                        {{ $request->date_start ? \Carbon\Carbon::parse($request->date_start)->format('d/m/Y') : '—' }}
                                    </span>
                                    <i class="fa fa-arrow-right text-muted mx-1"></i>
                                    <span class="badge badge-info">
                                        {{ $request->date_end ? \Carbon\Carbon::parse($request->date_end)->format('d/m/Y') : '—' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fa fa-globe mr-1"></i>Destination
                                </h6>
                                <p class="h6 mb-0">{{ $request->region ?? 'Non précisée' }}</p>
                            </div>

                            <div class="info-section mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fa fa-users mr-1"></i>Voyageurs
                                </h6>
                                <p class="h6 mb-0">{{ $request->people_count ?? 'Non précisé' }} personne(s)</p>
                            </div>
                        </div>
                    </div>

                    @if ($request->status === 'no_festival_found')
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle mr-2"></i>
                            <strong>Information :</strong> Pour le moment, nous n'avons pas de festival prévu à cette
                            date. Revenez faire votre demande lorsque cette date est proche.
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-primary">
                            <i class="fa fa-lightbulb-o mr-1"></i>
                            <strong>{{ $request->proposals->count() }}</strong> proposition(s)
                        </span>

                        @if ($request->status === 'pending')
                            <span class="badge badge-secondary">
                                <i class="fa fa-clock-o mr-1"></i>En attente
                            </span>
                        @endif
                    </div>

                    @if ($request->status === 'generating')
                        <div class="alert alert-info d-flex align-items-center mt-3">
                            <div class="typing-indicator mr-3">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <span><i class="fa fa-cog fa-spin mr-2"></i>L'IA réfléchit à votre demande...</span>
                        </div>
                    @endif

                    @if ($request->proposals->count())
                        <div class="proposals-container mt-4">
                            <h6 class="text-muted mb-3">
                                <i class="fa fa-lightbulb-o mr-2"></i>Propositions générées
                            </h6>

                            @foreach ($request->proposals as $proposal)
                                <div class="proposal-card mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-cog text-primary mr-2"></i>
                                                    <strong>Proposition #{{ $proposal->id }}</strong>
                                                </div>
                                                <span
                                                    class="badge badge-{{ $proposal->status === 'generated' ? 'secondary' : ($proposal->status === 'accepted' ? 'success' : 'danger') }} badge-pill">
                                                    {{ ucfirst($proposal->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="proposal-content mb-3">
                                                {!! Str::markdown($proposal->response_text) !!}
                                            </div>

                                            <div class="price-section text-center p-3 bg-light rounded">
                                                <h5 class="text-success mb-0">
                                                    <i class="fa fa-euro mr-1"></i>
                                                    {{ number_format($proposal->total_price, 2, ',', ' ') }} €
                                                </h5>
                                                <small class="text-muted">Prix total TTC</small>
                                            </div>

                                            @if ($proposal->status === 'generated')
                                                <div class="action-buttons mt-3 text-center">
                                                    <form action="{{ route('proposals.accept', $proposal) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-lg mr-2">
                                                            <i class="fa fa-check mr-1"></i>Accepter et payer
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn btn-outline-danger btn-lg"
                                                        onclick="document.getElementById('refusal-{{ $proposal->id }}').classList.toggle('d-none')">
                                                        <i class="fa fa-times mr-1"></i>Refuser
                                                    </button>

                                                    <div id="refusal-{{ $proposal->id }}" class="mt-3 d-none"
                                                        wire:ignore>
                                                        <div class="form-group">
                                                            <textarea wire:model.defer="rejectionReason" class="form-control" rows="3"
                                                                placeholder="Expliquez pourquoi vous refusez cette proposition... Discutez avec l'IA pour trouver une solution."></textarea>
                                                        </div>
                                                        <button wire:click="rejectProposal({{ $proposal->id }})"
                                                            class="btn btn-primary">
                                                            <i class="fa fa-paper-plane-o mr-1"></i>Envoyer
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Message Client (si refus) --}}
                                @if ($proposal->status === 'rejected' && $proposal->rejection_reason)
                                    <div class="alert alert-warning mt-2">
                                        <div class="d-flex align-items-start">
                                            <i class="fa fa-comment text-warning mr-2 mt-1"></i>
                                            <div>
                                                <p class="mb-0 mt-1">{{ $proposal->rejection_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Bulle de génération en cours --}}
                            @if ($request->status === 'generating' || isset($generatingProposals[$request->id]))
                                <div class="alert alert-info d-flex align-items-center">
                                    <div class="typing-indicator mr-3">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <span><i class="fa fa-cog fa-spin mr-2"></i>L'IA réfléchit à votre demande...</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fa fa-plane fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune demande de voyage</h5>
                    <p class="text-muted">Vous n'avez pas encore créé de demande de devis.</p>
                    <a href="{{ route('request.create') }}" class="btn btn-primary btn-lg">
                        <i class="fa fa-plus mr-2"></i>Créer ma première demande
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if ($requests->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <style>
        /* Styles généraux */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .info-section {
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        .proposal-content {
            line-height: 1.6;
        }

        .proposal-content p {
            margin-bottom: 1rem;
        }

        .proposal-content ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .proposal-content li {
            margin-bottom: 0.5rem;
        }

        .price-section {
            border: 2px solid #e9ecef;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .action-buttons {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .empty-state {
            max-width: 400px;
            margin: 0 auto;
        }

        /* Animation de frappe */
        .typing-indicator {
            display: flex;
            gap: 4px;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #6c757d;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .action-buttons .btn {
                display: block;
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .action-buttons .btn:last-child {
                margin-bottom: 0;
            }
        }

        /* Hover effects */
        .proposal-card .card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            transition: transform 0.1s ease;
        }
    </style>
</div>
