<?php

namespace App\Console\Commands;

use App\Models\Festival;
use App\Models\Proposal;
use App\Models\ProposalDetail;
use App\Models\Request;
use Illuminate\Console\Command;
use OpenAI;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $request = Request::find(1);

        if (!$request) {
            $this->error('Request not found.');
            return;
        }

        // 1. Recherche d’un festival correspondant à la région et aux dates
        $festival = Festival::where('region', $request->region)
            ->whereDate('start_date', '<=', $request->date_end)
            ->whereDate('end_date', '>=', $request->date_start)
            ->inRandomOrder()
            ->first();

        if (!$festival) {
            $this->warn("No matching festival found for region '{$request->region}' and dates {$request->date_start->format('Y-m-d')} to {$request->date_end->format('Y-m-d')}");
            return;
        }

        $this->info("🎉 Festival found: {$festival->name} ({$festival->start_date} - {$festival->end_date}) in {$festival->region}");

        $duration = $request->date_start->diffInDays($request->date_end) + 1;

        $duration = $request->date_start->diffInDays($request->date_end) + 1;

        $prompt = <<<EOT
Tu es un assistant de voyage IA spécialisé dans l'organisation de séjours sur mesure incluant des festivals de musique.

Voici les informations du client :

- 🎵 Genres musicaux préférés : {$this->list($request->genres)}
- 💰 Budget maximum à ne pas dépasser : {$request->budget} €
- 📅 Dates de voyage : du {$request->date_start->format('d/m/Y')} au {$request->date_end->format('d/m/Y')} ({$duration} jours)
- 🌍 Région souhaitée : {$request->region}
- 👥 Nombre de personnes : {$request->people_count}
- 🧠 Goûts culturels : {$this->list($request->cultural_tastes)}
- ⚠️ Phobies à éviter : {$this->list($request->phobias)}
- 🚫 Allergies à prendre en compte : {$this->list($request->allergies)}

Festival sélectionné :

- 🪩 Nom : {$festival->name}
- 📆 Dates : du {$festival->start_date->format('d/m/Y')} au {$festival->end_date->format('d/m/Y')}
- 📍 Lieu : {$festival->location}, {$festival->region}
- 📝 Description : {$festival->description}

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

        $messages = [
            ['role' => 'system', 'content' => 'Tu es un assistant de voyage IA. Sois structuré, professionnel et convivial.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $client = OpenAI::client(config('services.open_ai.api_key'));

        $response = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => $messages,
            'temperature' => 0.7,
        ]);

        $aiResponse = $response->choices[0]->message->content ?? null;

        if (!$aiResponse) {
            $this->error('❌ Aucune réponse reçue de l’IA.');
            return;
        }

        $this->info("\n💬 Réponse IA :\n" . $aiResponse);

        preg_match('/prix total.+?(\d+[.,]?\d*)\s*€?/i', $aiResponse, $matchesTotal);
        $totalPrice = isset($matchesTotal[1]) ? (float) str_replace(',', '.', $matchesTotal[1]) : 0;

        $proposal = Proposal::create([
            'request_id'     => $request->id,
            'festival_id'    => $festival->id,
            'prompt_text'    => $prompt,
            'response_text'  => $aiResponse,
            'total_price'    => $totalPrice,
            'status'         => 'generated',
        ]);

        preg_match_all('/\*\*(.+?)\*\*[\s:-]+(.+?)\s+-\s+(\d+[.,]?\d*)\s*€/i', $aiResponse, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            ProposalDetail::create([
                'proposal_id' => $proposal->id,
                'name'        => trim($match[1]),
                'description' => trim($match[2]),
                'price'       => (float) str_replace(',', '.', $match[3]),
            ]);
        }

        $this->info("✅ Proposition créée avec ID : {$proposal->id}");
    }

    private function list($value)
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return trim(str_replace(['[', ']', '"'], '', $value));
    }
}
