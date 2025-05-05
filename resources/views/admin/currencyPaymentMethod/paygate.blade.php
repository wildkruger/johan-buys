<!-- PayGate - Client ID -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="paygate_id">{{ __('PayGate ID') }}</label>
	<div class="col-sm-6">
		<input class="form-control f-14" name="paygate[paygate_id]" type="text" placeholder="{{ __('PayGate ID') }}" value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->paygate_id : '' }}" id="paygate_id">

		@if ($errors->has('paygate[paygate_id]'))
			<span class="help-block">
				<strong>{{ $errors->first('paygate[paygate_id]') }}</strong>
			</span>
		@endif
	</div>
</div>
<div class="clearfix"></div>

<!-- PayGate - Client Secret -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="encryption_key">{{ __('Encryption Key') }}</label>
	<div class="col-sm-6">
		<input class="form-control f-14" name="paygate[encryption_key]" type="text" placeholder="{{ __('Encryption Key') }}" value="{{ isset($currencyPaymentMethod->method_data) ? json_decode($currencyPaymentMethod->method_data)->encryption_key : '' }}" id="encryption_key">
		@if ($errors->has('paygate[encryption_key]'))
			<span class="help-block">
				<strong>{{ $errors->first('paygate[encryption_key]') }}</strong>
			</span>
		@endif
	</div>
</div>

<!-- PayGate - Mode -->
<div class="form-group row">
	<label class="col-sm-3 control-label mt-11 f-14 fw-bold text-md-end" for="paygate[mode]">{{ __('Mode') }}</label>
	<div class="col-sm-6">
		<select class="form-control f-14" name="paygate[mode]" id="paygate_mode">
			<option value="">{{ __('Select Mode') }}</option>
			<option value='sandbox' {{ isset($currencyPaymentMethod->method_data) && (json_decode($currencyPaymentMethod->method_data)->mode) == 'sandbox' ? 'selected':"" }} >{{ __('sandbox') }}</option>
			<option value='live' {{ isset($currencyPaymentMethod->method_data) && (json_decode($currencyPaymentMethod->method_data)->mode) == 'live' ? 'selected':"" }} >{{ __('live') }}</option>
		</select>
	</div>
</div>
<div class="clearfix"></div>
