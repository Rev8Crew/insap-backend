<?php

namespace App\Modules\Importer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImporterImportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'importer_id' => 'required|integer',
            'record_id' => 'required|integer',
            'params' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
