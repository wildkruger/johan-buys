@php
    $extensions = json_encode(getFileExtensions(2));
@endphp

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

			<div class="col-lg-4 col-xs-10">

				<div class="mt-5">
					<h3 class="sub-title">{{ __('Confirmation') }}</h3>
					<p class="text-gray-500 text-16 text-justify">{{ __('Take a look before you send. Don\'t worry, if the recipient does not have an account, we will get them set up for free.') }} </p>
				</div>
			</div>

			<div class="col-lg-8">
				<div class="row">
					<div class="col-xl-10">
						<div class="d-flex w-100">
							<ol class="breadcrumb w-100">
								<li class="breadcrumb-active text-white">{{ __('Create') }}</li>
								<li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>
								<li class="active">{{ __('Success') }}</li>
							</ol>
						</div>


						<div class="card bg-secondary mt-4" id="crypto_exchange_details">
							<div class="h-box cex_black-bg">
								<div class="row">
									<div class="col-md-4 col-6">
										<a class="cex_back-text exchange-confirm-back-btn cursor-pointer">
											<u>
												<i class="fas fa-long-arrow-alt-left"></i>
												{{ __('Back') }}
											</u>
										</a>
									</div>
									<div class="col-md-8 md-65p col-6">
										<span class="font-20 fw-bold cex_c-blue">{{ __(ucwords(str_replace("_"," ", $transInfo['exchangeType']))) }}</span>
									</div>
								</div>
							</div>
				
							<div class="card-body shadow" >
								<form method="POST" action="{{ route('user_dashboard.crypto_buy_sell.success')}}" id="crypto_buy_sell_from" enctype="multipart/form-data">
	                                @csrf
	                                <input type="hidden" name="exchangeType" value="{{ $transInfo['exchangeType'] }}" id="exchangeType">
		                            
									<div class="d-flex pb-2 pt-2">
										<p>{{ __('Time Remaining')}} : </p><p class="px-2 font-weight-bold text-danger" id="timer"></p>										
									</div>
		                            <div class="row">
		                                <div class="col-md-6 col-lg-6 col-xs-12 col-6 p-3">
		                                    <div class="d-flex justify-content-sm-start">
		                                        <div class="d-flex flex-column pt">
		                                            <p><span class="ptsendget cex_c-blublack darkmode-text">{{ __('You Send') }}</span></p>
		                                            <div class="d-flex align-items-center">
														<p class="font-weight-bold font-18 cex_crypto-text">{{  formatNumber($transInfo['defaultAmnt'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrCode'] }}</p>										
														@if(currencyLogo($transInfo['fromCurrencyLogo']))
															<img class="c-dimension mx-2" src="{{ currencyLogo($transInfo['fromCurrencyLogo']) }}" alt="{{ $transInfo['fromCurrCode'] }}">
														@endif

													</div>
													<p> <span class="ptsendget font-14 darkmode-text">{{ __('Fee') }} ≈  </span> <span class="font-14 darkmode-text">{{  formatNumber($transInfo['totalFees'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrCode'] }}</span> </p>
													<span class="wallet-error"></span>
												</div>
		                                    </div>
		                                </div>
		                                <div class="col-md-6 col-lg-6 col-xs-12 col-6 p-3">
		                                    <div class="d-flex justify-content-start">
		                                        <div class="d-flex flex-column pt">
		                                            <p class="darkmode-text cex_c-blublack"><span class="ptsendget darkmode-text">{{ __('You Get') }}</span></p>
													<div class="d-flex align-items-center">
														<p class="font-weight-bold font-18 cex_crypto-text">{{  formatNumber($transInfo['getAmount'], $transInfo['to_currency']) }} {{ $transInfo['toCurrCode'] }}</p>

														@if(currencyLogo($transInfo['toCurrencyLogo']))
															<img class="c-dimension mx-2" src="{{ currencyLogo($transInfo['toCurrencyLogo']) }}" alt="{{ $transInfo['toCurrCode'] }}">
														@endif
													
													</div>
		                                            
													<p> <span class="ptsendget font-14">1 {{ $transInfo['fromCurrCode'] }} ≈ {{  formatNumber($transInfo['dCurrencyRate'], $transInfo['to_currency']) }} {{ $transInfo['toCurrCode'] }}</p>
												</div>
		                                    </div>
		                                </div>
		                            </div>

		                            @if($transInfo['exchangeType'] !== 'crypto_buy')

		                        	<div class="row">
		                                <div class="col-md-12 col-lg-12 col-xs-12 ">
		                                    <div class="form-group mt-8p">
		                                        <label for="payment">
		                                            <p class="cex_c-blublack">{{ __('Send Crypto') }} </p>
		                                        </label>
		                                        <div>
			                                        <select class="sl_common_bx form-control" name="pay_with" id="pay_with">
						                            	<option value="others">{{ $transInfo['fromCurrCode'] }} {{ __('Address') }}
						                                </option>
						                            	@if($transInfo['fromWallet'])
							                                <option value="wallet"> {{ __('Wallet') }} ( {{ $transInfo['fromWallet']->balance }} {{ $transInfo['fromCurrCode'] }} )
							                                </option>
						                                @endif
						                            </select>
		                                        </div>		                                        
		                                    </div>
		                                </div>
		                            </div>

		                            <div id="payment_details">
		                            	<div class="row">
			                                <div class="col-md-12 col-lg-12 col-xs-12">
												<div class="form-group mt-10p">
			                                        <p class="text-center">{{ __('Please make payment') }}  <strong class="c-blue">{{  formatNumber($transInfo['finalAmount'], $transInfo['from_currency']) }} {{ $transInfo['fromCurrCode'] }}</strong> {{ __('to our') }} <strong class="c-gray-black">{{ __('Merchant Address') }}</strong></p>
			                                    </div>
			                                    <div class="form-group d-flex flex-row justify-content-center">
													<div class="d-flex flex-column qr-width">
                                                    <div class="user-profile-qr-code text-center">
                                                        <img class="w-130" src="{{ qrGenerate($transInfo['merchantAddress']) }}"/>
                                                    </div>
			                                       </div>
												</div>
			                                </div>
			                            </div>
										<div class="text-break d-flex justify-content-center align-items-center">
											<p id="merchantAddress"><strong>{{ $transInfo['merchantAddress'] }}</strong></p>
											<span id="copyButton" class="input-group-addon btn" title="{{ __('Click to copy') }}">
												<img src="{{ url('Modules/CryptoExchange/Resources/assets/landing/images/copy-btn.png') }}" alt="{{ __('Copy') }}">
											</span>
											<p class="ml-2 copyText text-success d-none"> {{ __('Copied') }} <i class="fa fa-check text-success"></i></p>
										</div>

			                            <div class="row mt-2">
			                                <div class="col-md-12 col-lg-12 col-xs-12">
												<div class="form-group">
													<label for="payment_details">
			                                            <p class="cex_c-blublack">{{ __('Your Payment Details') }}</p>
			                                        </label>
													<div class="payment_details_section">
														<textarea class="form-control payment_details hover-unset" required data-value-missing="{{ __('This field is required.') }}" id="exampleFormControlTextarea1" name="payment_details" rows="2"></textarea>
													</div>
													<span class="error">{{ $errors->first('payment_details') }}</span>
												  </div>
			                                </div>
			                            </div>

			                            <div class="row">
			                                <div class="col-md-12 col-lg-12 col-xs-12 modifyform">
			                                    <div class="form-group">
			                                        <label for="payment_proof">
			                                            <p class="cex_c-blublack">{{ __('Payment Proof') }}</p>
			                                        </label>
			                                        <div class="payment_proof_section">
				                                        <input type="file" required data-value-missing="{{ __('Please select a file.') }}"  name="proof_file" class="form-control payment_details" id="file" placeholder="file" >
			                                        </div>
				                                    <span id="fileSpan" class="file-error"></span>
		                                        	<span class="error">{{ $errors->first('proof_file') }}</span>

			                                    </div>
			                                </div>
			                            </div>
		                            </div>
		                            @endif

		                            @if($transInfo['exchangeType'] !== 'crypto_sell')
		                            <div class="row">
		                                <div class="col-md-12 col-lg-12 col-xs-12 ">
		                                    <div class="form-group">
		                                        <label for="payment">
		                                            <p class="cex_c-blublack">{{ __('You Receive Crypto') }}</p>
		                                        </label>
					                            <select class="sl_common_bx form-control" name="receive_with" id="receive_with">
					                            	<option value="address">{{ $transInfo['toCurrCode'] }} {{ __('Address') }}  <small> </small>
					                                </option>
					                                <option value="wallet">{{ $transInfo['toCurrCode'] }} {{ __('Wallet') }}  </option>
					                            </select>
		                                    </div>
		                                </div>
		                            </div>

	                            	<div class="row" id="crypto_address_section">
		                                <div class="col-md-12 col-lg-12 col-xs-12 ">
		                                    <div class="form-group">
		                                        <label for="crypto_address">
		                                            <p class="cex_c-blublack">{{ __('Receiving Address') }} </p>
		                                        </label>
		                                        <div class="crypto_address_section">
		                                        	<input class="form-control crypto_address" type="text" id="crypto_address" required data-value-missing="{{ __('This field is required.') }}" placeholder="{{ __('Please provide your') }} {{ $transInfo['toCurrCode'] }} {{ __('address') }}" name="crypto_address">
		                                        </div>
		                                        <small class="form-text cex_c-blublack"><b> * {{ __('Providing wrong address may permanent loss of your coin') }} </b></small>
		                                        <span class="error">{{ $errors->first('crypto_address') }}</span>
		                                    </div>
		                                </div>
		                            </div>

		                            @endif

		                            <div class="row">
		                                <div class="col-md-12 col-lg-12 col-xs-12">
	                                        <button class="btn btn-primary px-4 py-2 submit-button" type="submit" id="exchange-confirm-submit-btn">
	                                            <i class="fa fa-spinner fa-spin displaynone" id="spinner"></i>
	                                            <strong>
	                                                <span class="exchange-confirm-submit-btn-txt" id="phone_verification_button_text">
	                                                    {{ __('Confirm') }}
	                                                </span>
	                                            </strong>
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
	</div>
</section>
@endsection

@section('js')
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/validation.min.js') }}"></script>

<script type="text/javascript">
    'use strict';
    var confirmText = "{{ __('Confirming...') }}";
    var extensions = JSON.parse(@json($extensions));
    var extensionsValidationRule = extensions.join('|');
    var extensionsValidation = extensions.join(', ');
    var errorMessage = '{{ __("Please select (:x) file.") }}';
    var invalidFileText = errorMessage.replace(':x', extensionsValidation);
    var exchangeTypeValue = "{{ $transInfo['exchangeType'] }}";
    var defaultAmnt = "{{ $transInfo['defaultAmnt'] }}";
    var fromCurrencyValue = "{{ $transInfo['from_currency'] }}";
    var toCurrencyValue = "{{ $transInfo['to_currency'] }}";
    var expireTime = "{{ $transInfo['expire_time'] }}";
    var expireText = "{{ __('Expired') }}";
    var walletCheckUrl = "{{ route('guest.crypto_exchange.wallet_check')}}";
</script>

<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/user_dashboard.min.js') }}"></script>
<script src="{{ asset('Modules/CryptoExchange/Resources/assets/js/countdown.min.js') }}"></script>
@endsection
