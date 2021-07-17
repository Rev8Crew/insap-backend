<?php

namespace App\Modules\Importer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImporterDeleteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
