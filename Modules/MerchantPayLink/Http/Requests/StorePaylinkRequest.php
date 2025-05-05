<?php

namespace Modules\MerchantPayLink\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaylinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'currency_id' => 'required|integer|exists:currencies,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
