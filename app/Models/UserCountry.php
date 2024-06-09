<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCountry extends Model
{
    use HasFactory;

    protected $table = 'user_countries';
    protected $fillable = ['user_id', 'country_id'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
