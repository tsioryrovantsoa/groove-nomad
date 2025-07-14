<?php

namespace Database\Seeders;

use App\Models\MusicGenre;
use App\Services\GoogleSheetReader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MusicGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GoogleSheetReader $sheetReader): void
    {
        $sheetTitle = 'Genre';
        $spreadsheetId = '1Ld-z3WUBVcqKEHmplQJjj8dMqgEvuH-eRhsP7E8b8VY';

        $rows = $sheetReader->getSheetData($spreadsheetId, $sheetTitle);

        if ($rows) {
            foreach ($rows as $row) {
                $name = trim($row[0] ?? '');
                if ($name !== '') {
                    MusicGenre::firstOrCreate(['name' => $name]);
                }
            }
        }

        sleep(1); // Optional: sleep to avoid hitting API rate limits
    }
}
