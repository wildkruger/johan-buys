<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('Merchant Payment') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap-css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/frontend/css/merchant_payment/merchant_payment.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css')}}">

    <script  type="text/javascript" src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}"></script>
    <script  type="text/javascript" src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/popper.min.js') }}"></script>
    <script  type="text/javascript" src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/bootstrap.min.js') }}"></script>

    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
    @endif

    <script type="text/javascript">
        var SITE_URL = "{{ url('/') }}";
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-4"></div>
            <div class="col-md-4 col-sm-4"></div>
            <div class="col-md-2 col-sm-4">
                <h2>@lang('message.footer.language')</h2>
                <div class="form-group">
                    <select class="form-control" id="lang">
                        @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                            <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'>{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <div class="row mt-3">
            <div class="col-md-8 offset-md-2" style="border: 1px solid #ddd;">
                <div class="panel panel-default box-shadow" style="margin-top: 15px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if ($isMerchantAvailable)
                                    @if ($merchant->logo)
                                        <img alt="{{ __('Not found') }}" src="{{ url('public/user_dashboard/merchant/' . $merchant->logo) }}" class="img-fluid img-circle">
                                    @else
                                        <img alt="{{ __('Not found') }}" src="{{ url('public/uploads/merchant/merchant.jpg') }}" class="img-fluid img-circle">
                                    @endif
                                    <h3 class="font-weight-bold">{{ $merchant->business_name }}</h3>
                                    <h4>{{ moneyFormat($merchant->currency->code, formatNumber(floatval(isset($amount) ? $amount : 0))) }}</h4>
                                @endif
                                <p class="mt-3 mb-2 text-bold" style="color: #F7B900">{{ __('Should you have a Freed Trade account, click on the arrow next to Freed Trade.') }} <br> {{ __('Alternatively click on the arrow next to PayGate to complete the transaction.') }}</p>
                                <hr class="style-two">
                                <div><i class="fa fa-angle-down fa-2x" aria-hidden="true"></i></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 30px;">
                                {{-- {{ dd($mathodData) }} --}}
                                @if ($isMerchantAvailable)
                                    @php 
                                        $paymentMethodLogo = !empty(settings('logo')) ?  asset('public/images/logos/' . settings('logo')) : asset('public/uploads/userPic/default-logo.jpg');
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <form id="check" action="" style="display: block;">
                                                    <div class="plan-card-group">
                                                        <div class="row">
                                                            @if (!empty($payment_methods))
                                                                @foreach ($payment_methods as $value)
                                                                    @if (in_array($value['id'], [Mts, Paygate]))
                                                                        <div class="col-md-12 col-xs-12">
                                                                            <div class="row mb-3">
                                                                                <div class="col-md-4 col-xs-4">
                                                                                    <div class="pull-left">
                                                                                        <label for="{{ $value['id'] }}" id="{{ $value['id'] }}">
                                                                                            @if ($value['id'] == 1)
                                                                                                <img height="120px;" width="120px;" class="img-fluid" src='{{ $paymentMethodLogo }}' alt="{{ $value['name'] }}">
                                                                                            @else
                                                                                                <img style="height: 40px; border-radius: 5px;" height="60px;" width="120px;" class="img-responsive" src='{{ asset("public/images/payment_gateway/".strtolower($value['name']). ".jpg" ) }}' alt="{{ $value['name'] }}">
                                                                                            @endif
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 col-xs-4">
                                                                                    <div class="pull-left" style="padding-top: 8px">{{ $value['name'] == 'Mts' ? settings('name') : $value['name'] }}</div>
                                                                                </div>
                                                                                <div class="col-md-4 col-xs-4">
                                                                                    <a class="payment_method_btn pull-right" href="#" data-id="{{ $value['name'] }}"><i class="fa fa-angle-right fa-2x" aria-hidden="true"></i></a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="bs-callout bs-callout-danger" style="margin-top: 5%;">
                                                        @if (!$isMerchantAvailable)
                                                            <h4 style="color:red">{{ __('Merchant not found.') }}</h4>
                                                        @else
                                                            <p style="font-size: 13px;">{{ __('The recipient underwent a special verification and confirmed his reliability') }}</p>
                                                            <p style="font-size: 13px;">{{ __('By clicking, you agree to our Terms of Use and Privacy Policy') }}</p>
                                                        @endif
                                                        <a href="{{ url('admin/') }}">
                                                            <img src="{{ $paymentMethodLogo }}" class="img-fluid" style="margin:auto;" height="180px" width="180px">
                                                        </a>
                                                    </div>
                                                </form>
                                                <!--- MTS GATEWAY START-->
                                                <form style="display: none;" action="{{ url('merchant/wallet-payment/' . $paymentInfo['grant_id'] . '/' . $paymentInfo['token']) }}" id="Mts" name="Mts" method="post" accept-charset="utf-8" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="grant_id" value="{{ $paymentInfo['grant_id'] }}">
                                                    <input type="hidden" name="token" value="{{ $paymentInfo['token'] }}">
                                                    <input type="hidden" name="amount" value="{{ $amount }}">

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-primary express-payment-confirm-submit-btn">
                                                                <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                                <span class="express-payment-confirm-submit-btn-txt" style="font-weight: bolder;">{{ __('submit payment') }}</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--- MTS GATEWAY END-->
                                                <!--- Paygate GATEWAY START-->
                                                <form id="Paygate" name="Paygate" method="post" action="{{ url('merchant/api/paygate-payment') }}" accept-charset="UTF-8" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="grant_id" value="{{ $paymentInfo['grant_id'] }}">
                                                    <input type="hidden" name="token" value="{{ $paymentInfo['token'] }}">
                                                    <input type="hidden" name="amount" value="{{ $amount }}">

                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">{{ __('Enter your Email Address') }}</label>
                                                        <input type="email" class="form-control" id="email" name="email" required>
                                                    </div>
                                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                        <a href="#" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                                        <button type="submit" class="btn btn-primary">{{ __('Submit payment') }}</button>
                                                    </div>
                                                </form>
                                                <!--- Paygate GATEWAY END-->
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.payment_method_btn').on('click', function(event) {
                var payment_by = $(this).attr("data-id");
                $("#check").hide();
                if (payment_by == "Paygate") {
                    $('#Paygate').removeAttr('style');
                }else if (payment_by == "Mts") {
                    $('#Mts').submit();
                }
            });
        });

        
        $('.express-payment-cancel-btn').on('click', function(event) {
            $(".express-payment-cancel-link").click(function(e) {
                e.preventDefault();
            });
            $("#check").show();
        });

        function disable() {
            $(".express-payment-confirm-submit-btn").attr("disabled", true);
            $(".fa-spin").show();
            $(".express-payment-confirm-submit-btn-txt").text("{{ __('Submitting...') }}");
        }
    </script>
</body>

</html>
