<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public $fillable = [
        'name',
        'image_url',
        'colorway',
        'store_url',
    ];

    public function itemSizes(): HasMany
    {
        return $this->hasMany(ItemSize::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Item $item) {
            // * `deleting` event is only fired on single model instances.
            foreach($item->itemSizes as $itemSize){
                $itemSize->delete();
            }
        });
    }
}
