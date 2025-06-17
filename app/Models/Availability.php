<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Availability extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $fillable = [
        'value'
    ];

    public function itemSizes(): BelongsToMany
    {
        return $this->belongsToMany(ItemSize::class);
    }
}
