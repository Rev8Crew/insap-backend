<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'string|email',
            'name' => 'string|min:3',
            'password' => 'string|min:4|confirmed',
            'is_active' => 'integer'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
