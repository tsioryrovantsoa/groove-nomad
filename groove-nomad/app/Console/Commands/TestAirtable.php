<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestAirtable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-airtable';

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
        $apiKey = config('services.airtable.api_key');
        $baseId = config('services.airtable.base_id');
        $tableName = "Test";

        $url = "https://api.airtable.com/v0/{$baseId}/{$tableName}";

        $data = [
            'fields' => [
                'Nom Client' => 'Djo',
                'Email' => 'salohiniaina.fanaingamampiandra@epitech.digital',
                'Festival Choisi' => 'Ton Festival',
                'Statut' => 'Todo',
            ],
        ];

        $response = Http::withToken($apiKey)
            ->post($url, $data);

        if ($response->successful()) {
            $this->info('Données envoyées avec succès vers Airtable !');
        } else {
            $this->error('Erreur : ' . $response->body());
        }
    }
}
