<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Product;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'weight' => [
            ],
            'fineness' => [
            ],
            'length' => [
            ],
            'size' => [
            ],
            'clip_type' => [
            ],
            'short_description' => [
            ],
            'long_description' => [
            ],
            'brand' => [
            ],
            'status' => [
            ],
            'price_type' => [
            ],
            'discount_allowed' => [
            ],          
            'au' => [
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
