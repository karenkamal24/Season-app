<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountriesSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(EmergencyNumbersSeeder::class);
        $this->call(BagTypesSeeder::class);
        $this->call(ItemCategoriesSeeder::class);
        $this->call(ItemsSeeder::class);
        $this->call(PackingTipsSeeder::class);
    }
}
