<?php
declare(strict_types=1);

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordDataCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => 'required|int|exists:projects,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'date_start' => 'nullable|date_format:Y-m-d',
            'date_end' => 'nullable|date_format:Y-m-d',
            'image' => 'nullable|file'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
