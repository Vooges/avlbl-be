<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\UserItemSize;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::factory(25)
            ->has(ItemSize::factory(5)
                ->has(UserItemSize::factory(2)))
            ->create();
    }
}
