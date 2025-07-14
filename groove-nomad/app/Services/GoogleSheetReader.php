<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetReader
{
    protected Client $client;
    protected Sheets $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Laravel Google Sheets App');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->client->setAuthConfig(storage_path('app/private/google/credentials.json'));
        $this->client->setAccessType('offline');

        $this->service = new Sheets($this->client);
    }

    /**
     * Récupère les valeurs d’une feuille Google Sheet
     *
     * @param string $spreadsheetId ID de la Google Sheet
     * @param string $sheetTitle Titre de la feuille
     * @return array|null Les lignes ou null en cas d’erreur
     */
    public function getSheetData(string $spreadsheetId, string $sheetTitle): ?array
    {
        $range = "'$sheetTitle'";

        try {
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (\Exception $e) {
            Log::error("Erreur lors de la lecture de la feuille Google Sheet : " . $e->getMessage());
            return null;
        }
    }
}
