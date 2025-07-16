<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncRequestsToAirtable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:sync-to-airtable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie une donnée d\'exemple vers Airtable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = config('services.airtable.api_key');
        $baseId = config('services.airtable.base_id');
        $tableName = "Demande";

        $url = "https://api.airtable.com/v0/{$baseId}/{$tableName}";

        $data = [
            'fields' => [
                'Name' => 'Tsiory Rovantsoa',
                'email' => 'tsioryrovantsoa@gmail.com',
            ],
        ];

        $response = Http::withToken($apiKey)
            ->post($url, $data);

        if ($response->successful()) {
            $this->info("Donnée d'exemple envoyée avec succès vers Airtable !");
        } else {
            $this->error("Erreur lors de l'envoi vers Airtable: " . $response->body());
        }

        $this->info('Traitement terminé !');
    }
} 