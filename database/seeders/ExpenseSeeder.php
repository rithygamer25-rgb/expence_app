<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\User;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure at least one test user exists to assign these expenses to
        $user = User::first() ?? User::create([
            'name' => 'Na Rithy',
            'email' => 'rithy@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Grab lookup ids from your previously seeded database items
        $foodCategory = Category::where('name', 'Food & Dining')->first();
        $shoppingCategory = Category::where('name', 'Shopping')->first();
        $transportCategory = Category::where('name', 'Transportation')->first();

        $creditCardMethod = PaymentMethod::where('name', 'Credit Card')->first();
        $cashMethod = PaymentMethod::where('name', 'Cash')->first();

        // 3. Define the demo data array
        $mockExpenses = [
            [
                'user_id'           => $user->id,
                'location'          => 'Starbucks Coffee',
                'date'              => '2026-06-12',
                'amount'            => 12.50,
                'category_id'       => $foodCategory?->id ?? 1,
                'payment_method_id' => $creditCardMethod?->id ?? 1,
            ],
            [
                'user_id'           => $user->id,
                'location'          => 'Supermarket Plaza',
                'date'              => '2026-06-11',
                'amount'            => 64.20,
                'category_id'       => $shoppingCategory?->id ?? 2,
                'payment_method_id' => $cashMethod?->id ?? 2,
            ],
            [
                'user_id'           => $user->id,
                'location'          => 'Gas & Fuel Station',
                'date'              => '2026-06-08',
                'amount'            => 35.00,
                'category_id'       => $transportCategory?->id ?? 3,
                'payment_method_id' => $creditCardMethod?->id ?? 1,
            ],
        ];

        // 4. Persist data into your expenses table
        foreach ($mockExpenses as $expense) {
            Expense::create($expense);
        }
    }
}
