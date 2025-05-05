@extends('cryptoexchange::frontend.layouts.app')

@section('content')
<div class="crypto-first-section px-240p" id="stripe-payment-gateway">
    <div class="container-fluid mt-215 pb-131">
        <div class="row px-vw">
            <div class="col-md-5 col-lg-4 mt-mid-40p">
                <div class="d-flex step-div-parent">
                    <div class="step-div ml-11n">

                        <!--check status start for 1st_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="first-circle border-set" id="first-circle">
                                <div class="second-circle border-set bg-set" id="second-circle">
                                    <div class="third-circle visible" id="third-circle">
                                        <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/success.svg') }}" alt="success" class="success-img img-fluid">
                                    </div>
                                    <div class="curent-second-circle curent-display" id="curent-second-circle">

                                    </div>
                                </div>
                            </div>
                            <div class="text-center d-flex align-items-center ml-19"><p class="align-items-center crypto-font-22 poppins5 status-color mb-unset">{{ __('Start Exchange') }}</p></div>
                        </div>
                        <div class="cp-exchange-stick-step ml-step-21 ml-step-success"></div>
                        <!-- status end for 1st_step_status-->

                        <!--check status start for 2nd_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="first-circle border-set" id="first-circle_step2">
                                <div class="second-circle border-set bg-set" id="second-circle_step2">
                                    <div class="third-circle visible" id="third-circle_step2">
                                        <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/success.svg') }}" alt="success" class="success-img img-fluid">
                                    </div>
                                    <div class="curent-second-circle curent-display" id="curent-second-circle_step2">

                                    </div>
                                </div>
                            </div>
                            <div class="text-center d-flex align-items-center ml-19"> <p class="crypto-font-22 poppins5 status-color mb-unset">{{ __('Verify your Identity') }}</p></div>
                        </div>

                        <div class="cp-exchange-stick-step ml-step-21"></div>
                        <!-- status end for 2nd_step_status-->
                        <!--check status start for 3rd_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="first-circle border-set" id="first-circle_step3">
                                <div class="second-circle border-set bg-set" id="second-circle_step3">
                                    <div class="third-circle visible" id="third-circle_step3">
                                        <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/success.svg') }}" alt="success"
                                            class="success-img img-fluid">
                                    </div>
                                    <div class="curent-second-circle curent-display"
                                        id="curent-second-circle_step3">

                                    </div>
                                </div>
                            </div>
                            <div class="text-center d-flex align-items-center ml-19">
                                <p class="crypto-font-22 poppins5 status-color mb-unset">
                                    @if($transInfo['from_type'] == 'crypto_sell')
                                        {{ __('Receiving Account Details') }}
                                    @else
                                        {{ __('Provide Crypto Address') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="exchange-stick-step-65 ml-step-21"></div>
                        <!-- status end for 3rd_step_status-->
                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                                <div class="curent-second-circle curent-display-show">
                                </div>
                            </div>
                            <div class="text-center d-flex align-items-center ml-28"> <p class="crypto-font-22 poppins5 status-color-active mb-unset">{{ __('Make Payment') }}</p></div>
                        </div>

                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 4th_step_status-->

                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class="text-center d-flex align-items-center ml-28"> <p class="crypto-font-22 poppins5 status-color mb-unset">{{ __('Complete Transaction') }}</p></div>
                        </div>
                        <!-- status end for 4th_step_status-->
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-lg-8 pl-203p">
                <div class="crypto-box mob-mt-40">
                    <div class="box-header">
                        <div class="d-flex">
                            <div class="back-padding back-arrow d-flex justify-content-between align-items-center my-auto exchange-confirm-back-btn cursor-pointer">
                                <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/back-arrow.svg') }}" alt="back-arrow">
                                <a class="font-16 OpenSans-600 back-color" >{{ __('Back') }}</a>
                            </div>
                            <p class="font-20 OpenSans-700 c-white text-center mb-unset center-padding">{{ __('Crypto Buy') }}</p>

                        </div>
                    </div>
                    <div class="box-body box-border pr-28">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-6 mob">
                                <div><span class="font-16 OpenSans-400 c-blublack">{{ __('You are Sending') }}</span></div>
                                <div class="d-flex"><span class="font-28 OpenSans-600 c-blublack">{{  formatNumber($transInfo['totalAmount'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span></div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-6">
                                <div class="d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <p class="mb-unset font-16 OpenSans-600 c-blublack">{{ __('Medium') }}</p>
                                        <div><img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/stripe.svg') }}" alt="stripe"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <form id="Stripe" name="Stripe" method="post" action="#" accept-charset="UTF-8" >
                        {{ csrf_field() }}
                        <input name="currency" value="{{ $transInfo['fromCurrencyCode'] }}" type="hidden">
                        <input name="amount" class="form-control" value="{{ $transInfo['totalAmount'] }}" type="hidden">

                        <div class="form-group next-step form-mb-12 mt-32">
                            <label for="exampleInputEmail1otp"><span class="font-18 font-mob-14 poppins5 c-blublack">{{ __('Card Number') }}</span> </label>
                            <div id="card-number"></div>
                            <input type="text" class="form-control font-16-line OpenSans-400 c-blublack mt-3n" required data-value-missing="{{ __('This field is required.') }}" name="cardNumber" maxlength="19" id="cardNumber" onkeypress="return isNumber(event)">
                            <div id="card-errors" class="error"></div>

                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <div class="row">
                                        <div class="col-lg-4 pr-4">
                                            <label for="usr" class="OpenSans-400">{{ __('Month') }}</label>
                                            <div class="month">
                                                <select class="form-control valid stripe-day" name="month" id="month" aria-required="true" aria-invalid="false">
                                                    {!! getStripeMonths() !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 mt-4 mt-lg-0 pr-4 year">
                                            <label for="usr" class="OpenSans-400">{{ __('Year') }}</label>
                                            <input type="text" class="form-control" required data-value-missing="{{ __('This field is required.') }}" name="year" id="year" maxlength="2" onkeypress="return isNumber(event)">
                                        </div>

                                        <div class="col-lg-4 mt-4 mt-lg-0">
                                            <div class="form-group cvc">
                                                <label for="usr" class="OpenSans-400">{{ __('CVC') }}</label>
                                                <input type="text" class="form-control" name="cvc" id="cvc" required data-value-missing="{{ __('This field is required.') }}" maxlength="4" onkeypress="return isNumber(event)">
                                                <div id="card-cvc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <p class="error" id="stripeError"></p>
                            </div>
                        </div>

                        <div class="mt-next-2 next">
                            <button
                                class="load-btn btn btn-bg-color btn-lg btn-block c-white font-20 OpenSans-600 d-flex justify-content-center align-items-center standard-payment-submit-btn" id="stripe_payment" type="submit"
                                >  {{ __('Submit') }}</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>
    <script type="text/javascript">
        'use strict';
        var goToPaymentText = "{{ __('Go to payment') }}";
        var totalAmount = "{!! $transInfo['totalAmount'] !!}";
        var csrfToken = "{{ csrf_token() }}";
        var submitting = "{{ __('Submitting...') }}";
        var stripePaymentUrl = "{{ route('guest.crypto_exchange.stripe_payment')}}";
        var stripeUrl = "{{ route('guest.crypto_exchange.stripe')}}";
    </script>
    <script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>
@endsection
