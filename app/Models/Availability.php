<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function itemSizes()
    {
        return $this->belongsToMany(ItemSize::class);
    }
}
