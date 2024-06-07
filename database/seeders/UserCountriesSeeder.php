<?php

namespace Database\Seeders;

use App\Models\Colour;
use App\Models\Country;
use App\Models\User;
use App\Models\UserColour;
use App\Models\UserCountry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserCountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $countries = Country::all();
        
        if (count($users) > 0) {
            foreach ($users as $user) {

                UserCountry::create([
                    'user_id' => $user->id,
                    'country_id' => $countries->random(1)[0]['id']
                ]);
            }
        }
    }
}
