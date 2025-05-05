<?php

namespace Modules\MerchantPayLink\Http\Controllers;

use DateTime;
use Exception;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\FeesLimit;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\CurrencyPaymentMethod;
use Illuminate\Contracts\Support\Renderable;
use Modules\MerchantPayLink\Entities\ProfilePayment;
use Modules\MerchantPayLink\Services\PaylinkService;
use Modules\MerchantPayLink\Services\PaymentMethodService;
use Modules\MerchantPayLink\Http\Requests\StorePaylinkRequest;

class MerchantPayLinkController extends Controller
{
    protected $paylinkService;
    protected $paymentMethodService;

    public function __construct(PaylinkService $paylinkService, PaymentMethodService $paymentMethodService)
    {
        $this->paylinkService = $paylinkService;
        $this->paymentMethodService = $paymentMethodService;
    }

    public function showPaymentPage(string $paylinkCode)
    {
        $user = $this->paylinkService->getUserByPaylinkCode($paylinkCode);

        clearPaymentSession('payment_data');

        if (is_null($user)) {
            echo __('User not found or Invalid link');
            exit;
        }

        $currency = Currency::where([
            'code' => 'ZAR',
            'status' => 'Active',
        ])->first();

        if (is_null($currency)) {
            echo __('Currency not set! Please contact with Administrator.');
            exit;
        }

        $paymentMethod = PaymentMethod::where([
            'name' => 'Paygate',
            'status' => 'Active',
        ])->first();

        if (is_null($paymentMethod)) {
            echo __('Payment method not set! Please contact with Administrator.');
            exit;
        }

        $feesLimit = FeesLimit::where([
            'transaction_type_id' => Profile_payment,
            'currency_id' => $currency->id,
            'payment_method_id' => $paymentMethod->id,
            'has_transaction' => 'Yes',
        ])->first();

        if (is_null($feesLimit)) {
            echo __('Fees limit not set! Please contact with Administrator.');
            exit;
        }

        $paymentData = [
            'paylinkUrl' => generatePublicUrl($user),
            'paylinkCode' => $paylinkCode,
            'user_id' => $user->id,
            'currency_code' => $currency->code,
            'payment_method_name' => $paymentMethod->name,
            'percentage' => $feesLimit->charge_percentage,
            'charge_fixed' => $feesLimit->charge_fixed,
        ];

        storePaymentSession('payment_data', $paymentData);

        return view('merchantpaylink::paylink.show', compact('user', 'currency', 'paymentMethod', 'feesLimit'));
    }

