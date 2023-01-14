<?php

namespace App\Modules\Processing\Resources;

use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Appliance\Resources\ApplianceResource;
use App\Modules\Processing\Models\Process;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *     schema="ProcessResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="interpreter", type="string"),
 *     @OA\Property(property="options", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="created_at", type="string"),
 *     @OA\Property(property="updated_at", type="string"),
 * )
 *
 * @mixin Process
 */
class ProcessResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,

            'process_type' => ProcessType::labels()[$this->type],
            'process_type_id' => $this->type,

            'children' => self::make($this->whenLoaded('children')),

            'process_interpreter' => ProcessInterpreter::labels()[$this->interpreter],
            'process_interpreter_class' => $this->interpreter,

            'options' => $this->options,

            'appliance_id' => $this->appliance_id,
            'plugin_id' => $this->plugin_id,

            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),

            'appliance' => ApplianceResource::make($this->whenLoaded('appliance')),
            'user' => UserResource::make($this->whenLoaded('user')),

            'fields' => $this->whenLoaded('fields'),
            'plugin' => $this->whenLoaded('plugin'),

            'archive' => $this->whenLoaded('archiveFile')
        ];
    }
}
