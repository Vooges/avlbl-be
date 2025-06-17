<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemSize;
use App\Models\Item;
use App\Models\Availability;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemSize>
 */
class ItemSizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>

     */
    protected $model = ItemSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->randomNumber(2, true),
            'item_id' => Item::inRandomOrder()->first()->id,
            'availability_id' => Availability::inRandomOrder()->first()->id,
        ];
    }
}
