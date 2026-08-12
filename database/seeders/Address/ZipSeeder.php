<?php

namespace Database\Seeders\Address;

use App\Models\Address\City;
use App\Models\Address\Zip;
use Illuminate\Database\Seeder;
use function storage_path;

class ZipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = storage_path('seeder'. DIRECTORY_SEPARATOR . 'telepulesek.csv');

        $data = array_map(function ($row) {
            return str_getcsv($row, "\t");
        }, file($csv));

        $cities = []; // Már beszúrt városok
        $zips = []; // Már beszúrt irányítószámok

        \DB::beginTransaction();

        foreach ($data as $row) {
            $zip = $row[0];
            $city = $row[1];
            if ($row[2] == 'BU') {
                $city = 'Budapest';
            }

            if (!isset($cities[$city])) {
                $cities[$city] = City::create([
                    'name' => $city,
                    'country_id' => 1
                ]);
            }
            if (!isset($zips[$zip])) {
                $zips[$zip] = Zip::create([
                    'name' => $zip,
                    'country_id' => 1
                ]);
            }

            $cityModel = $cities[$city];
            $zipModel = $zips[$zip];

            $zipModel->cities()->attach($cityModel);

        }

        \DB::commit();
    }
}
