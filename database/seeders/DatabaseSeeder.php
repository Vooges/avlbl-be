<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\ItemSize;
use App\Models\UserItemSize;
use App\Models\Availability;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AvailabilitySeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ItemSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AvailabilitySeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ItemSeeder::class,
        ]);
    }
}
