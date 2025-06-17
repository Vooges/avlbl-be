<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserItemSize;
use App\Models\User;
use App\Models\ItemSize;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserItemSize>
 */
class UserItemSizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>

     */
    protected $model = UserItemSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'item_size_id' => ItemSize::inRandomOrder()->first()->id,
        ];
    }
}
