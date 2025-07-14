<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FestivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/festivals_translated_fr_cleaned.csv');

        if (!File::exists($path)) {
            return;
        }

        $csv = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($csv));

        foreach ($csv as $row) {
            $data = array_combine($header, $row);

            Festival::create([
                'name'         => $data['name'],
                'url'          => $data['url'],
                'image'        => $data['image'],
                'start_date'   => $data['startDate'],
                'end_date'     => $data['endDate'],
                'description'  => [
                    'en' => $data['description'],
                    'fr' => $data['description_fr'],
                ],
                'location'     => $data['location'],
                'city'         => $data['city'],
                'region'       => $data['region'],
                'page'         => (int) $data['page'],
                'region_abbr'  => $data['region_abbr'],
            ]);
        }
    }
}
