<?php

namespace Plugins\adcp\Models;

use Illuminate\Database\Eloquent\Model;

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
        'depth_values',
        'date',
        'record_id'
    ];

    protected $casts = [
        'depth_values' => 'array'
    ];
}
