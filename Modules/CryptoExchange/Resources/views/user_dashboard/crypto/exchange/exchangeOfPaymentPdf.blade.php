<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{{ __('Crypto Exchange') }}</title>
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/CryptoExchange/Resources/assets/css/pdf.min.css')}}">

</head>
  
<body>
   <div class="container">
    <table class="mt-table">
        <tr>
            <td>
            @if(!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                <img src='{{ url('public/images/logos/' . settings('logo')) }}' class="imglogo"alt="Logo"/>
            @else
                <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="imgdefaultlogo">
            @endif
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="form-title">{{ __('Exchange From') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-15" class="td-15">{{ optional($currencyExchange->fromCurrency)->code }}</td>
                    </tr>
                    <br><br>

                    <tr>
                        <td class="td-16 form-title">{{ __('Exchange To') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-15">{{ optional($currencyExchange->toCurrency)->code }}</td>
                    </tr>
                    <br><br>

                    <tr>
                        <td class="td-16 form-title">{{ __('Exchange Rate') }}</td>
                    </tr>
                    <tr>
                        <td class="td-15">1 {{ optional($currencyExchange->fromCurrency)->code }} = {{ formatNumber($currencyExchange->exchange_rate, optional($currencyExchange->toCurrency)->id) }} {{ optional($currencyExchange->toCurrency)->code }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table class="mt-20">
                    <tr>
                        <td class="td-16 form-title">{{ __('Transaction ID') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-15">{{ $currencyExchange->uuid }}</td>
                    </tr>
                    <br><br>

                    <tr>
                        <td class="td-16 form-title">{{ __('Transaction Date') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-15">{{ dateFormat($currencyExchange->created_at) }}</td>
                    </tr>
                    <br><br>

                    <tr>
                        <td class="td-16 form-title">{{ __('Status') }}</td>
                    </tr>
                    <tr>
                        <td class="td-15"> {{ getStatus($currencyExchange->status) }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table class="table_one">
                    <tr>
                        <td colspan="2" class="detailscol">{{ __('Details') }}</td>
                    </tr>
                    <tr>
                        <td class="exchange_amount">{{ __('Exchange Amount') }}
                        </td>
                        <td class="text-15">{{ optional($currencyExchange->fromCurrency)->symbol }} {{ formatNumber($currencyExchange->amount, optional($currencyExchange->fromCurrency)->id)  }}</td>
                    </tr>
                        @if ($currencyExchange->fee > 0)
                            <tr class="tr-padding">
                                <td  class="fee">{{ __('Fee') }}</td>
                                <td class="symbol">{{ optional($currencyExchange->fromCurrency)->symbol }} {{ formatNumber($currencyExchange->fee, optional($currencyExchange->fromCurrency)->id)  }}</td>
                            </tr>
                        @endif
                    <tr>
                        <td colspan="2" class="colspan2"></td>
                    </tr>
                    <tr>
                        <td class="total">{{ __('Total') }}</td>
                        <td class="optional">{{ optional($currencyExchange->fromCurrency)->symbol }} {{ formatNumber($currencyExchange->amount + $currencyExchange->fee, optional($currencyExchange->fromCurrency)->id)  }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
   </div>
</body>
</html>
