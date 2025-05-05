@extends('cryptoexchange::frontend.layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/select2.min.css')}}">
@endsection

@section('content')
<div class="navandbody-section p-main" id="crypto-front-initiate">
    <!-- Crypto exchange section -->
    <div>
        <div class="container-fluid px-240p mt-156 pb-10 row-head">
            <div class="row d-unset">
                <div class="col-md-6 col-sm-12 col-sm-12 col-xs-12 mw-auto">
                    <div class="pt-95">
                        <p class="f-21 OpenSans-600" data-aos="fade-up"
                            data-aos-anchor-placement="top-bottom">{{ __('THE SAFEST & MOST RELIABLE') }}</p>
                        <p class="bold-text p-0">
                            <span class="crypto-text OpenSans-700">{{ __('CRYPTO') }}</span><br>
                            <span class="exchange-text OpenSans-700">{{ __('EXCHANGE') }}</span>
                        </p>
                        <div class="OpenSans-400 font-20  text-width">{{ __('Buy, sell, and exchange most popular cryptocurrencies on Freed Trade easily, safely & securely with low fees in just a few minutes.') }}
                        </div>
                        <p class="OpenSans-400 font-16 col-md-11 mulish4 c-blue2"></p>
                        <p class="font-22 OpenSans-600 c-blue2 mt-38">{{ __('Let\'s Get Started..') }}</p>
                        <div
                            class="button-widths d-flex justify-content-between align-items-center mt-14 cursor-pointer btn-animate text-light">
                            <a href="{{ url('/register') }}" class="OpenSans-600">
                               {{ __('Create an Account') }}
                            </a>
                            <div class="ml-27p svg-img-parent">
                                <div class="svg-img">
                                    <svg width="54" height="54" viewBox="0 0 54 54" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect width="54" height="54" rx="6" fill="none"></rect>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M15.8181 39.1818C15.4164 38.7801 15.4164 38.1289 15.8181 37.7273L34.7272 18.8182C35.1288 18.4165 35.7801 18.4165 36.1817 18.8182C36.5834 19.2198 36.5834 19.8711 36.1817 20.2727L17.2726 39.1818C16.871 39.5835 16.2198 39.5835 15.8181 39.1818Z"
                                            fill="#403E5B"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M24.2441 19.5454C24.2441 18.9774 24.7046 18.5169 25.2727 18.5169L35.4545 18.5169C36.0225 18.5169 36.483 18.9774 36.483 19.5454L36.483 29.7273C36.483 30.2953 36.0225 30.7558 35.4545 30.7558C34.8864 30.7558 34.426 30.2953 34.426 29.7273L34.426 20.574L25.2727 20.574C24.7046 20.574 24.2441 20.1135 24.2441 19.5454Z"
                                            fill="#403E5B"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 pl-unset mw-auto">
                    <div class="row">
                        <div class="col-md-12 p-1-res">
                            <div class="pt-80">
                                @include('user_dashboard.layouts.common.alert')
                                @if( transactionTypeCheck() )
                                <nav class="nav-dimension">
                                    <div class="navmp nav nav-tabs nav-fill cursor-p" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link c-off-white padding-a OpenSans-700 crypto crypto_swap active"
                                            id="nav-home-tab"  role="tab" data-type="crypto_swap"
                                            aria-controls="nav-home" aria-selected="true">{{ __('Crypto Swap') }}
                                        </a>

                                        <a class="nav-item nav-link c-off-white padding-a OpenSans-700 crypto crypto_buy"
                                            id="nav-profile-tab"
                                            role="tab" aria-controls="nav-profile"
                                            aria-selected="false" data-type="crypto_buy">{{ __('Crypto Buy / Sell') }}
                                        </a>
                                    </div>
                                </nav>
                                @else
                                    <nav class="nav-dimension">
                                        <div class="navmp nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                            <a class="nav-item nav-link c-off-white padding-a OpenSans-700"
                                                id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                                                aria-controls="nav-home" aria-selected="true">{{ ( transactionTypeCheck('crypto_buy_sell') ) ?  __('Crypto Buy / Sell') : __(' Crypto Swap')  }}
                                            </a>
                                        </div>
                                    </nav>
                                @endif

                                <form action="{{ route('guest.crypto_exchange.verification') }}"  method="POST" accept-charset='UTF-8' id="crypto-send-form">
                                    @csrf
                                    <input type="hidden" name="from_type" id="from_type" value="{{ $exchange_type }}">

                                    <div class="tab-content tab-dimension" id="nav-tabContent">
                                        <div class="box-shadow tabpan-rad tab-pane fade show active bg-light"
                                            id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                            
                                            <!-- You Send Section -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="boxdiv yousend-top bg-light mx-28 mt-35 box-bg-one">
                                                        <div class="d-flex justify-content-between">

                                                            <!-- You Send Amount -->
                                                            <div class="mt-2 mt-n8-res w-100 avoid-fc">
                                                                <span class="font-14 OpenSans-400 c-blue2 pl-20 ">{{ __('You Send') }}</span>
                                                                <br>
                                                                <input type="text" class="form-control custom-height w-100 input-customization s-font-24 c-blue2 mulish4 pl-20 mt-5pn" autocomplete="off" name="send_amount" id="send_amount" value="0.1" onkeypress="return isNumberOrDecimalPointKey(this, event);" oninput="restrictNumberToPrefdecimalOnInput(this)">
                                                            </div>

                                                            <!-- You Send Currencies -->
                                                            <div class="apto-dropdown-wrappers2">

                                                                <div class="d-flex justify-content-between align-content-center">
                                                                    <div>
                                                                        <select class="form-currency min-width" name="from_currency" id="from_currency" >
                                                                            @foreach($cryptoDirections as $exchangeDirection)
                                                                                <option value='{{ optional($exchangeDirection->fromCurrency)->id }}'  data-thumbnail="{{ optional($exchangeDirection->fromCurrency)->logo }}" class="font-20 OpenSans-400" > {{ optional($exchangeDirection->fromCurrency)->code }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Limit Fees Estimated Text -->
                                            <div class="row">                   
                                                <div class="col-md-10 col-10 parent-2">
                                                    <div class="ul-one ul-ml-51 dot display-hide">
                                                    </div>
                                                <div class="d-flex align-items-center dot dot-message display-hide">
                                                    <div class="ul-two ul-ml-47 dot display-hide">
                                                    </div>

                                                    <p class="mb-unset OpenSans-400 font-13 c-blue2 pl-16 send_amount_error"></p> 
                                                </div>
                                                
                                                    <div class="ul-three ul-ml-51">
                                                    </div>
                                                    <div class="d-flex align-items-center h-9p">
                                                        <div class="ul-four ul-ml-47">
                                                        </div>
                                                        <p class="mb-unset OpenSans-400 font-13 c-blue2 pl-16"> {{ __('Fees') }} : <span class="exchange_fee"></span></p> 
                                                    </div>
                                                    <div class="ul-five ul-ml-51">
                                                    </div>
                                                    <div class="d-flex align-items-center h-9p">
                                                        <div class="ul-six ul-ml-47">
                                                        </div>
                                                        <p class="mb-unset OpenSans-400 font-13 c-blue2 pl-16">{{ __('Estimated rate') }} : <span class="rate"></span></p> 
                                                    </div>
                                                    
                                                    <div class="ul-seven ul-ml-51">

                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-2 d-flex align-items-center">
                                                    <div class="buy-sell-btn display-flex justify-content-center align-items-center switch-box display-hide cur-pointer">
                                                        <svg class="hh" width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.75 0C12.3358 0 12 0.373096 12 0.833333C12 1.29357 12.3358 1.66667 12.75 1.66667H15.75C15.9489 1.66667 16.1397 1.75446 16.2803 1.91074C16.421 2.06702 16.5 2.27899 16.5 2.5V17.4164L13.4226 14.2511C13.0972 13.9163 12.5695 13.9163 12.2441 14.2511C11.9186 14.5858 11.9186 15.1285 12.2441 15.4632L16.4107 19.7489C16.6068 19.9506 16.8761 20.0308 17.1305 19.9895C17.1694 19.9964 17.2093 20 17.25 20C17.6095 20 17.9098 19.719 17.983 19.344L21.7559 15.4632C22.0814 15.1285 22.0814 14.5858 21.7559 14.2511C21.4305 13.9163 20.9028 13.9163 20.5774 14.2511L18 16.9021V2.5C18 1.83696 17.7629 1.20107 17.341 0.732233C16.919 0.263392 16.3467 0 15.75 0H12.75ZM4.62801 4.01042C4.396 3.97235 4.1488 4.03861 3.96967 4.20921L0.21967 7.78064C-0.0732233 8.05958 -0.0732233 8.51184 0.21967 8.79079C0.512563 9.06974 0.987437 9.06974 1.28033 8.79079L4 6.20063V20.625C4 21.2549 4.23705 21.859 4.65901 22.3044C5.08097 22.7498 5.65326 23 6.25 23H9.25C9.66421 23 10 22.6456 10 22.2083C10 21.7711 9.66421 21.4167 9.25 21.4167H6.25C6.05109 21.4167 5.86032 21.3333 5.71967 21.1848C5.57902 21.0363 5.5 20.835 5.5 20.625V6.67682L7.71967 8.79079C8.01256 9.06974 8.48744 9.06974 8.78033 8.79079C9.07322 8.51184 9.07322 8.05958 8.78033 7.78064L5.487 4.64413C5.42152 4.27741 5.11645 4 4.75 4C4.70846 4 4.66771 4.00356 4.62801 4.01042Z" fill="#403E5B" fill-opacity="0.6"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- You Get Section -->
                                            <div class="row pb-36">
                                                <div class="col-md-12">
                                                    <div class="boxdiv bg-light mx-28 box-bg-two">
                                                        <div class="d-flex justify-content-between">

                                                            <!-- You Get Amount -->
                                                            <div class="mt-2 mt-n8-res w-100 avoid-fc">
                                                                <span class="font-14 poppins5 c-blue2 pl-20">{{ __('You Get') }}</span>
                                                                <br>
                                                                <input type="text" class="form-control custom-height w-100 input-customization s-font-24 c-blue2 mulish4 pl-20 mt-5pn" autocomplete="off" name="get_amount" id="get_amount" value="0.1" onkeypress="return isNumberOrDecimalPointKey(this, event);" oninput="restrictNumberToPrefdecimalOnInput(this)">
                                                            </div>

                                                            <!-- You Send Currency -->
                                                            <div class="apto-dropdown-wrappers">
                                                                <div class="d-flex justify-content-between align-content-center">
                                                                    <div>
                                                                        <select class="to-currency min-width" name="to_currency" id="to_currency" >
                                                                            @if(isset($toBuyCurrencies))
                                                                            @foreach($toBuyCurrencies as $to_currency)
                                                                            <option value='{{ $to_currency->id }}' data-thumbnail="{{ $to_currency->logo }}" class="font-20 poppins5" > {{ $to_currency->code }}</option>
                                                                            @endforeach
                                                                            @endif

                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Exchange Button -->
                                            <div class="row pb-36">
                                                <div class="col-md-12">
                                                    <div class="mx-28 exchangebutton-2 text-center btn-bg-color">
                                                        <button class="btn-lg btn-block btn cur-pointer op-unset" id="crypto_buy_sell_button" type="submit"><span
                                                                class="exc-font-22 OpenSans-600 c-white" id="rp_text">{{ __('Exchange') }}</span> </button>
                                                    </div>
                                                </div>
                                            </div>                           
                                        </div>
                                        <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                            aria-labelledby="nav-profile-tab">
                                            <div class="section">
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
    <!-- Crypto exchange section End-->
</div>

<!-- Buy Sell Exchange section -->
<div class="mt-100">
    <div class="px-240p">
        <div class="text-center">
            <div data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                <span class="OpenSans-600 font-24 c-blue3">{{ __('BUY SELL EXCHANGE') }}</span>
            </div>
            <p class="OpenSans-600 font-60 c-blublack" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                {{ __('YOUR TRUSTED CRYPTO EXCHANGE') }}</p>
            <div class="d-flex flex-row justify-content-center" data-aos="fade-up"
                data-aos-anchor-placement="top-bottom">
                <hr class="new4">
            </div>
        </div>

        <div class="d-flex gap-32 mt-44 flx-wrap" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
            <div class="cryptobox1 flex-basis-full">
                <p class="OpenSans-600 font-32 c-blublack f-18-res">{{ __('Fast Crypto Exchange') }}</p>
                <p class="OpenSans-400 font-20 c-blublack mb_unset f-14-res">{{ __('Freed Trade is the easiest place to buy, sell & exchange cryptocurrency. Verify your identity started now.') }}</p>
                <div class="float-right">
                    <img class="mtop-23" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/cryto-bg.svg') }}" alt="cryto-bg.svg">
                </div>
            </div>
            <div class="cryptobox2 flex-basis-full">
                <div class="row">
                    <img class="mt-23" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/buy-crypto-bg.svg') }}" alt="cryto-bg.svg">
                </div>
                <p class="OpenSans-600 font-32 c-blublack mt-41 f-18-res">{{ __('Buy Crypto with Fiat') }} </p>
                <p class="OpenSans-400 font-20 c-blublack f-14-res">{{ __('With Freed Trade, you can buy any crypto with more than 50 fiat currencies using a bank deposit or card payment.') }}
                </p>
            </div>
            <div class="cryptobox3 flex-basis-full">
                <p class="OpenSans-600 font-32 c-blublack f-18-res">{{ __('Advanced Data Encryption') }}</p>
                <p class="OpenSans-400 font-20 p2-font-20 c-blublack f-14-res">{{ __('Your transaction data is secured via end-to-end encryption, ensuring that only you have access to your personal information.') }}</p>
                <div class="row pr-lg1">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="parent-dot parent-dot-right">
                            <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/dot-right.svg') }}" class="img-dot img-fluid" alt="dot-right">
                            <div class="lock">
                                <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/locks.svg') }}" class="img-fluid" alt="locks">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="first-section-b">
    <div class="container-fluid first-section-b-px-240p">
        <div class="row mt-140">
            <div class="col-md-12 col-xs-12">
                <div class="text-center" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    <span class="OpenSans-600 font-24 c-blue3 text-center headline">{{ __('HOW IT WORKS') }}</span>
                </div>
                <p class="OpenSans-600 font-60 text-center c-blublack headline" data-aos="fade-up"
                    data-aos-anchor-placement="top-bottom">{{ __('FEW EASY STEPS TO MAKE') }}</p>
                <div class="d-flex flex-row justify-content-center" data-aos="fade-up"
                    data-aos-anchor-placement="top-bottom">
                    <hr class="new4 text-center">
                </div>
            </div>
        </div>
        <div class="row mt-44">
            <div class="col-md-5 col-xs-12 col-sm-12" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                <div class="accordion ml-28" id="accordionStepper">
                    <div class="card mod-card">
                        <div class="card-header b-unset bg-unset" id="headingOne">
                            <div class="d-flex c-text-parent">
                                <div class="active-circle-bg circle-bg circle-bg-one active-round text-light text-center d-flex justify-content-center ml-n50 mt-n12">
                                    <span class="d-flex align-items-center multistep-font step-active-color OpenSans-600 ">1</span>
                                </div>
                                <p class="text-left font-28 OpenSans-600 c-text c-blublack mt-n4 ml-26 "
                                    type="button" data-toggle="collapse" data-target="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne">
                                    {{ __('Choose Currency') }}
                                </p>
                            </div>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                            data-parent="#accordionStepper">
                            <div class="">
                                <p class="font-20 OpenSans-400 ml-55 mt-n8 w-378">
                                    {{ __('Select the cryptocurrency pair you\'d like to exchange. You can exchange crypto or fiat amount at either fixed or floating rates.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mod-card">
                        <div class="card-header b-unset bg-unset " id="headingTwo">
                            <div class="d-flex c-text-parent">
                                <div
                                    class="circle-bg circle-bg-two active-round text-center d-flex justify-content-center ml-n50 mt-n12">
                                    <span
                                        class="d-flex align-items-center multistep-font step-color OpenSans-600">2</span>
                                </div>
                                <p class="text-left font-28 OpenSans-600 c-text c-blublack text-left collapsed mt-n4 ml-26"
                                    type="button" data-toggle="collapse" data-target="#collapseTwo"
                                    aria-expanded="false" aria-controls="collapseTwo">
                                   {{ __('Verify Your Identy') }}
                                </p>
                            </div>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                            data-parent="#accordionStepper">
                            <div>
                                <p class="font-20 OpenSans-400 ml-55 mt-n8 w-378">
                                    {{ __('Provide your email or phone & click next step, enter the OTP code that you received, you are verified.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mod-card">
                        <div class="card-header b-unset bg-unset" id="headingThree">
                            <div class="d-flex c-text-parent">
                                <div class="circle-bg circle-bg-three active-round text-center d-flex justify-content-center ml-n50 mt-n12">
                                    <span class="d-flex align-items-center multistep-font step-color OpenSans-600">3</span>
                                </div>
                                <p class="text-left font-28 OpenSans-600 c-blublack collapsed mt-n4 ml-26 c-text"
                                    type="button" data-toggle="collapse" data-target="#collapseThree"
                                    aria-expanded="false" aria-controls="collapseThree">
                                   {{ __('Enter Receiving Details') }}
                                </p>
                            </div>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                            data-parent="#accordionStepper">
                            <div>
                                <p class="font-20 OpenSans-400 ml-55 mt-n8 w-378">
                                    {{ __('Enter the address of the crypto wallet that your cryptocurrency will be sent to. Or provide the account details where you receive the fiat balance') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card overflow-unset b-unset">
                        <div class="card-header b-unset bg-unset" id="headingFour">
                            <div class="d-flex c-text-parent">
                                <div class="circle-bg circle-bg-four text-center active-round d-flex justify-content-center ml-n36-res ml-n50 ml-38n mt-n12">
                                    <span class="d-flex align-items-center multistep-font step-color OpenSans-600">4</span>
                                </div>
                                <p class="text-left c-text font-28 OpenSans-600 c-blublack collapsed mt-n4 ml-26"
                                    type="button" data-toggle="collapse" data-target="#collapseFour"
                                    aria-expanded="false" aria-controls="collapseFour">
                                    {{ __('Make Payment') }}
                                </p>
                            </div>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                            data-parent="#accordionStepper">
                            <div>
                                <p class="font-20 OpenSans-400 ml-55 mt-n8 w-378">
                                    {{ __('Check all exchange details, get an estimated transaction time, and send your funds to our address. Provide transaction proof with attachment.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-xs-12 col-sm-12 d-n-res">
                <div class="img-div" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/lp-cover.svg') }}" alt="" class="img-fluid">
                </div>
                <div class="right-dot-img">
                    <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/dot-right.svg') }}" alt="dot-right">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- How It Works Section End-->


<!-- CTA Banner Section -->
<div class="second-section signup-today signip-parent">
    <div class="container-fluid">
        <div class="signup-today-p">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="d-flex flex-column" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                        <span class="font-24 OpenSans-700 c-blue-shade f-16-res">{{ __('HASSLE FREE') }}</span>
                        <p class="font-38 OpenSans-700 c-white">{{ __('BUY SELL EXCHANGE') }}</p>
                    </div>
                    <div class="cbtn-bg d-flex justify-content-between align-items-center mt-12 cursor-pointer btn-animate-two"
                        data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                        <a href="{{url('/register')}}" class="text-light OpenSans-600">{{ __('Create an Account') }}</a>
                        <div class="ml-27p svg-img-parent-two">
                            <div class="svg-img-two">
                                <svg width="54" height="54" viewBox="0 0 54 54" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="54" height="54" rx="6" fill="" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.8181 39.1816C15.4164 38.78 15.4164 38.1287 15.8181 37.7271L34.7272 18.818C35.1288 18.4163 35.7801 18.4163 36.1817 18.818C36.5834 19.2196 36.5834 19.8709 36.1817 20.2725L17.2726 39.1816C16.871 39.5833 16.2198 39.5833 15.8181 39.1816Z"
                                        fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M24.2441 19.5454C24.2441 18.9774 24.7046 18.5169 25.2727 18.5169L35.4545 18.5169C36.0225 18.5169 36.483 18.9774 36.483 19.5454L36.483 29.7272C36.483 30.2953 36.0225 30.7557 35.4545 30.7557C34.8864 30.7557 34.426 30.2953 34.426 29.7272L34.426 20.5739L25.2727 20.5739C24.7046 20.5739 24.2441 20.1134 24.2441 19.5454Z"
                                        fill="white" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    <div class="img-child"><img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/man.svg') }}" alt="man" class="img-fluid"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CTA Banner Section End-->

<!-- Frequently Asked Questions Section-->
<div class="third-section pt-120" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
    <div class="container-fluid px3-460p row-heads position-relative">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <span class="OpenSans-600 font-24 c-blue3 text-center" data-aos="fade-up" data-aos-anchor-placement="top-bottom">{{ __('WE GOT YOU COVERED') }}</span>
                </div>
                <p class="OpenSans-600 font-60 text-center c-blublack" data-aos="fade-up" data-aos-anchor-placement="top-bottom">{{ __('FREQUENTLY ASKED QUESTIONS') }}</p>

                <div class="d-flex flex-row justify-content-center" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    <hr class="new4 text-center">
                </div>
                <div class="text-center mt-44"><span class="text-center font-32-p5 OpenSans-600 c-blublack mb-12 p-0 mb-unset">{{ __('Crypto Exchange Process') }}</span></div>
            </div>
        </div>

        <div class="row" data-aos="fade-up" data-aos-anchor-placement="top-bottom">
            <div class="col-md-12 p-0-res">
                <div id="main">
                    <div class="container">
                        <p class="text-center font-32-p5 poppins5 c-blublack mb-12 p-0 mb-unset"></p>
                        <div class="accordion" id="faq">
                            <div class="card">
                                <div class="card-header" id="faqhead1">
                                    <a href="#" class="btn btn-header-link collapsed exp-font-22 OpenSans-600"
                                        data-toggle="collapse" data-target="#faq1" aria-expanded="true"
                                        aria-controls="faq1">{{ __('What is Freed Trade Crypto Exchange?') }}</a>
                                </div>

                                <div id="faq1" class="collapse" aria-labelledby="faqhead1" data-parent="#faq">
                                    <div class="card-body OpenSans-400">
                                        {{ __('Freed Trade lets you exchange cryptocurrency in a fast and secure way. Just verify your identity, choose a currency pair you would like to exchange, and click the submit button. Afterward, provide your reciving details, make payment and wait for a bit. In several minutes, the exchanged amount will arrive in your account.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqhead2">
                                    <a href="#" class="btn btn-header-link collapsed  exp-font-22 OpenSans-600"
                                        data-toggle="collapse" data-target="#faq2" aria-expanded="true"
                                        aria-controls="faq2">{{ __('Why trust Freed Trade?') }}</a>
                                </div>

                                <div id="faq2" class="collapse" aria-labelledby="faqhead2" data-parent="#faq">
                                    <div class="card-body OpenSans-400">
                                       {{ __('FreedTrade is an instant cryptocurrency exchange that has been operating on the market since 2021. We successfully serviced millions of customers over this time and continue to provide quick crypto-to-crypto, crypto-to-fiat & fiat-to-crypto exchanges and purchases to more than 2.6 million users every month. In order to enhance the functionality of our crypto exchange, we collaborate with the leading companies in the industry. Our dedicated Support team stands guard 24/7 to help you with any exchange-related questions that might arise.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqhead3">
                                    <a href="#" class="btn btn-header-link collapsed exp-font-22 OpenSans-600"
                                        data-toggle="collapse" data-target="#faq3" aria-expanded="true"
                                        aria-controls="faq3">{{ __('What cryptocurrencies do you support?') }}</a>
                                </div>

                                <div id="faq3" class="collapse" aria-labelledby="faqhead3" data-parent="#faq">
                                    <div class="card-body OpenSans-400">
                                        {{ __('We will soon support over 200 cryptocurrencies that are available for instant crypto exchange and purchase at the best execution prices. Since the crypto market is developing rapidly, we are continually building up the list of crypto assets, so you can exchange, sell, and buy new digital currencies within minutes. Exchange and buy Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Litecoin (LTC), and a wide variety of other crypto assets using payment methods that suit you the most (Visa, Mastercard, bank transfer).') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="faqhead4">
                                    <a href="#" class="btn btn-header-link collapsed exp-font-22 OpenSans-600"
                                        data-toggle="collapse" data-target="#faq4" aria-expanded="true"
                                        aria-controls="faq4">{{ __('How fast will my transaction be processed?') }}</a>
                                </div>

                                <div id="faq4" class="collapse" aria-labelledby="faqhead4" data-parent="#faq">
                                    <div class="card-body OpenSans-600">
                                        {{ __('Typically, a crypto money exchange takes around 10-40 minutes. However, a cryptocurrency exchange might take more time should there be congestion within a particular blockchain.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dot-left-side">
            <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/half-dot-left.svg') }}" alt="dot-logo" class="img-fluid">
        </div>
        <div class="dot-right-side">
            <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/half-right-dot.svg') }}" alt="dot-logo" class="img-fluid">
        </div>
    </div>

</div>
<!-- Frequently Asked Questions Section End-->

<!--Market Trends Section-->
<div>
    <div class="container-fluid px-240p pt-140 pb-140">
        <div class="img-row row mt-327">
            <div class="col-md-6 col-xs-12">
                <p data-aos="fade-up" data-aos-anchor-placement="top-bottom"><span
                        class="font-32-p5 OpenSans-700 hasle-text">{{ __('Hassle free') }} <br></span>
                    <span class="font-48 OpenSans-700 fast-sectext">{{ __('FAST & SECURED ') }}<br></span>
                    <span class="font-64-crypto OpenSans-600 last-crypto-text">{{ __('CRYPTO') }}</span> <span
                        class="font-64-crypto poppins7">{{ __('EXCHANGE') }}</span><span
                        class="hasle-text font-64-crypto">.</span>
                </p>
                <p class="font-28-6 c-blublack OpenSans-600 mt-23" data-aos="fade-up"
                    data-aos-anchor-placement="top-bottom">{{ __('Sign up now to build your own portfolio for free!') }}</p>
                <div class="button-widths d-flex justify-content-between align-items-center mt-32 cursor-pointer btn-animate text-light OpenSans-600"
                    data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                    <a href="{{url('/register')}}" class="OpenSans-600">
                        {{ __('Create an Account') }}
                    </a>
                    <div class="ml-27p svg-img-parent">
                        <div class="svg-img">
                            <svg width="54" height="54" viewBox="0 0 54 54" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="54" height="54" rx="6" fill="none"></rect>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M15.8181 39.1818C15.4164 38.7801 15.4164 38.1289 15.8181 37.7273L34.7272 18.8182C35.1288 18.4165 35.7801 18.4165 36.1817 18.8182C36.5834 19.2198 36.5834 19.8711 36.1817 20.2727L17.2726 39.1818C16.871 39.5835 16.2198 39.5835 15.8181 39.1818Z"
                                    fill="#403E5B"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M24.2441 19.5454C24.2441 18.9774 24.7046 18.5169 25.2727 18.5169L35.4545 18.5169C36.0225 18.5169 36.483 18.9774 36.483 19.5454L36.483 29.7273C36.483 30.2953 36.0225 30.7558 35.4545 30.7558C34.8864 30.7558 34.426 30.2953 34.426 29.7273L34.426 20.574L25.2727 20.574C24.7046 20.574 24.2441 20.1135 24.2441 19.5454Z"
                                    fill="#403E5B"></path>
                            </svg>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 img-top-gap" data-aos="fade-up"
                data-aos-anchor-placement="top-bottom">
                <img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/cryptoimg.png') }}" alt="img" class="img-fluid mt-n96">
            </div>
        </div>
    </div>
</div>
<!--Market Trends Section-->

<!-- Download section -->
<div class="pt-86 dark-app pb-144 position-relative">
    <img class="app-dot" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/Dotapp.png') }}" alt="">
    <div class="px-240 position-relative">
        <div class="bg-app">
            <div class="row">
                <div class="col-md-6 order-last order-md-first pay-img">
                    <img class="ml-171 mt-81 desktop-mobile-view" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/app-img.png') }}" alt="">
                    <img class="ml-171 mt-81 app-mobile-view" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/app-mobile-view.png') }}" alt="">
                </div>
                <div class="col-md-4 order-first order-md-last">
                    <div class="mt-148">
                        <p class="color-FE OpenSans-600 f-18 leading-24 mb-0 text-center">{{ __('DOWNLOAD THE APP') }}</p>
                        <p class="color-05B OpenSans-700 f-34 mb-23 mt-7 text-center app-content" data-content="REASONS">{{ __('Try it on mobile today') }}</p>
                        <p class="small-border mb-0 bgd-blue m-auto"></p>
                    </div>

                    <div class="d-flex mt-56 app-sec">
                        @foreach(getAppStoreLinkFrontEnd() as $app)
                            @if (!empty($app->logo) && file_exists('public/uploads/app-store-logos/thumb/'.$app->logo))
                                <a href="{{ $app->link }}" target="_blank">
                                    <img class="cursor-pointer {{ $app->company == 'Apple' ?  'ml-3 ml-r11' : '' }} app-image" src="{{ url('public/uploads/app-store-logos/thumb/'.$app->logo) }}" alt="{{ $app->company }}">
                                </a>
                            @else
                                <a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <img class="app-dot-right" src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/app-dot-right.png') }}" alt="">
    </div>
</div>

<!-- Download section End-->

@endsection

@section('js')

@include('common.restrict_number_to_pref_decimal')
@include('common.restrict_character_decimal_point')

<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.min.js')}}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/select2.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    'use strict';
    var requiedText = "{{ __('This field is required.') }}";
    var numberText = "{{ __('Please enter a valid number.') }}";
    var exchangeText = "{{ __('Swap') }}";
    var buyText = "{{ __('Buy') }}";
    var sellText = "{{ __('Sell') }}";
    var directionNotAvaillable = "{{ __('Direction not available.') }}";
    var decimalPreferrence = "{{ preference('decimal_format_amount_crypto', 8) }}";
    var noResult = "{{ __('No Result') }}";
    var directionListUrl = "{{ route('guest.crypto_exchange.direction_list')}}";
    var directionAmountUrl = "{{ route('guest.crypto_exchange.direction_amount')}}";
    var directionTypeUrl = "{{ route('guest.crypto_exchange.direction_type')}}";
    var confirmationUrl = "{{ route('guest.crypto_exchange.verification')}}";
</script>

<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>

@endsection