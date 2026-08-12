<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Property;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
            'is_business' => [
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
            'is_franchise' => [
            ],
            'is_own' => [
            ],
            'stock_value' => [
            ],
            'pldege_value' => [
            ],
            'email' => [
            ],
            'phone' => [
            ],
            'facebook' => [
            ],
            'instagram' => [
            ],
            'tiktok' => [
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
