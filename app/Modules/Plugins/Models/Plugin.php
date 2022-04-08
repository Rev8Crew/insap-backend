<?php

namespace App\Modules\Plugins\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Plugins\Models\Plugin
 *
 * @property int $id
 * @property string|null $name
 * @property string $slug
 * @property array|null $settings
 * @property string|null $service_class
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin active()
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereServiceClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plugin whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

