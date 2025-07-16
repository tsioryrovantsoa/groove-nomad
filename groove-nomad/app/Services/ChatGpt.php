<?php

namespace App\Services;

use App\Models\Festival;
use App\Models\Proposal;
use App\Models\ProposalDetail;
use App\Models\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;

class ChatGpt
{
    private $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.open_ai.api_key'));
    }

    /**
     * Génère une proposition de voyage basée sur une demande et un festival
     *
     * @param Request $request
     * @param Festival $festival
     * @return Proposal|null
     */
    public function generateTravelProposal(Request $request, Festival $festival): ?Proposal
    {
        try {
            $request->update(['status' => 'generating']);

            $prompt = $this->buildTravelPrompt($request, $festival);
            $aiResponse = $this->getAiResponse($prompt, $request);

            if (!$aiResponse) {
                Log::error('Aucune réponse reçue de l\'IA pour la demande', [
                    'request_id' => $request->id,
                    'festival_id' => $festival->id
                ]);
                return null;
            }

            $totalPrice = $this->extractTotalPrice($aiResponse);

            $proposal = Proposal::create([
                'request_id'     => $request->id,
                'festival_id'    => $festival->id,
                'prompt_text'    => $prompt,
                'response_text'  => $aiResponse,
                'total_price'    => $totalPrice,
                'status'         => 'generated',
            ]);

            $this->createProposalDetails($proposal, $aiResponse);

            Log::info('Proposition de voyage générée avec succès', [
                'proposal_id' => $proposal->id,
                'request_id' => $request->id,
                'festival_id' => $festival->id,
                'total_price' => $totalPrice
            ]);

            return $proposal;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de la proposition de voyage', [
                'request_id' => $request->id,
                'festival_id' => $festival->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Formate une liste pour l'affichage
     *
     * @param mixed $value
     * @return string
     */
    private function formatList($value): string
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return trim(str_replace(['[', ']', '"'], '', $value));
    }

    /**
     * Nettoie et encode correctement le texte pour l'IA
     *
     * @param string $text
     * @return string
     */
    private function cleanTextForAI(string $text): string
    {
        // Supprimer les caractères non-UTF8
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Convertir en UTF-8 si nécessaire
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
        }
        
        // Nettoyer les caractères spéciaux problématiques
        $text = str_replace(['', '', '', '', '', ''], '', $text);
        
        return $text;
    }

    /**
     * Construit le prompt pour l'IA avec historique des refus
     *
     * @param Request $request
     * @param Festival $festival
     * @return string
     */
    private function buildTravelPrompt(Request $request, Festival $festival): string
    {
        $duration = $request->date_start->diffInDays($request->date_end) + 1;

        // Récupérer l'historique des propositions refusées
        $rejectedProposals = $request->proposals()
            ->where('status', 'rejected')
            ->whereNotNull('rejection_reason')
            ->orderBy('created_at', 'asc')
            ->get();

        $rejectionHistory = '';
        if ($rejectedProposals->count() > 0) {
            $rejectionHistory = "\n\nHISTORIQUE DES PROPOSITIONS REFUSEES :\n\n";

            foreach ($rejectedProposals as $index => $proposal) {
                $rejectionHistory .= "Proposition #{$proposal->id} (refusee le " . $proposal->created_at->format('d/m/Y') . ") :\n";
                $rejectionHistory .= "Motif du refus : " . $this->cleanTextForAI($proposal->rejection_reason) . "\n";
                $rejectionHistory .= "Prix propose : {$proposal->total_price} EUR\n";
                $rejectionHistory .= "Festival : " . $this->cleanTextForAI($proposal->festival->name) . "\n\n";
            }

            $rejectionHistory .= "IMPORTANT : Prends en compte ces refus pour proposer quelque chose de different et mieux adapte.\n\n";
        }

        $prompt = "Tu es un assistant de voyage IA specialise dans l'organisation de sejours sur mesure incluant des festivals de musique.

Voici les informations du client :

- Genres musicaux preferes : " . $this->cleanTextForAI($this->formatList($request->genres)) . "
- Budget maximum a ne pas depasser : {$request->budget} EUR
- Dates de voyage : du {$request->date_start->format('d/m/Y')} au {$request->date_end->format('d/m/Y')} ({$duration} jours)
- Region souhaitee : " . $this->cleanTextForAI($request->region) . "
- Nombre de personnes : {$request->people_count}
- Gouts culturels : " . $this->cleanTextForAI($this->formatList($request->cultural_tastes)) . "
- Phobies a eviter : " . $this->cleanTextForAI($this->formatList($request->phobias)) . "
- Allergies a prendre en compte : " . $this->cleanTextForAI($this->formatList($request->allergies)) . "

Festival selectionne :

- Nom : " . $this->cleanTextForAI($festival->name) . "
- Dates : du {$festival->start_date->format('d/m/Y')} au {$festival->end_date->format('d/m/Y')}
- Lieu : " . $this->cleanTextForAI($festival->location) . ", " . $this->cleanTextForAI($festival->region) . "
- Description : " . $this->cleanTextForAI($festival->description) . "{$rejectionHistory}

---

OBJECTIF :

Propose un programme de sejour immersif et coherent de {$duration} jours qui integre ce festival, avec :

- Hebergement adapte
- Transports securises
- Activites culturelles liees aux gouts du client
- Repas si possible
- Respect des phobies et allergies

---

REGLES STRICTES :

1. LE BUDGET DE {$request->budget} EUR TTC NE DOIT JAMAIS ETRE DEPASSE
2. INTERDICTION de proposer des suggestions d'optimisation ou de depassement de budget
3. INTERDICTION de mentionner des alternatives ou des ajustements
4. Si le budget ne peut pas etre respecte, propose une version plus economique (duree reduite, hebergement moins cher, etc.)
5. Pour chaque element du sejour, utilise EXACTEMENT ce format :
   **Nom de l'element** : Description de l'element - Prix TTC : XXX EUR
6. Termine OBLIGATOIREMENT par ce recapitulatif exact :

Recapitulatif :

Transport : XXX EUR

Hebergement : XXX EUR

Activites : XXX EUR

Pass Festival : XXX EUR

Prix total TTC : XXX EUR

IMPORTANT : Le prix total doit etre inferieur ou egal a {$request->budget} EUR. Si ce n'est pas possible, propose une version plus economique.";

        return $this->cleanTextForAI($prompt);
    }

    /**
     * Obtient la réponse de l'IA avec historique des conversations
     *
     * @param string $prompt
     * @param Request $request
     * @return string|null
     */
    private function getAiResponse(string $prompt, Request $request): ?string
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => 'Tu es un assistant de voyage IA. Sois structure, professionnel et convivial.'],
            ];

            // OPTIMISATION : Limiter l'historique aux 3 dernières propositions
            $previousProposals = $request->proposals()
                ->whereIn('status', ['generated', 'rejected'])
                ->limit(3)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($previousProposals as $proposal) {
                // OPTIMISATION : Tronquer les contenus trop longs et nettoyer
                $promptText = strlen($proposal->prompt_text) > 1000 
                    ? substr($proposal->prompt_text, 0, 1000) . '...' 
                    : $proposal->prompt_text;
                    
                $responseText = strlen($proposal->response_text) > 2000 
                    ? substr($proposal->response_text, 0, 2000) . '...' 
                    : $proposal->response_text;

                $messages[] = [
                    'role' => 'user',
                    'content' => "Proposition precedente #{$proposal->id} :\n" . $this->cleanTextForAI($promptText)
                ];

                $messages[] = [
                    'role' => 'assistant',
                    'content' => $this->cleanTextForAI($responseText)
                ];

                if ($proposal->status === 'rejected' && $proposal->rejection_reason) {
                    $messages[] = [
                        'role' => 'user',
                        'content' => "Cette proposition a ete refusee. Motif : " . $this->cleanTextForAI($proposal->rejection_reason)
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $this->cleanTextForAI($prompt)];

            // OPTIMISATION : Réduire la température pour des réponses plus rapides
            $response = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => $messages,
                'temperature' => 0.5,
                'max_tokens' => 2000,
            ]);

            return $response->choices[0]->message->content ?? null;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la communication avec l\'IA', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Extrait le prix total de la réponse de l'IA
     *
     * @param string $aiResponse
     * @return float
     */
    private function extractTotalPrice(string $aiResponse): float
    {
        // Modifier pour chercher "Prix total TTC" au lieu de "prix total" avec émoji
        preg_match('/Prix total TTC\s*:\s*(\d+[.,]?\d*)\s*EUR/i', $aiResponse, $matchesTotal);
        return isset($matchesTotal[1]) ? (float) str_replace(',', '.', $matchesTotal[1]) : 0;
    }

    /**
     * Crée les détails de la proposition à partir de la réponse de l'IA
     *
     * @param Proposal $proposal
     * @param string $aiResponse
     * @return void
     */
    private function createProposalDetails(Proposal $proposal, string $aiResponse): void
    {
        // Extraire le récapitulatif avec un regex flexible - sans émoji
        if (preg_match('/Recapitulatif\s*:\s*\n\n(.*?)\n\nPrix total TTC\s*:\s*(\d+[.,]?\d*)\s*EUR/is', $aiResponse, $recapMatches)) {
            $recapContent = $recapMatches[1];
            
            // Extraire chaque ligne du récapitulatif - chercher EUR au lieu de €
            preg_match_all('/([^:]+):\s*(\d+[.,]?\d*)\s*EUR/i', $recapContent, $recapLines, PREG_SET_ORDER);
            
            foreach ($recapLines as $line) {
                $name = trim($line[1]);
                $price = (float) str_replace(',', '.', $line[2]);
                
                // Ne pas stocker le prix total
                if ($name !== 'Prix total TTC') {
                    ProposalDetail::create([
                        'proposal_id' => $proposal->id,
                        'name'        => $name,
                        'description' => 'Récapitulatif du séjour',
                        'price'       => $price,
                    ]);
                }
            }
        }
    }

    /**
     * Trouve un festival correspondant à une demande
     *
     * @param Request $request
     * @return Festival|null
     */
    public function findMatchingFestival(Request $request): ?Festival
    {
        // OPTIMISATION : Ajouter des index et optimiser la requête
        return Festival::select('id', 'name', 'start_date', 'end_date', 'location', 'region', 'description')
            ->where('region', $request->region)
            ->whereDate('start_date', '<=', $request->date_end)
            ->whereDate('end_date', '>=', $request->date_start)
            ->inRandomOrder()
            ->first();
    }

    /**
     * Génère une proposition complète pour une demande
     *
     * @param Request $request
     * @return Proposal|null
     */
    public function generateProposalForRequest(Request $request): ?Proposal
    {
        $festival = $this->findMatchingFestival($request);

        if (!$festival) {
            Log::warning('Aucun festival correspondant trouvé', [
                'request_id' => $request->id,
                'region' => $request->region,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end
            ]);
            return null;
        }

        return $this->generateTravelProposal($request, $festival);
    }
}
