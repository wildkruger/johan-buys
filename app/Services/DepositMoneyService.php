<?php

/**
 * @package DepositMoneyService
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 * @created 21-12-2022
 */



namespace App\Services;

use App\Exceptions\Api\V2\{
    PaymentFailedException,
    DepositMoneyException
};
use App\Http\Helpers\Common;
use App\Models\{
    CurrencyPaymentMethod,
    PaymentMethod,
    FeesLimit,
    Deposit,
    Wallet,
    Bank
};
use Illuminate\Support\Facades\DB;

class DepositMoneyService
{
    /**
     * @var Common
     */
    private $helper;

    public function __construct(Common $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Get the currencies list for deposit
     *
     * @return void
     */
    public function getSelfCurrencies()
    {
        $result = [
            "currencies" => []
        ];

        Wallet::with("currency:id,code,type")
            ->where("user_id", auth()->id())
            ->whereHas("active_currency")
            ->join("fees_limits", "fees_limits.currency_id", "wallets.currency_id")
            ->where("fees_limits.has_transaction", "Yes")
            ->where("fees_limits.transaction_type_id", Deposit)
            ->get()
            ->map(function ($item) use (&$result) {
                if ($item->is_default == "Yes") {
                    $result["default"] = $item->currency_id;
                }
                $result["currencies"][$item->currency_id] = [
                    "id" => $item->currency_id,
                    "code" => optional($item->currency)->code,
                    "type" => optional($item->currency)->type
                ];
                return $item;
            });
        $result["currencies"] = array_values($result["currencies"]);
        return $result;
    }


    public function getBanklist($currencyId)
    {
        $banks = Bank::where(['currency_id' => $currencyId])->get(['id', 'bank_name', 'is_default', 'account_name', 'account_number']);
        $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $currencyId)
            ->where('activated_for', 'like', "%deposit%")
            ->where('method_data', 'like', "%bank_id%")
            ->get(['method_data']);

        $bankList = $this->bankList($banks, $currencyPaymentMethods);

        if (count($bankList) == 0) {
            throw new DepositMoneyException(__("Banks does not exist for selected currency."));
        }
        return $bankList;
    }

    public function bankList($banks, $currencyPaymentMethods)
    {
        $selectedBanks = [];
        foreach ($banks as $bank) {
            foreach ($currencyPaymentMethods as $cpm) {
                if (!empty($cpm->method_data)) {
                    $methodData = json_decode($cpm->method_data);
                }
                if (isset($methodData->bank_id) && $bank->id == $methodData->bank_id) {
                    $selectedBanks[] = [
                        'id' => $bank->id,
                        'bank_name' => $bank->bank_name,
                        'is_default' => $bank->is_default,
                        'account_name' => $bank->account_name,
                        'account_number' => $bank->account_number,
                    ];
                }
            }
        }
        return $selectedBanks;
    }


