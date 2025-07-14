<?php

namespace Database\Seeders;

use App\Models\Phobia;
use App\Services\GoogleSheetReader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhobiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GoogleSheetReader $sheetReader): void
    {
        $sheetTitle = 'Phobie';
        $spreadsheetId = '1Ld-z3WUBVcqKEHmplQJjj8dMqgEvuH-eRhsP7E8b8VY';

        $rows = $sheetReader->getSheetData($spreadsheetId, $sheetTitle);

        if ($rows) {
            foreach ($rows as $row) {
                $name = trim($row[0] ?? '');
                $description = trim($row[1] ?? '');
                if ($name !== '') {
                    Phobia::firstOrCreate(['name' => $name, 'description' => $description]);
                }
            }
        }

        sleep(1); // Optional: sleep to avoid hitting API rate limits
    }
}
