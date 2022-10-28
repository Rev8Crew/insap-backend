<?php

namespace App\Modules\Project\Resources;

use App\Modules\Processing\Resources\ProcessResource;
use App\Modules\Project\Models\Record;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin Record */
class RecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => $this->order,
            'date' => $this->date,

            'files' => $this->files,
            'params' => $this->params,

            'import_status' => $this->import_status,
            'import_error' => $this->import_error,

            'user' => UserResource::make($this->whenLoaded('user')),
            'process' => ProcessResource::make($this->whenLoaded('process')),
            'record_data' => RecordDataResource::make($this->whenLoaded('recordData')),

            'is_active' => $this->is_active,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d.m.Y H:i:s'),
        ];
    }
}
