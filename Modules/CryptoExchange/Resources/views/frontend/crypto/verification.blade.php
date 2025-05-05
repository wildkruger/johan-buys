@extends('cryptoexchange::frontend.layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/intl-tel-input-17.0.19/css/intlTelInput.min.css') }}">
@endsection

@section('content')
<div class="crypto-first-section px-240p" id="crypto-exchange-verification">
    <div class="container-fluid mt-215 pb-131">
        <div class="row px-vw">
            <div class="col-md-5 col-lg-4">
                <div class="d-flex step-div-parent mt-mid-40p">
                    <div class="step-div ml-11n">
                        <div class="steper-div-2 d-flex">
                            <div class="first-circle border-set" id="first-circle">
                                <div class="second-circle border-set bg-set" id="second-circle">
                                    <div class="third-circle visible" id="third-circle">
                                        <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/success.svg') }}" alt="success" class="success-img img-fluid">
                                    </div>
                                    <div class="curent-second-circle curent-display" id="curent-second-circle">

                                    </div>
                                    <div class="curent-second-circle curent-display" id="curent-second-circle"></div>
                                </div>
                            </div>
                            <div class=" d-flex align-items-center ml-19"><p class="align-items-center crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Start Exchange') }}</p></div>
                        </div>
                        <div class="exchange-stick-step-65 ml-step-success"></div>
                        <!--  status end for 1st_step_status-->
                        <!--check status start for 2nd_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                                <div class="curent-second-circle curent-display-show">
                                </div>
                            </div>
                            <div class=" d-flex align-items-center ml-28"> <p class="crypto-font-22 OpenSans-600 status-color-active mb-unset">{{ __('Verify your Identity') }}</p></div>
                        </div>
                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 2nd_step_status-->

                        <!--check status start for 3rd_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class=" d-flex align-items-center ml-28">
                                <p class="crypto-font-22 OpenSans-600 status-color mb-unset">
                                @if($transInfo['from_type'] == 'crypto_sell')
                                    {{ __('Receiving Account Details') }}
                                @else
                                    {{ __('Provide Crypto Address') }}
                                @endif
                                </p>
                            </div>
                        </div>

                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 3rd_step_status-->
                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class="text-center d-flex align-items-center ml-28"> <p class="crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Make Payment') }}</p></div>
                        </div>

                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 4th_step_status-->

                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class=" d-flex align-items-center ml-28"> <p class="crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Complete Transaction') }}</p></div>
                        </div>
                        <!-- status end for 4th_step_status-->
                    </div>
                </div>
            </div>

            <div class="col-md-7 col-lg-8 pl-203p">
                <div class="crypto-box mob-mt-40">
                    <div class="box-header">
                        <div class="d-flex">
                            <div class="back-padding back-arrow d-flex justify-content-between align-items-center my-auto  exchange-confirm-back-btn cursor-pointer">
                                <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/back-arrow.svg') }}" alt="back-arrow">
                                <a class="font-16 OpenSans-600 back-color" >{{ __('Back') }}</a>
                            </div>
                            <p class="font-20 OpenSans-700 c-white text-center mb-unset center-padding">{{ __(ucwords(str_replace("_"," ", $transInfo['from_type']))) }} </p>
                        </div>
                    </div>
                    <div class="box-body box-border">
                        <!-- You Send Section -->
                        <div>
                            <span class="font-16 OpenSans-400 c-blublack">{{ __('You Send') }}</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['defaultAmnt'], $fromCurrency->id) }} {{ $fromCurrency->code }}</span>

                            @if(currencyLogo($fromCurrency->logo))
                        	   <img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($fromCurrency->logo) }}" alt="{{ $fromCurrency->code }}">
                            @endif
                        </div>
                        <div class="mb-font text-break mt-4n">
                            <span class="font-14 OpenSans-400"> {{ __('Fees') }} ≈ {{ formatNumber($transInfo['totalFees'], $fromCurrency->id) }} {{ $fromCurrency->code }} </span>
                        </div>

                        <!-- You Get Section -->
                        <div class="mt-29">
                            <span class="font-16 OpenSans-400 c-blublack">{{ __('You Get') }}</span>
                        </div>
                        <div class="d-flex text-break">
                            <span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['finalAmount'], $toCurrency->id) }} {{ $transInfo['currCode'] }}</span>

                             @if(currencyLogo($toCurrency->logo))
                               <img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($toCurrency->logo) }}" alt="{{ $toCurrency->code }}">
                            @endif

                        </div>
                        <div class="mb-font mt-4n">
                            <span class="font-14 OpenSans-400">1 {{ $fromCurrency->code }} ≈ {{  formatNumber($transInfo['dCurrencyRate'], $toCurrency->id) }} {{ $toCurrency->code }}</span>
                        </div>

                        <!-- Email or phone -->
                        <div class="mt-29 pr-28">
                        	@if($pref == 'phone')
                                <div class="form-group next-step-phone">
                                    <label for="exampleInputEmail1">
                                        <span class="font-18 font-mob-14 OpenSans-600 c-blublack">{{ __('Phone Number') }}</span>
                                    </label>
                                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                    <input type="tel" name="phone" class="form-control cc" id="phone"><br>
                                    <span id="phone-error"></span>
                                    <span id="tel-error"></span>
                                    <span id="phone-config-error" class="error"></span>
                                </div>
                                <div class="mt-next next" id="verification_field">
                                    <button type="button" class="load-btn btn btn-next btn-bg-color btn-lg btn-block c-white font-20 OpenSans-600" id="verify_phone"><i class="phone_spinner fa fa-spinner fa-spin display-hide"></i><span class="exchange-confirm-submit-btn-txt" id="phone_verification_next_text">{{ __('Next Step') }}</span></button>
                                </div>
                            @else
                                <div class="form-group next-step">
                                    <label for="exampleInputEmail1">
                                        <span class="font-18 font-mob-14 OpenSans-400 c-blublack">{{ __('Email') }}</span> 
                                    </label>
                                    <input type="email" name="email" class="form-control font-16-line mulish4 c-blublack mt-1n" id="email" placeholder="{{ __('Enter Your Email') }}">
                                    <span id="email-error" class="error"></span>
                                </div>
                                <div class="mt-next next" id="verification_field">
                                    <button class="btn btn-next btn-bg-color btn-lg btn-block c-white font-20 OpenSans-600" id="verify_email"><i class="email_spinner fa fa-spinner fa-spin display-hide"></i><span class="exchange-confirm-submit-btn-txt" id="emil_verification_button_text"> {{ __('Next Step') }} <i class="fa fa-angle-right"></i></span>
                                    </button>
                                </div>
                            @endif

                            <!-- OTP -->
                            <div class="form-group next-step form-mb-12 display-hide" id="otp_details">
                                <label for="exampleInputEmail1otp">
                                    <span class="font-18 font-mob-14 OpenSans-600 c-blublack"> {{ __('OTP') }} </span> 
                                </label>
                                <input type="text" class="form-control" id="phone_verification_code" name="phone_verification_code" placeholder="{{ __('Enter OTP code to Verify') }}">
                                <span id="code-error"></span>
                            </div>

                            <div class="mt-next next display-hide" id="submit_field">
                                <button class="btn btn-next btn-bg-color btn-lg btn-block c-white font-20 OpenSans-600" id="{{ ($pref == 'phone') ? 'phone_verification_button' : 'email_verification_button' }}"><i class="verify_confirm fa fa-spinner fa-spin display-hide"  id="spinner"></i>
                                    <span class="exchange-confirm-submit-btn-txt" id="phone_verification_button_text">{{ __('Verify') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('public/plugins/intl-tel-input-17.0.19/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/isValidPhoneNumber.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/fpjs2/fpjs2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript"> 
    'use strict';
    var hasPhoneError = false;
    var hasEmailError = false;
    var from_type = "{{ $transInfo['from_type'] }}";
    var defaultCountry = "{{ getDefaultCountry() }}";
    var validPhoneText = "{{ __('Please enter a valid international phone number.') }}";
    var emailConfigText = "{{ __('Email settings not configured.') }}";
    var phoneConfigText = "{{ __('Phone settings not configured.') }}";
    var validEmailText = "{{ __('Please provide a valid email.') }}";
    var otpRequiredText = "{{ __('OTP code is required.') }}";
    var verifyingText = "{{ __('Verifying...') }}";
    var requiedText = "{{ __('This field is required.') }}";
    var utilsScriptFile = "{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/utils.min.js') }}";
    var otpText = "{{ __('OTP sending..') }}";
    var verify = "{{ __('Verify') }}";
    var nextText = "{{ __('Next Step') }}";
    var defaultAmount = "{{ $transInfo['defaultAmnt'] }}";
    var finalAmount = "{{ $transInfo['finalAmount'] }}";
    var fromCurrencyId = "{{ $fromCurrency->id }}";
    var toCurrencyId = "{{ $toCurrency->id }}";
    var emailVerificationUrl = "{{ route('guest.crypto_exchange.email_verification') }}";
    var emailVerificationSuccessUrl = "{{ route('guest.crypto_exchange.email_verification_success') }}";
    var phoneVerificationUrl = "{{ route('guest.crypto_exchange.phone_verification') }}";
    var phoneVerificationSuccessUrl = "{{ route('guest.crypto_exchange.phone_verification_success') }}";
    var receivingInforUrl = "{{ route('guest.crypto_exchange.receiving_info') }}";
</script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>
@endsection


