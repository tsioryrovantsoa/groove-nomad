<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Request;
use App\Jobs\GenerateProposalJob;
use App\Models\Proposal;

class RequestList extends Component
{
    use WithPagination;

    public $rejectionReason = '';

    public function rejectProposal($proposalId)
    {
        $proposal = Proposal::with('request')->findOrFail($proposalId);
        
        // Vérification que la proposition est bien en état 'generated'
        if ($proposal->status !== 'generated') {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Cette proposition ne peut pas être refusée.'
            ]);
            return;
        }

        // Validation du motif de refus - utiliser le nom de la propriété
        $this->validate([
            'rejectionReason' => 'nullable|string|max:1000'
        ]);

        $request = $proposal->request;
        
        $proposal->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason ?? null,
        ]);

        // Lancer la génération d'une nouvelle proposition
        GenerateProposalJob::dispatch($request);
        
        $this->rejectionReason = '';
        
        $this->dispatch('alert', [
            'type' => 'info',
            'message' => 'Proposition refusée ! Nouvelle proposition en cours de génération...'
        ]);
    }
    
    public function render()
    {
        $user = auth()->user();
        
        $requests = Request::with(['proposals' => function($query) {
            $query->orderBy('created_at', 'asc'); // Tri par date de création croissante
        }])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(1);

        return view('livewire.request-list', [
            'requests' => $requests
        ]);
    }
}
