<?php

namespace Database\Seeders\Address;

use App\Models\Address\StreetType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StreetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $url = getenv("STREET_TYPE_SOURCE_URL");
        if (!$url) {
            $url = 'https://ceginformaciosszolgalat.kormany.hu/download/b/46/11000/kozterulet_jelleg_2015_09_07.txt';
        }

        $file = fopen($url, "r");

        if ($file) {

            $i = 1;

            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                StreetType::create([
                    'id' => $i,
                    'name' => ucfirst($line),
                    'short_name' => $line
                ]);

                $i++;
            }

            fclose($file);
        }

    }
}
