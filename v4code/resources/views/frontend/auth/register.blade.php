@extends('frontend.layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/intl-tel-input-17.0.19/css/intlTelInput.min.css') }}">
@endsection

@section('content')

<!-- Sign-up section start -->
<div class="container-fluid container-layout px-0" id="personal-info-store">
    <div class="main-auth-div">
        <div class="row">
            <div class="col-md-6 col-xl-5 hide-small-device">
                <div class="bg-pattern">
                    <div class="bg-content">
                        <div class="d-flex justify-content-start"> 
                            <div class="logo-div">
                                <a href="{{ url('/') }}">
                                    <img src="{{ image(settings('logo'), 'logo') }}" alt="{{ __('Brand Logo') }}">
                                </a>
                            </div>
                        </div> 
                        <div class="transaction-block">
                            <div class="transaction-text">
                                <h3 class="mb-6p">{{ __('Hassle free money') }}</h3>
                                <h1 class="mb-2p">{{ __('Transactions') }}</h1>
                                <h2>{{ __('Right at you fingertips') }}</h2>
                            </div>
                        </div>
                        <div class="transaction-image">
                            <div class="static-image">
                                <img class="img img-fluid" src="{{ asset('public/frontend/templates/images/login/signup-static-img.svg') }}">
                            </div>
                        </div>
                    </div>
                </div>                  
            </div>
            <div class="col-md-6 col-12 col-xl-7">
                @include('frontend.layouts.common.alert')
                @error('email')
                    <span class="error"> {{ $message }} </span>
                @enderror
                <div class="auth-section d-flex align-items-center create-account">
                      <div class="auth-module">
                      <div class="d-flex align-items-center back-direction mb-2 back-signin">
                                <a href="{{ url('/') }}" class="d-inline-flex align-items-center back-btn">
                                  {!! svgIcons('left_angle') !!}
                                  <span class="ms-1">{{ __('Back') }}</span>
                                </a>
                            </div>
                        <form action="{{ route('register.personal.info') }}" class="form-horizontal" id="personal-info-form" method="POST">
                            @csrf
                            <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                            <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                            <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">

                            <div class="auth-module-header">
                                <p class="mb-0 text-start auth-title">{{ __('Create Account') }}</p>
                                <p class="mb-0 text-start auth-text mt-12"> {{ __('Already have an account?') }} <a href="{{ url('login') }}">{{ __('Sign in here') }}</a></p>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('First Name') }} <span class="star">*</span></label>
                                        <input type="text" class="form-control input-form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('First name'), 'y' => __('30')]) }}"> 
                                        @error('first_name')
                                            <span class="error"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Last Name') }} <span class="star">*</span></label>
                                        <input type="text" class="form-control input-form-control" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                        required data-value-missing="{{ __('This field is required.') }}"
                                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Last name'), 'y' => __('30')]) }}"> 
                                        @error('last_name')
                                            <span class="error"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Email') }} <span class="star">*</span></label>
                                        <input type="email" class="form-control input-form-control" id="email" name="email" value="{{ old('email')}}" 
                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" 
                                        data-type-mismatch="{{ __('Enter a valid :x.', [ 'x' => strtolower(__('email'))]) }}">
                                        @error('email')
                                            <span class="error"> {{ $message }} </span>
                                        @enderror
                                        <span id="email_error"></span>
                                        <span id="email_ok" class="text-success"></span>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Phone') }}</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                        <span id="duplicate-phone-error"></span>
                                        <span id="tel-error"></span>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ __('Password') }} <span class="star">*</span></label>
                                        <div id="password-div" class="position-relative">
                                            <input type="password" class="form-control input-form-control" name="password" id="password" 
                                            required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                            minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                            <span class="eye-icon cursor-pointer" id="eye-icon-show">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.71998 1.71967C2.01287 1.42678 2.48775 1.42678 2.78064 1.71967L5.50969 4.44872C5.55341 4.48345 5.59378 4.52354 5.62977 4.5688L13.423 12.3621C13.4739 12.4011 13.5204 12.4471 13.561 12.5L16.2806 15.2197C16.5735 15.5126 16.5735 15.9874 16.2806 16.2803C15.9877 16.5732 15.5129 16.5732 15.22 16.2803L12.8547 13.915C11.771 14.5491 10.479 15 9.00031 15C6.85406 15 5.10432 14.0515 3.80787 12.9694C2.51318 11.8889 1.62553 10.6393 1.18098 9.93536C1.1751 9.92606 1.16907 9.91657 1.16291 9.90687C1.07468 9.768 0.960135 9.5877 0.902237 9.33506C0.85549 9.13108 0.855506 8.86871 0.902276 8.66474C0.960212 8.41207 1.07508 8.23131 1.16354 8.09212C1.16975 8.08235 1.17583 8.07278 1.18175 8.06341C1.63353 7.34824 2.55099 6.05644 3.89682 4.95717L1.71998 2.78033C1.42709 2.48744 1.42709 2.01256 1.71998 1.71967ZM4.96371 6.02406C3.73433 6.99464 2.87554 8.19074 2.44991 8.86452C2.42329 8.90666 2.40463 8.93624 2.38903 8.96192C2.37862 8.97905 2.37176 8.99088 2.36719 8.99912C2.36719 8.99941 2.36719 8.99969 2.36719 8.99998C2.36719 9.00029 2.36719 9.00059 2.36719 9.00089C2.3717 9.00902 2.37845 9.02067 2.38868 9.0375C2.40417 9.06302 2.42272 9.09243 2.44923 9.1344C2.84872 9.76697 3.6393 10.8749 4.76902 11.8178C5.89697 12.7592 7.31781 13.5 9.00031 13.5C10.015 13.5 10.9334 13.2311 11.7506 12.8109L10.5242 11.5845C10.0776 11.8483 9.55635 12 9.00031 12C7.34346 12 6.00031 10.6569 6.00031 9C6.00031 8.44396 6.15203 7.92272 6.41579 7.47614L4.96371 6.02406ZM7.551 8.61135C7.51791 8.73524 7.50031 8.86549 7.50031 9C7.50031 9.82843 8.17188 10.5 9.00031 10.5C9.13482 10.5 9.26507 10.4824 9.38896 10.4493L7.551 8.61135ZM9.00031 4.5C8.71392 4.5 8.43614 4.52137 8.1669 4.56117C7.75714 4.62176 7.37586 4.33869 7.31527 3.92893C7.25469 3.51917 7.53776 3.13789 7.94751 3.0773C8.28789 3.02698 8.63899 3 9.00031 3C11.1466 3 12.8963 3.94854 14.1928 5.03057C15.4874 6.11113 16.3751 7.36072 16.8196 8.06464C16.8255 8.07394 16.8316 8.08343 16.8377 8.09312C16.9259 8.23201 17.0405 8.41232 17.0984 8.66498C17.1451 8.86897 17.1451 9.13136 17.0983 9.33533C17.0404 9.58804 16.9253 9.76906 16.8367 9.90844C16.8305 9.91825 16.8244 9.92786 16.8184 9.93727C16.5797 10.3152 16.2174 10.8436 15.7374 11.4168C15.4715 11.7344 14.9985 11.7763 14.6809 11.5104C14.3633 11.2445 14.3214 10.7714 14.5873 10.4539C15.0158 9.94209 15.3393 9.47006 15.5503 9.13608C15.577 9.09384 15.5957 9.06416 15.6114 9.0384C15.6219 9.02109 15.6288 9.00916 15.6334 9.00086C15.6334 9.00059 15.6334 9.00031 15.6334 9.00003C15.6334 8.99972 15.6334 8.99942 15.6334 8.99911C15.6289 8.99099 15.6222 8.97934 15.6119 8.9625C15.5965 8.93698 15.5779 8.90757 15.5514 8.8656C15.1519 8.23303 14.3613 7.12506 13.2316 6.18218C12.1037 5.24078 10.6828 4.5 9.00031 4.5Z" fill="#6A6B87"></path>
                                                </svg>
                                            </span>
                                            <span class="eye-icon-hide d-none cursor-pointer" id="eye-icon-hide">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.76901 6.18218C3.63929 7.12505 2.84871 8.23303 2.44922 8.8656C2.42271 8.90757 2.40417 8.93697 2.38868 8.96248C2.37845 8.97932 2.3717 8.99097 2.36719 8.99909C2.36719 8.99939 2.36719 8.9997 2.36719 9C2.36719 9.0003 2.36719 9.00061 2.36719 9.00091C2.3717 9.00903 2.37845 9.02068 2.38868 9.03752C2.40417 9.06303 2.42271 9.09243 2.44922 9.1344C2.84871 9.76697 3.63929 10.8749 4.76901 11.8178C5.89696 12.7592 7.3178 13.5 9.0003 13.5C10.6828 13.5 12.1036 12.7592 13.2316 11.8178C14.3613 10.8749 15.1519 9.76697 15.5514 9.1344C15.5779 9.09243 15.5964 9.06303 15.6119 9.03751C15.6222 9.02068 15.6289 9.00903 15.6334 9.00091C15.6334 9.00061 15.6334 9.0003 15.6334 9C15.6334 8.9997 15.6334 8.99939 15.6334 8.99909C15.6289 8.99097 15.6222 8.97932 15.6119 8.96249C15.5964 8.93697 15.5779 8.90757 15.5514 8.8656C15.1519 8.23303 14.3613 7.12505 13.2316 6.18218C12.1036 5.24077 10.6828 4.5 9.0003 4.5C7.3178 4.5 5.89696 5.24078 4.76901 6.18218ZM3.80786 5.03057C5.10431 3.94854 6.85405 3 9.0003 3C11.1466 3 12.8963 3.94854 14.1927 5.03057C15.4874 6.11113 16.3751 7.36071 16.8196 8.06464C16.8255 8.07394 16.8315 8.08343 16.8377 8.09313C16.9259 8.23198 17.0405 8.41227 17.0984 8.66488C17.1451 8.86884 17.1451 9.13116 17.0984 9.33512C17.0405 9.58773 16.9259 9.76802 16.8377 9.90687C16.8315 9.91657 16.8255 9.92606 16.8196 9.93536C16.3751 10.6393 15.4874 11.8889 14.1927 12.9694C12.8963 14.0515 11.1466 15 9.0003 15C6.85405 15 5.10431 14.0515 3.80786 12.9694C2.51318 11.8889 1.62553 10.6393 1.18097 9.93536C1.17509 9.92606 1.16906 9.91657 1.1629 9.90688C1.07469 9.76802 0.960152 9.58774 0.902251 9.33512C0.8555 9.13116 0.8555 8.86884 0.902251 8.66488C0.960152 8.41226 1.07469 8.23198 1.1629 8.09312C1.16906 8.08343 1.17509 8.07394 1.18097 8.06464C1.62553 7.36071 2.51318 6.11113 3.80786 5.03057ZM9.0003 7.5C8.17188 7.5 7.5003 8.17157 7.5003 9C7.5003 9.82843 8.17188 10.5 9.0003 10.5C9.82873 10.5 10.5003 9.82843 10.5003 9C10.5003 8.17157 9.82873 7.5 9.0003 7.5ZM6.0003 9C6.0003 7.34315 7.34345 6 9.0003 6C10.6572 6 12.0003 7.34315 12.0003 9C12.0003 10.6569 10.6572 12 9.0003 12C7.34345 12 6.0003 10.6569 6.0003 9Z" fill="#6A6B87"></path>
                                                </svg>
                                            </span>

                                            @error('password')
                                                <span class="error"> {{ $message }} </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group mb-3" id="confirm-password-div">
                                        <label class="form-label">{{ __('Confirm Password') }} <span class="star">*</span></label>
                                        <input type="password" class="form-control input-form-control" id="password_confirmation" name="password_confirmation" 
                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                        minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                    </div>
                                    <div class="d-grid mb-3 mb-3p">
                                        <button class="btn btn-lg btn-primary" type="submit" id="registrationSubmitBtn">
                                            <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                                                <span class="visually-hidden"></span>
                                            </div>
                                            <span class="px-1" id="registrationSubmitBtnTxt">{{ __('Continue') }}</span>
                                            <span id="rightAngle">{!! svgIcons('right_angle_md') !!}</span>
                                        </button>
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
<!-- Sign-up section end -->
@endsection

@section('js')
<script src="{{ asset('public/dist/plugins/html5-validation-1.0.0/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/intl-tel-input-17.0.19/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/js/isValidPhoneNumber.min.js') }}" type="text/javascript"></script>

<script>
    'use strict';
    let requiredText = '{{ __("This field is required.") }}';
    let validEmailText = '{{ __("Please enter a valid email address.") }}';
    let samePasswordText = '{{ __("Please enter the same value again.") }}'
    let confirmSamePasswordText = '{{ __("Please enter same value as the password field.") }}';
    let alphabetSpaceText = '{{ __("Please enter only alphabet and spaces.") }}';
    let signingUpText = '{{ __("Continuing...") }}';
    let countryShortCode = '{{ getDefaultCountry() }}';
    let validPhoneNumberText = '{{ __("Please enter a valid international phone number.") }}'
    let utilsJsScript = '{{ asset("public/dist/plugins/intl-tel-input-17.0.19/js/utils.min.js") }}';
</script>
<script src="{{ asset('public/frontend/customs/js/register/register.min.js') }}" type="text/javascript"></script>
@endsection
