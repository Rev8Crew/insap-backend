<?php

namespace App\Modules\Plugins\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    public const TEST_PLUGIN_SLUG = 'adcp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'settings',
        'is_active',
        'slug',
        'service_class'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function scopeActive($query)
    {
        $query->where('is_active', ActiveStatus::ACTIVE);
    }
}

