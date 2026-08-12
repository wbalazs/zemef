<?php

namespace App\Http\Requests;


use App\Models\Photography;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PhotographyRequest extends FormRequest
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
            'list_name' => ['required', 'min:3'],
            'products_array' => ['required', 'array'],
            'products_array.*' => ['required', 'exists:products,product_id']
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
            'list_name' => 'lista megnevezése',
            'products_array.*' => 'termékek',
            'products_array' => 'termékek'
        ];
    }
}
