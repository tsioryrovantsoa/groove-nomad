<?php

namespace App\Jobs;

use App\Models\Request;
use App\Services\ChatGpt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateProposalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Début de génération de proposition', [
                'request_id' => $this->request->id
            ]);

            $chatGptService = new ChatGpt();
            $proposal = $chatGptService->generateProposalForRequest($this->request);

            if ($proposal) {
                Log::info('Proposition générée avec succès', [
                    'request_id' => $this->request->id,
                    'proposal_id' => $proposal->id,
                    'total_price' => $proposal->total_price
                ]);

                // Ici tu peux envoyer une notification par email
                // ou mettre à jour le statut de la demande
                $this->request->update(['status' => 'generated']);
            } else {
                Log::warning('Échec de génération de proposition', [
                    'request_id' => $this->request->id
                ]);

                $this->request->update(['status' => 'no_festival_found']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de proposition', [
                'request_id' => $this->request->id,
                'error' => $e->getMessage()
            ]);

            $this->request->update(['status' => 'generation_failed']);
            throw $e;
        }
    }
}
