<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemSize extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $fillable = [
        'value',
        'item_id',
        'availability_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }

    public function userItemSizes()
    {
        return $this->hasMany(UserItemSize::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (ItemSize $itemSize) {
            $itemSize->userItemSizes()->delete();
        });
    }
}