    public function getPaymentMethods($currencyId, $currencyType, $transactionType)
    {
        $condition = ($currencyType == 'fiat') ? getPaymoneySettings('payment_methods')['mobile']['fiat']['deposit'] : getPaymoneySettings('payment_methods')['mobile']['crypto']['deposit'];

        $feesLimits = FeesLimit::whereHas('currency', function ($q) {
            $q->where('status', '=', 'Active');
        })
            ->whereHas('payment_method', function ($q) use ($condition) {
                $q->whereIn('id', $condition)->where('status', '=', 'Active');
            })
            ->where(['transaction_type_id' => $transactionType, 'has_transaction' => 'Yes', 'currency_id' => $currencyId])
            ->get(['payment_method_id']);

        $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $currencyId)->where('activated_for', 'like', "%deposit%")->get(['method_id']);
        $currencyPaymentMethodFeesLimitCurrenciesList = $this->currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods);
        return $currencyPaymentMethodFeesLimitCurrenciesList;
    }

    public function currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods)
    {
        $selectedCurrencies = [];
        foreach ($feesLimits as $feesLimit) {
            foreach ($currencyPaymentMethods as $currencyPaymentMethod) {
                if ($feesLimit->payment_method_id == $currencyPaymentMethod->method_id) {
                    $selectedCurrencies[$feesLimit->payment_method_id]['id']   = $feesLimit->payment_method_id;
                    $selectedCurrencies[$feesLimit->payment_method_id]['name'] = $feesLimit->payment_method->name;
                    $selectedCurrencies[$feesLimit->payment_method_id]['alias'] = strtolower(preg_replace("/\s+/", "", $feesLimit->payment_method->name));
                }
            }
        }
        return $selectedCurrencies;
    }


    /**
     * Validate deposit amount data
     *
     * @param int $currencyId
     * @param float $amount
     * @param int $paymentMethodId
     *
     * @return void
     *
     * @throws DepositMoneyException
     */
    public function validateDepositable($currencyId, $amount, $paymentMethodId)
    {
        $success['paymentMethodName'] = PaymentMethod::where('id', $paymentMethodId)->value('name');
        $success['paymentMethodAlias'] = strtolower(preg_replace("/\s+/", "", $success["paymentMethodName"]));

        $feesDetails = $this->findOrFailFeesDetails($currencyId, $paymentMethodId);

        // Check if the wallet is in required limit
        $this->amountIsInLimit($feesDetails, $amount);

        //Code for Fees Limit Starts here
        $feesPercentage = $amount * ($feesDetails->charge_percentage / 100);
        $feesFixed = $feesDetails->charge_fixed;
        $totalFess = $feesPercentage + $feesFixed;
        $totalAmount = $amount + $totalFess;
        
        // Values for calculation
        $success['feesPercentage'] = $feesPercentage;
        $success['feesFixed'] = $feesFixed;
        $success['amount'] = $amount;
        $success['totalAmount'] = $totalAmount;

        // values for display
        $success['formatted_percentageFees'] = $feesDetails->charge_percentage ? formatNumber($feesDetails->charge_percentage) . "%" : "";
        $success['formatted_fixedFees'] = formatNumber($feesDetails->charge_fixed ?? 0);
        $success['formatted_totalFees']  = formatNumber($totalFess);
        $success['formatted_amount'] = formatNumber($amount);
        $success['formatted_totalAmount'] = formatNumber($totalAmount);

        // additional values
        $success['currency_id'] = $feesDetails->currency_id;
        $success['currencySymbol'] = $feesDetails->currency->symbol;
        $success['currencyCode'] = $feesDetails->currency->code;
        $success['currencyType'] = $feesDetails->currency->type;
        $success['min'] = $feesDetails->min_limit;
        $success['max'] = $feesDetails->max_limit;

        return $success;
    }


    /**
     * Calculate total amount
     *
     * @param FeesLimit $feesDetails
     * @param float $amount
     *
     * @return float
     */
    public function getTotalAmount(FeesLimit $feesDetails, $amount): float
    {
        return $amount + ($feesDetails->charge_fixed ?? 0) + ($amount * (($feesDetails->charge_percentage ?? 0) / 100));
    }


    /**
     * Returns FeesLimit of corresponding currency and payment method
     *
     * @param int $currencyId
     * @param int $paymentMethodId
     *
     * @return FeesLimit
     */
    public function getFeesDetails($currencyId, $paymentMethodId)
    {
        return FeesLimit::with('currency')->where(['transaction_type_id' => Deposit, 'currency_id' => $currencyId, 'payment_method_id' => $paymentMethodId, 'has_transaction' => 'Yes'])->first(['charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
    }


    /**
     * Finds corresponding FeesLimit or throws error
     *
     * @param int $currencyId
     * @param int $paymentMethodId
     *
     * @return FeesLimit
     *
     * @throws DepositMoneyException
     */
    public function findOrFailFeesDetails($currencyId, $paymentMethodId)
    {
        $feesDetails = $this->getFeesDetails($currencyId, $paymentMethodId);
        // If currency fees are not set
        if (!is_null($feesDetails)) {
            return $feesDetails;
        }
        throw new DepositMoneyException(__("Currency fees are not set."), [
            "reason" => "feesNotSet",
            "message" => __("Currency fees are not set."),
            "status" => "401"
        ]);
    }


    /**
     * Returns Wallet of corresponding currency
     *
     * @param int $currencyId
     *
     * @return Wallet
     */
    public function getWallet($currencyId)
    {
        return Wallet::where(['currency_id' => $currencyId, 'user_id' => auth()->id()])->first();
    }


    public function firstOrCreateWallet($currencyId)
    {
        $wallet = $this->getWallet($currencyId);
        if (is_null($wallet)) {
            $wallet = Wallet::createWallet(auth()->id(), $currencyId);
        }
        return $wallet;
    }


    /**
     * Finds corresponding Wallet or throws error
     *
     * @param int $currencyId
     *
     * @return Wallet
     *
     * @throws DepositMoneyException
     */
    public function findOrFailWallet($currencyId)
    {
        $wallet = $this->getWallet($currencyId);

        if (!is_null($wallet)) {
            return $wallet;
        }
        throw new DepositMoneyException(__("Wallet not found."), [
            "reason" => "walletNotFound",
            "message" => __("Wallet not found."),
            "status" => "401"
        ]);
    }


    /**
     * Check if the transfer amount does not exceeds the limit
     *
     * @param FeesLimit $fees
     * @param double $amount
     *
     * @return bool
     *
     * @throws DepositMoneyException
     */
    public function amountIsInLimit(FeesLimit $fees, $amount)
    {
        $minError = (float) $amount < $fees->min_limit;
        $maxError = $fees->max_limit &&  $amount > $fees->max_limit;

        if (!$minError && !$maxError) {
            return true;
        }
        // Check if the deposit amount exceeds the limit set by the admin
        if ($minError || $maxError) {
            if (is_null($fees->max_limit)) {
                throw new DepositMoneyException(__("The amount must be greater than or equal to :x.", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit))]), [
                    "reason" => "minLimit",
                    "currencyCode" => optional($fees->currency)->code,
                    "minLimit" => $fees->min_limit,
                    "message" => __("The amount is lower than the minimum limit of :y.", ["y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit))]),
                    "status" => "401"
                ]);
            }
            throw new DepositMoneyException(__("The amount must be between :y and :x.", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit)), "y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]), [
                "reason" => "minMaxLimit",
                "currencyCode" => optional($fees->currency)->code,
                "minLimit" => $fees->min_limit,
                "maxLimit" => $fees->max_limit,
                "message" => __("The minimum amount should be :x and the maximum amount :y.", ["x" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->min_limit)), "y" => moneyFormat(optional($fees->currency)->code, formatNumber($fees->max_limit))]),
                "status" => "401"
            ]);
        }
    }


    /**
     * Process after payment has been done
     *
     * @param int $currencyId
     * @param int $paymentMethodId
     * @param float $totalAmount
     * @param float $amount
     * @param array $response
     *
     * @return array
     *
     * @throws PaymentFailedException
     */
    public function processPaymentConfirmation(
        $currencyId,
        $paymentMethodId,
        $totalAmount,
        $amount,
        $response
    ) {
        try {
            DB::beginTransaction();
            if (isset($response["type"]) && $response["type"] == "bank") {
                $deposit = Deposit::success(
                    $currencyId,
                    $paymentMethodId,
                    auth()->id(),
                    ["totalAmount" => $totalAmount, "amount" => $amount],
                    "Pending",
                    "bank",
                    $response["attachment"] ?? null,
                    $response["bank"]
                );
                $response = miniCollection($response)->only(["action", "message"]);
            } else {
                $deposit = Deposit::success(
                    $currencyId,
                    $paymentMethodId,
                    auth()->id(),
                    ["totalAmount" => $totalAmount, "amount" => $amount],
                );
            }

            DB::commit();
            $this->helper->sendTransactionNotificationToAdmin("deposit", [
                "data" => $deposit["deposit"]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new PaymentFailedException($th->getMessage());
        }
        return array_merge(["message" => __("Deposit successful.")], $response);
    }


    /**
     * Get palpal credentials
     *
     * @param int $bankId
     *
     * @return Bank
     *
     * @throws DepositMoneyException
     */
    public function getBankDetails($bankId)
    {
        $bank = Bank::with("file:id,filename")->select("account_name", "account_number", "bank_name", "file_id")->firstWhere("id", $bankId);
        if (is_null($bank)) {
            throw new DepositMoneyException(__("Bank details not found."));
        }
        if ($bank->file_id) {
            $bank->logo = $bank->file->filename;
        }
        return $bank;
    }


    /**
     * Get palpal credentials
     *
     * @param int $currencyId
     * @param string $type
     *
     * @return array
     *
     * @throws DepositMoneyException
     */
    public function getPaypalInfo($currencyId, $type)
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => Paypal])
            ->where('activated_for', 'like', "%" . $type . "%")
            ->first(['method_data']);

        if (is_null($currencyPaymentMethod)) {
            throw new DepositMoneyException(__("Palpal payment method not found."));
        }

        return json_decode($currencyPaymentMethod->method_data);
    }
}
