"use strict";

function restrictNumberToPrefdecimalOnInput(e)
{
    let type = 'crypto';
    restrictNumberToPrefdecimal(e, type);
}

function formatNumberToPrefDecimal(num = 0)
{
    let decimalFormat = decimalPreferrence;
    num = ((Math.abs(num)).toFixed(decimalFormat))
    return num;
}


if ($('.main-content').find('#crypto_exchange_user').length) {

    $(window).on('load', function()
    {
        beforeLoad();
        $(".spinner").addClass('fa fa-spinner fa-spin displaynone');
        $('#rp_text').text(nextText);
        let previousUrl = localStorage.getItem("previousUrl");
        let confirmationUrl = SITE_URL+'/crypto-exchange/confirm';
        if (confirmationUrl == previousUrl) {
          var exchangeType = localStorage.getItem("exchangeType");
          var sendAmount = localStorage.getItem("defaultAmnt");
          $("#send_amount").val(sendAmount)
          $('.'+exchangeType).trigger('click');
          localStorage.removeItem("previousUrl");
        }
        let fromCurrencyId = $("#from_currency").val();
        let toCurrencyId = $("#to_currency").val();
        var sendAmount = $("#send_amount").val();
        if (fromCurrencyId && toCurrencyId) {
            getDirectionTabAmount(fromCurrencyId, toCurrencyId, sendAmount);
        }
        
    });

    $(document).on('change', "#from_currency", function () {
        beforeLoad();
        let fromCurrencyId = $("#from_currency").val();
        let type = $("#from_type").val();
        if (fromCurrencyId && type) {
            getCurrenciesExceptFromCurrencyType(fromCurrencyId, type);
        }
    });

    $(document).on('change', "#to_currency", function () {
        beforeLoad();
        let fromCurrencyId = $("#from_currency").val();
        let toCurrencyId = $("#to_currency").val();
        let sendAmount = $("#send_amount").val();
        if (fromCurrencyId && toCurrencyId && sendAmount) {
            getDirectionAmount(fromCurrencyId, toCurrencyId, sendAmount);
        }
    });

    $(document).on('keyup', '#send_amount', $.debounce(700, function() {
        beforeLoad();
        let fromCurrencyId = $("#from_currency").val();
        let toCurrencyId = $("#to_currency").val();
        let sendAmount = $("#send_amount").val();
        if (fromCurrencyId && toCurrencyId && sendAmount) {
            getDirectionAmount(fromCurrencyId, toCurrencyId, sendAmount);
        }
    }));

    $(document).on('keyup', '#get_amount', $.debounce(700, function() {
        let fromCurrencyId = $("#from_currency").val();
        let toCurrencyId = $("#to_currency").val();
        let getAmount = $("#get_amount").val();
        beforeLoad(getAmount);
        if (fromCurrencyId && toCurrencyId && getAmount) {
            getDirectionAmount(fromCurrencyId, toCurrencyId, null, getAmount);
        }
    }));

    function beforeLoad( getAmount = null ) {
        $('.rate').text('');
        $('.exchange_fee').text('');
        $('.direction_error').text('');
        $("#crypto_buy_sell_button").attr("disabled", true);
        ( getAmount ) ? $("#send_amount").val('-') : $("#get_amount").val('-');
    }

    function getDirectionTabAmount(fromCurrencyId, toCurrencyId, sendAmount = null, getAmount = null)
    {
        let token = $("#token").val();
        
        if (fromCurrencyId && toCurrencyId) {
            $.ajax({
                method: "GET",
                url: directionAmountUrl,
                dataType: "json",
                cache: false,
                data: {
                    "_token": token,
                    'from_currency_id': fromCurrencyId,
                    'to_currency_id': toCurrencyId,
                    'send_amount': sendAmount,
                    'get_amount': getAmount,
                }
            })
            .done(function (response)
            {
                $('.send_amount_error').text('');
                $('#send_amount').val(response.success.send_amount);
                $('#get_amount').val(response.success.get_amount);
                $('.rate').text(response.success.exchange_rate);
                $('.exchange_fee').text(response.success.exchange_fee);
                if (response.success.status == 200) {
                     $('#crypto_buy_sell_button').attr('disabled', false);
                } else {
                     $('.send_amount_error').addClass('error').text(response.success.message);
                     $('#crypto_buy_sell_button').attr('disabled', true);
                }
                $("input").prop('disabled', false);
            });
        } else {
            $('#crypto_buy_sell_button').attr('disabled', true);
            $('.direction_error').addClass('error').text(directionNotAvaillable);
            $('#get_amount').val(0);
        }
    }

    function getCurrenciesExceptFromCurrencyType(fromCurrencyId, type)
    {
        let token = $("#token").val();
        if (fromCurrencyId && type) {
            $.ajax({
                method: "GET",
                url: directionListUrl,
                dataType: "json",
                cache: false,
                data: {
                    "_token": token,
                    'type': type,
                    'from_currency_id': fromCurrencyId,
                }
            })
            .done(function (response)
            {
                let toOptions = '';
                $.each(response.directionCurrencies, function(key, value)
                {
                    toOptions += `<option value="${value.id}" >${value.code}</option>`
                });
                $('#to_currency').html(toOptions);
                let fromCurrencyId = $("#from_currency").val();
                let toCurrencyId = $("#to_currency").val();
                let sendAmount = $("#send_amount").val();
                if (fromCurrencyId && toCurrencyId && sendAmount) {
                    getDirectionAmount(fromCurrencyId, toCurrencyId, sendAmount);
                }
            });
        }
    }

    function getDirectionAmount(fromCurrencyId, toCurrencyId, sendAmount = null, getAmount = null)
    {
        let token = $("#token").val();
        if (fromCurrencyId && toCurrencyId ) {
            $.ajax({
                method: "GET",
                url: directionAmountUrl,
                dataType: "json",
                cache: false,
                data: {
                    "_token": token,
                    'from_currency_id': fromCurrencyId,
                    'to_currency_id': toCurrencyId,
                    'send_amount': sendAmount,
                    'get_amount': getAmount,
                },
            })
            .done(function (response)
            {
                $('.send_amount_error').text('');
                $('#send_amount').val(response.success.send_amount);
                $('#get_amount').val(response.success.get_amount);
                $('.rate').text(response.success.exchange_rate);
                $('.exchange_fee').text(response.success.exchange_fee);
                $('.flash-container').hide();
                if (response.success.status == 200) {
                     $('#crypto_buy_sell_button').attr('disabled', false);
                } else {
                     $('.send_amount_error').addClass('error').text(response.success.message);
                     $('#crypto_buy_sell_button').attr('disabled', true);
                }
                $("input").prop('disabled', false);
            });
        }
    }

    $(document).on('click', ".crypto", function ()
    {
        beforeLoad();
        let type = $(this).attr('data-type');
        $('.crypto').removeClass('active');
        $(this).addClass('active');
        $('.send_amount_error').text('');
        $("#from_type").val(type);
        getCurrenciesByType(type);
    });

    function getCurrenciesByType(directionType)
    {
      let token = $("#token").val();
      if (directionType) {
          $.ajax({
              method: "GET",
              url: directionTypeUrl,
              dataType: "json",
              cache: false,
              data: {
                  "_token": token,
                  'direction_type': directionType,
              }
          })
          .done(function (response)
          {
              let fromOptions = '';
              $.each(response.fromCurrencies, function(key, value)
              {
                  fromOptions += `<option value="${value.from_currency.id}" >${value.from_currency.code}</option>`;
              });
              $('#from_currency').html(fromOptions);
              
              let toOptions = '';
              $.each(response.toCurrencies, function(key, value)
              {
                  toOptions += `<option value="${value.id}" >${value.code}</option>`;
              });
              $('#to_currency').html(toOptions);
              
              let text = (response.status == '401') ? directionNotAvaillable : '' ;
              $('.direction_error').addClass('error').text(text);
              if (localStorage.getItem("from_currency") && localStorage.getItem("to_currency")) {
                 $("#from_currency").val(localStorage.getItem("from_currency"));
                 $("#to_currency").val(localStorage.getItem("to_currency"));
                 localStorage.removeItem("from_currency");
                 localStorage.removeItem("to_currency");
              }
              let fromCurrencyId = $("#from_currency").val();
              let toCurrencyId = $("#to_currency").val();
              let sendAmount = $("#send_amount").val();
              if (fromCurrencyId && toCurrencyId && sendAmount) {
                getDirectionTabAmount(fromCurrencyId, toCurrencyId, sendAmount);
              }
          });
      }
    }

    $(document).on('submit', '#crypto-send-form', function() {
        $("#crypto_buy_sell_button").attr("disabled", true);
        $(".spinner").removeClass('displaynone');
        var pretext = $("#rp_text").text();
        $('#rp_text').text(exchangeText);
        setTimeout(function(){
            $('#rp_text').text(pretext);
            $("#crypto_buy_sell_button").removeAttr("disabled");
            $(".spinner").addClass('displaynone');
        }, 1000);
    });
}

