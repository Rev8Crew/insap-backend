<?php

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordCreateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'description' => 'required|string|',
            'date' => 'nullable|date_format:Y-m-d',

            'process_id' => 'required|int|exists:processes,id',
            'record_data_id' => 'required|int|exists:record_data,id',
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
