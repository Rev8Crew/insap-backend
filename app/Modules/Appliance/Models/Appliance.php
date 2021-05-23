<?php

namespace App\Modules\Appliance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * App\Modules\Appliance\Models\Appliance
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int $is_active Is project active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appliance extends Model
{
    use HasFactory, LogsActivity;

    /**
     *  Log all fillable attr
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];
}
