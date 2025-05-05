"use Strict"
$(".select2").select2({});

$(document).ready(function() {
    $('#payment-method').change(function() {
        var selectedValue = $(this).val();
        if (selectedValue === 'stripe') {
            $('#card-div').removeClass('hidden');
        } else {
            $('#card-div').addClass('hidden');
        }
    });
});