    public function storePayment(StorePaylinkRequest $request)
    {
        try {
            $paymentMethodId = $request->payment_method_id;

            $paymentMethod = PaymentMethod::find($paymentMethodId, ['id', 'name', 'status']);
            $paymentMethodName = ucfirst(strtolower($paymentMethod->name));

            if (!$paymentMethod) {
                return redirect()->back()->with('error', __('Payment method not found!'));
            }

            $paymentData = getPaymentSessionData('payment_data');
            storePaymentSession('payment_data', array_merge($request->all(), [
                'transaction_type_id' => Profile_payment,
                'charge_percentage' => $request->amount * $paymentData['percentage'] / 100,
                'charge_fixed' => $paymentData['charge_fixed'] ?? 0,
                'total_fee' => $request->amount * $paymentData['percentage'] / 100 + $paymentData['charge_fixed'],
            ]));

            if ($paymentMethodName == 'Paygate') {
                return redirect()->route('paylink.paygate');
            }
        } catch (Exception $e) {
            return redirect($paymentData['paylinkUrl'])->with('error', $e->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function paygate()
    {
        $data['paymentData'] = getPaymentSessionData('payment_data');
        
        if (!$data['paymentData']) {
            return redirect($data['paymentData']['paylinkUrl'])->with('error', __('Payment data not found!'));
        }

        return view('merchantpaylink::paylink.paygate', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function paygatePayment(Request $request)
    {
        try {

            $validated = $request->validate([
                'email' => 'required|email'
            ]);

            DB::beginTransaction();

            
            $paymentData = getPaymentSessionData('payment_data');
            $currency = Currency::find($paymentData['currency_id']);

            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $paymentData['currency_id'], 'method_id' => $paymentData['payment_method_id']])->where('activated_for', 'like', "%deposit%")->first(['method_data']);

            if (is_null($currencyPaymentMethod)) {
                throw new Exception(__('Currency payment method data is unavailable. Please contact with your system administrator for assistance.'));
            }

            $method_data = json_decode($currencyPaymentMethod->method_data);

            $encryptionKey = $method_data->encryption_key;
            $dateTime = new DateTime();
            $returnUrl =  url('paylink/paygate-return-url');


            if (parse_url($returnUrl, PHP_URL_HOST) === 'localhost' || parse_url($returnUrl, PHP_URL_HOST) === '127.0.0.1') {
                throw new Exception(__('It is not possible to make a deposit using Paygate on a localhost environment.'));
            }

            if ('ZAR' != $currency->code) {
                throw new Exception(__('Paygate does not support this currency.'));
            }

            $returnUrl = parse_url($returnUrl, PHP_URL_HOST) === 'localhost' ? 'https://webhook.site/c16f60d4-681b-4a47-890b-68719d9cfc52' : $returnUrl;

            
            $postData = array(
                'PAYGATE_ID'        => (int) $method_data->paygate_id,
                'REFERENCE'         => 'pgtest_' . time(),
                'AMOUNT'            => $paymentData['amount'] * 100,
                'CURRENCY'          => $currency->code,
                'RETURN_URL'        => $returnUrl,
                'TRANSACTION_DATE'  => $dateTime->format('Y-m-d H:i:s'),
                'LOCALE'            => 'en-za',
                'COUNTRY'           => 'ZAF',
                'EMAIL'             => $validated['email'],
            );
            
            $checksum = md5(implode('', $postData) . $encryptionKey);
            $postData['CHECKSUM'] = $checksum;
            $queryRequest = $this->initiateTrans($postData);

            $result = [];
            parse_str($queryRequest, $result);

            \Log::info($result);


            $process_url = 'https://secure.paygate.co.za/payweb3/process.trans';

            $data['result'] = $result;
            $data['process_url'] = $process_url;

            storePaymentSession('payment_data', array_merge($request->all(), [
                'uuid' => $result['PAY_REQUEST_ID'],
                'subtotal' => $paymentData['amount'] - $paymentData['total_fee'],
                'total' => $paymentData['amount'],
                'status' => 'Pending',
            ]));

            $paymentStoreData = getPaymentSessionData('payment_data');
            $paymentStoreData['payer_details'] = [
                'first_name' => $paymentStoreData['first_name'],
                'last_name' => $paymentStoreData['last_name'],
                'email' => $paymentStoreData['email']
            ];

            $profilePayment = (new \Modules\MerchantPayLink\Entities\ProfilePayment())->createProfilePayment($paymentStoreData);
            $paymentStoreData['transaction_reference_id'] = $profilePayment->id;
            $paymentStoreData['REFERENCE'] = $result['REFERENCE'];
            $transaction = (new Transaction())->createTransaction($paymentStoreData);


            $response = (new \App\Http\Helpers\Common())->sendTransactionNotificationToAdmin('payment', ['data' => $profilePayment]);


            DB::commit();

            return view('user_dashboard.deposit.payweb3', $data);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(getPaymentSessionData('payment_data')['paylinkUrl'])->with('error', $e->getMessage().' '.$e->getLine() . ' '.$e->getFile());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function paygatePaymentSuccess()
    {
        if (empty(session('transaction'))) {
            return redirect('deposit');
        } else {
            $data['transaction'] = session('transaction');
            return view('user_dashboard.deposit.success', $data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function paygateReturnResponse(Request $request)
    {
        $arrayValue = $request->all();
        try {

            $transaction = Transaction::where(['uuid' => $arrayValue['PAY_REQUEST_ID']])->first();

            $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => $transaction->currency_id, 'method_id' => $transaction->payment_method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $method_data = json_decode($currencyPaymentMethod->method_data);
            $encryptionKey = $method_data->encryption_key;

            $sessionData['encryptionKey'] = $encryptionKey;
            $sessionData['PAYGATE_ID'] = $method_data->paygate_id;
            $sessionData['REFERENCE'] = $transaction->note;
            $sessionData['PAY_REQUEST_ID'] = $arrayValue['PAY_REQUEST_ID'];

            
            $doQueryResponse = $this->doQuery($sessionData);

            $queryResponse = [];
            parse_str($doQueryResponse, $queryResponse);

            if ($queryResponse['TRANSACTION_STATUS'] == "1" && $transaction->status != "Success") {
                
                DB::beginTransaction();

                $transaction->status = "Success";
                $transaction->save();
                
                $profilePayment = ProfilePayment::find($transaction->transaction_reference_id);
                $profilePayment->status = "Success";
                $profilePayment->save();

                if ($transaction->payment_method_id == Paygate) {

                    $wallet = Wallet::where([
                        'user_id'     => $transaction->user_id,
                        'currency_id' => $transaction->currency_id,
                    ])->first();
                    
                    if ($wallet) {
                        $wallet->increment('balance', $transaction->subtotal);
                    } else {
                        Wallet::create([
                            'user_id'     => $transaction->user_id,
                            'currency_id' => $transaction->currency_id,
                            'balance'     => $transaction->subtotal,
                        ]);
                    }
                }
                
                DB::commit();

                $data['transaction'] = $transaction;
                return view('merchantpaylink::paylink/success', $data);
            } else {
                DB::rollBack();
                return redirect('/');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/');
        }
    }

    public function getPaymentMethods(Request $request)
    {
        $paymentMethods = $this->paymentMethodService->getActivePaymentMethods(
            $request->transaction_type_id,
            $request->currency_id
        );

        return response()->json($paymentMethods);
    }

    public function initiateTrans($data) 
    {
        $fieldsString = http_build_query($data);
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://secure.paygate.co.za/payweb3/initiate.trans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function doQuery($sessionData)
    {
        $encryptionKey = $sessionData['encryptionKey'];
        $data = array(
            'PAYGATE_ID'        => $sessionData['PAYGATE_ID'],
            'PAY_REQUEST_ID'    => $sessionData['PAY_REQUEST_ID'],
            'REFERENCE'         => $sessionData['REFERENCE'],
        );
        $checksum = md5(implode('', $data) . $encryptionKey);
        $data['CHECKSUM'] = $checksum;
        $fieldsString = http_build_query($data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://secure.paygate.co.za/payweb3/query.trans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
