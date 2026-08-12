<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Customer;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => [
                'required', 'min:3'
            ],
            'is_company' => [
            ],
            'tax_identity' => [
            ],
            'card_number' => [
            ],
            'mothers_name' => [
            ],
            'birth_place' => [
            ],
            'birth_date' => [
            ],
            'identity_card_number' => [
            ],
            'marketing_accept' => [
            ],
            'terms_accept' => [
            ],
            'email' => [
            ],
            'phone' => [
            ],
            'vzj' => [
            ],
            'au' => [
            ],
            'comment' => [
            ],
            'web' => [
            ],
            'company_registration_number' => [
            ],
            'tax_number' => [
            ],
            'registration_number' => [
            ]

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
            
        ];
    }
}
