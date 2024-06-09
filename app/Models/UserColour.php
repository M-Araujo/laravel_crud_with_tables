<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserColour extends Model
{
    use HasFactory;

    protected $table = 'user_colours';
    protected $fillable = ['user_id', 'colour_id'];


    public function colour(): BelongsTo
    {
        return $this->belongsTo(Colour::class);
    }
}
