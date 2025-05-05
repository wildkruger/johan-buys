<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Paylink payment') }}</title>
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('resources/views/Themes/modern/assets/public/public payment/css/payment.css') }}">

    <style>
        .error {
            color: red;
        }

        .rounded-full {
            border-radius: 50%
        }
    </style>
</head>

<body>
    <div class="main-content py-5 px-md-5">
        <div class="container bg-white">
            <!-- header -->
            <header class="row header-section">
                <div class="col-4 merchant-logo">
                    @if (!empty($user->picture) && file_exists(public_path('user_dashboard/profile/' . $user->picture)))
                        <img src="{{ asset('public/user_dashboard/profile/' . $user->picture) }}"
                            class="pay-merchant-logo" alt="Merchant Logo">
                    @else
                        <img src="{{ asset('public/user_dashboard/images/avatar.jpg') }}"
                            class="img-fluid pay-merchant-logo rounded-full" alt="Merchant Logo">
                    @endif
                </div>
                <div class="col-8 d-flex justify-content-end align-items-center">

                    @if (!empty(settings('logo')) && file_exists(public_path('images/logos/' . settings('logo'))))
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('public/images/logos/' . settings('logo')) }}" alt="logo"
                                class="pay-paygenius-logo">
                        </a>
                    @else
                        <a href="{{ url('/') }}">
                            <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}"
                                class="img-responsive pay-paygenius-logo" width="80" height="50" alt="logo">
                        </a>
                    @endif

                    <div class="pay-language gilroy-medium">
                        <label for="language" class="pay-prompt">{{ __('Select language') }} </label> <br>
                        <select class="form-control select2" data-minimum-results-for-search="Infinity" id="language">
                            @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                                <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }}
                                    value="{{ $lang->short_name }}"> {{ $lang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </header>
            <main class="main" role="main">

                @if (session('error'))
                    <div class="alert alert-danger mt-2">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Paylink section -->
                <section id="pay-context" class="row">
                    <div class="col-md-8 gilroy-medium">
                        <h2 class="color-info-200 gilroy-medium">{{ __('Enter your details below') }}: </h2>
                    </div>
                </section>
                <form method="post" class="gilroy-medium" id="paylink-form" action="{{ route('paylink.store') }}">
                    @csrf

                    <input type="hidden" name="transaction_type" value="{{ Deposit }}" id="transactionType">
                    <input type="hidden" name="currency_id" id="currency_id" value="{{ $currency->id }}">
                    <section id="pay-payments" class="row pay-details">
                        <div class="col-sm-2 pay-section-icon">
                            <svg class="pay-img-card" xmlns="http://www.w3.org/2000/svg" width="800px" height="800px"
                                viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M20 15C20.5523 15 21 14.5523 21 14C21 13.4477 20.5523 13 20 13C19.4477 13 19 13.4477 19 14C19 14.5523 19.4477 15 20 15Z"
                                    fill="#635BFF" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.775 0.985398C18.4919 0.460783 20.2821 1.55148 20.6033 3.3178L20.9362 5.14896C22.1346 5.54225 23 6.67006 23 8V10.7639C23.6137 11.3132 24 12.1115 24 13V15C24 15.8885 23.6137 16.6868 23 17.2361V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V8C1 6.51309 2.08174 5.27884 3.50118 5.04128L16.775 0.985398ZM21 16C21.5523 16 22 15.5523 22 15V13C22 12.4477 21.5523 12 21 12H18C17.4477 12 17 12.4477 17 13V15C17 15.5523 17.4477 16 18 16H21ZM21 18V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V8C3 7.44772 3.44772 7 4 7H20C20.55 7 20.9962 7.44396 21 7.99303L21 10H18C16.3431 10 15 11.3431 15 13V15C15 16.6569 16.3431 18 18 18H21ZM18.6954 3.60705L18.9412 5H10L17.4232 2.82301C17.9965 2.65104 18.5914 3.01769 18.6954 3.60705Z"
                                    fill="#635BFF" />
                            </svg>
                        </div>

                        <div class="col-sm-10">
                            <h3>{{ __('Payment Details') }}: </h3>
                            <div class="form-group">
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-addon h-38p">ZAR</span>
                                    <div class="w-100">
                                        <input type="number" class="form-control" name="amount" id="amount" placeholder="{{ __('Amount') }}">
                                        @error('amount')
                                            <span class="error amount-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- <div id="small_container_zar">
                                <small class="pay-note">{{ __('Fee') }}: {{ $currency->code }}
                                    <span>{{ formatNumber($feesLimit->charge_percentage, $currency->id) }} %</span> +
                                    <span>{{ formatNumber($feesLimit->charge_fixed, $currency->id) }}</span></small>
                            </div> --}}
                        </div>
                    </section>
                    <section id="pay-user-info" class="row pay-details">
                        <div class="col-sm-2 pay-section-icon pay-section-icon">
                            <svg class="pay-img-card" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px"
                                viewBox="0 0 20 20" version="1.1">

                                <title>profile_round [#1342]</title>
                                <desc>Created with Sketch.</desc>
                                <defs>

                                </defs>
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                    fill-rule="evenodd">
                                    <g id="Dribbble-Light-Preview" transform="translate(-140.000000, -2159.000000)"
                                        fill="#635BFF">
                                        <g id="icons" transform="translate(56.000000, 160.000000)">
                                            <path
                                                d="M100.562548,2016.99998 L87.4381713,2016.99998 C86.7317804,2016.99998 86.2101535,2016.30298 86.4765813,2015.66198 C87.7127655,2012.69798 90.6169306,2010.99998 93.9998492,2010.99998 C97.3837885,2010.99998 100.287954,2012.69798 101.524138,2015.66198 C101.790566,2016.30298 101.268939,2016.99998 100.562548,2016.99998 M89.9166645,2004.99998 C89.9166645,2002.79398 91.7489936,2000.99998 93.9998492,2000.99998 C96.2517256,2000.99998 98.0830339,2002.79398 98.0830339,2004.99998 C98.0830339,2007.20598 96.2517256,2008.99998 93.9998492,2008.99998 C91.7489936,2008.99998 89.9166645,2007.20598 89.9166645,2004.99998 M103.955674,2016.63598 C103.213556,2013.27698 100.892265,2010.79798 97.837022,2009.67298 C99.4560048,2008.39598 100.400241,2006.33098 100.053171,2004.06998 C99.6509769,2001.44698 97.4235996,1999.34798 94.7348224,1999.04198 C91.0232075,1998.61898 87.8750721,2001.44898 87.8750721,2004.99998 C87.8750721,2006.88998 88.7692896,2008.57398 90.1636971,2009.67298 C87.1074334,2010.79798 84.7871636,2013.27698 84.044024,2016.63598 C83.7745338,2017.85698 84.7789973,2018.99998 86.0539717,2018.99998 L101.945727,2018.99998 C103.221722,2018.99998 104.226185,2017.85698 103.955674,2016.63598"
                                                id="profile_round-[#1342]">

                                            </path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="col-sm-10">
                            <h3>{{ __('Details') }}: </h3>
                            <div>
                                <div class="form-group mb-4">
                                    <input type="text" class="form-control" placeholder="{{ __('First Name') }}" name="first_name">
                                    @error('first_name')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-4">
                                    <input type="text" class="form-control" placeholder="{{ __('Last Name') }}" name="last_name">
                                    @error('last_name')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-4">
                                    <input type="email" class="form-control" placeholder="{{ __('Email') }}" name="email">
                                    @error('email')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="pay-billing-info" class="row pay-details">
                        <div id="payment_billing_icon_container" class="col-sm-2 pay-section-icon">

                            <svg class="pay-img-card" xmlns="http://www.w3.org/2000/svg" width="800px"
                                height="800px" viewBox="0 0 24 24" fill="none">
                                <path d="M2 8.50488H22" stroke="#635BFF" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6 16.5049H8" stroke="#635BFF" stroke-width="1.5" stroke-miterlimit="10"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.5 16.5049H14.5" stroke="#635BFF" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M6.44 3.50488H17.55C21.11 3.50488 22 4.38488 22 7.89488V16.1049C22 19.6149 21.11 20.4949 17.56 20.4949H6.44C2.89 20.5049 2 19.6249 2 16.1149V7.89488C2 4.38488 2.89 3.50488 6.44 3.50488Z"
                                    stroke="#635BFF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="col-sm-10">
                            <div>
                                <h3 id="payment_method_string">{{ __('Payment Method') }}: </h3>
                                <select class="form-control select2" name="payment_method_id" id="paymentMethods"
                                    data-minimum-results-for-search="Infinity"></select>

                                <button type="submit" class="btn btn-primary px-4 py-2 mt-4">{{ __('Pay Now') }}</a>
                            </div>
                        </div>
                    </section>
                </form>
            </main>
        </div>
    </div>
    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/jquery3.6.min.js') }}"></script>
    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/bootstrap5.min.js') }}"></script>
    <script src="{{ asset('resources/views/Themes/modern/assets/public/public payment/js/select2.min.js') }}"></script>

    <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/jquery.validate.min.js') }}"></script>
    <script>
        $("#paylink-form").validate({
            rules: {
                amount: {
                    required: true,
                    number: true
                },
                currency_id: {
                    required: true
                },
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                }
            }
        });

        $(".select2").select2({});

        var getPaymentMethodsAjaxUrl = "{{ url('/get-payment-methods') }}";
        var transactionTypeId = "{{ Deposit }}";

        $(document).ready(function() {
            // Define the function to get payment methods
            function fetchPaymentMethods(transactionTypeId, currencyId) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: getPaymentMethodsAjaxUrl,
                        type: 'POST',
                        data: {
                            transaction_type_id: transactionTypeId,
                            currency_id: currencyId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr, status, error) {
                            reject(xhr.responseText);
                        }
                    });
                });
            }

            // Function to update payment methods dropdown
            function updatePaymentMethodsDropdown(transactionTypeId, currencyId) {
                fetchPaymentMethods(transactionTypeId, currencyId)
                    .then(function(paymentMethods) {
                        $('#paymentMethods').empty(); // Clear the existing options
                        $.each(paymentMethods, function(key, value) {
                            $('#paymentMethods').append($('<option>', {
                                value: value.id,
                                text: value.name
                            }));
                        });
                    })
                    .catch(function(error) {
                        console.error('Error fetching payment methods:', error);
                    });
            }

            // Event listener for currency change
            $(window).on('load', function() {
                var transactionTypeId = $('#transactionType').val();
                var currencyId = $('#currency_id').val();
                updatePaymentMethodsDropdown(transactionTypeId, currencyId);
            });

            // Trigger the event on page load
            // $('#currency').trigger('change');
        });

        $('#language').on('change', function(e) {
            e.preventDefault();
            lang = $(this).val();
            url = '{{ url('change-lang') }}';
            $.ajax({
                type: 'get',
                url: url,
                data: {
                    lang: lang
                },
                success: function(msg) {
                    if (msg == 1) {
                        location.reload();
                    }
                }
            });
        });
    </script>
</body>

</html>
