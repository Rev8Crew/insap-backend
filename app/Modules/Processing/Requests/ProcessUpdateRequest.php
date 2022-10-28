<?php
declare(strict_types=1);

namespace App\Modules\Processing\Requests;

use App\Enums\Process\ProcessInterpreter;
use Illuminate\Foundation\Http\FormRequest;

class ProcessUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'process_id' => 'required|int|exists:processes,id',

            'name' =>'nullable|string',
            'description' =>'nullable|string',
            'plugin_id' => 'nullable|int',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
