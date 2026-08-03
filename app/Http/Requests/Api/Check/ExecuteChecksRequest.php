<?php

namespace App\Http\Requests\Api\Check;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExecuteChecksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => [
                'required', 
                'integer', 
            ],
            'check_ids' => [ 
                'array',
            ],
            'check_ids.*' => [
                'integer', 
                'distinct',
            ],
        ];
    }
}
