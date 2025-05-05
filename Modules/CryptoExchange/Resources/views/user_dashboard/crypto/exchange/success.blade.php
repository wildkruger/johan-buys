@extends('user_dashboard.layouts.app')
@section('css')

<link rel="stylesheet" href="{{ asset('Modules/CryptoExchange/Resources/assets/css/user_dashboard.min.css') }}">

@endsection
@section('content')

<section class="min-vh-100">
	<div class="container-fluid mt-5">
		<!-- Page title start -->
		<div>
			<h3 class="page-title">{{ __('Crypto Transaction') }}</h3>
		</div>
		<!-- Page title end-->
		<div class="row justify-content-center mt-4 mb-4">
			<div class="col-lg-4">
				<!-- Sub title start -->
				<div class="mt-5">
					<h3 class="sub-title">{{ $result['status'] }}</h3>
						@if( $result['status'] == 'Pending' )
							<p class="text-gray-500 text-16 text-justify">{{ __('Transaction created with pending status, admin will reveiw your transaction details. If admin approve the transaction then you will receive the amount.') }}</p>
						@else
							<p class="text-gray-500 text-16 text-justify">{{ __('Transaction has been created with success status, amount has been added on your wallet.') }}</p>
						@endif
				</div>
				<!-- Sub title end-->
			</div>
			<div class="col-lg-8 col-xs-12">
				<div class="row">
					<div class="col-md-10">
						<div class="crypto-box mob-mt-40">
							<div class="box-header bg-secondary">
								<div class="d-flex justify-content-center">
									<p class="font-20 fw-bold OpenSans-600 cex_c-blue text-center mb-unset">
										{{ __(ucwords(str_replace("_"," ", $result['type']))) }}
									</p>
								</div>
							</div>
		
							<div class="box-body bg-secondary box-border pr-28">
							   <div class="d-flex justify-content-center">
								<img src="{{ url('Modules/CryptoExchange/Resources/assets/landing/images/success2.svg') }}" alt="" class="w-responsive img-fluid">
								   
							   </div>
							   <div class="d-flex flex-column justify-content-center">
							   	 <p class="font-22 cex_c-blublack OpenSans-400 mb-unset text-center">
									{{ __('Transaction Completed') }}								
								 </p>
								 @if( $result['status'] == 'Pending' )
								 <p class="text-center">
									{{ __('Please wait for admin approval') }}								
								 </p>
								 @else
								 <p class="text-center">
									{{ __('Please check your wallet balance') }}
								 </p>
								 @endif

							   </div>
							   <div class="d-flex flex-column justify-content-center text-center total-div border mt-32p">
								<span class="font-16 OpenSans-400 cex_c-blublack">{{ __('Total') }}</span>
								<div class="d-flex justify-content-center">
									<div>
										<span class="font-28 fw-bold OpenSans-600 cex_c-blublack"> 
											{{ formatNumber($result->amount, optional($result->fromCurrency)->id) }}  {{ optional($result->fromCurrency)->code }}
										</span>
									</div>
									<div class="ml-10 mt-2 align-items-center">
										@if( isset($result->fromCurrency->logo) && currencyLogo(optional($result->fromCurrency)->logo))
											<img class="c-dimension mx-2" src="{{ currencyLogo(optional($result->fromCurrency)->logo) }}" alt="{{ optional($result->fromCurrency)->code }}">
										@endif 

									</div>
								</div>
							   </div>
							   <div class="cp-stick"></div>
							   <div class="d-flex flex-column justify-content-center text-center total-div border">
								<span class="font-16 OpenSans-400 cex_c-blublack">{{ __('Getting Amount') }}</span>
									<div class="d-flex justify-content-center">
										<div>
											<span class="font-28 fw-bold OpenSans-600 cex_c-blublack"> 
												{{ formatNumber($transInfo['getAmount'], optional($result->toCurrency)->id) }} {{ optional($result->toCurrency)->code }}
											</span>
										</div>
										<div class="ml-10 mt-2 align-items-center"> 
											@if( isset($result->toCurrency->logo) && currencyLogo(optional($result->toCurrency)->logo))
												<img class="c-dimension mx-2" src="{{ currencyLogo(optional($result->toCurrency)->logo) }}" alt="{{ optional($result->toCurrency)->code }}">
											@endif 
											
										</div>
									</div>
							   </div>
							   <p class="mt-12 text-center">
								</strong>{{ __('Exchange Rate') }}:<strong> 1 {{ optional($result->fromCurrency)->code }}</strong> ≈ <strong>{{ formatNumber($result->exchange_rate, $result->to_currency) }}</strong>
								{{ optional($result->toCurrency)->code }}
							 </p>
							 <p class="text-center mt-12">{{__('Track the transaction')}} :</p>
								<p class="text-center a-tag track-tag"><a class="cc" href="{{ $transInfo['trackUrl']}}" target="_blank">{{ $transInfo['trackUrl']}}</a> </p>
		
		
							   <div class="d-flex flex-column justify-content-center text-center total-div mt-10">
								<div class="flex-btn d-flex justify-content-center">
		
									<a href="{{ route('crypto_exchange.print', $result->id)}}" target="_blank" class="print-btn d-flex print-bg mob-mb-12">
										<img src="{{ asset('Modules/CryptoExchange/Resources/assets/landing/images/print.svg') }}" alt="">

										<span class="ml-11 text-light font-16 OpenSans-400"> {{__('Print')}}</span>
									</a>
											
									<a href="{{ route('user_dashboard.crypto_buy_sell.create')}}" class="font-15 OpenSans-400 cex_c-blublack mb-unset ml-24 print-btn-again d-flex justify-content-center align-items-center">
									{{__('Exchange Again')}}</a>

								</div>
							   </div>
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

@endsection
