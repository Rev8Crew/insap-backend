<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|min:3',
            'password' => 'required|string|min:4|confirmed',
            'is_active' => 'integer'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
