<?php
declare(strict_types=1);

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'record_id' => 'required|integer|exists:records,id',

            'name' =>'nullable|string',
            'description' =>'nullable|string',
            'date' => 'nullable|string|date_format:Y-m-d',
            'process_id' => 'nullable|integer|exists:processes,id',
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
