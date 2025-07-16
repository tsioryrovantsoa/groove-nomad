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
            $rejectionHistory = "\n\n📋 **HISTORIQUE DES PROPOSITIONS REFUSÉES :**\n\n";

            foreach ($rejectedProposals as $index => $proposal) {
                $rejectionHistory .= "**Proposition #{$proposal->id}** (refusée le " . $proposal->created_at->format('d/m/Y') . ") :\n";
                $rejectionHistory .= "❌ Motif du refus : {$proposal->rejection_reason}\n";
                $rejectionHistory .= "💰 Prix proposé : {$proposal->total_price} €\n";
                $rejectionHistory .= "🎪 Festival : {$proposal->festival->name}\n\n";
            }

            $rejectionHistory .= "⚠️ **IMPORTANT** : Prends en compte ces refus pour proposer quelque chose de différent et mieux adapté.\n\n";
        }

        return <<<EOT
Tu es un assistant de voyage IA spécialisé dans l'organisation de séjours sur mesure incluant des festivals de musique.

Voici les informations du client :

- 🎵 Genres musicaux préférés : {$this->formatList($request->genres)}
- 💰 Budget maximum à ne pas dépasser : {$request->budget} €
- 📅 Dates de voyage : du {$request->date_start->format('d/m/Y')} au {$request->date_end->format('d/m/Y')} ({$duration} jours)
- 🌍 Région souhaitée : {$request->region}
- 👥 Nombre de personnes : {$request->people_count}
- 🧠 Goûts culturels : {$this->formatList($request->cultural_tastes)}
- 🧭 Type d'aventure : {$request->adventure_type}
- ⚠️ Phobies à éviter : {$this->formatList($request->phobias)}
- 🚫 Allergies à prendre en compte : {$this->formatList($request->allergies)}

Festival sélectionné :

- 🪩 Nom : {$festival->name}
- 📆 Dates : du {$festival->start_date->format('d/m/Y')} au {$festival->end_date->format('d/m/Y')}
- 📍 Lieu : {$festival->location}, {$festival->region}
- 📝 Description : {$festival->description}{$rejectionHistory}

---

🎯 **Objectif** :

Propose un **programme de séjour immersif et cohérent** de {$duration} jours qui intègre ce festival, avec :

- 🛌 Hébergement adapté
- 🚗 Transports sécurisés
- 🎭 Activités culturelles liées aux goûts du client
- 🍽️ Repas si possible
- 👁️‍🗨️ Respect des phobies et allergies

---

⚠️ **Très important :**

1. **Respecte strictement le budget de {$request->budget} € TTC**
2. Pour chaque élément du séjour, indique clairement :
   - Un **titre**
   - Une **brève description**
   - Un **prix TTC** en euros
3. Termine par un **récapitulatif clair des coûts** :

Format attendu :

Récapitulatif :

Transport : xxx €

Hébergement : xxx €

Activités : xxx €

Pass Festival : xxx €

💶 Prix total TTC : xxx €
Si le budget est dépassé, **ne le dépasse pas**. Propose plutôt une version optimisée (durée plus courte, alternatives économiques, etc.)

Formate la réponse pour qu'elle soit :
- Facile à lire
- Claire et professionnelle
- Facile à extraire pour une application web (avec sections bien séparées)
EOT;
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
                ['role' => 'system', 'content' => 'Tu es un assistant de voyage IA. Sois structuré, professionnel et convivial.'],
            ];

            // OPTIMISATION : Limiter l'historique aux 3 dernières propositions
            $previousProposals = $request->proposals()
                ->whereIn('status', ['generated', 'rejected'])
                ->limit(3)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($previousProposals as $proposal) {
                // OPTIMISATION : Tronquer les contenus trop longs
                $promptText = strlen($proposal->prompt_text) > 1000 
                    ? substr($proposal->prompt_text, 0, 1000) . '...' 
                    : $proposal->prompt_text;
                    
                $responseText = strlen($proposal->response_text) > 2000 
                    ? substr($proposal->response_text, 0, 2000) . '...' 
                    : $proposal->response_text;

                $messages[] = [
                    'role' => 'user',
                    'content' => "Proposition précédente #{$proposal->id} :\n{$promptText}"
                ];

                $messages[] = [
                    'role' => 'assistant',
                    'content' => $responseText
                ];

                if ($proposal->status === 'rejected' && $proposal->rejection_reason) {
                    $messages[] = [
                        'role' => 'user',
                        'content' => "Cette proposition a été refusée. Motif : {$proposal->rejection_reason}"
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            // OPTIMISATION : Réduire la température pour des réponses plus rapides
            $response = $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => $messages,
                'temperature' => 0.5, // Réduire de 0.7 à 0.5
                'max_tokens' => 2000, // Limiter la longueur de réponse
            ]);

            return $response->choices[0]->message->content ?? null;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la communication avec l\'IA', [
                'error' => $e->getMessage()
            ]);
            return null;
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
        preg_match('/prix total.+?(\d+[.,]?\d*)\s*€?/i', $aiResponse, $matchesTotal);
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
        preg_match_all('/\*\*(.+?)\*\*[\s:-]+(.+?)\s+-\s+(\d+[.,]?\d*)\s*€/i', $aiResponse, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            ProposalDetail::create([
                'proposal_id' => $proposal->id,
                'name'        => trim($match[1]),
                'description' => trim($match[2]),
                'price'       => (float) str_replace(',', '.', $match[3]),
            ]);
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
