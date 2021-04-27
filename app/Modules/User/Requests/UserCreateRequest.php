<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'email'     => 'required|email|unique:users',
            'name'      => 'required|string|min:3',
            'password'  => 'required|string|min:4|confirmed',
            'is_active' => 'integer',
        ];
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
