<?php

namespace App\Modules\Project\Resources;

use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *     schema="RecordDataResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="order", type="integer"),
 *     @OA\Property(property="created_at", type="string"),
 *     @OA\Property(property="updated_at", type="string"),
 * )
 *
 * @mixin RecordData
 */
class RecordDataResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => $this->order,

            'date_start' => $this->date_start ? Carbon::parse($this->date_start)->format('d.m.Y H:i:s') : null,
            'date_end' => $this->date_end ? Carbon::parse($this->date_end)->format('d.m.Y H:i:s') : null,

            'image' => $this->image,

            'creator_user' => UserResource::make($this->whenLoaded('creatorUser')),

            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),
        ];
    }
}
