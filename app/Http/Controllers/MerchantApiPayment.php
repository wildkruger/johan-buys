<?php

namespace App\Http\Controllers;

use DB, Session, Auth, Exception;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\{AppToken,
    AppTransactionsInfo,
    MerchantPayment,
    Transaction,
    MerchantApp,
    Preference,
    Currency,
    Wallet
};
use App\Models\Merchant;
use App\Models\PaymentMethod;
use App\Models\CurrencyPaymentMethod;
use Illuminate\Support\Facades\Artisan;

class MerchantApiPayment extends Controller
{
    protected $helper;
    public function __construct()
    {
        $this->helper = new Common();
    }

    public function verifyClient(Request $request)
    {
        $app      = $this->verifyClientIdAndClientSecret($request->client_id, $request->client_secret);
        $response = $this->createAccessToken($app); //will expire in one hour
        return json_encode($response);
    }

    protected function verifyClientIdAndClientSecret($client_id, $client_secret)
    {
        $app = MerchantApp::where(['client_id' => $client_id, 'client_secret' => $client_secret])->first();
        if (!$app)
        {
            $res = [
                'status'  => 'error',
                'message' => 'Can not verify the client. Please check client Id and Client Secret',
                'data'    => [],
            ];
            return json_encode($res);
        }
        return $app;
    }

    protected function createAccessToken($app)
    {
        $token = $app->accessToken()->create(['token' => Str::random(26), 'expires_in' => time() + 3600]);
        $res   = [
            'status'  => 'success',
            'message' => 'Client Verified',
            'data'    => [
                'access_token' => $token->token,
            ],
        ];
        return $res;
    }

