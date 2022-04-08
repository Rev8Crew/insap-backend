<?php

namespace Plugins\adcp\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Plugins\adcp\Models\Adcp
 *
 * @property int $id
 * @property int|null $step_id
 * @property int|null $expedition_number
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $distance
 * @property float|null $speed
 * @property float|null $max_depth
 * @property array|null $depths
 * @property string $date
 * @property int $record_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereDepths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereExpeditionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereMaxDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Adcp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Adcp extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'step_id',
        'latitude',
        'longitude',
        'distance',
        'speed',
        'max_depth',
        'depths',
        'date',
        'expedition_number',
        'record_id'
    ];

    protected $casts = [
        'depths' => 'array'
    ];
}