if ($('.main-content').find('#crypto_exchange_details').length) {

    function exchangeBack() {
        localStorage.setItem("previousUrl", document.URL);
        localStorage.setItem("exchangeType", exchangeTypeValue);
        localStorage.setItem("from_currency", fromCurrencyValue);
        localStorage.setItem("to_currency", toCurrencyValue);
        localStorage.setItem("defaultAmnt", defaultAmnt);
        history.back();
    }

    $(window).on('load', function()
    {
        paymentOption();
    });

    $(document).on('change', '#pay_with', function() {
        paymentOption();
    });

    $(document).on('change', '#file', function() {
        var ext = $('#file').val().replace(/^.*\./, '');
        let fileInput = document.getElementById('file'); 
        const fileTypes = extensions;
        if (!fileTypes.includes(ext)) {
            fileInput.value = '';
            $('.file-error').addClass('error').text(invalidFileText);
            $('#fileSpan').fadeIn('slow').delay(2000).fadeOut('slow');
            return false;
        } else {
            $('.file-error').removeClass('error').text('');
            return true;
        }
    })

    function paymentOption()
    {
        if ($('#pay_with').val() == 'others') {
            $("#payment_details").show();
            $('.payment_details').attr("disabled", false);
        } else {
            $("#payment_details").hide();
            $('.payment_details').attr("disabled", true);   
        }
        walletCheck();
    }

    $(document).on('change', '#receive_with', function() {
        receiveOption();
    });

    function receiveOption()
    {
        if ( $('#receive_with').val() == 'address') {
            $("#crypto_address_section").css('display','block');
            $('.crypto_address').attr("disabled", false); 
        } else {
            $("#crypto_address_section").css('display','none');
            $('.crypto_address').attr("disabled", true);   
        }
    }

    function walletCheck()
    {
        let token = $("#token").val();
        if ($('#pay_with').val() == 'others') {
            $('.wallet-error').removeClass('error').text('');
            $('#exchange-confirm-submit-btn').attr('disabled', false);
        } else {
            $.ajax({
                method: "GET",
                url: walletCheckUrl,
                dataType: "json",
                cache: false,
                data: {
                    "_token": token,
                }
            })
            .done(function (response)
            {
                if (response.success.status == 200) {
                    $('.wallet-error').removeClass('text-danger').text('');
                    $('#exchange-confirm-submit-btn').attr('disabled', false);
                } else {
                    $('.wallet-error').addClass('text-danger').text(response.success.message);
                    $('#exchange-confirm-submit-btn').attr('disabled', true);
                }
            });
        }
    }

    $(document).on('submit', '#crypto_buy_sell_from', function() {

        $("#exchange-confirm-submit-btn").attr("disabled", true);
        localStorage.removeItem("previousUrl");
        localStorage.removeItem("from_currency");
        localStorage.removeItem("to_currency");
        $(".fa-spin").removeClass('displaynone');
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.exchange-confirm-back-btn', function(e) {
        e.preventDefault();
        exchangeBack();
    });

    $('#copyButton, #merchantAddress').on('click', function () {
		let address = $('#merchantAddress').text();
        let elem = document.createElement("textarea");
        document.body.appendChild(elem);
        elem.value = address;
        elem.select();
        document.execCommand("copy");
        document.body.removeChild(elem);
        $('#copyButton').addClass('d-none');
        $('.copyText').removeClass('d-none');
	});
}