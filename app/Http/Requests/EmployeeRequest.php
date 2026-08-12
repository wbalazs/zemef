<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Employee;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'firstname' => [
                'required', 'min:3'
            ],
            'lastname' => [
                'required', 'min:3'
            ],
            'companies' => [
                'required'
            ],
            'businesses' => [
                'required'
            ],
            'maiden_name' => [
            ],
            'birth_place' => [
            ],
            'birth_date' => [
            ],
            'tax_number' => [
            ],
            'social_number' => [
            ],
            'address_card_number' => [
            ],
            'identity_card_number' => [
            ],
            'home_zip' => [
            ],
            'home_zip_id' => [
            ],
            'home_city' => [
            ],
            'home_city_id' => [
            ],
            'home_address' => [
            ],
            'home_floor' => [
            ],
            'home_door' => [
            ],
            'mailing_zip' => [
            ],
            'mailing_zip_id' => [
            ],
            'mailing_city' => [
            ],
            'mailing_city_id' => [
            ],
            'mailing_address' => [
            ],
            'mailing_floor' => [
            ],
            'mailing_door' => [
            ],
            'private_phone' => [
            ],
            'company_phone' => [
            ],
            'private_email' => [
            ],
            'start_date' => [
                'date_format:d-m-Y'
            ],
            'end_date' => [
                'date_format:d-m-Y'
            ],
            'status' => [
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
