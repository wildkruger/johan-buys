@extends('cryptoexchange::frontend.layouts.app')

@section('content')
<div class="crypto-first-section px-240p">
    <div class="container-fluid mt-215 pb-131">
        <div class="row">
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
                                <a class="font-16 poppins5 back-color">{{ __('Back') }}</a>
                            </div>
                            <p class="font-20 poppins6 c-white text-center mb-unset center-padding">{{ __('Crypto Buy') }}</p>

                        </div>
                    </div>
                    <div class="box-body box-border pr-28">
                        <div class="row">
                            <div class="col-md-6">
                                <div><span class="font-16 poppins5 c-blublack">{{ __('You are Sending') }}</span></div>
                                <div class="d-flex"><span class="font-28 poppins5 c-blublack">{{  formatNumber($transInfo['totalAmount'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <p class="mb-unset font-16 poppins5 c-blublack">{{ __('Medium') }}</p>
                                        <div><img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/Payeer.svg') }}" alt="paypal"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="mt-4">

                        <form method="post" action="https://payeer.com/merchant/" id="payeer" accept-charset="UTF-8">
                            <input type="hidden" name="m_shop" value="<?=$m_shop?>">
                            <input type="hidden" name="m_orderid" value="<?=$m_orderid?>">
                            <input type="hidden" name="m_amount" value="<?=$m_amount?>">
                            <input type="hidden" name="m_curr" value="<?=$m_curr?>">
                            <input type="hidden" name="m_desc" value="<?=$m_desc?>">
                            <input type="hidden" name="m_sign" value="<?=$sign?>">
                            <input type="hidden" name="form[ps]" value="2609">
                            <input type="hidden" name="form[curr[2609]]" value="{{ $form_currency_code }}">
                            <input type="hidden" name="m_params" value="<?=$m_params?>">
                            <input type="hidden" name="m_cipher_method" value="AES-256-CBC">
                            <input type="submit" name="m_process" id="payeer-submit-button" value="{{ __('Click here if you are not redirected automatically') }}" />
                        </form>
    
                       </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>  
@endsection

@section('js')
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/payeer.min.js') }}"></script>
@endsection

