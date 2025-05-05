<!DOCTYPE html>
<html lang="en">

<head>
    <title>@lang('message.express-payment-form.merchant-payment')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Ensures optimal rendering on mobile devices. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
    <!-- Bootstrap 5.0.2 -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('public/backend/bootstrap/dist/css/bootstrap-css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('public/frontend/css/merchant_payment/merchant_payment.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css') }}">
    <!-- jQuery 3 -->
    <script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/popper.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap-js/bootstrap.min.js') }}" type="text/javascript">
    </script>
    <script type="text/javascript">
        var SITE_URL = "{{ url('/') }}";
    </script>
</head>

<body>

    <div class="container text-center mt-3">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="panel panel-default box-shadow mt-3">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="mb-4">{{ __('Transaction amount') }}: <span class="text-primary">{{ $paymentData['currency_code'] }}</span> <span class="text-secondary">{{ formatNumber($paymentData['amount']) }}</span></h2>
                                <h6 class="mb-3" style="color: #6c757d">{{ __('If the transaction is approved, PayWeb will email the payment confirmation to this address, unless overridden at the gateway level using the Payment Confirmation setting.') }}</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="bs-callout bs-callout-danger">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div class="" id="payment">
                                                    <form method="post" action="{{ route('paylink.paygate.store') }}" id="paygate-form">
                                                        @csrf

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="control-label f-14 mb-2" for="email" style="float: left;">{{ __('Email') }}</label>
                                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required value="{{ $paymentData['email'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">

                                                                <div class="pull-right mt-3">
                                                                    <a href="{{ $paymentData['paylinkUrl'] }}" class="btn btn-danger standard-payment-cancel-btn border f-14 fw-bold me-2">{{ __('Cancel') }}</a>
                                                                    <button type="submit" class="btn btn-primary standard-payment-submit-btn f-14">
                                                                        <i class="spinner fa fa-spinner fa-spin d-none"></i>
                                                                        <span class="standard-payment-submit-btn-txt f-14 fw-bold">Submit</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

    <script>
        $('#paygate-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            }
        });
    </script>
</body>

</html>
