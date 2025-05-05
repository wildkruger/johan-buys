@extends('user_dashboard.layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('Modules/CryptoExchange/Resources/assets/css/user_dashboard.min.css') }}">
@endsection

@section('content')
<section class="section-06 history min-vh-100">
  <div class="container-fluid mt-4">
    <div>
      <h3 class="page-title">{{ __('Crypto Transaction') }}</h3>
    </div>
    <div class="row justify-content-center mt-4 mb-4">
        <!-- Description Start -->
        <div class="col-xs-10 col-lg-4">
          <div class="mt-5">
            <h3 class="sub-title">{{ __('Start Exchange') }}</h3>
            <p class="text-gray-500 text-16 text-justify">{{ __('Crypto exchange manual currencies from the comfort of your home, quickly, safely with a minimal fees. Select the wallet & put the amount you want to exchange') }}</p>
          </div>
        </div>
        <!-- Description Start -->
        
        <div class="col-lg-8">
          <div class="row">
            <div class="col-xl-10">
              @include('user_dashboard.layouts.common.alert')
              
              <!-- Multi Stepper Start -->
              <div class="d-flex w-100 mt-4">
                  <ol class="breadcrumb w-100">
                      <li class="breadcrumb-first text-white">{{ __('Create') }}</li>
                      <li>{{ __('Confirmation') }}</li>
                      <li class="active">{{ __('Success') }}</li>
                  </ol>
              </div>
              <!-- Multi stepper End -->

              <!-- Exchange Buy Sell Section Start -->
              <div class="cryptocard mt-4" id="crypto_exchange_user">
                <div class="rounded cex_white-bg px-nav postion-r">
                  <div class="row">
                    <div class="col-md-12 nav-flex">
                      <!-- Nav start -->
                      <nav>
                        <div class="nav nav-tabs nav-fill nav-block" id="nav-tab" role="tablist">
                          @if(transactionTypeCheck('crypto_swap'))          
                            <a class="w-323p postion-r nav-item nav-link crypto crypto_swap {{ (isset($transInfo['exchangeType']) && $transInfo['exchangeType'] == 'crypto_swap' )  ? 'active' : ((isset($exchangeType) && $exchangeType =='crypto_swap' ) ? 'active': '' ) }} " data-type="crypto_swap" href="#">{{ __('Crypto Swap') }}</a>
                          @endif
                          @if(transactionTypeCheck('crypto_buy_sell'))
                            <a class="w-323p postion-r nav-item nav-link crypto crypto_buy {{ ((isset($transInfo['exchangeType']) && $transInfo['exchangeType'] == 'crypto_buy') )  ? 'active' :((isset($exchangeType) && $exchangeType =='crypto_buy' ) ? 'active': '' ) }}" data-type="crypto_buy" href="#">{{ __('Crypto Buy') }}</a>
                      
                            <a class="w-323p postion-r nav-item nav-link crypto crypto_sell {{ (isset($transInfo['exchangeType']) && $transInfo['exchangeType'] == 'crypto_sell' )  ? 'active' : '' }}" data-type="crypto_sell" href="#">{{ __('Crypto Sell') }}</a>
                          @endif
                        </div>
                      </nav>
                      <!-- Nav End -->
                    </div>
                  </div>
                </div>
                
                <!-- Exchange start -->
                <div class="rounded bg-secondary px-35">
                  <form action="{{ route('user_dashboard.crypto_buy_sell.confirm') }}"  method="POST" accept-charset='UTF-8' id="crypto-send-form">
                      @csrf
                    <!-- Send Currency Div-->
                    <div class="row pt-10p">
                      <div class="col-md-8">
                        <input type="hidden" name="from_type" id="from_type" value="{{ isset($transInfo['exchangeType'])  ? $transInfo['exchangeType'] : $exchangeType }}">

                        <!-- Send Amount-->
                        <div class="form-group">
                            <label for="label1">{{__('You Send')}}</label>
                            <div>
                                <input type="text" autocomplete="off" required class="form-control" name="send_amount" id="send_amount" value="{{ isset($transInfo['defaultAmnt'])  ? formatNumber($transInfo['defaultAmnt'], $fromCurrency->id) : 1.00 }}" required onkeypress="return isNumberOrDecimalPointKey(this, event);" oninput="restrictNumberToPrefdecimalOnInput(this)">
                            </div>                                         
                            <p class="send_amount_error"></p>
                            <p class="direction_error"></p>
                            <p><span><small><strong>{{ __('Fees') }}</strong></small></span> : <small class="exchange_fee"></small></p>
                            <p><span><small><strong>{{ __('Estimated rate') }}</strong></small></span> : <small class="rate"></small></p>
                        </div>                                    
                      </div>

                      <!-- Send Currency -->
                      <div class="col-md-4 pb-3">
                        <div class="form-group">
                          <label for="cb-form-currency">{{ __('Currency') }}</label>
                          <div>
                            <select class="form-control select2" name="from_currency" id="from_currency">
                            @foreach($cryptoDirections as $exchangeDirection)
                              <option value="{{ optional($exchangeDirection->fromCurrency)->id }}" {{ ($fromCurrency->id == optional($exchangeDirection->fromCurrency)->id) ? 'selected' : '' }}> {{ optional($exchangeDirection->fromCurrency)->code }}  </option>
                            @endforeach
                          </select>
                          </div>                                                                                           
                        </div>
                      </div>                   
                    </div>
                    
                    <!-- Receive Currency Div-->
                    <div class="row">
                      <!-- Receive Amount-->
                      <div class="col-md-8">
                          <div class="form-group">
                            <label for="label2" class="mt-2">{{__('You Get')}}</label>
                            <input type="text" autocomplete="off" class="form-control" name="get_amount" id="get_amount" value="{{ isset($transInfo['finalAmount']) ?  formatNumber($transInfo['finalAmount'], $toCurrency->id) : 0.1 }}" required onkeypress="return isNumberOrDecimalPointKey(this, event);" oninput="restrictNumberToPrefdecimalOnInput(this)">
                          </div>
                      </div>
                      
                      <!-- Receive Currency -->
                      <div class="col-md-4 pb-3">
                        <div class="form-group">
                          <label for="to_currency" class="mt-2">{{ __('Currency') }}</label>
                          <select class="form-control select2" name="to_currency" id="to_currency">
                            @if(isset($toBuyCurrencies))
                              @foreach($toBuyCurrencies as $to_currency)
                                <option value="{{ $to_currency->id }}" {{ $to_currency->id == $toCurrency->id ? 'selected' : '' }}  >{{ $to_currency->code }}</option>
                              @endforeach
                            @endif
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-1">
                      <div class="col-md-12">
                          <a href="#">
                            <button class="btn btn-primary px-4 py-2" id="crypto_buy_sell_button" disabled > <i class="spinner"></i> <span id="rp_text" >{{ __('Next') }}</span> </button>
                          </a>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- Exchange End -->
              </div>
              <!-- Exchange Buy Sell Section End -->
            </div>
          </div>
        </div>
    </div>
  </div>
</section>
@endsection

@section('js')

<script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>

@include('common.restrict_character_decimal_point')
@include('common.restrict_number_to_pref_decimal')

<script type="text/javascript">
  'use strict';
  var waitingText = "{{ __('Please Wait') }}";
  var LoadingText = "{{ __('Loading...') }}";
  var submitText = "{{ __('Submitting...') }}";
  var requiedText = "{{ __('This field is required.') }}";
  var numberText = "{{ __('Please enter a valid number.') }}";
  var exchangeText = "{{ __('Exchanging...') }}";
  var nextText = "{{ __('Next') }}";
  var directionNotAvaillable = "{{ __('Direction not available.') }}";
  var decimalPreferrence = "{{ preference('decimal_format_amount_crypto', 8) }}";
  var directionListUrl = "{{ route('guest.crypto_exchange.direction_list')}}";
  var directionAmountUrl = "{{ route('guest.crypto_exchange.direction_amount')}}";
  var directionTypeUrl = "{{ route('guest.crypto_exchange.direction_type')}}";
  var walletCheckUrl = "{{ route('guest.crypto_exchange.wallet_check')}}";
</script>

<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/user_dashboard.min.js') }}"></script>

@endsection
