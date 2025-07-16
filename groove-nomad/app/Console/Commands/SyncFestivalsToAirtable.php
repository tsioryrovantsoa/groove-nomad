<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Festival;

class SyncFestivalsToAirtable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'festivals:sync-to-airtable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise tous les festivals de la base de données vers Airtable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('services.airtable.api_key');
        $baseId = config('services.airtable.base_id');
        $tableName = "Festivals";

        $url = "https://api.airtable.com/v0/{$baseId}/{$tableName}";

        // Récupérer tous les festivals
        $festivals = Festival::all();

        $this->info("Récupération de {$festivals->count()} festivals...");

        foreach ($festivals as $festival) {
            $data = [
                'fields' => [
                    'name' => $festival->name,
                    'url' => $festival->url,
                    'image' => $festival->image,
                    'start_date' => $festival->start_date ? $festival->start_date->format('Y-m-d') : null,
                    'end_date' => $festival->end_date ? $festival->end_date->format('Y-m-d') : null,
                    'description' => $festival->description,
                    'location' => $festival->location,
                    'city' => $festival->city,
                    'region' => $festival->region,
                    'page' => $festival->page,
                    'region_abbr' => $festival->region_abbr,
                ],
            ];

            $response = Http::withToken($apiKey)
                ->post($url, $data);

            if ($response->successful()) {
                $this->info("Festival '{$festival->name}' envoyé avec succès vers Airtable !");
            } else {
                $this->error("Erreur pour le festival '{$festival->name}': " . $response->body());
            }
        }

        $this->info('Traitement terminé !');
    }
} 