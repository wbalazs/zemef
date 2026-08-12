<?php

namespace App\Http\Requests;

use App\Tag;
use App\Models\Tool;
use App\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ToolRequest extends FormRequest
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
            'from' => [
                'required'
            ],
            'identity_number' => [
            ],
            'serial_number' => [
            ],
            'description' => [
            ],
            'failure_description' => [
            ],
            'entry_date' => [
            ],
            'withdrawal_date' => [
            ],
            'value' => [
            ],
            'review_needed' => [
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
            'name' => 'Név',
            'from' => 'Partner',
            'identity_number' => 'Identity number',
            'serial_number' => 'Serial number',
            'tool_type_comment' => 'Típus megjegyzés',
            'failure_description' => 'Failure description',
            'entry_date' => 'Entry date',
            'withdrawal_date' => 'Withdrawal date',
            'value' => 'Value',
            'review_needed' => 'Review needed',
            'status' => 'Status'
        ];
    }
}
