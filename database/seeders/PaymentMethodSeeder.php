<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultMethods = [
            ['user_id' => null, 'name' => 'Cash'],
            ['user_id' => null, 'name' => 'QR AC'],
            ['user_id' => null, 'name' => 'QR ABA'],
            ['user_id' => null, 'name' => 'Credit Card'],
            ['user_id' => null, 'name' => 'Bank Transfer'],
        ];

        foreach ($defaultMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
