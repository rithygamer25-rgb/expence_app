<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            [
                'user_id' => null, // Global default option
                'name' => 'Food & Dining',
                'icon' => 'bi-shop',
                'color_theme' => 'primary',
            ],
            [
                'user_id' => null,
                'name' => 'Shopping',
                'icon' => 'bi-cart',
                'color_theme' => 'success',
            ],
            [
                'user_id' => null,
                'name' => 'Transportation',
                'icon' => 'bi-fuel-pump',
                'color_theme' => 'warning',
            ],
            [
                'user_id' => null,
                'name' => 'Bills & Utilities',
                'icon' => 'bi-lightning-charge',
                'color_theme' => 'danger',
            ],
        ];

        foreach ($defaultCategories as $category) {
            Category::create($category);
        }
    }
}