    /**
     * [Generat URL]
     * @param  Request $request  [email, password]
     * @return [view]  [redirect to merchant confirm page or redirect back]
     */
    public function generatedUrl(Request $request)
    {
        try {
            $token = $request->token;
            $grantId = $request->grant_id;

            $appTransactionsInfo = AppTransactionsInfo::getAppTransactionInfo($token, $grantId);

            if (!$appTransactionsInfo) {
                return redirect('payment/fail')->withError(__('Merchant not found!'));
            }

            $merchant = Merchant::getMerchant($appTransactionsInfo->app?->merchant_id);

            if (!$merchant) {
                return redirect('payment/fail')->withError(__('Merchant not found!'));
            }

            $userStatus = $this->helper->getUserStatus($merchant->user?->status);

            if ($userStatus === 'Suspended') {
                return view('merchantPayment.user_suspended', ['message' => __('Merchant is suspended!')]);
            } elseif ($userStatus === 'Inactive') {
                return view('merchantPayment.user_inactive', ['message' => __('Merchant is inactive!')]);
            }

            $paygate = \App\Models\PaymentMethod::where(['name' => 'Paygate'])->first(['id']);
            $currencyPaymentMethod = \App\Models\CurrencyPaymentMethod::where(['currency_id' => $merchant->currency->id, 'method_id' => $paygate->id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);

            if (empty($currencyPaymentMethod)) {
                return redirect('payment/fail')->withError(__('Currency Payment Method data not found!'));
            }

            $data = [
                'isMerchantAvailable' => true,
                'paymentInfo' => [
                    'grant_id' => base64_encode($grantId),
                    'token' => base64_encode($token)
                ],
                'payment_methods' => PaymentMethod::getActivePaymentMethods(),
                'merchant' => $merchant,
                'cpm' => PaymentMethod::getCurrencyPaymentMethods($merchant->currency->id),
                'amount' => $appTransactionsInfo->amount,
                'mathodData' => json_decode($currencyPaymentMethod->method_data),
            ];

            return view('merchantPayment.payment_methods', $data);
        } catch (Exception $e) {
            return redirect('payment/fail')->withError(__($e->getMessage())); 
        }
    }

    public function merchantWalletPayment(Request $request,  $grantId, $token)
    {
        if (!auth()->check()) {
            return $this->processPaymentForGuestUser($request, $grantId, $token);
        } else {
            return $this->processPaymentForAuthenticatedUser($request, $grantId, $token);
        }
    }

    private function processPaymentForGuestUser(Request $request, $grantId, $token)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->only('email', 'password');
            if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
                $this->setDefaultSessionValues();
                $transInfo = $this->getTransactionData(base64_decode($grantId), base64_decode($token));

                if ($this->canProceedWithPayment($transInfo)) {
                    return view('merchantPayment.confirm', $this->checkoutToPaymentConfirmPage($transInfo));
                }
            }
        }

        return view('merchantPayment.login', ['setting' => settings('general')]);
    }

    private function processPaymentForAuthenticatedUser(Request $request, $grantId, $token)
    {
        $transInfo = $this->getTransactionData(base64_decode($grantId), base64_decode($token));

        if (!empty($transInfo) && $this->canProceedWithPayment($transInfo)) {
            return view('merchantPayment.confirm', $this->checkoutToPaymentConfirmPage($transInfo));
        }

        return redirect()->to('payment/fail');
    }

    private function canProceedWithPayment($transInfo)
    {
        $loggedInUser = auth()->user();

        if ($transInfo->app->merchant->user->id == $loggedInUser->id) {
            auth()->logout();
            $this->helper->one_time_message('error', __('Merchant cannot make payment to himself!'));
            return false;
        }

        $userStatus = $this->helper->getUserStatus($loggedInUser->status);

        if ($userStatus === 'Suspended') {
            $data['message'] = __('You are suspended to do any kind of transaction!');
            return view('merchantPayment.user_suspended', $data);
        } elseif ($userStatus === 'Inactive') {
            auth()->logout();
            $this->helper->one_time_message('danger', __('Your account is inactivated. Please try again later!'));
            return redirect('/login');
        }

        return true;
    }

    public function paygatePayment(Request $request)
    {
        Artisan::call('optimize:clear');
        $data['message'] = "Fail";
        $data['status']  = 401;
        try {
            $validation = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'amount'      => 'required|numeric',
                'grant_id'    => 'required',
                'token'       => 'required',
            ]);
            if ($validation->fails()) {
                $data['message'] = $validation->errors()->first();
                return response()->json(['data' => $data]);
            }

            $transInfo = AppTransactionsInfo::with([
                'app:id,merchant_id',
                'app.merchant:id,user_id,business_name,fee',
                'app.merchant.user:id,first_name,last_name,email',
            ])->where(['grant_id' => base64_decode($request->grant_id), 'token' => base64_decode($request->token), 'status' => 'pending'])->where('expires_in', '>=', time())
            ->first(['id', 'app_id', 'payment_method', 'currency', 'amount', 'success_url', 'cancel_url']);

            if (!$transInfo) {
                $data['message']  = __("Merchant not found!");
                return response()->json([
                    'data' => $data
                ]);
            }
            $currencyCode = $transInfo->currency;
            $currency = Currency::where('code', $currencyCode)->first(['id', 'code']);

            if ($currency->code != "ZAR") {
                $data['message'] = __('You can not pay using this currency.');
                return response()->json(['data' => $data]);
            }
            
            $data['currency_id'] = $currency->id;
            $data['merchant_id'] = $merchant_id = $transInfo->app->merchant_id;

            $merchantChk = \App\Models\Merchant::find($merchant_id, ['id', 'user_id', 'status', 'fee', 'currency_id']);

            if (!$merchantChk) {
                $data['message'] = __('Merchant not found!');
                return response()->json(['data' => $data]);
            }
            if ($merchantChk->status != 'Approved') {
                $data['message'] = __('Merchant not approved!');
                return response()->json(['data' => $data]);
            }

            $data['user_id'] = $merchantChk->user_id;

            $amount = $request->amount;
            $paymentMethod = \App\Models\PaymentMethod::where(['name' => 'Paygate'])->first(['id']);
            $data['payment_method_id'] = $paymentMethod->id;
            $currencyPaymentMethod = \App\Models\CurrencyPaymentMethod::where(['currency_id' => $currency->id, 'method_id' => $paymentMethod->id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData = json_decode($currencyPaymentMethod->method_data);
            
            $data['percentage'] = $merchantChk->fee;
            $data['charge_percentage'] = ($transInfo->app->merchant->fee * $amount) / 100;

            if (empty($methodData) && (empty($methodData->paygate_id) || empty($methodData->encryption_key))) {
                $data['message'] = 'method data of currency' . $currencyCode . ' not found!';
                return response()->json(['data' => $data]);
            }

            $data['amount'] = $amount;

            $paymentService = new \App\Services\PaymentService($methodData->encryption_key);
            $initiateResult = $paymentService->initiatePayment([
                'paygate_id' => $methodData->paygate_id,
                'currency' => $currency->code,
                'amount' => $amount,
                'email' => $request->email,
                'returnUrl' => route('paygate.payment.success', [
                    'success_url' => $transInfo->success_url,
                    'cancel_url' => $transInfo->success_url
                ]),
            ]);

            if (array_key_exists('ERROR', $initiateResult)) {
                $data['message'] = __($initiateResult['ERROR']);
                return response()->json(['data' => $data]);
            }
            $data['uuid'] = $initiateResult['PAY_REQUEST_ID'];
            $data['status'] = "Pending";
            $data['note'] = $initiateResult['REFERENCE'];

            $payment = (new MerchantPayment())->createMerchantPayment($data);
            $data['transaction_reference_id'] = $payment->id;
            $data['subtotal'] = $data['amount'] - $data['charge_percentage'];
            $data['total'] = $data['amount'];
            (new Transaction())->createTransaction($data);
            
            return view('user_dashboard.deposit.payweb3', ['result' => $initiateResult]);
        } catch (Exception $e) {
            $data['message'] =  $e->getMessage();
            return response()->json(['data' => $data]);
        }
    }

    public function paygateSuccessPayment(Request $request)
    {
        try {

            $paygateInfo = $request->except([
                'TRANSACTION_STATUS', 'success_url', 'cancel_url'
            ]);
            
            $transaction = Transaction::where(['uuid' => $paygateInfo['PAY_REQUEST_ID']])->first();

            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $transaction->currency_id, 'method_id' => $transaction->payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData = json_decode($currencyPaymentMethod->method_data);

            $paygateInfo['PAYGATE_ID'] = $methodData->paygate_id;
            $paygateInfo['REFERENCE'] = $transaction->note;

            $paymentService = new \App\Services\PaymentService($methodData->encryption_key);
            $doQueryResponse = $paymentService->queryPayment($paygateInfo);

            if ($doQueryResponse['TRANSACTION_STATUS'] == "1") {

                DB::beginTransaction();

                $transaction->status = "Success";
                $transaction->save();

                $merchantPayment = MerchantPayment::find($transaction->transaction_reference_id);
                $merchantPayment->status = "Success";
                $merchantPayment->save();

                $merchantWallet = Wallet::where(['user_id' => $transaction->user_id, 'currency_id' => $transaction->currency_id])->first(['id', 'balance']);
                if (empty($merchantWallet)) {
                    $wallet              = new Wallet();
                    $wallet->user_id     = $transaction->user_id;
                    $wallet->currency_id = $transaction->currency_id;
                    $wallet->balance     = $merchantPayment->amount;
                    $wallet->is_default  = 'No';
                    $wallet->save();
                } else {
                    $merchantWallet->balance = ($merchantWallet->balance + $merchantPayment->amount);
                    $merchantWallet->save();
                }
                \DB::commit();
                // Send mail to admin
                $response = $this->helper->sendTransactionNotificationToAdmin('payment', ['data' => $merchantPayment]);
                return redirect()->to($request->success_url);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect()->to($request->cancel_url);
        }
    }

    protected function checkoutToPaymentConfirmPage($transInfo)
    {
        //check expired or not
        if (!$transInfo)
        {
            abort(403, 'Url has been deleted or expired.');
        }
        //Check whether merchant is suspended
        $checkExpressMerchantUser = $this->helper->getUserStatus($transInfo->app->merchant->user->status);
        if ($checkExpressMerchantUser == 'Suspended')
        {
            $data['message'] = __('Merchant is suspended!');
            $data['status'] = $checkExpressMerchantUser;
            return $data;
        }
        //Check whether merchant is Inactive
        elseif ($checkExpressMerchantUser == 'Inactive')
        {
            $data['message'] = __('Merchant is inactive!');
            $data['status'] = $checkExpressMerchantUser;
            return $data;
        }
        else
        {
            $data['status'] = 'Active'; //used to eliminate errors if paid by user is active
        }
        //check if currency exists in wallets
        $availableCurrency = [];
        $wallets           = Wallet::with(['currency:id,code'])->where(['user_id' => $transInfo->app->merchant->user->id])->get(['currency_id']); //2.3
        foreach ($wallets as $wallet)
        {
            $availableCurrency[] = $wallet->currency->code;
        }
        if (!in_array($transInfo->currency, $availableCurrency))
        {
            $this->helper->one_time_message('error', __('The :x wallet does not exist for the payment', ['x' => $transInfo->currency]));
            return redirect()->to('payment/fail');
        }

        $data['currSymbol'] = $currSymbol = Currency::where('code', $transInfo->currency)->first(['symbol'])->symbol;
        $data['transInfo']  = $transInfo;

        //Put transaction informations to Session
        Session::put('transInfo', $transInfo);
        return $data;
    }

    public function storeTransactionInfo(Request $request)
    {
        $paymentMethod = $request->payer;
        $amount        = $request->amount;
        $currency      = $request->currency;
        $successUrl    = $request->successUrl;
        $cancelUrl     = $request->cancelUrl;

        # check token missing
        $hasHeaderAuthorization = $request->hasHeader('Authorization');
        if (!$hasHeaderAuthorization)
        {
            $res = [
                'status'  => 'error',
                'message' => 'Access token is missing',
                'data'    => [],
            ];
            return json_encode($res);
        }

        # check token authorization
        $headerAuthorization = $request->header('Authorization');
        $token               = $this->checkTokenAuthorization($headerAuthorization);

        # Currency Validation
        $res = $this->currencyValidaation($token, $currency);
        if (!empty($res['status']))
        {
            return json_encode($res);
        }

        # Amount Validation
        $res = $this->amountValidaation($amount);
        if (!empty($res['status']))
        {
            return json_encode($res);
        }

        if (false)
        {
            $res = [
                'status'  => 'error',
                'message' => 'Validation error',
                'data'    => [],
            ];
            return json_encode($res);
        }

        # Update/Create AppTransactionsInfo and return response
        $res = $this->updateOrAppTransactionsInfoAndReturnResponse($token->app_id, $paymentMethod, $amount, $currency, $successUrl, $cancelUrl);
        return json_encode($res);
    }

    /**
     * [Set Necessary Values To Session]
     */
    protected function setDefaultSessionValues()
    {
        $preferences = Preference::getAll()->where('field', '!=', 'dflt_lang');
        if (!empty($preferences)) {
            foreach ($preferences as $pref)
            {
                $pref_arr[$pref->field] = $pref->value;
            }
        }
        if (!empty($preferences)) {
            Session::put($pref_arr);
        }

        // default_currency
        if (!empty(settings('default_currency'))) {
            Session::put('default_currency', settings('default_currency'));
        }

        //default_timezone
        $default_timezone = auth()->user()->user_detail->timezone;
        if (!$default_timezone) {
            Session::put('dflt_timezone_user', session('dflt_timezone'));
        } else {
            Session::put('dflt_timezone_user', $default_timezone);
        }

        // default_language
        if (!empty(settings('default_language'))) {
            Session::put('default_language', settings('default_language'));
        }

        // company_name
        if (!empty(settings('name'))) {
            Session::put('name', settings('name'));
        }

        // company_logo
        if (!empty(settings('logo'))) {
            Session::put('company_logo', settings('logo'));
        }
    }

    /**
     * [check Token Authorization]
     * @param  [request] $headerAuthorization [header authorization request]
     * @return [string]  [token]
     */
    protected function checkTokenAuthorization($headerAuthorization)
    {
        $accessToken = $headerAuthorization;
        $tokenType   = '';
        $actualToken = '';
        if (preg_match('/\bBearer\b/', $accessToken))
        {
            $tokenType   = 'bearer';
            $t           = explode(' ', $accessToken);
            $key         = array_keys($t);
            $last        = end($key);
            $actualToken = $t[$last];
        }
        $token = AppToken::where('token', $actualToken)->where('expires_in', '>=', time())->first();
        if (!$token)
        {
            $res = [
                'status'  => 'error',
                'message' => 'Unauthorized token or token has been expired',
                'data'    => [],
            ];
            return json_encode($res);
        }
        return $token;
    }

    protected function currencyValidaation($token, $currency)
    {
        $acceptedCurrency = [];
        $wallets          = $token->app->merchant->user->wallets;
        foreach ($wallets as $wallet)
        {
            $acceptedCurrency[] = $wallet->currency->code;
        }
        //TODO:: Accepted currency will come from database or from merchant currency

        $res = ['status' => ''];
        if (!in_array($currency, $acceptedCurrency))
        {
            $res = [
                'status'  => 'error',
                'message' => 'Currency ' . $currency . ' is not supported by this merchant!',
                'data'    => [],
            ];
        }
        return $res;
    }

    protected function amountValidaation($amount)
    {
        $res = ['status' => ''];
        if ($amount <= 0)
        {
            $res = [
                'status'  => 'error',
                'message' => 'Amount cannot be 0 or less than 0.',
                'data'    => [],
            ];
        }
        return $res;
    }

    protected function updateOrAppTransactionsInfoAndReturnResponse($tokenAppId, $paymentMethod, $amount, $currency, $successUrl, $cancelUrl)
    {
        try
        {
            $grandId  = random_int(10000000, 99999999);
            $urlToken = Str::random(20);

            AppTransactionsInfo::updateOrCreate([
                'app_id'         => $tokenAppId,
                'payment_method' => $paymentMethod,
                'amount'         => $amount,
                'currency'       => $currency,
                'success_url'    => $successUrl,
                'cancel_url'     => $cancelUrl,
                'grant_id'       => $grandId,
                'token'          => $urlToken,
                'status'         => 'pending',
                'expires_in'     => time() + (60 * 60 * 5), // url will expire in 5 hours after generation
            ]);

            $url = url("merchant/payment?grant_id=$grandId&token=$urlToken");
            $res = [
                'status'  => 'success',
                'message' => '',
                'data'    => [
                    'approvedUrl' => $url,
                ],
            ];
            return $res;
        }
        catch (Exception $e)
        {
            print $e;
            exit;
        }
    }

    public function confirmPayment()
    {
        if (!auth()->check())
        {
            $getLoggedInCredentials = Session::get('credentials');
            if (Auth::attempt($getLoggedInCredentials))
            {
                $this->setDefaultSessionValues();
                $successPath = $this->storePaymentInformations();
                return redirect()->to($successPath);
            }
            else
            {
                $this->helper->one_time_message('error', __('Unable to login with provided credentials!'));
                return redirect()->back();
            }
        }
        $this->setDefaultSessionValues();
        $data = $this->storePaymentInformations();
        if ($data['status'] == 200)
        {
            return redirect()->to($data['successPath']);
        }
        else
        {
            if ($data['status'] == 401)
            {
                $this->helper->one_time_message('error', __('The :x does not exist.', ['x' => __('currency')]));
            }
            elseif ($data['status'] == 402)
            {
                $this->helper->one_time_message('error', __('User does not have the wallet - :x. Please exchange to wallet - :x.', ['x' => $data['currency']]));
            }
            elseif ($data['status'] == 403)
            {
                $this->helper->one_time_message('error', __('User does not have sufficient balance.'));
            }
            return redirect()->to('payment/fail');
        }
        Session::forget('transInfo');
    }

    protected function storePaymentInformations()
    {
        $transInfo = Session::get('transInfo');
        $unique_code = unique_code();
        $amount      = $transInfo->amount;
        $currency    = $transInfo->currency;
        $p_calc      = ($transInfo->app->merchant->fee / 100) * $amount;

        //Check currency exists in system or not
        $curr = Currency::where('code', $currency)->first(['id']);
        if (!$curr)
        {
            DB::rollBack();
            $data['status'] = 401;
            return $data;
        }

        $senderWallet = Wallet::where(['user_id' => auth()->user()->id, 'currency_id' => $curr->id])->first(['id', 'balance']);
        if (!$senderWallet)
        {
            DB::rollBack();
            $data['status']   = 402;
            $data['currency'] = $transInfo->currency;
            return $data;
        }

        if ($senderWallet->balance < $amount)
        {
            DB::rollBack();
            $data['status'] = 403;
            return $data;
        }

        try
        {
            DB::beginTransaction();

            $data = [];

            //Check User has the wallet or not
            $senderWallet->balance = $senderWallet->balance - $amount;
            $senderWallet->save();

            // Add on merchant
            $merchantPayment                    = new MerchantPayment();
            $merchantPayment->merchant_id       = $transInfo->app->merchant_id;
            $merchantPayment->currency_id       = $curr->id;
            $merchantPayment->payment_method_id = 1;
            $merchantPayment->user_id           = auth()->user()->id;
            $merchantPayment->gateway_reference = $unique_code;
            $merchantPayment->order_no          = '';
            $merchantPayment->item_name         = '';
            $merchantPayment->uuid              = $unique_code;
            $merchantPayment->charge_percentage = $p_calc;
            $merchantPayment->charge_fixed      = 0;
            $merchantPayment->amount            = $amount - $p_calc;
            $merchantPayment->total             = $amount;
            $merchantPayment->status            = 'Success';
            $merchantPayment->save();

            $transaction_A                           = new Transaction();
            $transaction_A->user_id                  = auth()->user()->id;
            $transaction_A->end_user_id              = $transInfo->app->merchant->user_id;
            $transaction_A->merchant_id              = $transInfo->app->merchant_id;
            $transaction_A->currency_id              = $curr->id;
            $transaction_A->payment_method_id        = 1;
            $transaction_A->uuid                     = $unique_code;
            $transaction_A->transaction_reference_id = $merchantPayment->id;
            $transaction_A->transaction_type_id      = Payment_Sent;
            $transaction_A->subtotal                 = $amount;
            $transaction_A->percentage               = $transInfo->app->merchant->fee;
            $transaction_A->charge_percentage        = 0;
            $transaction_A->charge_fixed             = 0;
            $transaction_A->total                    = '-' . ($transaction_A->subtotal);
            $transaction_A->status                   = 'Success';
            $transaction_A->save();

            $transaction_B                           = new Transaction();
            $transaction_B->user_id                  = $transInfo->app->merchant->user_id;
            $transaction_B->end_user_id              = auth()->user()->id;
            $transaction_B->merchant_id              = $transInfo->app->merchant_id;
            $transaction_B->currency_id              = $curr->id;
            $transaction_B->payment_method_id        = 1;
            $transaction_B->uuid                     = $unique_code;
            $transaction_B->transaction_reference_id = $merchantPayment->id;
            $transaction_B->transaction_type_id      = Payment_Received;
            $transaction_B->subtotal                 = $amount - ($p_calc);
            $transaction_B->percentage               = $transInfo->app->merchant->fee; //fixed
            $transaction_B->charge_percentage        = $p_calc;
            $transaction_B->charge_fixed             = 0;
            $transaction_B->total                    = $transaction_B->charge_percentage + $transaction_B->subtotal;
            $transaction_B->status                   = 'Success';
            $transaction_B->save();


            $transInfo->status = 'success';
            $transInfo->save();

            //updating/Creating merchant wallet
            $merchantWallet          = Wallet::where(['user_id' => $transInfo->app->merchant->user_id, 'currency_id' => $curr->id])->first(['id', 'balance']);
            if (empty($merchantWallet))
            {
                $wallet              = new Wallet();
                $wallet->user_id     = $transInfo->app->merchant->user_id;
                $wallet->currency_id = $curr->id;
                $wallet->balance     = ($amount - $p_calc);
                $wallet->is_default  = 'No';
                $wallet->save();
            }
            else
            {
                $merchantWallet->balance = $merchantWallet->balance + ($amount - $p_calc); //fixed -- not amount with fee(total); only amount)
                $merchantWallet->save();
            }

            DB::commit();

            // Send mail to admin
            $this->helper->sendTransactionNotificationToAdmin('payment', ['data' => $merchantPayment]);

            //pass the response to success url
            $response = [
                'status'         => 200,
                'transaction_id' => $merchantPayment->uuid,
                'merchant'       => $merchantPayment->merchant->user->first_name . ' ' . $merchantPayment->merchant->user->last_name,
                'currency'       => $merchantPayment->currency->code,
                'fee'            => $merchantPayment->charge_percentage,
                'amount'         => $merchantPayment->amount,
                'total'          => $merchantPayment->total,
            ];
            $response            = json_encode($response);
            $encodedResponse     = base64_encode($response);
            $successPath         = $transInfo->success_url . '?' . $encodedResponse;
            $data['status']      = 200;
            $data['successPath'] = $successPath;
            return $data;
        }
        catch (Exception $e)
        {
            DB::rollBack();
            $this->helper->one_time_message('error', $e->getMessage());
            return redirect()->to('payment/fail');
        }
    }

    public function cancelPayment()
    {
        $transInfo     = Session::get('transInfo');
        $trans         = AppTransactionsInfo::find($transInfo->id, ['id', 'status', 'cancel_url']);
        $trans->status = 'cancel';
        $trans->save();
        Session::forget('transInfo');
        return redirect()->to($trans->cancel_url);
    }

    protected function getTransactionData($grant_id,$token)
    {
        return AppTransactionsInfo::with([
            'app:id,merchant_id',
            'app.merchant:id,user_id,business_name,fee',
            'app.merchant.user:id,first_name,last_name,status',
        ])
        ->where(['grant_id' => $grant_id, 'token' => $token, 'status' => 'pending'])->where('expires_in', '>=', time())
        ->first(['id', 'app_id', 'payment_method', 'currency', 'amount', 'success_url', 'cancel_url']);
    }
}
