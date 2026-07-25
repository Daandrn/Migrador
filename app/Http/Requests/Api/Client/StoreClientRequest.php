<?php

namespace App\Http\Requests\Api\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'host'     => 'required|string',
            'port'     => 'required|numeric',
            'user'     => 'required|string',
            'password' => 'required|string',
            'db_name'  => 'required|string',
            'driver'   => 'required|string',
            'active'   => 'required|bool',
        ];
    }
}
