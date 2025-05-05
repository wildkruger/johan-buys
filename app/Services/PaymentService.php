<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $encryptionKey = 'secret';

    public function __construct($key)
    {
        $this->encryptionKey = $key;
    }

    public function initiatePayment($data)
    {
        $returnUrl = parse_url($data['returnUrl'], PHP_URL_HOST) === 'localhost' ? 'https://webhook.site/d8823f9e-34df-4e06-b119-14c5669f0684' : $data['returnUrl'];

        $initiateData = [
            'PAYGATE_ID' => $data['paygate_id'],
            'REFERENCE' => 'pgtest_' . time(),
            'AMOUNT' => (int) $data['amount'] * 100,
            'CURRENCY' => $data['currency'],
            'RETURN_URL' => $returnUrl,
            'TRANSACTION_DATE' => now()->format('Y-m-d H:i:s'),
            'LOCALE' => 'en-za',
            'COUNTRY' => 'ZAF',
            'EMAIL' => $data['email'],
        ];

        // Log::info("message", ['data' => $initiateData]);

        // Calculate the checksum
        $initiateData['CHECKSUM'] = md5(implode('', $initiateData) . $this->encryptionKey);

        $initiateResult = $this->sendCurlRequest('https://secure.paygate.co.za/payweb3/initiate.trans', $initiateData);

        $responseData = [];
        parse_str($initiateResult, $responseData);

        return $responseData;
    }

    public function queryPayment($data)
    {
        $queryData = [
            'PAYGATE_ID'        => $data['PAYGATE_ID'],
            'PAY_REQUEST_ID'    => $data['PAY_REQUEST_ID'],
            'REFERENCE'         => $data['REFERENCE'],
        ];
        $queryData['CHECKSUM'] = md5(implode('', $queryData) . $this->encryptionKey);

        // Query Payment
        $queryResult = $this->sendCurlRequest('https://secure.paygate.co.za/payweb3/query.trans', $queryData);

        $responseData = [];
        parse_str($queryResult, $responseData);

        return $responseData;
    }

    public function parseResult($result) {
        $responseData = [];
        parse_str($result, $responseData);
        return $responseData;
    }

    public function sendCurlRequest($url, $data) {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
        $result = curl_exec($ch);
    
        curl_close($ch);
    
        return $result;
    }
}
