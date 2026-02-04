<?php
// app/Models/MobilePaymentConfig.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilePaymentConfig extends Model
{
    protected $fillable =
    [
        'country',
        'operator_name',
        'operator_code',
        'merchant_code',
        'ussd_pattern',
        'payment_instructions',
        'is_active'
    ];
}
