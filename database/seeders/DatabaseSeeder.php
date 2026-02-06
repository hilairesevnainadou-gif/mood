<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            RequiredDocumentsSeeder::class,
            FundingTypeSeeder::class,
            MobilePaymentConfigSeeder::class,

        ]);
    }
}
