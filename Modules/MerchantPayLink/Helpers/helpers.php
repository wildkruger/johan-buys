<?php

if (!function_exists('generateUniqueToken')) {
    /**
     * Generate unique token
     *
     * @param object $user
     * @return string
     */
    function generateUniqueToken($user)
    {
        $username = strtolower($user->first_name . $user->last_name);
        $encodedId = base64_encode($user->id);

        return $username . '_' . $encodedId;
    }
}

if (!function_exists('generatePublicUrl')) {
    /**
     * Generate public url
     *
     * @param string $baseUri
     * @param object $user
     * @return string
     */
    function generatePublicUrl($user = null, $baseUri = null)
    {
        $baseUri = empty($baseUri) ? 'profile/' : $baseUri;
        $user = empty($user) ? auth()->user() : $user;
        $uniqueToken = generateUniqueToken($user);

        return url($baseUri . $uniqueToken);
    }
}

if (!function_exists('storePaymentSession')) {
    /**
     * Store or update payment session data in Laravel session
     *
     * @param string $key
     * @param array $newData
     * @return void
     */
    function storePaymentSession($key, array $newData)
    {
        $existingData = session()->get($key, []);
        $mergedData = array_merge($existingData, $newData);
        session()->put($key, $mergedData);
    }
}

if (!function_exists('getPaymentSessionData')) {
    /**
     * Retrieve payment session data from Laravel session
     *
     * @param string $key
     * @return mixed
     */
    function getPaymentSessionData($key)
    {
        return session()->get($key);
    }
}

if (!function_exists('clearPaymentSession')) {
    /**
     * Clear payment session data
     *
     * @param string $key
     * @return void
     */
    function clearPaymentSession($key)
    {
        if (session()->has($key)) {
            return session()->forget($key);
        }
    }
}

if (!function_exists('generateInvoiceNumber')) {
    /**
     * Generate an Invoice Number based on the transaction ID.
     *
     * @param int $transactionId
     * @return string
     */
    function generateInvoiceNumber($transactionId)
    {
        // Pad the transaction ID with leading zeros to a total length of 7 characters
        $paddedTransactionId = str_pad($transactionId, 7, '0', STR_PAD_LEFT);

        // Generate the invoice number
        return 'Invoice #' . $paddedTransactionId;
    }
}
