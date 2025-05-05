<?php

namespace Modules\CryptoExchange\Http\Controllers\Users;

use Session;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\EmailController;
use Modules\CryptoExchange\Entities\{ExchangeDirection,
    CryptoExchange
};
use Modules\CryptoExchange\Http\Requests\{CryptoUserRequest,
    CryptoExchangeRequest
};


class CryptoExchangeManualController extends Controller
{
    protected $helper;
    protected $email;
    protected $cryptoExchange;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email = new EmailController();
        $this->cryptoExchange = new CryptoExchange();
    }

    public function exchange()
    {
        setActionSession();
        $data = [];
        $data['icon'] = 'money';
        $data['menu'] = 'Crypto Exchange';
        $data['content_title'] = 'Crypto Exchange';

        $data['exchangeType'] = (preference('transaction_type') == 'crypto_buy_sell' ) ? 'crypto_buy' : 'crypto_swap';
        $data['cryptoDirections'] = (new ExchangeDirection)->exchangeDirectionWithCurrency($data['exchangeType'], 'from_currency_id');

        if (!m_g_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU') && m_aic_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU')) {
            return view('vendor.installer.errors.user');
        }
        // Get from currency related to currencies from directions
        if (count($data['cryptoDirections'])) {
            $direction =  $data['cryptoDirections']->first();
            // From and To currency details
            $data['fromCurrency'] = $this->helper->getCurrencyObject(['id' => $direction->from_currency_id], ['id','code', 'symbol','type', 'address', 'logo']);
            $data['toCurrency'] = $this->helper->getCurrencyObject(['id' => $direction->to_currency_id], ['id','code', 'symbol','type', 'address', 'logo']);

            // Reciving currencies
            $data['toBuyCurrencies'] = ExchangeDirection::getCurrencies($direction->from_currency_id, $data['exchangeType']);
        }
        return view('cryptoexchange::user_dashboard.crypto.exchange.create', $data);
    }

    public function exchangeOfCurrency(CryptoExchangeRequest $request)
    {
        actionSessionCheck();
        $data = [];
        $from_currency_id = $request->from_currency;
        $to_currency_id = $request->to_currency;
        $send_amount = $request->send_amount;
        $exchange_type = $request->from_type;
        $receiverAddress = isset($request->crypto_address) ? $request->crypto_address : '';
        // Direction valid currecny pair check
        $pairCheck = currencyPairCheck($from_currency_id, $to_currency_id , $exchange_type);
        if (!$pairCheck) {
            $this->helper->one_time_message('error', __('Invalid currency pair.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        // Get direction
        $direction = ExchangeDirection::getDirection($from_currency_id, $to_currency_id);
        if (empty($direction) || is_null($direction)) {
            $this->helper->one_time_message('error', __('Invalid direction.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');     
        }

        if ($direction->status == 'Inactive') {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');    
        }
        // Limit Check
        if ($send_amount > $direction->max_amount || $send_amount < $direction->min_amount) {
            $this->helper->one_time_message('error', __('Limit : :x - :y', ['x' => formatNumber($direction->min_amount, $from_currency_id), 'y' => formatNumber($direction->max_amount, $from_currency_id)]));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        // Direction type check
        if ($exchange_type !== $direction->type) {
            $this->helper->one_time_message('error', __('Invalid direction.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        $user_id = auth()->user()->id;
        $data['fromCurrency'] = $fromCurrency = $this->helper->getCurrencyObject(['id' => $from_currency_id], ['code', 'symbol','type', 'address', 'logo']);
        $data['toCurrency'] = $toCurrency = $this->helper->getCurrencyObject(['id' => $to_currency_id], ['code', 'symbol', 'type', 'address', 'logo']);
        $data['merchantAddress'] = ($request->from_type == 'crypto_buy') ? '' :  $data['fromCurrency']->address;
        $fromWallet = $this->helper->getUserWallet(['currency:id,code,symbol'], ['user_id' => $user_id, 'currency_id' => $from_currency_id], ['id', 'currency_id', 'balance']);
        $toWallet = $this->helper->getUserWallet(['currency:id,code,symbol'], ['user_id' => $user_id, 'currency_id' => $to_currency_id], ['id', 'currency_id', 'balance']);
        $exchange_rate = $direction->exchange_rate;
        if ($direction->exchange_from == 'api') {
            $exchange_rate =  getCryptoCurrencyRate($fromCurrency->code, $toCurrency->code);
        }
        if (!$exchange_rate) {
            $this->helper->one_time_message('error', __('Crypto compare rate invalid.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }

        if ($exchange_type == 'crypto_buy' &&  !$fromWallet ) {
            $this->helper->one_time_message('error', __('Wallet not available.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }

        $feesPercentage = $send_amount * ($direction->fees_percentage / 100);
        $feesFixed = $direction->fees_fixed;
        $totalFees = $feesPercentage + $feesFixed;
        $totalAmount = $send_amount + $totalFees;
        date_default_timezone_set(preference('dflt_timezone'));
        $expireTime = date("F d, Y h:i:s A", strtotime('+5 minutes'));
        $data['transInfo']['dCurrencyRate'] = $exchange_rate;
        $data['transInfo']['fromCurrCode'] = $fromCurrency->code;
        $data['transInfo']['fromCurrencyLogo'] = $fromCurrency->logo;
        $data['transInfo']['toCurrencyLogo'] = $toCurrency->logo;
        $data['transInfo']['toCurrCode'] = $toCurrency->code;
        $data['transInfo']['finalAmount'] = $totalAmount;
        $data['transInfo']['defaultAmnt'] = $send_amount;
        $data['transInfo']['totalFees'] = $totalFees;
        $data['transInfo']['percentage'] = $direction->fees_percentage;
        $data['transInfo']['feesPercentage'] = $feesPercentage;
        $data['transInfo']['feesFixed'] = $feesFixed;
        $data['transInfo']['getAmount'] = $send_amount * $exchange_rate;
        $data['transInfo']['from_currency'] = $from_currency_id;
        $data['transInfo']['to_currency'] = $to_currency_id;
        $data['transInfo']['merchantAddress'] = $data['merchantAddress'];
        $data['transInfo']['exchangeType'] = $exchange_type;
        $data['transInfo']['reciever_address'] = $receiverAddress;
        $data['transInfo']['fromWallet'] = $fromWallet;
        $data['transInfo']['expire_time'] = $expireTime;
        session(['transInfo' => $data['transInfo']]);
        session(['paymentInfo' => $data]);
        return redirect()->route('user_dashboard.crypto_buy_sell.payment_confirm');     
    }

    public function paymentConfirm() 
    {
        $paymentInfo = session('paymentInfo');
        if (empty($paymentInfo)) {
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        $data = $paymentInfo;
        return view('cryptoexchange::user_dashboard.crypto.exchange.details', $data);
    }

    public function exchangeOfCurrencyConfirm(CryptoUserRequest $request)
    {
        actionSessionCheck();
        $sessionValue = session('transInfo');
        if (!expireTimeCheck($sessionValue['expire_time'])) {
            $this->helper->one_time_message('error', __('Transaction time over.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        if (empty($sessionValue)) {
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        $fromCurrency = $sessionValue['from_currency'];
        $toCurrency = $sessionValue['to_currency'];
        $exchangeType = $sessionValue['exchangeType'];
        $reciever_address = (isset($request->crypto_address)) ? $request->crypto_address : '' ;
        $user_id = auth()->user()->id;
        $uuid = unique_code();
        $direction = ExchangeDirection::getDirection($fromCurrency, $toCurrency);
        if ($direction->status == 'Inactive') {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }

        $fromWallet = $this->helper->getUserWallet(['currency:id,code,symbol,type'], ['user_id' => $user_id, 'currency_id' => $fromCurrency], ['id', 'currency_id', 'balance']);
        $toWallet = $this->helper->getUserWallet(['currency:id,code,symbol'], ['user_id' => $user_id, 'currency_id' => $toCurrency], ['id', 'currency_id', 'balance']);
        $cryptoPayWith = ($exchangeType == 'crypto_buy') ? 'wallet' : $request->pay_with ;
        $cryptoRecieve = ($exchangeType == 'crypto_sell') ? 'wallet' : $request->receive_with ;
        if ($exchangeType == 'crypto_buy' &&  !$fromWallet ) {
            $this->helper->one_time_message('error', __('Wallet not available.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        // Balance check
        if ($cryptoPayWith == 'wallet' && $fromWallet->balance < $sessionValue['finalAmount'] ) {
            $this->helper->one_time_message('error', __('Insufficient balance.'));
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        $status = ($cryptoPayWith == 'wallet' && $cryptoRecieve == 'wallet') ? 'Success' : 'Pending';
        $transaction_type_id = ($exchangeType == 'crypto_swap') ? Crypto_Swap : (($exchangeType == 'crypto_buy') ? Crypto_Buy : Crypto_Sell) ;
        $fileName = '';
        if (isset($request->proof_file)) {
            $fileName = insertDetailsFile($request->proof_file, public_path('uploads/files/crypto-details-file'));
            if (!$fileName) {
                $this->helper->one_time_message('error', __('Invalid file type.'));
                return redirect()->route('user_dashboard.crypto_buy_sell.create');
            }
        }
        $arr = [
            'unauthorisedStatus' => null,
            'user_id'                   => $user_id,
            'toWalletCurrencyId'        => $toCurrency,
            'fromWalletCurrencyId'      => $fromCurrency,
            'fromWallet'                => $fromWallet,
            'toWallet'                  => $toWallet,
            'uuid'                      => $uuid,
            'destinationCurrencyExRate' => $sessionValue['dCurrencyRate'],
            'amount'                    => $sessionValue['defaultAmnt'],
            'fee'                       => $sessionValue['totalFees'],
            'finalAmount'               => $sessionValue['finalAmount'],
            'getAmount'                 => $sessionValue['getAmount'],
            'transaction_type_id'       => $transaction_type_id,
            'percentage'                => $sessionValue['percentage'],
            'charge_percentage'         => $sessionValue['feesPercentage'],
            'charge_fixed'              => $sessionValue['feesFixed'],
            'exchange_type'             => $sessionValue['exchangeType'],
            'fromCurrCode'              => $sessionValue['fromCurrCode'],
            'toCurrCode'                => $sessionValue['toCurrCode'],
            'merchantAddress'           => $sessionValue['merchantAddress'],
            'receiver_address'          => $reciever_address,
            'file_name'                 => $fileName,
            'payment_details'           => $request->payment_details,
            'receiving_details'         => '',
            'verification_via'          => '',
            'phone'                     => NULL,
            'payment_method_id'         => NULL,
            'bank_id'                   => NULL,
            'cryptoPayWith'             => $cryptoPayWith,
            'cryptoRecieve'             => $cryptoRecieve,
            'status'                    => $status,
        ];
        //Get response
        $response = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
        if ($response['status'] != 200) {
            if (empty($response['cryptoExchangeId'])) {
                Session::forget('transInfo');
                $this->helper->one_time_message('error', $response['ex']['message']);
                return redirect()->route('user_dashboard.crypto_buy_sell.create');
            }
        }
        //Admin Notification
        $notificationToAdmin = $this->helper->sendTransactionNotificationToAdmin('crypto-exchange', ['data' => $response['cryptoExchange']]);
        $data = [];
        $data['result'] = CryptoExchange::with('fromCurrency:id,code,symbol,logo', 'toCurrency:id,code,symbol,logo')
                        ->where(['id' => $response['cryptoExchangeId']])->first();
        $data['transInfo']['getAmount']   = $sessionValue['getAmount'];
        $data['transInfo']['trackUrl']    = url('track-transaction', $uuid);
        Session::forget('transInfo');
        clearActionSession();
        session(['successInfo' => $data]);
        return redirect()->route('user_dashboard.crypto_buy_sell.success_page');
    }

    public function cryptoExchangeSuccess()
    {
        $sessionValue =  session('successInfo');
        if (empty($sessionValue)) {
            return redirect()->route('user_dashboard.crypto_buy_sell.create');
        }
        $data = $sessionValue;
        Session::forget(['successInfo', 'paymentInfo']);
        return view('cryptoexchange::user_dashboard.crypto.exchange.success', $data);
    }


    public function exchangeOfPrintPdf($trans_id)
    {
        $data = [];
        $data['currencyExchange'] = CryptoExchange::with([
            'fromCurrency:id,code,symbol',
            'toCurrency:id,code,symbol',
        ])->where(['id' => $trans_id])->first();
        generatePDF('cryptoexchange::user_dashboard.crypto.exchange.exchangeOfPaymentPdf', 'crypto_exchanges_tramsactopm_', $data);
    }

}
