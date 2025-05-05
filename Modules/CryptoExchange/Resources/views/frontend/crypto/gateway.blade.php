@php
 $extensions = json_encode(getFileExtensions(2));
@endphp

@extends('cryptoexchange::frontend.layouts.app')

@section('content')
<div class="crypto-first-section px-240p" id="crypto-transaction-gateway">
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
                            <div class="text-center d-flex align-items-center ml-19"><p class="align-items-center  crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Start Exchange') }}</p></div>
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
                            <div class="text-center d-flex align-items-center ml-19"> <p class="crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Verify your Identity') }}</p></div>
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
                                <p class="crypto-font-22 OpenSans-600 status-color mb-unset">
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
                            <div class="text-center d-flex align-items-center ml-28"> <p class="crypto-font-22 OpenSans-600 status-color-active mb-unset">{{ __('Make Payment') }}</p></div>
                        </div>

                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 4th_step_status-->

                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class="text-center d-flex align-items-center ml-28"> <p class="crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Complete Transaction') }}</p></div>
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
                            <p class="font-16 OpenSans-700 c-white text-center mb-unset center-padding">{{ __( ucwords(str_replace("_"," ", $transInfo['from_type'])) ) }}</p>
                            <p class="font-16 OpenSans-600 c-white text-center mb-unset center-padding" id="timer"></p>

                        </div>
                    </div>
                    <div class="box-body box-border">
                        
                        <form method="POST" action="{{ $url }}" id="crypto_buy_sell_from" enctype="multipart/form-data">
	                        @csrf
	                        <input type="hidden" name="from_currency" value="{{ $transInfo['from_currency'] }}">
	                        <input type="hidden" name="to_currency" value="{{ $transInfo['to_currency'] }}">
                            <input type="hidden" name="send_amount" value="{{ $transInfo['finalAmount'] }}">
	                        <input type="hidden" name="exchange_type" value="{{ $transInfo['from_type'] }}">

                            <!-- You Send Section -->
                            <div>
                                <span class="font-16 OpenSans-400 c-blublack">{{ __('You Send') }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['defaultAmnt'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span>

                                @if( currencyLogo($transInfo['fromCurrencyLogo']) )
                                    <img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($transInfo['fromCurrencyLogo']) }}" alt="{{ $transInfo['fromCurrencyCode'] }}">
                                @endif
                            </div>
                            <div class="mb-font text-break mt-4n">
                                <span class="font-14 OpenSans-400"> {{ __('Fees') }} ≈ {{ formatNumber($transInfo['totalFees'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span>
                            </div>
                            
                            <!-- You Get Section -->
                            <div class="mt-29">
                                <span class="font-16 OpenSans-400 c-blublack">{{ __('You Get') }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['finalAmount'], $transInfo['to_currency']) }} {{ $transInfo['currCode'] }}
                                </span>
                                @if( currencyLogo($transInfo['toCurrencyLogo']) )
                                    <img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($transInfo['toCurrencyLogo']) }}" alt="{{ $transInfo['currCode'] }}">
                                @endif
                            </div>
                            <div class="mb-font text-break mt-4n">
                                <span class="font-14 OpenSans-400">1 {{ $transInfo['fromCurrencyCode'] }} ≈ {{ formatNumber($transInfo['dCurrencyRate'], $transInfo['to_currency']) }} {{ $transInfo['currCode'] }}</span>
                            </div>
                            <!-- You Get Section End-->
                       
                            @if($transInfo['merchantAddress'])

                                <div class="details-curency details-curency-p mt-1n">             
                                    <p class="font-16 OpenSans-400 mt-25 pr-28">{{ __('Please make payment') }}  
                                        <strong class="c-blue">{{ formatNumber($transInfo['totalAmount'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</strong> {{ __('to our') }} 
                                        <strong class="c-gray-black">{{ __('Merchant Address') }}</strong>
                                    </p>
                                </div>
                                <div class="merchant mt-27 text-center mb-14">
                                    <span class="font-18 OpenSans-600 c-blublack">{{ __('Merchant Address') }}</span>
                                </div>
                                <div class="d-flex justify-content-center mx-auto">
                                    <img class="img-fluid w-130" src="{{ qrGenerate($transInfo['merchantAddress']) }}"/>
                                </div>

                                <div class="mt-2 text-break d-flex justify-content-center align-items-center">
                                    <span id="merchantAddress"><strong>{{ $transInfo['merchantAddress'] }}</strong></span>
                                    <span id="copyButton" class="input-group-addon btn" title="{{ __('Click to copy') }}">
                                        <img src="{{ url('Modules/CryptoExchange/Resources/assets/landing/images/copy-btn.png') }}" alt="{{ __('Copy') }}">
                                    </span>
                                    <span class="ml-2 copyText text-success d-none"> {{ __('Copied') }} <i class="fa fa-check text-success"></i></span>
                                </div>
                            @endif

                            @if($transInfo['from_type'] == 'crypto_buy')

                                <div class="mt-31 pr-28">
                                    <p class="font-18 OpenSans-600 mb-unset mt-36">{{ __('Select Payment Method') }} </p>                           

                                    <div class="d-flex flex-wrap col-gap-10 mt-3 response" style="gap:10px !important">
                                        <input type="hidden" name="gateway" id="payment_method_id" value="{{ old('gateway', $currencyPaymentMethods[0]->id) }}">
                                            @foreach($currencyPaymentMethods as $currencyPaymentMethod)
                                            <div class="mt-gateway">
                                                <div class="gateway {{ $loop->first ? 'g5' : '' }}" id="{{ $currencyPaymentMethod->id }}">
                                                    <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/' . strtolower($currencyPaymentMethod->name) . '.png') }}" alt="{{ $currencyPaymentMethod->name }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <span class="error">{{ $errors->first('gateway') }}</span>

                                    <div class="mt-next d-flex justify-content-center next">
                                        <button type="submit" id="verify_phone" class="text-center btn-bg-color btn-lg btn-block text-light font-20 poppins6 c-blublack submit-button" ><i class="fa fa-spinner fa-spin exchange-display" id="spinner"></i><span class="exchange-confirm-submit-btn-txt" id="phone_verification_button_text">{{ __('Submit') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($transInfo['from_type'] != 'crypto_buy')
                                <div class="mt-16 pr-28">
                                    <div class="form-group next-step make-payment">
                                        <label for="exampleInputEmail1"><span class="font-18 OpenSans-400 c-blublack">{{ __('Your Payment Details') }}</span></label>
                                        <textarea class="form-control font-16-line mulish4 c-blublack mt-1n" id="exampleFormControlTextarea1" required data-value-missing="{{ __('This field is required.') }}" name="payment_details"rows="2" placeholder="{{ __('Payment Details') }}"></textarea>
                                        <span class="error">{{ $errors->first('payment_details') }}</span>
                                    </div>
                                <div class="pb-3p"> <label for="exampleInputEmail1"><span class="font-18 OpenSans-400 c-blublack">{{ __('Payment Proof') }}</span></label></div>
                                    <div class="form-group proof_file make-payments">
                                        <input type="file" name="proof_file" class="form-control" required data-value-missing="{{ __('Please select a file.') }}"  id="file" placeholder="file"> 
                                        <span class="error">{{ $errors->first('proof_file') }}</span>                          
                                    </div>
                                    <span id="fileSpan" class="file-error"></span>
                            
                                    <div class="mt-next d-flex justify-content-center next">
                                        <button type="submit" id="verify_phone" class="text-center btn-bg-color btn-lg btn-block text-light font-20 poppins6 c-blublack submit-button" ><i class="fa fa-spinner fa-spin exchange-display" id="spinner"></i><span class="exchange-confirm-submit-btn-txt" id="phone_verification_button_text">{{ __('Submit') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

    <script src="{{ theme_asset('public/js/fpjs2/fpjs2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>
    <script type="text/javascript">
        'use strict';
        var extensions = JSON.parse(@json($extensions));
        var extensionsValidationRule = extensions.join('|');
        var extensionsValidation = extensions.join(', ');
        var errorMessage = '{{ __("Please select (:x) file.") }}';
        var invalidFileText = errorMessage.replace(':x', extensionsValidation);
        var requiedText = "{{ __('This field is required.') }}";
        var submitText = "{{ __('Submitting...') }}";
        var expireTime = "{{ $expireTime }}";
        var expireText = "{{ __('Expired') }}";
        var receivingInforUrl = "{{ route('guest.crypto_exchange.receiving_info')}}";
    </script>
    <script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>
    <script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/countdown.min.js') }}"></script>
@endsection
