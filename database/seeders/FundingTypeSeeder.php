<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundingType;

class FundingTypeSeeder extends Seeder {
    public function run(): void {
        $types = [
            [
                'name' => 'Subvention Agriculture Durable',
                'code' => 'agri_sub_2024',
                'description' => 'Financement pour projets agricoles écologiques',
                'amount' => 5000000,
                'registration_fee' => 25000,
                'duration_months' => 24,
                'required_documents' => ['identity', 'land_title', 'business_plan', 'technical_study'],
                'category' => 'agriculture',
            ],
            [
                'name' => 'Fonds Commerce Local',
                'code' => 'commerce_local',
                'description' => 'Soutien aux petits commerces',
                'amount' => 2000000,
                'registration_fee' => 15000,
                'duration_months' => 12,
                'required_documents' => ['identity', 'trade_register', 'tax_certificate', 'business_plan'],
                'category' => 'commerce',
            ],
        ];

        foreach ($types as $type) {
            FundingType::create($type);
        }
    }
}
