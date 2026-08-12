<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Posting;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostingRequest extends FormRequest
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

    public function messages()
    {
        return [
            'from_to.required' => 'A Honnan-Hová mező megadása kötelező.',
            'from_to.min' => 'A Honnan-Hova mezőnek legalább 3 karakter hosszúnak kell lennie.',
            'posting_date.required' => 'A kiküldetés dátuma kötelező.',
            'posting_date.date' => 'A kiküldetés dátumának érvényes dátumnak kell lennie.',
            'customer_id.required' => 'Ügyfél megadása kötelező.',
            'distance.required' => 'A távolság megadása kötelező.',
            'employee_id.required' => 'Munkavállaló kiválasztása kötelező.',
            'vehicle_id.required' => 'Jármű kiválasztása kötelező.'
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_to' => [
                'required', 'min:3'
            ],
            'posting_date' => [
                'required', 'date'
            ],
            'customer_id' => [
                'required', 'integer'
            ],
            'distance' => [
                'required', 'integer'
            ],
            'employee_id' => [
                'required', 'integer'
            ],
            'vehicle_id' => [
                'required', 'integer'
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
