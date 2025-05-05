@extends('user.layouts.app')

@section('content')
<div class="text-center">
    <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20 text-uppercase">{{ __('Edit Merchant') }}</p>
    <p class="mb-0 gilroy-medium text-gray-100 f-16 r-f-12 merchant-title  mt-2 tran-title">{{ __('Fill in the information needed to update merchants information') }}</p>
</div>
@include('user.common.alert')
<div class="row new-merchant-top mt-24" id="merchantUpdate">
    <div class="col-xl-4">
        <div class="sticky-mode">
            <div class="d-flex align-items-center back-direction">
                <a href="{{ route('user.merchants.index') }}" class="text-gray-100 f-16 leading-20 gilroy-medium d-inline-flex align-items-center position-relative back-btn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn">{{ __('Back to list') }}</span>
                </a>
            </div>
            <div id="carouselExampleDark" class="carousel carousel-dark slide mt-12" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active carousel-inner-dimension bg-white" data-bs-interval="4000">
                        <p class="mb-0 text-primary f-16 leading-23 gilroy-medium text-center">{{ __('Being A') }}</p>
                        <p class="mb-0 text-dark f-24 leading-30 gilroy-Semibold mt-3p text-center">{{ __('Merchant') }}</p>
                        <div class="mt-32 text-center carosel-image">
                            <img src="{{ asset('public/user/templates/images/carosel-image.png') }}">
                        </div>
                        <p class="mb-0 f-15 leading-25 gilroy-medium text-gray-100 text-center mt-32 carosel-text-dimension">{{ __('Merchant account will allow your business to accept payments from your customers.') }}</p>
                    </div>
                    <div class="carousel-item carousel-inner-dimension bg-white" data-bs-interval="4000">
                        <p class="mb-0 text-primary f-16 leading-23 gilroy-medium text-center">{{ __('Being A') }}</p>
                        <p class="mb-0 text-dark f-24 leading-30 gilroy-Semibold mt-3p text-center">{{ __('Merchant') }}</p>
                        <div class="mt-32 text-center carosel-image">
                            <img src="{{ asset('public/user/templates/images/carosel-image-2.png') }}">
                        </div>
                        <p class="mb-0 f-15 leading-25 gilroy-medium text-gray-100 text-center mt-32 carosel-text-dimension">{{ __('Once a merchant is approved by the administrator, the merchant account will be ready to accept payments.') }}</p>
                    </div>
                    <div class="carousel-item carousel-inner-dimension bg-white" data-bs-interval="4000">
                        <p class="mb-0 text-primary f-16 leading-23 gilroy-medium text-center">{{ __('Being A') }}</p>
                        <p class="mb-0 text-dark f-24 leading-30 gilroy-Semibold mt-3p text-center">{{ __('Merchant') }}</p>
                        <div class="mt-32 text-center carosel-image">
                            <img src="{{ asset('public/user/templates/images/carosel-image-3.png') }}">
                        </div>
                        <p class="mb-0 f-15 leading-25 gilroy-medium text-gray-100 text-center mt-32 carosel-text-dimension">{{ __('Money added to your wallets when customer pays for product or service. You can create both standard and Express merchants with proper information.') }}</p>
                    </div>
                </div>
            </div>               
        </div>
    </div>
    <div class="col-xl-8 dis-mb-top">
        <form method="post" action="{{ route('user.merchants.update') }}" enctype="multipart/form-data" id="merchantUpdateForm">
            @csrf
            <input type="hidden" value="{{ $merchant->id }}" name="id" id="id">
            <div class="merchant-parent-form bg-white mt-32">
                <p class="mb-0 f-24 leading-30 gilroy-Semibold text-dark text-center">{{ __('Merchant Form') }}</p>
                <!-- Business Name -->
                <div class="row">
                    <div class="col-12">
                        <div class="label-top">
                            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-32 r-mt-amount r-mt-6">{{ __('Business Name') }} <span class="f-16 text-warning">*</span></label>
                            <input type="text" class="form-control input-form-control apply-bg" name="business_name" id="business_name" placeholder="{{ __('Enter your business name.') }}" value="{{ $merchant->business_name }}" required data-value-missing="{{ __('This field is required.') }}">
                            @error('business_name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Site URL -->
                <div class="row">
                    <div class="col-12">
                        <div class="label-top">
                            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-32 r-mt-amount r-mt-6">{{ __('Site URL') }} <span class="text-warning">*</span></label>
                            <input type="text" class="form-control input-form-control apply-bg" name="site_url" id="site_url" placeholder="{{ __('https://example.com') }}" value="{{ $merchant->site_url }}" required data-value-missing="{{ __('This field is required.') }}">
                            @error('site_url')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <p class="mb-0 text-gray-100 dark-B87 gilroy-regular f-12 mt-2"><em>* {{ __('Make sure to add http://') }}</em></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <!-- Currency -->
                            <div class="col-6">
                                <div class="mt-20 param-ref">
                                    <label class="gilroy-medium text-gray-100 mb-2 f-15">{{ __('Currency') }}<span class="text-warning">*</span></label>
                                    <select class="select2" data-minimum-results-for-search="Infinity" name="currency_id" id="currency">
                                        @foreach($activeCurrencies as $activeCurrency)
                                            @if (!empty($merchant->currency_id))
                                                <option value="{{ $activeCurrency->id }}" {{ $merchant->currency_id == $activeCurrency->id ? 'selected="selected"' : '' }}>{{ $activeCurrency->code }}</option>
                                            @else
                                                <option value="{{ $activeCurrency->id }}" {{ $defaultWallet->currency_id == $activeCurrency->id ? 'selected="selected"' : '' }}>{{ $activeCurrency->code }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span class="d-none error" id="currencyChangeWarning">{{ __('Changing currency will put the merchant status again in admin moderation.') }}</span>
                                </div>
                            </div>
                            <!-- Merchant Type -->
                            <div class="col-6">
                                <div class="mt-20 param-ref">
                                    <label class="gilroy-medium text-gray-100 mb-2 f-15">{{ __('Merchant Type') }}<span class="text-warning">*</span></label>
                                    <input readonly class="form-control input-form-control apply-bg" value="{{ ucfirst($merchant->type) }}">
                                    @error('site_url')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if (module('WithdrawalApi') && isActive('WithdrawalApi'))
                    <!-- Withdrawal Approval -->
                    <div class="row">
                        <div class="col-12">
                            <div class="mt-20 param-ref swithcer">
                                <label class="gilroy-medium text-gray-100 mb-2 f-15">{{ __('Withdrawal Approval') }}<span class="text-warning">*</span></label>
                                <br>
                                <input type="checkbox" name="withdrawal_approval" id="withdrawal_approval" {{ $merchant->withdrawal_approval == 'Yes' ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Message for administration -->
                <div class="row">
                    <div class="col-12">
                        <div class="label-top">
                            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="floatingTextarea">{{ __('Message for administration') }}</label>
                            <textarea class="form-control l-s0 input-form-control h-100p" name="note" id="note" placeholder="{{ __('Enter your message here.') }}" required data-value-missing="{{ __('This field is required.') }}">{{ $merchant->note }}</textarea>
                            @error('note')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div> 
                <!-- Business Logo -->
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3 mt-24 attach-file label-top">
                            <label for="formFileMultiple" class="form-label text-gray-100 gilroy-medium">{{ __('Business Logo') }}</label>
                            <input class="form-control upload-filed" type="file" name="logo" id="logo">
                            @error('logo')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="logo-update d-flex gap-20">
                    <div class="logo-update-box mt-2">
                        <img src="{{ image($merchant->logo, 'merchant') }}" id="merchantLogoPreviewEdit">
                    </div>
                    <div class="logo-description d-flex flex-column justify-content-center mt-7">
                        <p class="mb-0 f-12 leading-15 gilroy-regular text-gray-100">{{ __('Recommended size') }}: <strong class="text-dark">{{ __('100px * 100px') }}</strong></p>
                        <p class="mb-0 f-12 leading-15 gilroy-regular text-gray-100 mt-10">{{ __('Supported format') }}:<span class="text-dark">{{ __('jpeg, png, bmp, gif or svg') }}</span></p>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-lg btn-primary mt-4" id="merchantUpdateSubmitBtn">
                        <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                            <span class="visually-hidden"></span>
                        </div>
                        <span id="merchantUpdateSubmitBtnText">{{ __('Update Merchant') }}</span>
                        {!! svgIcons('right_angle') !!}
                    </button>
                </div> 
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('public/dist/plugins/html5-validation-1.0.0/validation.min.js') }}"></script>
    <script type="text/javascript">
        'use strict';
        var csrfToken = $('[name="_token"]').val();
        var merchantDefaultLogo = "{{ asset('public/uploads/userPic/default-image.png') }}";
        var merchantLogo = "{{ !empty($merchant->logo) ? $merchant->logo : null }}";
        var currentStatus = "{{ $merchant->status }}";
        var currencyCurrencyId = "{{ $merchant->currency_id }}";
        var submitButtonText = "{{ __('Processing...') }}";
    </script>

    <script src="{{ asset('public/user/customs/js/merchant.min.js') }}"></script>
@endpush