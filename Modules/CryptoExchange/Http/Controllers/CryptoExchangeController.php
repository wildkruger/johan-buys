<?php

namespace Modules\CryptoExchange\Http\Controllers;

use App\Http\Controllers\Users\EmailController;
use Modules\CryptoExchange\Http\Requests\{CryptoBuySellRequest,
    ReceivingInfoRequest,
    CryptoBuySellSuccessRequest
};
use Session, Validator, DB, Auth, URL;
use App\Repositories\StripeRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{PaymentMethod,
    CurrencyPaymentMethod,
    Transaction,
    Currency,
    Bank
};
use Modules\CryptoExchange\Entities\{
    ExchangeDirection,
    PhoneVerification,
    CryptoExchange
};

class CryptoExchangeController extends Controller
{
    protected $helper;
    protected $cryptoExchange;
    protected $stripeRepository;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->cryptoExchange = new CryptoExchange();
        $this->stripeRepository = new StripeRepository();
        $this->email = new EmailController();
    }

    public function cryptoExchange()
    {
        setActionSession();
        $data = [];
        $data['menu'] = 'Crypto Exchange';
        $data['pref'] = preference('transaction_type');
        $data['exchange_type'] = ($data['pref'] == 'crypto_buy_sell') ? 'crypto_buy' : 'crypto_swap';
        $this->sessionForget();
        $data['cryptoDirections'] = (new ExchangeDirection)->exchangeDirectionWithCurrency($data['exchange_type'], 'from_currency_id', []);

        if (count($data['cryptoDirections'])) {
            $data['toBuyCurrencies'] = ExchangeDirection::getCurrencies($data['cryptoDirections'][0]->from_currency_id, $data['exchange_type']);
        }
        if (!m_g_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU') && m_aic_c_v('Q1JZUFRPRVhDSEFOR0VfU0VDUkVU')) {
            return view('vendor.installer.errors.user');
        }
        return view('cryptoexchange::frontend.crypto.exchange', $data);
    }

    public function directionCurrencies()
    {
        $directionCurrencies = ExchangeDirection::getCurrencies(request()->from_currency_id,  request()->type);
        return response()->json([
            'directionCurrencies' => $directionCurrencies,
        ]);
    }

    public function getDirectionAmount()
    {
        $success = ['status' => 200, 'message' => ''];

        // Get Single Direction
        $direction = ExchangeDirection::getDirection(request()->from_currency_id, request()->to_currency_id);
        $exchangeRate = $direction->exchange_rate;

        // Exchange rate from Api
        if ($direction->exchange_from == 'api' && isset($direction->fromCurrency->code) && isset($direction->toCurrency->code) ){
            $exchangeRate = getCryptoCurrencyRate($direction->fromCurrency->code, $direction->toCurrency->code);
        }

        // Send Amount & Get Amount
        if (is_null(request()->send_amount)) {
            $success['send_amount'] = ($direction->type == 'crypto_buy') ? decimalFormat((request()->get_amount / $exchangeRate)) : cryptoFormat((request()->get_amount / $exchangeRate)) ;
            $success['get_amount'] = request()->get_amount ;
        } else {
            $success['send_amount'] = request()->send_amount;
            $success['get_amount'] = ($direction->type == 'crypto_buy' || $direction->type == 'crypto_swap' ) ? cryptoFormat((request()->send_amount * $exchangeRate)) : decimalFormat((request()->send_amount * $exchangeRate)) ;
        }

        // Exchange rate to display - 1 USD = 18,000.00 BTC
        if (isset($direction->fromCurrency->code) && isset($direction->toCurrency->code) ) {
            $success['exchange_rate'] = '1 '.optional($direction->fromCurrency)->code. ' = '. formatNumber($exchangeRate, request()->to_currency_id).' '.$direction->toCurrency->code;
        }

        // Exchange Fees
        $feesPercentage = $success['send_amount'] * ($direction->fees_percentage / 100);
        $feesFixed = $direction->fees_fixed;
        $success['exchange_fee'] = formatNumber($feesPercentage + $feesFixed, request()->from_currency_id);

        // Send amount mix max limit check
        if ($direction->min_amount > $success['send_amount'] || $direction->max_amount < $success['send_amount']) {
            $success['message'] = __('Limit : :x - :y', ['x' => formatNumber($direction->min_amount, request()->from_currency_id), 'y' => formatNumber($direction->max_amount, request()->from_currency_id)]);
            $success['status'] = 401;
            return response()->json([
                'success' => $success,
            ]);
        }

        // Auth user wallet or balance check
        if ($direction->type == 'crypto_buy' && Auth::check() && cryptoValidity('auth_user')) {
            $user_id   = Auth::id();
            $fromWallet = $this->helper->getUserWallet(['currency:id,code,symbol'], ['user_id' => $user_id, 'currency_id' => $direction->from_currency_id], ['id', 'currency_id', 'balance']);
            if (empty($fromWallet)) {
                $success['message'] = __('Wallet not Available.');
                $success['status'] = 401;
            } elseif ($fromWallet->balance < $success['send_amount']) {
                $success['message'] = __('Balance not Available.');
                $success['status'] = 401;
            }
        }

        if (!$exchangeRate) {
            $success['message'] = __('Crypto compare rate Invalid');
            $success['status'] = 401;
        }

        return response()->json([
            'success' => $success,
        ]);
    }

    public function getTabDirection()
    {
        $status = '201';
        $type = request()->direction_type;
        $toCurrencies = '';

        if ($type == 'crypto_buy' && (preference('available') == 'guest_user' || !Auth::check())){
            $fromCurrencies = $this->cryptoBuyWithGateway();
        } else {
            $fromCurrencies = (new ExchangeDirection)->exchangeDirectionWithCurrency($type, 'from_currency_id', ['fromCurrency']);
        }

        if (count($fromCurrencies)) {
            $toCurrencies = ExchangeDirection::getCurrencies($fromCurrencies[0]->from_currency_id, $type);
        } else {
            $status = '401';
        }
        return response()->json([
            'toCurrencies' => $toCurrencies,
            'fromCurrencies' => $fromCurrencies,
            'status' => $status 
        ]);
    }

    public function cryptoBuyWithGateway()
    {
        $cryptoBuyDirections = (new ExchangeDirection)->exchangeDirectionWithGateway('crypto_buy', 'gateways', 'fromCurrency');
        
        $fromCurrencies['id'] = [];
        foreach ($cryptoBuyDirections as $cryptoBuyDirection)
        {
            $gateways = explode(',' , $cryptoBuyDirection->gateways);

            $currencyPaymentMethods = [];
            foreach($gateways as $gateway)
            {
                $currencyPaymentMethods = CurrencyPaymentMethod::with('method')
                                            ->where('method_id', $gateway)
                                            ->where('currency_id', $cryptoBuyDirection->from_currency_id)
                                            ->where('activated_for', 'like', "%Crypto_Buy%")
                                            ->get();
                                        
                if (count($currencyPaymentMethods)) {
                    $fromCurrencies['id'][] =  $cryptoBuyDirection->id;
                }
            }
        }

        $fromCurrencies = $cryptoBuyDirections->whereIn('id', $fromCurrencies['id'])->unique('from_currency_id');

        return $fromCurrencies;
    }

    public function cryptoBuySell(CryptoBuySellRequest $request)
    {
        $from_currency_id = $request->from_currency;
        $to_currency_id = $request->to_currency;
        $send_amount = $request->send_amount;
        $type = $request->from_type;

        // Crypto exchange type validity check based on currency type
        // Crypto Exchange [crypto - crypto], Crypto Buy [fiat - crypto], Crypto Sell [crypto - fiat]
        $pairCheck = currencyPairCheck($from_currency_id, $to_currency_id, $type);
        if (!$pairCheck) {
            $this->helper->one_time_message('error', __('Invalid currency pair.'));
            return redirect()->route('guest.crypto_exchange.home');
        }

        // ExchangeAmountLimit check based on ExchangeDirection
        $direction = ExchangeDirection::getDirection($from_currency_id, $to_currency_id);
        if ($direction) {
            if ($request->send_amount > $direction->max_amount || $request->send_amount < $direction->min_amount) {
                $this->helper->one_time_message('error', __('Limit : :x - :y', ['x' => formatNumber($direction->min_amount, $from_currency_id), 'y' => formatNumber($direction->max_amount, $from_currency_id)]));
                return back()->withInput();
            }
        }

        $data = [];
        $data['fromCurrency'] = $fromCurrency = $this->helper->getCurrencyObject(['id' => $from_currency_id], ['id','code', 'symbol','type', 'address', 'logo']);
        $data['toCurrency'] = $toCurrency = $this->helper->getCurrencyObject(['id' => $to_currency_id], ['id','code', 'symbol','type', 'address', 'logo']);
        $merchantAddress = ($fromCurrency->type == 'crypto') ? $fromCurrency->address : '';
        $feesPercentage = $send_amount * ($direction->fees_percentage / 100);
        $feesFixed = $direction->fees_fixed;
        $totalFess = number_format($feesPercentage + $feesFixed, 8, '.', '') ;
        $totalAmount = $send_amount + $totalFess;
        $exchange_rate = $direction->exchange_rate;

        if ($direction->exchange_from == 'api') {
            $exchange_rate = getCryptoCurrencyRate($fromCurrency->code, $toCurrency->code);
        }
        if (!$exchange_rate) {
            $this->helper->one_time_message('error', __('Crypto compare rate invalid.'));
            return redirect()->route('guest.crypto_exchange.home');
        }

        $data['menu'] = 'Crypto Exchange';
        $data['transInfo'] = $request->all();
        $data['transInfo']['finalAmount'] = ($send_amount * $exchange_rate);
        $data['transInfo']['defaultAmnt'] = $send_amount;
        $data['transInfo']['totalAmount'] = $totalAmount;
        $data['transInfo']['percentage'] = $direction->fees_percentage;
        $data['transInfo']['feesPercentage'] = $feesPercentage;
        $data['transInfo']['fromCurrencyCode'] = $fromCurrency->code;
        $data['transInfo']['fromCurrencyLogo'] = $fromCurrency->logo;
        $data['transInfo']['toCurrencyLogo'] = $toCurrency->logo;
        $data['transInfo']['currCode'] = $toCurrency->code;
        $data['transInfo']['feesFixed'] = $feesFixed;
        $data['transInfo']['totalFees'] = $totalFess;
        $data['transInfo']['dCurrencyRate'] = $exchange_rate;
        $data['transInfo']['instruction'] = $direction->instruction;
        $data['transInfo']['merchantAddress'] = $merchantAddress;
        $data['transInfo']['exchangeType'] = $type;

        if (Auth::check() && cryptoValidity('auth_user')) {
            $data['cryptoDirections'] = $cryptoDirections = ExchangeDirection::where(['status' => 'active', 'type'  => $type ])->get()->unique('from_currency_id');
            if (count($cryptoDirections)) {
                $data['toBuyCurrencies'] = ExchangeDirection::getCurrencies($cryptoDirections[0]->from_currency_id, $type);
            }
            return view('cryptoexchange::user_dashboard.crypto.exchange.create', $data);
        }

        $data['pref'] = preference('verification');
        session(['transInfo' => $data['transInfo']]);

        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }

        return view('cryptoexchange::frontend.crypto.verification', $data);     
    }

    public function completePhoneVerification(Request $request)
    {
        $phoneFormatted = str_replace('+' . $request->carrierCode, "", $request->phone);
        if ($request->code)  {
            $verificationDetails = PhoneVerification::where(['phone' => $phoneFormatted])->first(['code']);
            if ($request->code == $verificationDetails->code) {
                Session::put('transInfo_crypto_phone', $request->carrierCode . $phoneFormatted);
                return response()->json([
                    'status'  => true,
                    'message' => __('Phone number verified successfully.'),
                    'success' => "alert-success",
                ]);
            } else  {
                return response()->json([
                    'status'  => false,
                    'message' => __('Verification code doesn\'t match.'),
                    'error'   => "alert-danger",
                ]);
            }
        }  else {
            return response()->json([
                'status'  => 500,
                'message' => __('Please enter verification code.'),
                'error'   => "alert-danger",
            ]);
        }
    }

    public function generatedPhoneVerificationCode(Request $request)
    {
        $data = ['status' => false, 'message' => 'No'];
        $sixDigitNumber = six_digit_random_number();
        $phoneFormatted = str_replace('+' . $request->carrierCode, "", $request->phone);
        $verification = PhoneVerification::firstOrCreate([
            'phone' => $phoneFormatted
        ]);
        $verification->code = $sixDigitNumber;
        $verification->save();
        //SMS
        if (!empty($request->phone)) {
            if (!empty($request->carrierCode) && !empty($request->phone)) {
                $message = __(':x is your crypto transaction verification code.', ['x' => $sixDigitNumber]);
                if (checkAppSmsEnvironment() == true ) {
                    if (!empty(getSmsConfigDetails()) && getSmsConfigDetails()->status == 'Active') {
                        $data['status'] = true;
                        $data['message'] = 'Yes';            
                        sendSMS('+'.$request->carrierCode . $phoneFormatted, $message);
                    } else  {
                        $data['status'] = false;
                        $data['message'] = 'No';
                    }
                }
            }
        }
        return response()->json(['data' => $data ]);
    }

    public function generatedEmailVerificationCode(Request $request)
    {
        $data = ['status' => true, 'message' => 'No'];
        $sixDigitNumber = six_digit_random_number();
        $email = $request->email;
        $verification = PhoneVerification::firstOrCreate([
            'phone' => $email
        ]);
        $verification->code = $sixDigitNumber;
        $verification->save();
        
        // Send email
        if (!empty($request->email)) {
            $message = __(':x is your crypto transaction verification code.', ['x' => $sixDigitNumber]);
            $subject = __('Verification Code');
            if (checkAppMailEnvironment() == true ) {
                $data['status'] = true;
                $data['message'] = 'Yes';
                $this->email->sendEmail($email, $subject, $message);
            } else {
                $data['status'] = false;
                $data['message'] = 'Yes';
            }
        }
        return response()->json(['data' => $data ]);
    }

    public function completeEmailVerification(Request $request)
    {
        $email = $request->email;
        $validation = Validator::make($request->all(), [
            'code' => 'required',
            'email' => 'required|email',
        ]);
        if ($validation->passes()) {
            $verificationDetails = PhoneVerification::where(['phone' => $email])->first(['code']);
            if ($request->code == $verificationDetails->code) {
                Session::put('transInfo_crypto_phone', $request->email);
                return response()->json([
                    'status'  => true,
                    'message' => __('Phone number verified successfully.'),
                    'success' => "alert-success",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('Verification code doesn\'t match.'),
                    'error' => "alert-danger",
                ]);
            }
        }  else {
            return response()->json([
                'status' => 500,
                'message' => $validation->errors()->all(),
                'error' => "alert-danger",
            ]);
        }
    }

    public function cryptoBuySellGateway()
    {
        if (URL::previous() != url('crypto-exchange/receiving-info')) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        $sessionValue = $data['transInfo'] = session('transInfo');
        $sessionPhone = session('transInfo_crypto_phone');
        if (empty($sessionValue) || empty($sessionPhone) ) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $direction = ExchangeDirection::getDirection($sessionValue['from_currency'], $sessionValue['to_currency']);
        if ($direction->type == 'crypto_buy') {
            $gateway_list = explode(',', $direction->gateways);
            $data['currencyPaymentMethods'] = ExchangeDirection::currencyPaymentMethodList($sessionValue['from_currency'], $gateway_list);
        }
        $data['menu']  = 'Crypto Exchange';
        $data['instruction'] = $direction->payment_instruction;
        $data['url'] = ($direction->type == 'crypto_buy') ? url('crypto-exchange/payment') : url('crypto-exchange/success');
        date_default_timezone_set(preference('dflt_timezone'));
        $data['expireTime'] = date("F d, Y h:i:s A", strtotime('+5 minutes'));
        session(['paymentInfo' => $data]);
        return redirect()->route('guest.crypto_exchange.payment-gateway');
    }

    public function cryptoBuySellPaymentGateway()
    {
        $sessionValue = session('transInfo');
        $sessionPhone = session('transInfo_crypto_phone');
        $paymentInfo = session('paymentInfo');
        if (empty($sessionValue) || empty($sessionPhone) || empty($paymentInfo) ) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $data = $paymentInfo;
        return view('cryptoexchange::frontend.crypto.gateway', $data);
    }

    public function cryptoBuySellReceive()
    {
        $data['menu'] = 'Crypto Exchange';
        $sessionValue = $data['transInfo'] = session('transInfo');
        $sessionPhone = session('transInfo_crypto_phone');
        if (empty($sessionValue) || empty($sessionPhone)) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        return view('cryptoexchange::frontend.crypto.receiving_details', $data);
    }

    public function receivingInfoStore(ReceivingInfoRequest $request)
    { 
        $sessionValue  = session('transInfo');  
        $sessionPhone = session('transInfo_crypto_phone');
        if (empty($sessionValue) || empty($sessionPhone)) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (isset($request->crypto_address)) {
            Session::put('transInfo_crypto_address', $request->crypto_address );
        }
        if (isset($request->receiving_details)) {
            Session::put('transInfo_receiving_details', $request->receiving_details );
        }
        return redirect()->route('guest.crypto_exchange.gateway');
    }

    public function cryptoBuySellSuccess(CryptoBuySellSuccessRequest $request)
    {
        $sessionValue = session('transInfo');
        $paymentInfo = session('paymentInfo');
        if (!expireTimeCheck($paymentInfo['expireTime'])) {
            $this->helper->one_time_message('error', __('Transaction time over.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $sessionPhone = session('transInfo_crypto_phone');
        $sessionCryptoAddress = session('transInfo_crypto_address');
        $sessionReceivingDetails = session('transInfo_receiving_details');
        $paymentmethod = session('payment_method_id');
        if (empty($sessionValue) ||  empty($sessionPhone) || (empty($sessionCryptoAddress) && empty($sessionReceivingDetails)) ) {
            return redirect()->route('guest.crypto_exchange.home');
        }
        $fileName = '';
        if (isset($request->proof_file)) {
            $fileName = insertDetailsFile($request->proof_file, public_path('uploads/files/crypto-details-file'));
            if (!$fileName) {
                $this->helper->one_time_message('error', __('Invalid file type.'));
                return redirect()->route('guest.crypto_exchange.home');
            }
        }
        $payment_details = isset($request->payment_details) ? $request->payment_details : '' ;
        $arr = $this->setTransactionArray($sessionValue, $fileName, $payment_details);
        $response = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
        if ($response['status'] != 200) {
            if (empty($response['cryptoExchangeId'])) {
                Session::forget('transInfo');
                $this->helper->one_time_message('error', $response['ex']['message']);
                return redirect()->route('guest.crypto_exchange.home');

            }
        }
        $data = [];
        $data['menu']  = 'Crypto Exchange';
        $data['transInfo'] = $cryptoExchange = $response['cryptoExchange'];
        $data['direction'] = $direction = ExchangeDirection::getDirection($cryptoExchange['from_currency'], $cryptoExchange['to_currency']);
        $data['transInfo']['send_amount'] = $cryptoExchange['amount'];
        $data['transInfo']['get_amount'] = $cryptoExchange['get_amount'];
        $data['transInfo']['finalAmount'] = $cryptoExchange['amount'] + $cryptoExchange['fee'];
        $data['transInfo']['dCurrencyRate'] = $cryptoExchange['exchange_rate'];
        $data['transInfo']['fee'] = $cryptoExchange['fee'];
        $data['transInfo']['uuid'] = $cryptoExchange['uuid'];
        $data['transInfo']['exchangeType'] = $direction->type;
        $data['transInfo']['fromCurrencyLogo'] = $sessionValue['fromCurrencyLogo'];
        $data['transInfo']['toCurrencyLogo'] = $sessionValue['toCurrencyLogo'];
        $data['transInfo']['trackUrl'] = url('track-transaction', $cryptoExchange['uuid']);
        $this->sessionForget();
        clearActionSession();
        $this->helper->sendTransactionNotificationToAdmin('crypto-exchange', ['data' => $response['cryptoExchange']]);
        return view('cryptoexchange::frontend.crypto.success', $data);
    }


    public function exchangeOfPrintPdf($trans_id)
    {
        $data = [];
        $data['transactionDetails'] = $transaction = Transaction::with(['cryptoapi_log:id,object_id,payload', 'currency:id,symbol,code'])
            ->where(['uuid' => $trans_id])
            ->first(['id','uuid', 'created_at', 'status', 'currency_id', 'payment_method_id', 'subtotal', 'charge_percentage', 'charge_fixed', 'total']);

        $data['payload'] = $payload = json_decode($transaction->cryptoapi_log->payload);

        $data['direction'] = $direction = ExchangeDirection::getDirection($payload->exchangeToCurrencyId, $payload->exchangeFromCurrencyId);

        generatePDF('cryptoexchange::frontend.crypto.exchangeOfPaymentPdf', 'crypto_exchanges_transaction_', $data);
    
    }

    /*Stripe Merchant Payment Starts*/
    public function stripeMakePayment(Request $request)
    {
        $data = [];
        $data['status']  = 200;
        $data['message'] = "Success";
        $validation = Validator::make($request->all(), [
            'cardNumber' => 'required',
            'month'      => 'required|digits_between:1,12|numeric',
            'year'       => 'required|numeric',
            'cvc'        => 'required|numeric',
            'currency'   => 'required',
            'amount'     => 'required',
        ]);
        if ($validation->fails()) {
            $data['message'] = $validation->errors()->first();
            $data['status']  = 401;
            return response()->json([
                'data' => $data
            ]);
        }
        $amount = $request->amount;
        $paymentMethod = PaymentMethod::where(['name'=> "Stripe"])->first(['id', 'name']);
        $method_id = $paymentMethod['id'];
        $currencyCode = $request->currency;
        $currency = Currency::where(['code'=> $currencyCode])->first(['id', 'code']);
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currency['id'], 'method_id' => $method_id])->where('activated_for', 'like', "%Crypto_Buy%")->first(['method_data']);
        $methodData = json_decode($currencyPaymentMethod->method_data);
        $secretKey = $methodData->secret_key;
        if (!isset($secretKey)) {
            $data['message']  = __("Payment gateway credentials not found!");
            return response()->json([
                'data' => $data
            ]);
        }
        $response = $this->stripeRepository->makePayment($secretKey, round($amount, 2), strtolower($currency->code), $request->cardNumber, $request->month, $request->year, $request->cvc);
        if ($response->getData()->status != 200) {
            $data['status']  = $response->getData()->status;
            $data['message'] = $response->getData()->message;
        } else {
            $data['paymentIntendId'] = $response->getData()->paymentIntendId;
            $data['paymentMethodId'] = $response->getData()->paymentMethodId;
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function stripePayment(Request $request)
    {
        $data =['message' => 'Fail', 'status' => 401];
        try {
            $validation = Validator::make($request->all(), [
                'amount'           => 'required|numeric',
                'paymentIntendId'  => 'required',
                'paymentMethodId'  => 'required',
            ]);
            if ($validation->fails()) {
                $data['message'] = $validation->errors()->first();
                return response()->json(['data' => $data]);
            }
            DB::beginTransaction();
            $currencyCode          = $request->currency;
            $currency              = Currency::where('code', $currencyCode)->first(['id', 'code']);
            $PaymentMethod         = PaymentMethod::where(['name' => 'Stripe'])->first(['id']);
            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $currency->id, 'method_id' => $PaymentMethod->id])->where('activated_for', 'like', "%Crypto_Buy%")->first(['method_data']);
            $methodData            = json_decode($currencyPaymentMethod->method_data);
            if (empty($methodData) || !isset($methodData->secret_key)) {
                $data['message'] = __('method data of currency :x not found.', ['x' => $currencyCode]);
                return response()->json(['data' => $data]);
            }
            $response = $this->stripeRepository->paymentConfirm($methodData->secret_key, $request->paymentIntendId, $request->paymentMethodId);
            if ($response->getData()->status != 200) {
                $data['message'] = $response->getData()->message;
                return response()->json(['data' => $data]);
            }
            $token = $response->getData()->id;
            $sessionValue      = session('transInfo');
            $arr = $this->setTransactionArray($sessionValue);
            $cryptoExchangeTransaction = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
            $data['cryptoExchange'] = $cryptoExchangeTransaction['cryptoExchange'];
            Session::put('cryptoExchange', $data['cryptoExchange']);
            DB::commit();
            $data['message'] = "Success";
            $data['status']  = 200;
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] =  $e->getMessage();
        }
        return response()->json(['data' => $data]);
    }

    public function paypalDepositPaymentSuccess($amount)
    {
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        try {
            DB::beginTransaction();
            actionSessionCheck();
            if (empty(session('transInfo'))) {
                return redirect()->route('guest.crypto_exchange.home');
            }
            $sessionValue = session('transInfo');
            $arr = $this->setTransactionArray($sessionValue);
            $cryptoExchangeTransaction = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
            $data['cryptoExchange'] =  $cryptoExchange = $cryptoExchangeTransaction['cryptoExchange'];
            DB::commit();
            $data['direction'] = $direction = ExchangeDirection::getDirection($cryptoExchange['from_currency'], $cryptoExchange['to_currency']);
            $data['transInfo']['id'] = $cryptoExchange['id'];
            $data['transInfo']['send_amount'] = $cryptoExchange['amount'];
            $data['transInfo']['get_amount'] = $cryptoExchange['get_amount'];
            $data['transInfo']['finalAmount'] = $cryptoExchange['amount'] + $cryptoExchange['fee'];
            $data['transInfo']['dCurrencyRate'] = $cryptoExchange['exchange_rate'];
            $data['transInfo']['fee'] = $cryptoExchange['fee'];
            $data['transInfo']['uuid'] = $cryptoExchange['uuid'];
            $data['transInfo']['exchangeType'] = $direction->type;
            $data['transInfo']['fromCurrencyLogo'] = optional($direction->fromCurrency)->logo;
            $data['transInfo']['toCurrencyLogo'] = optional($direction->toCurrency)->logo;
            $data['transInfo']['trackUrl']    = url('track-transaction', $cryptoExchange['uuid']);

            //clearing session
            $this->sessionForget();
            clearActionSession();
            return view('cryptoexchange::frontend.crypto.success', $data);

        } catch (Exception $e) {
            DB::rollBack();
            $this->sessionForget();
            clearActionSession();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect()->route('guest.crypto_exchange.home');

        }
    }

    public function paymentCancel()
    {
        clearActionSession();
        $this->helper->one_time_message('error', __('You have cancelled your payment.'));
        return back();
    }

    public function cryptoBuySellPayment(CryptoBuySellSuccessRequest $request)
    {
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $paymentInfo = session('paymentInfo');
        if (!expireTimeCheck($paymentInfo['expireTime'])) {
            $this->helper->one_time_message('error', __('Transaction time over.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $data = [];
        Session::put('payment_method_id', $request->gateway);
        $sessionValue = $data['transInfo'] = session('transInfo');
        $data['paymentmethod'] = $pm = PaymentMethod::where(['id' => $request->gateway])->first(['id', 'name']);
        $method = $pm->id;
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $sessionValue['from_currency'], 'method_id' => $method])->where('activated_for', 'like', "%Crypto_Buy%")->first(['method_data']);
        $data['payment_amount'] = $amount = $sessionValue['totalAmount'];
        if ($method == Stripe && !empty($currencyPaymentMethod)) {
             $data['publishable'] = json_decode($currencyPaymentMethod->method_data)->publishable_key;
             return view('cryptoexchange::frontend.crypto.stripe', $data);
        }
        if ($method == Paypal && !empty($currencyPaymentMethod)) {
            $data['clientId'] = json_decode($currencyPaymentMethod->method_data)->client_id;
            $data['currencyCode'] = $sessionValue['fromCurrencyCode'];
            $data['amount'] = number_format((float) $amount, 2, '.', '');
            return view('cryptoexchange::frontend.crypto.paypal', $data);
        }
        if ($method == PayUmoney && !empty($currencyPaymentMethod)) {
            $methodData = json_decode($currencyPaymentMethod->method_data);
            $currencyCode = $sessionValue['fromCurrencyCode'];
            if ($currencyCode !== 'INR') {
                $this->sessionForget();
                clearActionSession();
                $this->helper->one_time_message('error', __('PayUMoney only supports Indian Rupee(INR).'));
                return redirect()->route('guest.crypto_exchange.home');
            }

            $data['amount'] = formatNumber($amount);
            $data['mode'] = $methodData->mode;
            $data['key'] = $methodData->key;
            $data['salt'] = $methodData->salt;
            $data['email'] = session('transInfo_crypto_phone');
            $data['firstname'] = session('transInfo_crypto_phone');
            $data['txnid'] = unique_code();
            $data['productinfo'] = 'Account Deposit';
            $data['service_provider'] = 'payu_paisa';
            $data['surl'] = url('/crypto-exchange/payumoney_confirm');
            $data['furl'] = url('/crypto-exchange/payumoney_fail');

            $hashSequence = $data['key'] . '|' . $data['txnid'] . '|' . $data['amount'] . '|' . $data['productinfo'] . '|' . $data['firstname'] . '|' . $data['email'] . '|||||||||||' . $data['salt'];
            $data['hash'] = hash("sha512", $hashSequence);
            if ($data['mode'] == 'sandbox') {
                $data['action'] = "https://sandboxsecure.payu.in/_payment";
            } else {
                $data['action'] = "https://secure.payu.in/_payment";
            }
            return view('cryptoexchange::frontend.crypto.payumoney', $data);
        }

        if ($method == Bank) {
            $banks = Bank::where(['currency_id' => $sessionValue['from_currency']])->get(['id', 'bank_name', 'is_default', 'account_name', 'account_number']);
            $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', $sessionValue['from_currency'])->where('activated_for', 'like', "%Crypto_Buy%")->where('method_data', 'like', "%bank_id%")->get(['method_data']);
            $data['banks'] = $bankList = $this->bankList($banks, $currencyPaymentMethods);
            if (empty($bankList)) {
                $this->helper->one_time_message('error', __('Banks doesn\'t exist for selected currency.'));
                return redirect()->route('guest.crypto_exchange.home');
            }
            return view('cryptoexchange::frontend.crypto.bank', $data);
        }

        if ($method == Payeer && !empty($currencyPaymentMethod)) {
            $methodData = json_decode($currencyPaymentMethod->method_data);
            $currencyCode = $sessionValue['fromCurrencyCode'];
            $data['amount']  = formatNumber($amount);
            $transInfo = $paymentInfo['transInfo'];
            $currencyId = $transInfo['from_currency'];
            session()->put('payeer_secret_key', $methodData->secret_key);
            $data['m_shop']     = $m_shop     = $methodData->merchant_id;
            $data['m_orderid']  = $m_orderid  = six_digit_random_number();
            $data['m_amount']   = $m_amount   = number_format((float) $amount, 2, '.', '');
            $data['m_curr']             = $m_curr             = $currencyCode;
            $data['form_currency_code'] = $form_currency_code = $currencyCode;
            $data['m_desc']             = $m_desc             = base64_encode('cryptoExchange');
            $m_key                      = $methodData->secret_key;
            $arHash                     = array(
                $m_shop, $m_orderid, $m_amount, $m_curr, $m_desc,
            );

            $arParams = array(
                'success_url' => route('guest.crypto_exchange.payeer_payment_success'),
                'status_url'  => route('guest.crypto_exchange.payeer_payment_status'),
                'fail_url'    => route('guest.crypto_exchange.payeer_payment_fail'),
                'reference'   => array(
                    'email'   => session('transInfo_crypto_phone'),
                    'name'    => __('Front end user'),
                ),
            );
            $cipher = 'AES-256-CBC';
            $iv     = random_bytes(16);
            $key    = md5($methodData->encryption_key . $m_orderid);
            $m_params = urlencode(base64_encode(openssl_encrypt(json_encode($arParams), $cipher, $key, OPENSSL_RAW_DATA, $iv)));

            $arHash[] = $data['m_params']  = $m_params;
            $arHash[] = $m_key;
            $data['sign'] = strtoupper(hash('sha256', implode(":", $arHash)));

            return view('cryptoexchange::frontend.crypto.payeer', $data);
        }

    }


    public function payeerPaymentSuccess(Request $request)
    {
        if (isset($request['m_operation_id']) && isset($request['m_sign'])) {
            $payeer_secret_key = session()->get('payeer_secret_key');
            $m_key  = $payeer_secret_key;
            $arHash = array(
                $request['m_operation_id'],
                $request['m_operation_ps'],
                $request['m_operation_date'],
                $request['m_operation_pay_date'],
                $request['m_shop'],
                $request['m_orderid'],
                $request['m_amount'],
                $request['m_curr'],
                $request['m_desc'],
                $request['m_status'],
            );

            //additional parameters
            if (isset($request['m_params'])) {
                $arHash[] = $request['m_params'];
            }
            $arHash[]  = $m_key;
            $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
            if ($request['m_sign'] == $sign_hash && $request['m_status'] == 'success') {
                $sessionValue = session('transInfo');
                $arr = $this->setTransactionArray($sessionValue);
                try {
                    $cryptoExchangeTransaction = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
                    if ($cryptoExchangeTransaction['status'] != 200) {
                        if (empty($cryptoExchangeTransaction['cryptoExchange'])) {
                            $this->sessionForget();
                            $this->helper->one_time_message('error', $cryptoExchangeTransaction['ex']['message']);
                            return redirect()->route('guest.crypto_exchange.home');
                        }
                    }
                    $data['cryptoExchange'] =  $cryptoExchange = $cryptoExchangeTransaction['cryptoExchange'];
                    clearActionSession();
                    return view('cryptoexchange::frontend.crypto.success', $data);
                } catch (\Exception $e) {
                    $this->sessionForget();
                    clearActionSession();
                    $this->helper->one_time_message('error', $e->getMessage());
                    return redirect()->route('guest.crypto_exchange.home');
                }
            } else {
                $this->sessionForget();
                clearActionSession();
                $this->helper->one_time_message('error', __('Please try again later!'));
                return back();
            }
        }
    }

    public function payeerPaymentStatus(Request $request)
    {
        return 'Payeer Status Page =>' . $request->all();
    }

    public function payeerPaymentFail()
    {
        $this->sessionForget();
        clearActionSession();
        $this->helper->one_time_message('error', __('You have cancelled your payment.'));
        return redirect()->route('guest.crypto_exchange.home');
    }

    public function bankList($banks, $currencyPaymentMethod)
    {
        $selectedBanks = [];
        $i = 0;
        foreach ($banks as $bank)
        {
            foreach ($currencyPaymentMethod as $cpm)
            {
                if ($bank->id == json_decode($cpm->method_data)->bank_id) {
                    $selectedBanks[$i]['id'] = $bank->id;
                    $selectedBanks[$i]['bank_name'] = $bank->bank_name;
                    $selectedBanks[$i]['is_default'] = $bank->is_default;
                    $selectedBanks[$i]['account_name'] = $bank->account_name;
                    $selectedBanks[$i]['account_number'] = $bank->account_number;
                    $i++;
                }
            }
        }
        return $selectedBanks;
    }

    public function payumoneyPaymentConfirm()
    {
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        if ($_POST['status'] == 'success') {
            try {
                actionSessionCheck();
                DB::beginTransaction();
                if (empty(session('transInfo'))) {
                    return redirect()->route('guest.crypto_exchange.home');
                }
                $sessionValue = session('transInfo');
                $arr = $this->setTransactionArray($sessionValue);
                $cryptoExchangeTransaction = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
                $data['cryptoExchange'] = $cryptoExchange = $cryptoExchangeTransaction['cryptoExchange'];
                DB::commit();
                $data['direction'] = $direction = ExchangeDirection::getDirection($cryptoExchange['from_currency'], $cryptoExchange['to_currency']);
                $data['transInfo']['send_amount'] = $cryptoExchange['amount'];
                $data['transInfo']['get_amount'] = $cryptoExchange['get_amount'];
                $data['transInfo']['finalAmount'] = $cryptoExchange['amount'] + $cryptoExchange['fee'];
                $data['transInfo']['dCurrencyRate'] = $cryptoExchange['exchange_rate'];
                $data['transInfo']['fee'] = $cryptoExchange['fee'];
                $data['transInfo']['uuid'] = $cryptoExchange['uuid'];
                $data['transInfo']['exchangeType'] = $direction->type;
                $data['transInfo']['fromCurrencyLogo'] = optional($direction->fromCurrency)->logo;
                $data['transInfo']['toCurrencyLogo'] = optional($direction->toCurrency)->logo;
                $data['transInfo']['trackUrl'] = url('track-transaction', $cryptoExchange['uuid']);
                //clearing session
                $this->sessionForget();
                clearActionSession();
                return view('cryptoexchange::frontend.crypto.success', $data);
            } catch (Exception $e) {
                DB::rollBack();
                $this->sessionForget();
                clearActionSession();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect()->route('guest.crypto_exchange.home');
            }
        }
    }

    public function payumoneyPaymentFail(Request $request)
    {
        if ($_POST['status'] == 'failure') {
            clearActionSession();
            $this->helper->one_time_message('error', __('You have cancelled your payment.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
    }

    public function cryptoPaymentSuccess()
    {
        if (empty(session('cryptoExchange'))) {
            return redirect()->route('guest.crypto_exchange.home');
        } else {
            actionSessionCheck();
            $data = [];
            $data['transInfo'] =  $cryptoExchange = session('cryptoExchange');
            $data['direction'] = $direction = ExchangeDirection::getDirection($cryptoExchange['from_currency'], $cryptoExchange['to_currency']);
            $data['transInfo']['send_amount'] = $cryptoExchange['amount'];
            $data['transInfo']['get_amount'] = $cryptoExchange['get_amount'];
            $data['transInfo']['finalAmount'] = $cryptoExchange['amount'] + $cryptoExchange['fee'];
            $data['transInfo']['dCurrencyRate'] = $cryptoExchange['exchange_rate'];
            $data['transInfo']['fee'] = $cryptoExchange['fee'];
            $data['transInfo']['uuid'] = $cryptoExchange['uuid'];
            $data['transInfo']['exchangeType'] = $direction->type;
            $data['transInfo']['fromCurrencyLogo'] = optional($direction->fromCurrency)->logo;
            $data['transInfo']['toCurrencyLogo'] = optional($direction->toCurrency)->logo;
            $data['transInfo']['trackUrl'] = url('track-transaction', $cryptoExchange['uuid']);
            //clearing session
            $this->sessionForget();
            clearActionSession();
            return view('cryptoexchange::frontend.crypto.success', $data);
        }
    }

    public function setTransactionArray($sessionValue, $fileName = '', $payment_details = '')
    {
        $sessionPhone = session('transInfo_crypto_phone');
        $sessionCryptoAddress = session('transInfo_crypto_address');
        $sessionReceivingDetails = session('transInfo_receiving_details');
        $bank = session('bank');
        $uuid = unique_code();
        $exchangeType = $sessionValue['from_type'];
        $transaction_type_id = ($exchangeType == 'crypto_swap') ? Crypto_Swap : (($exchangeType == 'crypto_buy') ? Crypto_Buy : Crypto_Sell) ;
        $paymentmethod = ($exchangeType == 'crypto_buy') ? session('payment_method_id') : NULL ;
        $arr                       = [
            'unauthorisedStatus'        => null,
            'user_id'                   => null,
            'toWalletCurrencyId'        => $sessionValue['to_currency'],
            'fromWalletCurrencyId'      => $sessionValue['from_currency'],
            'fromCurrencyCode'          => $sessionValue['fromCurrencyCode'],
            'toCurrencyCode'            => $sessionValue['currCode'],
            'finalAmount'               => $sessionValue['totalAmount'],
            'uuid'                      => $uuid,
            'receiver_address'          => $sessionCryptoAddress,
            'merchantAddress'           => $sessionValue['merchantAddress'],
            'verification_via'          => preference('verification'),
            'phone'                     => $sessionPhone,
            'payment_details'           => $payment_details,
            'receiving_details'         => $sessionReceivingDetails,
            'payment_method_id'         => $paymentmethod,
            'file_name'                 => $fileName,
            'destinationCurrencyExRate' => $sessionValue['dCurrencyRate'],
            'amount'                    => $sessionValue['send_amount'],
            'getAmount'                 => $sessionValue['get_amount'],
            'transaction_type_id'       => $transaction_type_id,
            'fee'                       => $sessionValue['totalFees'],
            'percentage'                => $sessionValue['percentage'],
            'charge_percentage'         => $sessionValue['feesPercentage'],
            'charge_fixed'              => $sessionValue['feesFixed'],
            'formattedChargePercentage' => $sessionValue['send_amount'] * ($sessionValue['feesFixed'] / 100),
            'bank_id'                   => $bank,
            'exchange_type'             => $exchangeType,
            'cryptoPayWith'             => '',
            'cryptoRecieve'             => '',
            'status'                    => 'Pending',
        ];
        return $arr;
    }

    public function getBankDetailOnChange(Request $request)
    {
        $bank = Bank::with('file:id,filename')->where(['id' => $request->bank])->first(['bank_name', 'account_name', 'account_number', 'file_id']);
        if ($bank) {
            $data['status'] = true;
            $data['bank']   = $bank;
            if (!empty($bank->file_id)) {
                $data['bank_logo'] = $bank->file->filename;
            }
        } else {
            $data['status'] = false;
            $data['bank'] = __('Bank Not Found');
        }
        return $data;
    }

    public function bankPayment(Request $request)
    {
        if (!$this->directionValidityCheck()) {
            $this->helper->one_time_message('error', __('Exchange direction not active, please try again.'));
            return redirect()->route('guest.crypto_exchange.home');
        }
        $data['transInfo'] = $sessionValue = session('transInfo');
        $paymentmethod = session('payment_method_id');
        Session::put('bank', $request->bank);
        $fileName = '';
        if (isset($request->proof_file)) {
            $fileName = insertDetailsFile($request->proof_file, public_path('uploads/files/crypto-details-file'));
            if (!$fileName) {
                $this->helper->one_time_message('error', __('Invalid file type.'));
                return redirect()->route('guest.crypto_exchange.home');
            }   
        }
        $arr = $this->setTransactionArray($sessionValue, $fileName);
        $response = $this->cryptoExchange->processExchangeMoneyConfirmation($arr, 'web');
        $data['transInfo'] =  $cryptoExchange = $response['cryptoExchange'];
        $data['direction'] = $direction = ExchangeDirection::getDirection($cryptoExchange['from_currency'], $cryptoExchange['to_currency']);
        $data['transInfo']['send_amount'] = $cryptoExchange['amount'];
        $data['transInfo']['get_amount'] = $cryptoExchange['get_amount'];
        $data['transInfo']['finalAmount'] = $cryptoExchange['amount'] + $cryptoExchange['fee'];
        $data['transInfo']['dCurrencyRate'] = $cryptoExchange['exchange_rate'];
        $data['transInfo']['fee'] = $cryptoExchange['fee'];
        $data['transInfo']['uuid'] = $cryptoExchange['uuid'];
        $data['transInfo']['exchangeType'] = $direction->type;
        $data['transInfo']['fromCurrencyLogo'] = optional($direction->fromCurrency)->logo;
        $data['transInfo']['toCurrencyLogo'] = optional($direction->toCurrency)->logo;
        $data['transInfo']['trackUrl'] = url('track-transaction', $cryptoExchange['uuid']);

        if ($response['status'] != 200) {
            if (empty($response['cryptoExchangeId'])) {
                Session::forget('transInfo');
                $this->helper->one_time_message('error', $response['ex']['message']);
                return redirect()->route('guest.crypto_exchange.home');
            }
        }
        $this->sessionForget();
        clearActionSession();
        return view('cryptoexchange::frontend.crypto.success', $data);

    }

    public function walletCheck()
    {
        $sessionValue = session('transInfo');
        $success['status'] = 200;
        if (empty($sessionValue['fromWallet'])) {
            $success['message'] = __('Wallet not Available');
            $success['status'] = 401;
        } elseif ($sessionValue['fromWallet']->balance < $sessionValue['finalAmount']) {
            $success['message'] = __('Balance not available');
            $success['status'] = 401;
        }
        return response()->json([
            'success' => $success,
        ]);
    }

    public function trackTransaction($uuid)
    {
        $data = ['menu' => 'Crypto Exchange'] ;
        $data['transInfo'] = CryptoExchange::with('fromCurrency:id,logo,code','toCurrency:id,logo,code')->where('uuid', $uuid)->firstOrFail();
        return view('cryptoexchange::frontend.crypto.transaction_track', $data);
    }

    public function directionValidityCheck()
    {
        $sessionValue = session('transInfo');
        if (empty($sessionValue)) {
            return false;
        }
        $direction = ExchangeDirection::getDirection($sessionValue['from_currency'], $sessionValue['to_currency']);
        if (empty($direction) || $direction->status == 'Inactive') {
            return false;
        }
        return true;
    }

    public function sessionForget()
    {
        session()->forget([
            'transInfo_receiving_details', 
            'transInfo_crypto_address', 
            'transInfo_crypto_phone', 
            'payment_method_id', 
            'transInfo', 
            'amount', 
            'bank' 
        ]);
    }

}
