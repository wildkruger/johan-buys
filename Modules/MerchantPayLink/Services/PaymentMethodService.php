<?php

namespace Modules\MerchantPayLink\Services;

use App\Models\User;
use App\Models\FeesLimit;
use App\Models\CurrencyPaymentMethod;

class PaymentMethodService
{
    protected $feesLimit;
    protected $currencyPaymentMethod;

    public function __construct(FeesLimit $feesLimit, CurrencyPaymentMethod $currencyPaymentMethod)
    {
        $this->feesLimit = $feesLimit;
        $this->currencyPaymentMethod = $currencyPaymentMethod;
    }

    public function getUserByPaylinkCode($paylinkCode)
    {
        return User::with('merchant', 'wallets', 'wallets.currency')
            ->where('profile_paylink_code', $paylinkCode)
            ->first();
    }

    public function getActivePaymentMethods($transactionTypeId, $currencyId)
    {
        $feesLimits = $this->getActiveFeesLimits($transactionTypeId, $currencyId);
        $currencyPaymentMethods = $this->getCurrencyPaymentMethods($currencyId);

        return $this->filterAndFormatPaymentMethods($feesLimits, $currencyPaymentMethods);
    }

    private function getActiveFeesLimits($transactionTypeId, $currencyId)
    {
        return $this->feesLimit->with(['currency', 'payment_method'])
            ->where([
                'transaction_type_id' => $transactionTypeId,
                'has_transaction' => 'Yes',
                'currency_id' => $currencyId,
            ])
            ->whereHas('currency', function ($query) {
                $query->where('status', 'Active');
            })
            ->whereHas('payment_method', function ($query) {
                $query->where('status', 'Active');
            })
            ->get(['payment_method_id']);
    }

    private function getCurrencyPaymentMethods($currencyId)
    {
        return $this->currencyPaymentMethod->where('currency_id', $currencyId)
            ->where('activated_for', 'like', '%deposit%')
            ->pluck('method_id')
            ->toArray();
    }

    private function filterAndFormatPaymentMethods($feesLimits, $currencyPaymentMethods)
    {
        return $feesLimits->filter(function ($feesLimit) use ($currencyPaymentMethods) {
            return in_array($feesLimit->payment_method_id, $currencyPaymentMethods);
        })->map(function ($feesLimit) {
            return [
                'id' => $feesLimit->payment_method_id,
                'name' => $feesLimit->payment_method->name,
            ];
        })->keyBy('id')->toArray();
    }
}
