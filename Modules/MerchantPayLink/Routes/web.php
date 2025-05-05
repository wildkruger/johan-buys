<?php

use Illuminate\Support\Facades\Route;
use Modules\MerchantPayLink\Http\Controllers\MerchantPayLinkController;

Route::get('profile/{paylinkCode}', [MerchantPayLinkController::class, 'showPaymentPage'])->name('paylink.show');

Route::prefix('merchant')->group(function () {
    Route::post('/paylink/{uniqueId}/pay', [MerchantPayLinkController::class, 'processPayment'])->name('paylink.pay');
    Route::post('/paylink/store', [MerchantPayLinkController::class, 'storePayment'])->name('paylink.store');
    Route::get('/transactions', [MerchantPayLinkController::class, 'listTransactions']);
});

Route::post('/get-payment-methods', [MerchantPayLinkController::class, 'getPaymentMethods']);
Route::get('/paylink/paygate', [MerchantPayLinkController::class, 'paygate'])->name('paylink.paygate');
Route::post('/paylink/paygate-payment', [MerchantPayLinkController::class, 'paygatePayment'])->name('paylink.paygate.store');
Route::any('paylink/paygate-return-url', [MerchantPayLinkController::class, 'paygateReturnResponse']);

Route::get('/paylink/paygate-payment-success', [MerchantPayLinkController::class, 'paygatePaymentSuccess'])->name('paylink.paygate.success');
