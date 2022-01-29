<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 * @OA\Schema(
 *     schema="LoginRequest",
 *     @OA\Property(property="email", type="string", nullable=false, description="Пользовательский email"),
 *     @OA\Property(property="password", type="string", nullable=false, description="Пользовательский пароль"),
 * )
 */
class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' =>'required|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
