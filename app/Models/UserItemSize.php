<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserItemSize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $fillable = [
        'user_id',
        'item_size_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemSize()
    {
        return $this->belongsTo(ItemSize::class);
    }
}
