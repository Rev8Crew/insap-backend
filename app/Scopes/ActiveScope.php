<?php

namespace App\Scopes;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

trait ActiveScope
{
    public function scopeActive(Builder $query) {
        $query->where('is_active', ActiveStatus::ACTIVE);
    }
}
