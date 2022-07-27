<?php

namespace App\Modules\Appliance\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GetByProjectRequest
 * @OA\Schema(
 *     schema="ApplianceByProjectRequest",
 *     @OA\Property(property="project_id", type="int", nullable=false, description="id проекта"),
\\
 * )
 */
class GetByProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'project_id' => 'required|int|exists:projects,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
