<?php

namespace Modules\MerchantPayLink\Services;

use App\Models\User;
use Modules\MerchantPayLink\Entities\ProfilePayment;

class PaylinkService
{
    protected $profilePayment;

    public function __construct(ProfilePayment $profilePayment)
    {
        $this->profilePayment = $profilePayment;
    }

    public function getUserByPaylinkCode($paylinkCode)
    {
        return User::with('merchant', 'wallets', 'wallets.currency')
            ->where('profile_paylink_code', $paylinkCode)
            ->where('role_id', 3)
            ->first();
    }
}
