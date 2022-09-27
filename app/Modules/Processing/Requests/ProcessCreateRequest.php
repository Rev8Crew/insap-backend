<?php

namespace App\Modules\Processing\Requests;

use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use Illuminate\Foundation\Http\FormRequest;


class ProcessCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => 'required|int|exists:projects,id',
            'type' =>'required|int|min:0',
            'interpreter' => 'required|string|in:' . implode(',', ProcessInterpreter::variants()),
            'archive' => 'required|file',

            'name' =>'nullable|string',
            'description' =>'nullable|string',
            'options' => 'nullable|string',
            'appliance_id' => 'required|int|exists:appliances,id',
            'plugin_id' => 'nullable|int|',

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
