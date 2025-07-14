<?php

namespace App\Console\Commands;

use App\Models\MusicGenre;
use App\Services\GoogleSheetReader;
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
    public function handle(GoogleSheetReader $sheetReader)
    {
        $sheetTitle = 'Genre';
        $spreadsheetId = '1Ld-z3WUBVcqKEHmplQJjj8dMqgEvuH-eRhsP7E8b8VY';

        $rows = $sheetReader->getSheetData($spreadsheetId, $sheetTitle);

        if ($rows) {
            $name = trim($row[0] ?? '');
            dd($name);

            if ($name !== '') {
                MusicGenre::firstOrCreate(['name' => $name]);
            }
        }
    }
}
