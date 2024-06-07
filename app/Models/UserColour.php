<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserColour extends Model
{
    use HasFactory;

    protected $table = 'user_colours';
    protected $fillable = ['user_id', 'colour_id'];
}
