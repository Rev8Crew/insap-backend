<?php
declare(strict_types=1);

namespace App\Modules\Processing\Requests;

use App\Enums\Process\ProcessInterpreter;
use Illuminate\Foundation\Http\FormRequest;

class ProcessUpdateArchiveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'process_id' => 'required|int|exists:processes,id',
            'archive' => 'required|file',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
