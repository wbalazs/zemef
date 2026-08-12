<?php

namespace App\Http\Requests;

use App\Rules\CurrentSignCodeCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class SignCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_sign_code' => ['required', 'min:6', new CurrentSignCodeCheckRule],
            'sign_code' => ['required', 'min:6', 'confirmed', 'different:old_sign_code'],
            'sign_code_confirmation' => ['required', 'min:6'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }
}
