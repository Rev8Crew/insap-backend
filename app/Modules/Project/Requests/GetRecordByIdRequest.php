<?php
declare(strict_types=1);

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetRecordByIdRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id' => 'required|int|exists:records,id'
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
