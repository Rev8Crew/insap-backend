<?php

namespace App\Modules\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Projects extends Model
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
        'image',
        'order',
        'is_active'
    ];
}
