<?php
// database/seeders/MobilePaymentConfigSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MobilePaymentConfig;

class MobilePaymentConfigSeeder extends Seeder {
    public function run(): void {
        $configs = [
            [
                'country' => 'senegal',
                'operator_name' => 'Orange Money',
                'operator_code' => 'orange',
                'merchant_code' => '123456',
                'ussd_pattern' => '*144*4*1*{merchant_code}*{amount}*{motif}#',
                'payment_instructions' => "1. Composez le code\n2. Entrez votre code PIN\n3. Confirmez avec le motif",
            ],
            [
                'country' => 'senegal',
                'operator_name' => 'Wave',
                'operator_code' => 'wave',
                'merchant_code' => 'WAVE789',
                'ussd_pattern' => '*220*{motif}*1*{amount}#',
                'payment_instructions' => "1. Ouvrez l'app Wave\n2. Scannez ou entrez le code\n3. Validez",
            ],
            [
                'country' => 'cote_ivoire',
                'operator_name' => 'Orange Money CI',
                'operator_code' => 'orange_ci',
                'merchant_code' => 'CI12345',
                'ussd_pattern' => '*144*1*{merchant_code}*{amount}*{motif}#',
                'payment_instructions' => "1. Composez le code\n2. Validez avec votre PIN",
            ],
        ];

        foreach ($configs as $config) {
            MobilePaymentConfig::create($config);
        }
    }
}
