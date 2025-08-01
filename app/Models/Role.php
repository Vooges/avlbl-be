<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $fillable = [
        'value',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
