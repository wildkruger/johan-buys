<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <title>{{ __('Crypto Exchanges') }}</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('Modules/CryptoExchange/Resources/assets/css/report.min.css')}}">
    </head>
    
    <body>
        <div class="div1">
            <div class="h-80">
                <div class="div2">
                    <div>
                        <strong>
                            {{ ucwords(settings('name')) }}
                        </strong>
                    </div>
                    <br>
                    <div>
                        {{ __('Period') }} : {{ $date_range }}
                    </div>
                    <br>
                    <div>
                        {{ __('Print Date') }} : {{ dateFormat(now())}}
                    </div>
                </div>
                <div class="div3">
                    <div>
                        <div>
                            @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                                <img src="{{ url('public/images/logos/' . settings('logo')) }}" width="288" height="90" alt="Logo"/>
                            @else
                                <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" width="288" height="90">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearboth">
            </div>
            <div class="div4">
                <table class="div5">
                    <tr class="div6">
                        <td>{{ __('Date') }}</td>
                        <td>{{ __('User') }}</td>
                        <td>{{ __('Type') }}</td>
                        <td>{{ __('Amount') }}</td>
                        <td>{{ __('Fees') }}</td>
                        <td>{{ __('Total') }}</td>
                        <td>{{ __('Rate') }}</td>
                        <td>{{ __('From') }}</td>
                        <td>{{ __('To') }}</td>
                        <td>{{ __('Status') }}</td>
                    </tr>

                    @foreach($crypto_exchanges as $exchange)

                    <tr class="div7">
                        <td>{{ dateFormat($exchange->created_at) }}</td>
                        <td>{{ isset($exchange->email_phone) ? $exchange->email_phone : getColumnValue($exchange->user)  }}</td>
                        <td>{{ getColumnValue(optional($exchange->transaction)->transaction_type, 'name')  }}</td>
                        <td>{{ formatNumber($exchange->amount, $exchange->from_currency) }}</td>
                        <td>{{ formatNumber($exchange->fee, $exchange->from_currency) }}</td>
                        @php
                            $total = $exchange->fee + $exchange->amount;
                        @endphp
                        <td> {{ formatNumber($total, $exchange->from_currency) }}</td>
                        <td>{{ moneyFormat( optional($exchange->toCurrency)->symbol, formatNumber($exchange->exchange_rate, $exchange->to_currency) ) }}</td>
                        <td>{{ optional($exchange->fromCurrency)->code }} </td>
                        <td>{{ optional($exchange->toCurrency)->code }}</td>
                        <td>{{ getStatus($exchange->status) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </body>
</html>
