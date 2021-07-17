<?php

namespace App\Modules\Importer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImporterCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'description' => 'string',

            'appliance_id' => 'required|integer'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
