<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Paylink payment success') }}</title>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/payment.css') }}">
</head>

<body>
    <div class="main-content py-5 px-md-5">
        <div class="container bg-white">
            <div class="payment-container">
                <div class="payment-content">
                    <div class="payment-header">
                        @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('public/images/logos/' . settings('logo')) }}" class="pay-paygenius-logo" alt="logo">
                            </a>
                        @else
                            <a href="{{ url('/') }}">
                                <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="pay-paygenius-logo" width="80" height="50" alt="logo">
                            </a>
                        @endif

                        <p class="font-semibold text-black mb-0 font-20 gilroy-Semibold">{{ generateInvoiceNumber($transaction->id) }}</p>
                    </div>
                    <div class="gateway-form gilroy-medium" id="gateway-form1">
                        <table class="payment-table">
                            <tr>
                                <td class="font-semibold">{{ __('Method Name') }}: </td>
                                <td class="text-center">{{ $transaction?->payment_method?->name  }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ __('Gateway Currency') }}: </td>
                                <td class="text-center">{{ $transaction->currency->code }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ __('Gateway Charge') }}: </td>
                                <td class="text-center">{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed, $transaction->currency_id)) }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ __('Payed Amount') }}: </td>
                                <td class="text-center">{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->total, $transaction->currency_id)) }}</td>
                            </tr>
                        </table>
                    </div>
                    <p class="mb-2 text-sm text-black gilroy-medium">{{ __('Sender Details') }}</p>
                    <div class="gateway-form gilroy-medium">
                        <table class="payment-table">
                            <tr>
                                <td class="font-semibold">{{ __('Name') }}: </td>
                                <td class="text-center">{{ $transaction->profilePayment->payer_details['first_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ __('Email') }}: </td>
                                <td class="text-center">{{ $transaction->profilePayment->payer_details['email'] }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">{{ __('Note') }}: </td>
                                <td class="max-w-20 text-center">{{ __('Payment success') }}</td>
                            </tr>
                        </table>
                    </div><br>
                    <div class="payment-invoice gilroy-medium">
                        <div class="payment-border"><b>Invoiced To</b><br>{{ $transaction->user->first_name.' '.$transaction->user->last_name }}<br> </div>
                        <div class="payment-border"><b>Pay To</b><br> AMCoders <br> Dhaka, Dhaka <br> 1000, Bangladesh
                        </div>
                    </div>
                    <div class="payment-details gilroy-medium">
                        <table class="payment-table">
                            <tr>
                                <td><b>{{ __('Name') }}</b></td>
                            </tr>
                            <tr>
                                <td>{{ $transaction->user->first_name.' '.$transaction->user->last_name }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('Total') }} :</td>
                                <td class="text-center">{{ moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal, $transaction->currency_id)) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/jquery3.6.min.js') }}"></script>
    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/bootstrap5.min.js') }}"></script>
    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/select2.min.js') }}"></script>

</body>

</html>
