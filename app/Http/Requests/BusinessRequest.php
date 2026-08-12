<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Business;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BusinessRequest extends FormRequest
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
            'zip' => [
            ],
            'zip_id' => [
            ],
            'city' => [
            ],
            'city_id' => [
            ],
            'address' => [
            ],
            'floor' => [
            ],
            'door' => [
            ],
            'status' => [
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
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
