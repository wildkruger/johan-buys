<?php

namespace Modules\MerchantPayLink\Entities;

use App\Models\User;
use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfilePayment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'currency_id', 'payment_method_id', 'gateway_reference', 'payer_details', 'uuid', 'charge_percentage', 'charge_fixed', 'amount', 'total', 'status'];
    
    protected $casts = [
        'payer_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', "Active");
    }

    public function createProfilePayment($data)
    {
        $profilePayment = new ProfilePayment();
        $profilePayment->user_id = $data['user_id'];
        $profilePayment->currency_id = $data['currency_id'];
        $profilePayment->payment_method_id = $data['payment_method_id'];
        $profilePayment->gateway_reference = $data['gateway_reference'] ?? null;
        $profilePayment->payer_details = $data['payer_details'] ?? null;
        $profilePayment->uuid = $data['uuid'];
        $profilePayment->charge_percentage = $data['charge_percentage'];
        $profilePayment->charge_fixed = $data['charge_fixed'];
        $profilePayment->amount = $data['amount'];
        $profilePayment->total = $data['total'];
        $profilePayment->status = $data['status'];
        $profilePayment->save();

        return $profilePayment;
    }
}
