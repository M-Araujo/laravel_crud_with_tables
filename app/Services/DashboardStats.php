<?php

namespace App\Services;

use App\Models\Colour;
use App\Models\Country;
use App\Models\User;

class DashboardStats
{
    public function stats(): array
    {
        $users = User::all();
        $countries = Country::all();
        $colours = Colour::all();

        return [
            'total_users' => $users->count(),
            'total_countries' => $countries->count(),
            'total_colours' => $colours->count(),
            'has_kids' => $users->where('has_kids', 1)->count(),
            'user_colours' => Colour::withCount('user_colour as count')->get(),
            'user_countries' => Country::withCount('user_country as count')->get(),
            'has_kids_chartdata' => $users->groupBy('has_kids')
        ];
    }
}