<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property integer user_id
 * @property integer payment_method_id
 * @property integer type
 * @property float base_amount
 * @property integer base_currency_id
 * @property float target_amount
 * @property integer target_currency_id
 * @property float exchange_rate
 * @property string ip_address
 */
class Transaction extends Model
{
    use HasFactory;

    public const TYPE_DEPOSIT = 1;
    public const TYPE_WITHDRAW = 2;

    public function user()
    {
        return $this->hasOne(User::class,'id', 'user_id');
    }

    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class,'id', 'payment_method_id');
    }

    public function baseCurrency()
    {
        return $this->hasOne(Currency::class,'id', 'base_currency_id');
    }

    public function targetCurrency()
    {
        return $this->hasOne(Currency::class,'id', 'target_currency_id');
    }

    public static function getAllOfTheUser(int $userId): Collection
    {
        return self::where('user_id', $userId)->get();
    }
}
