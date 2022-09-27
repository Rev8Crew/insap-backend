<?php

namespace App\Modules\Plugins\Resources;

use App\Modules\Plugins\Models\Plugin;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Plugin $resource
 */
class PluginResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'service_class' => $this->resource->service_class,
            'is_active' => $this->resource->is_active,
            'settings' => $this->resource->settings
        ];
    }
}
