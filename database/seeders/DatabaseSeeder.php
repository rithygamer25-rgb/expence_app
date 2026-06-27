<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call your customized lookup table seeders
        $this->call([
            CategorySeeder::class,
            PaymentMethodSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
