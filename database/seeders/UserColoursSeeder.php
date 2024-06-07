<?php

namespace Database\Seeders;

use App\Models\Colour;
use App\Models\User;
use App\Models\UserColour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserColoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $colours = Colour::all();

        if (count($users) > 0) {
            foreach ($users as $user) {

                $user_colours = $colours->random(2);
                
                UserColour::create([
                    'user_id' => $user->id,
                    'colour_id' => $user_colours[0]['id']
                ]);
                UserColour::create([
                    'user_id' => $user->id,
                    'colour_id' => $user_colours[1]['id']
                ]);

            }
        }
    }
}
