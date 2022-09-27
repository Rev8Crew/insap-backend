<?php

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRecordsByRecordData extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'record_data_id' => 'required|integer|exists:record_data,id',
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
