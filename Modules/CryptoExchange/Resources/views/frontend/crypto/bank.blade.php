@php
 $extensions = json_encode(getFileExtensions(2));
@endphp

@extends('cryptoexchange::frontend.layouts.app')

@section('content')
<div class="crypto-first-section px-240p" id="crypto-bank-payment-method">
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
                            <div class="text-center d-flex align-items-center ml-19">
                                <p class="align-items-center crypto-font-22 poppins5 status-color mb-unset">{{ __('Start Exchange') }}</p>
                            </div>
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
                            <div class="text-center d-flex align-items-center ml-19"> 
                                <p class="crypto-font-22 poppins5 status-color mb-unset">{{ __('Verify your Identity') }}</p>
                            </div>
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
                                    <div class="curent-second-circle curent-display" id="curent-second-circle_step3"></div>
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
                            <div class="text-center d-flex align-items-center ml-28"> 
                                <p class="crypto-font-22 poppins5 status-color-active mb-unset">{{ __('Make Payment') }}</p>
                            </div>
                        </div>

                        <div class="exchange-stick-step ml-step-21"></div>
                        <!-- status end for 4th_step_status-->

                        <!--check status start for 4th_step_status-->
                        <div class="steper-div-2 d-flex">
                            <div class="second-circle border-set bg-unset">
                            </div>
                            <div class="text-center d-flex align-items-center ml-28"> 
                                <p class="crypto-font-22 poppins5 status-color mb-unset">{{ __('Complete Transaction') }}</p>
                            </div>
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
                                <a class="font-16 OpenSans-600 back-color">{{ __('Back') }}</a>
                            </div>
                            <p class="font-20 OpenSans-700 c-white text-center mb-unset center-padding">{{ __('Crypto Buy') }}</p>
                        </div>
                    </div>
                    <div class="box-body box-border pr-28">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-6">
                                <div>
                                    <span class="font-16 OpenSans-400 c-blublack">{{ __('You are Sending') }}</span>
                                </div>
                                <div class="d-flex">
                                    <span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['totalAmount'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('guest.crypto_exchange.bank_payment') }}" class="display-block" method="POST" accept-charset="UTF-8" id="bank_deposit_form" enctype="multipart/form-data">
                            <input value="{{ csrf_token() }}" name="_token" id="token" type="hidden">
                            <input value="{{ $paymentmethod }}" name="method" id="method" type="hidden">
                            <input value="{{ $transInfo['totalAmount'] }}" name="amount" id="amount" type="hidden">

                        <div class="row">
                            <div class="col-md-12">
                                <label for="usr" class="OpenSans-400">{{ __('Select Bank') }}</label>
                                <div class="bank-name">
                                    <select class="form-control valid bank" name="bank" id="bank" aria-required="true" aria-invalid="false">
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank['id'] }}" {{ isset($bank['is_default']) && $bank['is_default'] == 'Yes' ? "selected" : "" }} class="font-16 mulish6">{{ $bank['bank_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bank-details mt-20">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-12">
                                    @if ($bank['account_name'])
                                    <div class="d-flex flex-column mob">
                                        <p class="mb-unset font-12 OpenSans-400 c-blublack">{{ __('Bank Account Name') }}</p>
                                        <p class="mb-unset font-18 OpenSans-600 c-blublack" id="account_name">{{ $bank['account_name'] }}</p>
                                    </div>
                                    @endif

                                    @if ($bank['account_number'])

                                    <div class="d-flex flex-column mob mt-20">
                                        <p class="mb-unset font-12 OpenSans-400 c-blublack">{{ __('Bank Account Number') }}</p>
                                        <p class="mb-unset font-18 OpenSans-600 c-blublack" id="account_number">{{ $bank['account_number'] }}</p>

                                    </div>
                                    @endif
                                </div>

                                @if ($bank['bank_name'])
                                <div class="col-md-6 col-sm-6 col-12">
                                    <div class="d-flex flex-column float-xs-right mob">
                                        <p class="mb-unset font-12 OpenSans-400 c-blublack">{{ __('Bank Name') }}</p>
                                        <p class="mb-unset poppins5 font-18 OpenSans-600 c-blublack" id="bank_name">{{ $bank['bank_name'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                        </div>
                        <div class="mt-20">
                            <label for="exampleInputEmail1">
                                <span class="font-18 OpenSans-400 c-blublack">{{ __('Attached File') }}</span>
                            </label>
                        </div>
                        <div class="form-group make-payment avoid-fc">
                            <input type="file" required data-value-missing="{{ __('Please select a file.') }}" name="proof_file" class="form-control input-file-field" id="file">
                        </div>
                        <span id="fileSpan" class="file-error"></span>
                        <div class="mt-next-2 next mt-32">
                            <button id="bank_payment" type="submit"
                                class="load-btn btn btn-bg-color btn-lg btn-block c-white font-20 OpenSans-600 d-flex justify-content-center align-items-center"
                                >
                                <span class="load-spiner spinner-border spinner-border-sm display-hide" role="status" aria-hidden="true"></span>{{ __('Confirm') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@section('js')

<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var extensions = JSON.parse(@json($extensions));
    var extensionsValidationRule = extensions.join('|');
    var extensionsValidation = extensions.join(', ');
    var errorMessage = '{{ __("Please select (:x) file.") }}';
    var invalidFileText = errorMessage.replace(':x', extensionsValidation);
    var requiedText = "{{ __('This field is required.') }}";
    var confirmText = "{{ __('Confirming...') }}";
    var bankDetailsUrl = "{{ route('guest.crypto_exchange.bank_details')}}";
</script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>

@endsection
