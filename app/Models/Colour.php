<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colour extends Model
{
    use HasFactory;

    protected $table = 'colours';
    protected $fillable = ['name'];

    public function user_colour(): HasMany
    {
        return $this->hasMany(UserColour::class, 'colour_id');
    }
}
