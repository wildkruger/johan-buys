@extends('user_dashboard.layouts.app')

@section('content')

    <section class="min-vh-100">

        <div class="my-30">

            <div class="container-fluid">

                <!-- Page title start -->

                <div>

                    <h3 class="page-title">{{ __('Deposit Fund') }}</h3>

                </div>

                <!-- Page title end-->

                <div class="row mt-4">

                    <div class="col-lg-4">

                        <!-- Sub title start -->

                        <div class="mt-5">

                            <h3 class="sub-title">{{ __('Create Deposit') }}</h3>

                            <p class="text-gray-500 text-16">{{ __('Enter your deposit amount, currency and payment method') }}</p>

                        </div>

                        <!-- Sub title end-->

                    </div>



                    <div class="col-lg-8">

                        <div class="row">

                            <div class="col-xl-10">

                                <div class="d-flex w-100 mt-4">

                                    <ol class="breadcrumb w-100">

                                        <li class="breadcrumb-active text-white">{{ __('Create') }}</li>

                                        <li class="breadcrumb-first text-white">{{ __('Confirmation') }}</li>

                                        <li class="active">{{ __('Success') }}</li>

                                    </ol>

                                </div>



                                <div class="bg-secondary mt-5 shadow p-35">

                                    @include('user_dashboard.layouts.common.alert')

                                    <div>

                                        <div class="mb-4">

                                            <p class="sub-title">{{ __('Paygate Information') }}</p>

                                        </div>

                                        <form action="{{ url('deposit/paygate-payment') }}" method="post">

                                            @csrf

                                            <div class="row">

                                                <div class="col-md-12">

                                                    <div class="form-group">

                                                        <label class="text-center" for="email">{{ __('Email') }}</label>

                                                        <input type="email" class="form-control" name="email" id="email" value="{{ auth()->user()->email }}">

                                                    </div>

                                                </div>

                                            </div>



                                            <div class="row m-0 justify-content-between mt-2">

                                                <div>

                                                    <a href="#" class="deposit-confirm-back-btn"><p class="py-2 text-active text-underline deposit-confirm-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i>{{ __('Back') }}</u></p></a>

                                                </div>



                                                <div>

                                                    <button type="submit" class="btn btn-primary px-4 py-2 float-left" style="margin-top:10px;" id="deposit-stripe-submit-btn">

                                                        <i class="spinner fa fa-spinner fa-spin d-none"></i> 

                                                        <span id="deposit-stripe-submit-btn-txt" style="font-weight: bolder;">@lang('message.form.submit')</span>

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

        </div>

    </section>

@endsection

@section('js')

    <script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>

    <script src="{{ theme_asset('public/js/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>

    <script type="text/javascript"></script>

@endsection

