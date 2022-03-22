<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Customer;
use App\Models\Campaign;
use App\Models\PurchaseTransaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Customer::factory(10)
        //     ->hasPurchaseTransactions(rand(1, 5))
        //     ->create();

        // Campaign::factory(2)
        //     ->hasVouchers(rand(10, 100))
        //     ->create();

        Customer::factory(10)
            ->hasPurchaseTransactions(4)
            ->create();

        Campaign::factory(1)
            ->hasVouchers(3)
            ->create();
    }
}
