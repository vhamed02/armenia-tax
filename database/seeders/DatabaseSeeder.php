<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KycProfileSeeder::class,
            BankAccountSeeder::class,
            TransactionSeeder::class,
            AdminSeeder::class,
            ServiceProviderSeeder::class,
        ]);
    }
}
