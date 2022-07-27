<?php

namespace App\Modules\Project\Resources;

use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin RecordData */
class RecordDataResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => $this->order,

            'is_active' => $this->is_active,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),
        ];
    }
}
