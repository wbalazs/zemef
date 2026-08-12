<?php

namespace Database\Seeders\Address;

use App\Models\Address\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Magyarország',
        ]);

        Country::create([
            'name' => 'Amerikai Egyesült Államok'
        ]);

        Country::create([
            'name' => 'Németország'
        ]);

        Country::create([
            'name' => 'Anglia'
        ]);
    }
}
