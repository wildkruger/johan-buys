@extends('cryptoexchange::frontend.layouts.app')

@section('content')
<div class="crypto-first-section px-240p" id="crypto-receiving-info">
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
	                                <div class="curent-second-circle curent-display" id="curent-second-circle"></div>
	                            </div>
	                        </div>
	                        <div class="text-center d-flex align-items-center ml-19"><p class="align-items-center crypto-font-22 OpenSans-600 status-color mb-unset">{{ __('Start Exchange') }}</p></div>
	                    </div>

	                    <div class="cp-exchange-stick-step ml-step-success"></div>
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

	                    <div class="exchange-stick-step-65 ml-step-21"></div>
	                    <!-- status end for 2nd_step_status-->
	                    <!--check status start for 3rd_step_status-->
	                    <div class="steper-div-2 d-flex">
	                        <div class="second-circle border-set bg-unset">
	                            <div class="curent-second-circle curent-display-show">
	                            </div>
	                        </div>

	                        <div class="text-center d-flex align-items-center ml-28">
	                        	<p class="crypto-font-22 OpenSans-600 status-color-active mb-unset">
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
	                            <a class="font-16 OpenSans-600 back-color">{{ __('Back') }}</a>
	                        </div>
	                        <p class="font-20 OpenSans-700 c-white text-center mb-unset center-padding">{{ __(ucwords(str_replace("_"," ", $transInfo['from_type']))) }}</p>
	                    </div>
	                </div>
	                <div class="provide-box-body box-border">
	                	<form method="POST" action="{{ route('guest.crypto_exchange.receiving_info')}}" id="crypto_buy_sell_from" enctype="multipart/form-data">
                        	@csrf
                         	<input type="hidden" name="exchange_type" value="{{ $transInfo['from_type'] }}">
							<div>
								<span class="font-16 OpenSans-400 c-blublack">{{ __('You Send') }}</span>
							</div>
							<div class="d-flex">
								<span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['defaultAmnt'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span>
								@if(currencyLogo($transInfo['fromCurrencyLogo']))
									<img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($transInfo['fromCurrencyLogo']) }}" alt="{{ $transInfo['fromCurrencyCode'] }}">
								@endif
							</div>

							<div class="mb-font text-break mt-4n">
								<span class="font-14 OpenSans-400">{{ __('Fess') }} ≈ {{  formatNumber($transInfo['totalFees'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrencyCode'] }}</span>
							</div>

							<div class="mt-29">
								<span class="font-16 OpenSans-400 c-blublack">{{ __('You Get') }}</span>
							</div>

							<div class="d-flex">
								<span class="font-28 OpenSans-600 c-blublack">{{ formatNumber($transInfo['finalAmount'], $transInfo['to_currency']) }} {{ $transInfo['currCode'] }}</span>
								@if(currencyLogo($transInfo['toCurrencyLogo']))
									<img class="ml-12 c-dimension img-fluid mtop-5" src="{{ currencyLogo($transInfo['toCurrencyLogo']) }}" alt="{{ $transInfo['currCode'] }}">
								@endif
							</div>

							<div class="mb-font text-break mt-4n">
								<span class="font-14 OpenSans-400">1 {{$transInfo['fromCurrencyCode']}} ≈ {{  formatNumber($transInfo['dCurrencyRate'], $transInfo['to_currency']) }} {{ $transInfo['currCode'] }}</span>
							</div>
	                     
							<div class="mt-29 pr-28">

	                     		@if($transInfo['from_type'] != 'crypto_sell')
									<div class="form-group next-step">
										<label for="crypto_address">
											<span class="recieving-font-18 OpenSans-400 c-blublack">{{ __('Receiving Address') }}</span>
										</label>
										<input class="form-control font-16-line mulish4 c-blublack mt-1n" type="text" name="crypto_address" id="crypto_address" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" placeholder="{{ __('Please provide your') }} {{ $transInfo['currCode'] }} {{ __('address') }}">
										<span class="error">{{ $errors->first('crypto_address') }}</span>
									</div>
	                        	@else
									<div class="form-group next-step make-payment">
										<label for="payment_details">
											<span class="font-18 OpenSans-400 c-blublack">{{ __('Receiving Account Details') }}</span>
										</label>
										<textarea  placeholder="{{ $transInfo['currCode'] }} {{ __('Account Details') }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" class="form-control font-16-line mulish4 c-blublack mt-1n" id="payment_details" name="receiving_details"></textarea>
										<span class="error">{{ $errors->first('receiving_details') }}</span>

									</div>
                            	@endif

								<div class="mt-14 d-flex justify-content-center mt-1n next">
									<button type="submit"  class="btn-bg-color btn-lg btn-block text-light font-20 OpenSans-600 c-blublack text-center" id="crypto_buy_sell_button">
										<i class="fa fa-spinner fa-spin displaynone" id="spinner"></i>
										<span class="exchange-confirm-submit-btn-txt" id="crypto_buy_sell_button_text">{{ __('Next Step') }}</span>
									</button>
								</div>
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

<script src="{{ theme_asset('public/js/fpjs2/fpjs2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var requiedText = "{{ __('This field is required.') }}";
    var nextText = "{{ __('Next...') }}";
</script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/crypto_front.min.js') }}"></script>

@endsection
