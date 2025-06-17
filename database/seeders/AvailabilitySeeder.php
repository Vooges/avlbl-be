<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Availability;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            'In stock',
            'Out of stock',
            'Not retrieved yet',
        ];

        foreach($values as $value){
            Availability::firstOrCreate(['value' => $value]);
        }
    }
}
