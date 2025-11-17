<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Car Seeder
 * 
 * Seeds the database with sample cars matching the problem example.
 * Order: Car D (blue) -> Car B (red) -> Car E (blue) -> Car C (red) -> Car A (blue)
 */
class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cars matching the example from the problem description
        $cars = [
            ['name' => 'Car D', 'color' => 'blue', 'position' => 1000],
            ['name' => 'Car B', 'color' => 'red', 'position' => 2000],
            ['name' => 'Car E', 'color' => 'blue', 'position' => 3000],
            ['name' => 'Car C', 'color' => 'red', 'position' => 4000],
            ['name' => 'Car A', 'color' => 'blue', 'position' => 5000],
        ];

        foreach ($cars as $carData) {
            Car::create($carData);
        }

        // Optionally create more cars for testing with larger datasets
        if (env('SEED_LARGE_DATASET', false)) {
            $colors = ['blue', 'red', 'green', 'yellow', 'black', 'white'];
            $position = 6000;
            
            for ($i = 1; $i <= 100; $i++) {
                Car::create([
                    'name' => "Test Car $i",
                    'color' => $colors[array_rand($colors)],
                    'position' => $position + ($i * 1000),
                ]);
            }
        }
    }
}
