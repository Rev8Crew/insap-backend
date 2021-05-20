<?php

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCreateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4',
            'description' => 'string',
            'image' => 'file',
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
