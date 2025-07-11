<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client;
use Google\Service\Sheets;


class FetchGoogleSheetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-sheets:fetch';

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
        // $sheetTitle = $this->argument('sheet');
        $sheetTitle = 'Allergie';

        $this->info("Lecture des données de la feuille : $sheetTitle");

        // Configure Google Client
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets App');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        // ID de ta Google Sheet (à modifier avec le tien)
        $spreadsheetId = '1Ld-z3WUBVcqKEHmplQJjj8dMqgEvuH-eRhsP7E8b8VY'; // <-- Remplace ici

        // Formater le nom de la feuille (avec quotes si nécessaire)
        $range = "'$sheetTitle'";  // Toutes les données de la feuille

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                $this->warn("Aucune donnée trouvée dans la feuille : $sheetTitle");
                return;
            }

            $this->info("Données récupérées :");
            foreach ($values as $row) {
                $this->line(implode(' | ', $row));
            }
        } catch (\Exception $e) {
            $this->error("Erreur : " . $e->getMessage());
        }
    }
}
