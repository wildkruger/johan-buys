<?php

namespace Modules\CryptoExchange\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CryptoUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (request()->receive_with == 'address' &&  request()->pay_with == 'others') {
            return [
                'payment_details' => 'required', 
                'proof_file' => 'required',
                'crypto_address' => 'required', 
            ];    
        } elseif( request()->receive_with == 'address' ) {
             return [
                'crypto_address' => 'required', 
            ];
        } elseif (request()->pay_with == 'others') {
            return [
                'payment_details' => 'required', 
                'proof_file' => 'required', 
            ];
        } else {
            return [];
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function fieldNames()
    {

        return [
            'payment_details' => __('Payment Details'), 
            'proof_file' => __('Proof File'),
            'crypto_address' => __('Crypto Address'),
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
