<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCountry extends Model
{
    use HasFactory;

    protected $table = 'user_countries';
    protected $fillable = ['user_id', 'country_id'];
}
